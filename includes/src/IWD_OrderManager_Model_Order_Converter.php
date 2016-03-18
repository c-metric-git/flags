<?php
class IWD_OrderManager_Model_Order_Converter extends Mage_Core_Model_Abstract
{
    private $quote;

    public function convertOrderToQuote($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        $this->quote = Mage::getModel('sales/quote');
        $this->assignCustomerToQuote($order);
        $this->assignStoreToQuote($order);

        $this->quote->save();

        $this->assignAddressesToQuote($order);
        $this->quote->setIsActive(0);

        return $this->quote;
    }

    protected function assignCustomerToQuote($order){
        $store = Mage::getModel('core/store')->load($order->getStoreId());
        $website_id = $store->getWebsiteId();
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($website_id);
        $customer->loadByEmail($order->getCustomerEmail());
        $this->quote->assignCustomer($customer);

        if ($order->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $this->quote->assignCustomer($customer);
        } else {
            $this->quote->setIsMultiShipping(false)
                ->setCheckoutMethod('guest')
                ->setCustomerId(null)
                ->setCustomerEmail($order->getCustomerEmail())
                ->setCustomerIsGuest(true)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }
    }

    protected function assignAddressesToQuote($order)
    {
        $order_billing_address = $order->getBillingAddress();
        $order_shipping_address = $order->getShippingAddress();

        try{
            $address_form = Mage::getModel('customer/form');
            $address_form->setFormCode('customer_address_edit')->setEntityType('customer_address');
            $attributes = $address_form->getAttributes();
        } catch(Exception $e) {
            $type = Mage::getModel('eav/entity_type')->loadByCode('customer');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type);
        }

        foreach ($attributes as $attribute) {
            if (isset($order_shipping_address[$attribute->getAttributeCode()])) {
                $this->quote->getShippingAddress()->setData($attribute->getAttributeCode(), $order_shipping_address[$attribute->getAttributeCode()]);
            }

            if (isset($order_billing_address[$attribute->getAttributeCode()])) {
                $this->quote->getBillingAddress()->setData($attribute->getAttributeCode(), $order_billing_address[$attribute->getAttributeCode()]);
            }
        }

        $this->quote->getShippingAddress()
            ->setShippingMethod($order->getShippingMethod())
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setQuoteId($this->quote->getEntityId());

        $this->quote->getBillingAddress()
            ->setQuoteId($this->quote->getEntityId());

        $this->quote->getShippingAddress()->save();
        $this->quote->getBillingAddress()->save();
    }

    protected function assignStoreToQuote($order){
        $store_id = $order->getStoreId();
        $store = Mage::getModel('core/store')->load($order->getStoreId());
        $this->quote->setStore($store)->setStoreId($store_id);
    }



    public function createNewQuoteItems($order_id, $items)
    {
        $order = Mage::getModel('sales/order')->load($order_id);
        $quote = $this->createQuote($order, $items);
        return $quote->getAllItems();
    }

    public function removeAllQuoteItems($quote){
        $all_quote_items = $quote->getAllItems();
        foreach($all_quote_items as $item)
        {
            $quote->removeItem($item->getId())->save();
        }
        return $quote;
    }

    public function createQuote($order, $items=null)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $store_id = $order->getStoreId();
        $store = Mage::getModel('core/store')->load($order->getStoreId());
        $website_id = $store->getWebsiteId();

        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($website_id);
        $customer->loadByEmail($order->getCustomerEmail());

        $quote = Mage::getModel('sales/quote')->assignCustomer($customer);
        $quote = $quote->setStore($store)->setStoreId($store_id);

        foreach ($items as $product_id => $item) {
            $params = new Varien_Object($item);
            $this->addProductToQuote($quote, $product_id, $params);
        }

        if ($order->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $quote->assignCustomer($customer);
        } else {
            $quote->setIsMultiShipping(false);
            $quote->setCheckoutMethod('guest');
            $quote->setCustomerId(null);
            $quote->setCustomerEmail($order->getCustomerEmail());
            $quote->setCustomerIsGuest(true);
            $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        try{
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address');
            $attributes = $addressForm->getAttributes();
        } catch (Exception $e){
            $type = Mage::getModel('eav/entity_type')->loadByCode('customer_address');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type);
        }

        foreach ($attributes as $attribute) {
            if (isset($shippingAddress[$attribute->getAttributeCode()])) {
                $quote->getShippingAddress()->setData($attribute->getAttributeCode(), $shippingAddress[$attribute->getAttributeCode()]);
            }
            if (isset($billingAddress[$attribute->getAttributeCode()])) {
                $quote->getBillingAddress()->setData($attribute->getAttributeCode(), $billingAddress[$attribute->getAttributeCode()]);
            }
        }

        $quote->getShippingAddress()
            ->setShippingMethod($order->getShippingMethod())
            ->setCollectShippingRates(true)
            ->collectShippingRates();

        $quote->setTotalsCollectedFlag(false)->collectTotals();

        $quote->setIsActive(0);
        $quote->save();

        return $quote;
    }

    public function addProductToQuote($quote, $product_id, $params)
    {
        $quote_item = null;
        $product = Mage::getModel('catalog/product')
            ->setStoreId($quote->getStoreId())
            ->load($product_id);
        if ($product->getId()) {
            $product->setSkipCheckRequiredOption(true);
        }
        try {
            $quote_item = $quote->addProduct($product, $params);
            return $quote_item;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return null;
        }
    }

    public function createNewOrderItem($order_item_id, $options)
    {
        if(!isset($options['item'][$order_item_id])){
            return null;
        }

        $options = $options['item'][$order_item_id];

        $order_item = Mage::getModel('sales/order_item')->load($order_item_id);
        $options['product'] = $order_item->getProductId();

        //$this->updateProductFiles($options);
        $options = $this->_processFiles($options, $order_item_id);
        $quote_item_id = $order_item->getQuoteItemId();
        $quote_item = Mage::getModel('sales/quote_item')->load($quote_item_id);
        $store = Mage::app()->getStore($quote_item->getStoreId());
        $quote_id = $quote_item->getData('quote_id');
        $quote = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);
        $quote_item->setQuote($quote);

        $quote_item = $quote->updateItem($quote_item_id, $options);
        $quote->collectTotals();

        $item = Mage::getModel('sales/convert_quote')->itemToOrderItem($quote_item);

        return $item;
    }

    protected function _processFiles($options, $id)
    {
        $buyRequest = new Varien_Object($options);
        $params = array('files_prefix' => 'item_' . $id . '_');
        $buyRequest =  Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);

        if ($buyRequest->hasData()) {
            $product = Mage::getModel('catalog/product')->load($options['product']);
            $product->getTypeInstance(true)
                ->prepareForCartAdvanced($buyRequest, $product, Mage_Catalog_Model_Product_Type_Abstract::PROCESS_MODE_LITE);

            return $buyRequest->getData();
        }

        return $options;
    }
}