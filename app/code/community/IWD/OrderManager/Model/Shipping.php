<?php
class IWD_OrderManager_Model_Shipping extends Mage_Core_Model_Abstract
{
    const CustomMethodCode = 'ordermanager_custom';

    protected $params;

    public function updateOrderShipping($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $shipping = $this->prepareShippingObj($this->params);

            $this->editSipping($this->params['order_id'], $shipping);
            $this->addChangesToLog();
        }
    }

    public function editSipping($order_id, $shipping)
    {
        /* @var $order_edit IWD_OrderManager_Model_Order_Edit */
        $order_edit = Mage::getModel('iwd_ordermanager/order_edit');
        $order = Mage::getModel('sales/order')->load($order_id);
        $old_order = clone $order;

        $old_shipping = $order->getShippingDescription() . " (" . Mage::helper('core')->currency($order->getShippingInclTax(), true, false) . ")";

        $this->updateOrderShippingInformation($order, $shipping);

        $order_edit->collectOrderTotals($order_id);
        $order_edit->updateOrderPayment($order_id, $old_order);

        $new_shipping = $shipping->getDescription() . " (" . Mage::helper('core')->currency($shipping->getAmountInclTax(), true, false) . ")";
        Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("shipping_method", $old_shipping, $new_shipping);

        $this->notifyEmail();

        return 1;
    }

    public function getShippingRates($order)
    {
        $request = $this->prepareShippingRequest($order);
        $shipping = Mage::getModel('shipping/shipping');
        $result = $shipping->collectRates($request)->getResult();

        if ($result) {
            $rates = array();
            foreach ($result->getAllRates() as $_rate) {
                $rate = new Varien_Object();
                $rate->setData($_rate->getData());
                $carrier = $rate->getCarrier();

                if (!isset($rates[$carrier])) {
                    $rates[$carrier] = array();
                }

                $rate->setCode($carrier . '_' . $rate->getMethod());
                $rates[$carrier][$rate->getCode()] = $rate;
            }
            return $rates;
        }
        return null;
    }

    protected function init($params){
        if (!isset($params['order_id'])) {
            throw new Exception("Order id is not defined");
        }

        $this->params = $params;
    }

    protected function notifyEmail(){
        $notify = isset($this->params['notify']) ? $this->params['notify'] : null;
        $order_id = $this->params['order_id'];

        if ($notify) {
            $message = isset($this->params['comment_text']) ? $this->params['comment_text'] : null;
            $email = isset($this->params['comment_email']) ? $this->params['comment_email'] : null;
            $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $email, $message);
        }
    }

    protected function addChangesToConfirm()
    {
        $order_id = $this->params['order_id'];
        $order = Mage::getModel('sales/order')->load($order_id);
        $shipping = $this->prepareShippingObj($this->params);

        $old_shipping = $order->getShippingDescription() . " (" . Mage::helper('core')->currency($order->getBaseShippingInclTax(), true, false) . ")";
        $new_shipping = $shipping->getDescription() . " (" . Mage::helper('core')->currency($shipping->getAmountInclTax(), true, false) . ")";

        $base_currency_code = $order->getBaseCurrencyCode();
        $order_currency_code = $order->getOrderCurrencyCode();
        $base_grand_total = $order->getBaseGrandTotal() - $order->getBaseShippingInclTax() + $shipping->getAmountInclTax();
        $grand_total = Mage::helper('directory')->currencyConvert($base_grand_total, $base_currency_code, $order_currency_code);;
        $totals = array(
            'grand_total' => $grand_total,
            'base_grand_total' => $base_grand_total,
        );

        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        $logger->addNewTotalsToLog($totals);
        $logger->addChangesToLog("shipping_method", $old_shipping, $new_shipping);
        $logger->addCommentToOrderHistory($order_id, 'wait');
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::SHIPPING, $order_id, $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Order update not yet applied. Customer has been sent an email with a confirmation link. Updates will be applied after confirmation.');
        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];
        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::SHIPPING, $order_id);
    }

    protected function updateOrderShippingInformation($order, $shipping)
    {
        $base_shipping_incl_tax = $shipping->getAmountInclTax();
        $base_shipping_amount = $shipping->getAmount();
        $base_shipping_tax_amount = $base_shipping_incl_tax - $base_shipping_amount;

        /** convert currency **/
        $base_currency_code = $order->getBaseCurrencyCode();
        $order_currency_code = $order->getOrderCurrencyCode();
        if ($base_currency_code === $order_currency_code) {
            $shipping_amount = $base_shipping_amount;
            $shipping_incl_tax = $base_shipping_incl_tax;
            $shipping_tax_amount = $base_shipping_tax_amount;
        } else {
            $directory = Mage::helper('directory');
            $shipping_amount = $directory->currencyConvert($base_shipping_amount, $base_currency_code, $order_currency_code);
            $shipping_incl_tax = $directory->currencyConvert($base_shipping_incl_tax, $base_currency_code, $order_currency_code);
            $shipping_tax_amount = $directory->currencyConvert($base_shipping_tax_amount, $base_currency_code, $order_currency_code);
        }

        $order
            ->setShippingDescription($shipping->getDescription())->setShippingMethod($shipping->getMethod())
            ->setShippingAmount($shipping_amount)->setBaseShippingAmount($base_shipping_amount)
            ->setShippingInclTax($shipping_incl_tax)->setBaseShippingInclTax($base_shipping_incl_tax)
            ->setShippingTaxAmount($shipping_tax_amount)->setBaseShippingTaxAmount($base_shipping_tax_amount)
            ->save();
    }

    /* edit order  - recollect shipping */
    public function recollectShippingAmount($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        if ($order->getShippingMethod() == self::CustomMethodCode) {
            return $this->recollectShippingWithCustomShippingMethod($order);
        }

        return $this->recollectShippingWithStandardShippingMethod($order);
    }

    protected function recollectShippingWithCustomShippingMethod($order)
    {
        //add shipping tax
        $tax_amount = $order->getTaxAmount() + $order->getShippingTaxAmount();
        $base_tax_amount = $order->getBaseTaxAmount() + $order->getBaseShippingTaxAmount();
        $order->setTaxAmount($tax_amount)->setBaseTaxAmount($base_tax_amount)->save();

        return $order->getShippingAmount() - $order->getShippingInvoiced();
    }

    protected function recollectShippingWithStandardShippingMethod($order)
    {
        $old_amount = $order->getBaseShippingInclTax();
        $request = $this->prepareShippingRequest($order);

        $result = Mage::getModel('shipping/shipping')
            ->collectRates($request)
            ->getResult();

        if ($result) {
            $shipping_rates = $result->getAllRates();

            foreach ($shipping_rates as $shipping_rate) {
                $rate = Mage::getModel('sales/quote_address_rate')->importShippingRate($shipping_rate)->getData();
                if ($order->getShippingMethod() == $rate['code']) {

                    /** convert currency **/
                    $base_currency_code = $order->getBaseCurrencyCode();
                    $order_currency_code = $order->getOrderCurrencyCode();
                    $price = Mage::helper('directory')->currencyConvert($rate["price"], $base_currency_code, $order_currency_code);;
                    $base_price = $rate["price"];

                    $new_amount = $this->collectShipping($order, $price, $base_price);

                    $new_amount_currency = Mage::helper('core')->currency($new_amount, true, false);
                    $old_amount_currency = Mage::helper('core')->currency($old_amount, true, false);
                    Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("shipping_amount", $old_amount_currency, $new_amount_currency);

                    return $base_price - $order->getShippingInvoiced();
                }
            }
        }

        return $order->getShippingAmount() - $order->getShippingInvoiced();
    }


    public function prepareShippingRequest($order)
    {
        if($order->getIsVirtual()){
            return null;
        }

        $qty_ordered = $this->getQtyOrderItems($order);
        $weight = $this->getOrderWeight($order);

        $store_id = $order->getStoreId();
        $website_id = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
        $shipping_id = $order->getShippingAddress()->getId();
        $address = Mage::getModel('sales/order_address')->load($shipping_id);
        $request = Mage::getModel('shipping/rate_request');
        //$base_currency_code = $order->getBaseCurrencyCode();
        $base_currency_code = Mage::app()->getStore()->getBaseCurrency();
        $order_currency_code = $order->getOrderCurrencyCode();

        $request->setDestCountryId($address->getCountryId())
            ->setDestRegionId($address->getRegionId())
            ->setDestPostcode($address->getPostcode())
            ->setDestCity($address->getCity())
            ->setPackageValue($order->getBaseSubtotal())
            ->setPackageValueWithDiscount($order->getBaseSubtotalWithDiscount())
            ->setPackageWeight($weight)
            ->setFreeMethodWeight($address->getFreeMethodWeight())
            ->setPackageQty($qty_ordered)
            ->setStoreId($store_id)
            ->setWebsiteId($website_id)
            ->setBaseCurrency($base_currency_code)
            ->setPackageCurrency($order_currency_code)
            ->setBaseSubtotalInclTax($order->getBaseSubtotalInclTax())
            //->setAllItems($order->getAllItems())
            ->setPackagePhysicalValue($order->getBaseSubtotal());

        return $request;
    }

    protected function getQtyOrderItems($order)
    {
        $qty_ordered = 0;
        $order_items = $order->getAllItems();
        foreach ($order_items as $order_item) {
            if ($order_item->getIsVirtual() != 0 || $order_item->getParentItemId() != null) {
                continue;
            }

            $qty_ordered += $order_item->getQtyOrdered() - $order_item->getQtyRefunded() - $order_item->getQtyCanceled();
        }

        return $qty_ordered;
    }

    protected function getOrderWeight($order)
    {
        $weight = 0;
        $order_items = $order->getAllItems();
        foreach ($order_items as $order_item) {
            if ($order_item->getIsVirtual() != 0 || $order_item->getParentItemId() != null) {
                continue;
            }

            $weight += $order_item->getWeight() * ($order_item->getQtyOrdered() - $order_item->getQtyRefunded() - $order_item->getQtyCanceled());
        }

        return $weight;
    }

    protected function collectShipping($order, $shipping_amount, $base_shipping_amount, $estimate = false)
    {
        $store = $order->getStore();
        $shipping_id = $order->getShippingAddress()->getId();
        $billing_id = $order->getBillingAddress()->getId();
        $shipping_address = Mage::getModel('sales/order_address')->load($shipping_id);
        $billing_address = Mage::getModel('sales/order_address')->load($billing_id);
        $shipping_tax_class = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
        $tax_calculation_model = Mage::getSingleton('tax/calculation');
        $customer_group_id = $order->getCustomerGroupId();
        $customer_tax_class_id = Mage::getModel('customer/group')->getTaxClassId($customer_group_id);
        $shipping_tax_amount = 0;
        $base_shipping_tax_amount = 0;

        if ($shipping_tax_class) {
            $request = $tax_calculation_model->getRateRequest($shipping_address, $billing_address, $customer_tax_class_id, $store);

            if ($rate = $tax_calculation_model->getRate($request->setProductClassId($shipping_tax_class))) {
                $shipping_tax_amount = $shipping_amount - $shipping_amount / (1 + $rate / 100);
                $base_shipping_tax_amount = $base_shipping_amount - $base_shipping_amount / (1 + $rate / 100);

                $shipping_tax_amount = $store->roundPrice($shipping_tax_amount);
                $base_shipping_tax_amount = $store->roundPrice($base_shipping_tax_amount);
                $order->setTaxAmount($order->getTaxAmount() + $shipping_tax_amount);
                $order->setBaseTaxAmount($order->getBaseTaxAmount() + $base_shipping_tax_amount);
            }
        }

        if (Mage::helper('tax')->shippingPriceIncludesTax()) {
            $base_sipping_incl_tax = $base_shipping_amount;
            $base_sipping_amount = $base_shipping_amount - $base_shipping_tax_amount;
            $sipping_incl_tax = $shipping_amount;
            $sipping_amount = $shipping_amount - $shipping_tax_amount;
        } else {
            $base_sipping_incl_tax = $base_shipping_amount + $base_shipping_tax_amount;
            $base_sipping_amount = $base_shipping_amount;
            $sipping_incl_tax = $shipping_amount + $shipping_tax_amount;
            $sipping_amount = $shipping_amount;
        }

        if (!$estimate) {
            $order
                ->setShippingInclTax($sipping_incl_tax)->setBaseShippingInclTax($base_sipping_incl_tax)
                ->setShippingTaxAmount($shipping_tax_amount)->setBaseShippingTaxAmount($base_shipping_tax_amount)
                ->setShippingAmount($sipping_amount)->setBaseShippingAmount($base_sipping_amount)
                ->save();
        }

        return $base_sipping_incl_tax;
    }

    public function prepareShippingObj($params)
    {
        $method = $params['shipping_method_radio'];
        $shipping = new Varien_Object();

        $shipping->setDescription($params['s_description'][$method])
            ->setAmount($params['s_amount_excl_tax'][$method])
            ->setAmountInclTax($params['s_amount_incl_tax'][$method])
            ->setMethod($method);

        return $shipping;
    }

    public function estimateShippingAmount($order, $request, $estimate=true)
    {
        $old_amount = $order->getBaseShippingInclTax();

        if ($order->getShippingMethod() == self::CustomMethodCode) {
            return $old_amount;
        }

        $shipping = Mage::getModel('shipping/shipping');
        $result = $shipping->collectRates($request)->getResult();
        $base_currency_code = $order->getBaseCurrencyCode();
        $order_currency_code = $order->getOrderCurrencyCode();

        if ($result) {
            $shipping_rates = $result->getAllRates();

            foreach ($shipping_rates as $shipping_rate) {
                $rate = Mage::getModel('sales/quote_address_rate')->importShippingRate($shipping_rate)->getData();
                if ($order->getShippingMethod() == $rate['code']) {

                    /** convert currency **/
                    $price = Mage::helper('directory')->currencyConvert($rate["price"], $base_currency_code, $order_currency_code);;
                    $base_price = $rate["price"];

                    /** recalculate **/
                    $new_amount = $this->collectShipping($order, $price, $base_price, $estimate);

                    $new_amount_currency = Mage::helper('core')->currency($new_amount, true, false);
                    $old_amount_currency = Mage::helper('core')->currency($old_amount, true, false);

                    Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("shipping_amount", $old_amount_currency, $new_amount_currency);

                    return $new_amount;
                }
            }
        }

        /** shipping is not available with this request **/
        $this->noticeShippingIsNotAvailable();
        return null;
    }

    protected function noticeShippingIsNotAvailable()
    {
        $notice = Mage::helper('iwd_ordermanager')->__('Selected shipping method is no longer available due to order requirements. Please select a new shipping method.');
        Mage::getSingleton('adminhtml/session')->addNotice($notice);
        Mage::getSingleton('iwd_ordermanager/logger')->addNoticeToLog($notice);
    }
}