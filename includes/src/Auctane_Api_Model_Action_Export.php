<?php
/**
 * ShipStation
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@auctane.com so we can send you a copy immediately.
 *
 * @category    Shipping
 * @package     Auctane_Api
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Auctane_Api_Model_Action_Export
{
    /**
     * Perform an export according to the given request.
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @throws Exception
     */
    public function process(Mage_Core_Controller_Request_Http $request, Mage_Core_Controller_Response_Http $response)
    {
        // In case store is part of URL path use it to choose config.
        $store = $request->get('store');
        if ($store)
            $store = Mage::app()->getStore($store);

        $apiConfigCharset = Mage::getStoreConfig("api/config/charset", $store);

        $startDate = strtotime(urldecode($request->getParam('start_date')));
        $endDate = strtotime(urldecode($request->getParam('end_date')));
        if (!$startDate || !$endDate)
            throw new Exception('Start and end dates are required', 400);

        $page = (int) $request->getParam('page');

        /* @var $orders Mage_Sales_Model_Mysql4_Order_Collection */
        $orders = Mage::getResourceModel('sales/order_collection');
        // might use 'created_at' attribute instead
        $from = date('Y-m-d H:i:s', $startDate);
        $end = date('Y-m-d H:i:s', $endDate);
        $orders->addAttributeToFilter('updated_at', array('from' => $from,'to' => $end));
        if ($store)
            $orders->addAttributeToFilter('store_id', $store->getId());
        if ($page > 0)
            $orders->setPage($page, $this->_getExportPageSize());
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', $apiConfigCharset);
        $this->_writeOrders($orders, $xml, $store ? $store->getId() : 0);
        $xml->endDocument();

        $response->clearHeaders()
                ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                ->setBody($xml->outputMemory(true));
    }

     /**
     * get the size of page
     */
    protected function _getExportPageSize()
    {
        return (int) Mage::getStoreConfig('auctaneapi/config/exportPageSize');
    }

    /**
     * Write the ordes in xml file
     *
     * @param Varien_Data_Collection $orders
     * @param XMLWriter $xml
     * @param integer $storeId
     */
    protected function _writeOrders(Varien_Data_Collection $orders, XMLWriter $xml, $storeId = null)
    {
        $xml->startElement('Orders');
        $xml->writeAttribute('pages', $orders->getLastPageNumber());
        foreach ($orders as $order) {
            $this->_writeOrder($order, $xml, $storeId);
        }
        $xml->startElement('Query');
        $xml->writeCdata($orders->getSelectSql());
        $xml->endElement(); // Query
        $xml->startElement('Version');
        $xml->writeCdata('Magento ' . Mage::getVersion());
        $xml->endElement(); // Version
        $xml->startElement('Extensions');
        $xml->writeCdata(Mage::helper('auctaneapi')->getModuleList());
        $xml->endElement(); // Extensions
        $xml->endElement(); // Orders
    }

    /**
     * Write the order in xml file
     *
     * @param Mage_Sales_Model_Order $order
     * @param XMLWriter $xml
     * @param integer $storeId
     */
    protected function _writeOrder(Mage_Sales_Model_Order $order, XMLWriter $xml, $storeId = null)
    {
        $history = '';
        /* @var $status Mage_Sales_Model_Order_Status */
        foreach ($order->getStatusHistoryCollection() as $status) {
            if ($status->getComment()) {
                $history .= $status->getCreatedAt() . PHP_EOL;
                $history .= $status->getComment() . PHP_EOL . PHP_EOL;
            }
        }
        $history = trim($history);
        if ($history) {
            $order->setStatusHistoryText($history);
        }
        /* @var $gift Mage_GiftMessage_Model_Message */
        $gift = Mage::helper('giftmessage/message')->getGiftMessage($order->getGiftMessageId());
        $order->setGift($gift->isObjectNew() ? 'false' : 'true');
        
        $sender = $gift->getSender();
        $recipient = $gift->getRecipient();
        
        if (!$gift->isObjectNew()) {
            $message = sprintf("From: %s\nTo: %s\nMessage: %s", $sender, $recipient, $gift->getMessage());
            $order->setGiftMessage($message);
        }
        $helper = Mage::helper('auctaneapi');
        $xml->startElement('Order');
        $price = Auctane_Api_Model_System_Source_Config_Prices::BASE_PRICE;
        if ($helper->getExportPriceType($order->getStoreId()) == $price) {
            $helper->fieldsetToXml('base_sales_order', $order, $xml);
        } else {
            $helper->fieldsetToXml('sales_order', $order, $xml);
        }
        $xml->startElement('Customer');
        $xml->startElement('CustomerCode');
        $xml->writeCdata($order->getCustomerEmail());
        $xml->endElement(); // CustomerCode
        $xml->startElement('BillTo');
        $helper->fieldsetToXml('sales_order_billing_address', $order->getBillingAddress(), $xml);
        $xml->endElement(); // BillTo
        $xml->startElement('ShipTo');
        $helper->fieldsetToXml('sales_order_shipping_address', $order->getShippingAddress(), $xml);
        $xml->endElement(); // ShipTo
        $xml->endElement(); // Customer
        /** add purchase order nubmer */
        Mage::helper('auctaneapi')->writePoNumber($order, $xml);

        $xml->startElement('Items');
        //Check for the bundle child product to import
        $intImportChildProducts = Mage::getStoreConfig('auctaneapi/general/import_child_products');
        
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach ($order->getItemsCollection($helper->getIncludedProductTypes()) as $item) {
           $isBundle = 0;
            //Check for the parent bundle item type
            $parentItem = $this->_getOrderItemParent($item);
            if ($parentItem->getProductType() === 'bundle') {
                if ($intImportChildProducts == 2) {
                    continue;
                } else {
                    $isBundle = 1;
                }
            }
            $this->_orderItem($item, $xml, $storeId, $isBundle);
        }
        
        $intImportDiscount = Mage::getStoreConfig('auctaneapi/general/import_discounts');
        if ($intImportDiscount != 2) { // Import Discount is true
            $discounts = array();
            if ($order->getData('auctaneapi_discounts')) {
                $discounts = unserialize($order->getData('auctaneapi_discounts'));
                if (is_array($discounts)) {
                    $aggregated = array();
                    foreach ($discounts as $key => $discount) {
                        $keyData = explode('-', $key);
                        if (isset($aggregated[$keyData[0]])) {
                            $aggregated[$keyData[0]] += $discount;
                        } else {
                            $aggregated[$keyData[0]] = $discount;
                        }
                    }
                    Mage::helper('auctaneapi')->writeDiscountsInfo($aggregated, $xml);
                }
            }
        }
        $xml->endElement(); // Items
        $xml->endElement(); // Order
    }

    /**
     * Write the order item in xml file
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param XMLWriter $xml
     * @param integer $storeId
     * @param boolean $isBundle
     */
    protected function _orderItem(Mage_Sales_Model_Order_Item $item, XMLWriter $xml, $storeId = null, $isBundle = 0)
    {
        // inherit some attributes from parent order item
        if ($item->getParentItemId() && !$item->getParentItem()) {
            $item->setParentItem(Mage::getModel('sales/order_item')->load($item->getParentItemId()));
        }
        
        $exclude = Mage::helper('auctaneapi')->isExcludedProductType($item->getParentItem()->getProductType());
        // only inherit if parent has been hidden
        if ($item->getParentItem() && ($item->getPrice() == 0.000) && $exclude) {
            //set the store price of item from parent item
            $item->setPrice($item->getParentItem()->getPrice());
            //set the base price of item from parent item
            $item->setBasePrice($item->getParentItem()->getBasePrice());
        }

        if (!$item->getGiftMessageId() && $item->getParentItem()) {
             $giftId = $item->getParentItem()->getGiftMessageId();
        } else {
            $giftId = $item->getGiftMessageId();
        }
        /* @var $gift Mage_GiftMessage_Model_Message */
        $gift = Mage::helper('giftmessage/message')->getGiftMessage($giftId);
        $item->setGift($gift->isObjectNew() ? 'false' : 'true');
        $sender = $gift->getSender();
        $recipient = $gift->getRecipient();
        if (!$gift->isObjectNew()) {
            $message = sprintf("From: %s\nTo: %s\nMessage: %s", $sender, $recipient, $gift->getMessage());
            $item->setGiftMessage($message);
        }

       /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($item->getProductId());
        
        // inherit some attributes from parent product item
        if (($parentProduct = $this->_getOrderItemParentProduct($item, $storeId))) {
            if (!$product->getImage() || ($product->getImage() == 'no_selection'))
                $product->setImage($parentProduct->getImage());
            if (!$product->getSmallImage() || ($product->getSmallImage() == 'no_selection'))
                $product->setSmallImage($parentProduct->getSmallImage());
            if (!$product->getThumbnail() || ($product->getThumbnail() == 'no_selection'))
                $product->setThumbnail($parentProduct->getThumbnail());
        }

        $xml->startElement('Item');

        $helper = Mage::helper('auctaneapi');
        $priceType = Mage::helper('auctaneapi')->getExportPriceType($item->getOrder()->getStoreId());
        if ($priceType == Auctane_Api_Model_System_Source_Config_Prices::BASE_PRICE) {
            $helper->fieldsetToXml('base_sales_order_item', $item, $xml, $isBundle);
        } else {
            $helper->fieldsetToXml('sales_order_item', $item, $xml, $isBundle);
        }

        /* using emulation so that product images come from the correct store */
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($product->getStoreId());
        Mage::helper('auctaneapi')->fieldsetToXml('sales_order_item_product', $product, $xml);
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        $xml->startElement('Options');
        $this->_productAttribute($product, $xml, $storeId);
        $xml->endElement(); // Options
        $xml->endElement(); // Item
    }

     /**
     * Write the product attribute used in order
     *
     * @param Mage_Catalog_Model_Product $product
     * @param XMLWriter $xml
     * @param integer $storeId
     */
    protected function _productAttribute(Mage_Catalog_Model_Product $product, XMLWriter $xml, $storeId = null)
    {
        // custom attributes are specified in Admin > Configuration > Sales > Auctane Shipstation API
        // static because attributes can be cached, they do not change during a request
        static $attrs = null;
        if (is_null($attrs)) {
            $attrs = Mage::getResourceModel('eav/entity_attribute_collection');
            $attrIds = explode(',', Mage::getStoreConfig('auctaneapi/general/customattributes', $storeId));
            $attrs->addFieldToFilter('attribute_id', $attrIds);
        }

        /* @var $attr Mage_Eav_Model_Entity_Attribute */
        foreach ($attrs as $attr) {
            if ($product->hasData($attr->getName())) {
                // if an attribute has options/labels
                if (in_array($attr->getFrontendInput(), array('select', 'multiselect'))) {
                    $value = $product->getAttributeText($attr->getName());
                    if (is_array($value))
                        $value = implode(',', $value);
                } else {
                    $value = $product->getDataUsingMethod($attr->getName());
                }
                if ($value) {
                    //item option
                    $option = array(
                        'value' => $value,
                        'label' => $attr->getFrontendLabel()
                    );
                    $this->_writeOrderItemOption($option, $xml, $storeId);
                }
            }
        }
    }

    /**
     * Write the options used in order
     *
     * @param string $option
     * @param XMLWriter $xml
     */
    protected function _writeOrderItemOption($option, XMLWriter $xml)
    {
        $xml->startElement('Option');
        Mage::helper('auctaneapi')->fieldsetToXml('sales_order_item_option', $option, $xml);
        $xml->endElement(); // Option
    }

    /**
     * Safe way to lookup parent order items.
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Order_Item
     */
    protected function _getOrderItemParent(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getParentItem()) {
            return $item->getParentItem();
        }

        $parentItem = Mage::getModel('sales/order_item')
                ->load($item->getParentItemId());
        $item->setParentItem($parentItem);
        return $parentItem;
    }

    /**
     * Get the parent product of current item in a order
     * 
     * @param Mage_Sales_Model_Order_Item $item
     * @param mixed $storeId
     * @return Mage_Catalog_Model_Product
     */
    protected function _getOrderItemParentProduct(Mage_Sales_Model_Order_Item $item, $storeId = null)
    {
        if ($item->getParentItemId()) {
            // cannot use getParentItem() because we stripped parents from the order
            $parentItem = $this->_getOrderItemParent($item);
            // initialise with store so that images are correct
            return Mage::getModel('catalog/product')
                            ->setStoreId($storeId)
                            ->load($parentItem->getProductId());
        }
        return null;
    }

}
