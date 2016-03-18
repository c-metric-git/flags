<?php
class IWD_OrderManager_Model_Address
{
    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';

    protected $order_address;
    protected $address;
    protected $quote;

    public function getOrderAddressFields()
    {
        $helper = Mage::helper('iwd_ordermanager');

        return array(
            'prefix' => $helper->__("Prefix"),
            'firstname' => $helper->__("First name"),
            'middlename' => $helper->__("Middle Name/Initial"),
            'lastname' => $helper->__("Last name"),
            'suffix' => $helper->__("Suffix"),
            'company' => $helper->__("Company"),
            'street' => $helper->__("Street Address"),

            'region' => $helper->__("State/Province"),
            'country' => $helper->__("Country"),
            'region_id' => $helper->__("State/Province"),
            'country_id' => $helper->__("Country "),

            'city' => $helper->__("City"),
            'postcode' => $helper->__("Zip/Postal Code"),
            'telephone' => $helper->__("Telephone"),
            'fax' => $helper->__("Fax"),
            'email' => $helper->__("E-mail"),
            'vat_id' => $helper->__("VAT number"),
        );
    }

    public function isAllowEditAddress()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_address');
    }

    public function updateOrderAddress($new_address)
    {
        $this->init($new_address);

        if (isset($new_address['confirm_edit']) && !empty($new_address['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editOrderAddress();
            $this->addChangesToLog();
        }
    }

    public function editOrderAddress($address = null)
    {
        if($address !== null){
            $this->init($address);
        }

        $this->updateOrderAmounts();
        $this->updateOrderAddressFields();

        $this->notifyEmail();
    }

    protected function notifyEmail(){
        $notify = isset($this->address['notify']) ? $this->address['notify'] : null;
        $order_id = $this->address['order_id'];

        if ($notify) {
            $message = isset($this->address['comment_text']) ? $this->address['comment_text'] : "";
            $email = isset($this->address['comment_email']) ? $this->address['comment_email'] : null;
            $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $email, $message);
        }
    }

    protected function init($params)
    {
        if (!isset($params['address_id'])) {
            throw new Exception("Address id is not defined");
        }

        $this->order_address = Mage::getModel('sales/order_address')->load($params['address_id']);
        $this->address = $params;

        $this->setRegion();
        $this->setStreet();
    }

    protected function setStreet()
    {
        if (is_array($this->address['street'])) {
            $this->address['street'] = trim(implode("\n", $this->address['street']));
        }
    }

    protected function getStreetString($street_array)
    {
        if (is_array($street_array)) {
            return trim(implode("\n", $street_array));
        }
        return $street_array;
    }

    protected function setRegion()
    {
        if(isset($this->address['region']) && empty($this->address['region']))
        {
            if(isset($this->address['region_id']) && !empty($this->address['region_id']))
            {
                $this->address['region'] = Mage::getModel('directory/region')->load($this->address['region_id'])->getName();
            }
        }
    }

    public function addChangesToConfirm()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = Mage::getModel('sales/order_address')->load($this->address['address_id'])->getParentId();
        $type = $this->order_address->getAddressType() == self::TYPE_BILLING ?
            IWD_OrderManager_Model_Confirm_Options_Type::BILLING_ADDRESS : IWD_OrderManager_Model_Confirm_Options_Type::SHIPPING_ADDRESS;

        $this->estimateUpdateOrderAddressFields();
        $this->estimateUpdateOrderAmounts();

        $logger->addCommentToOrderHistory($order_id, 'wait');
        $logger->addLogToLogTable($type, $order_id, $this->address);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Order update not yet applied. Customer has been sent an email with a confirmation link. Updates will be applied after confirmation.');
        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = Mage::getModel('sales/order_address')->load($this->address['address_id'])->getParentId();

        $type = $this->order_address->getAddressType() == self::TYPE_BILLING ?
            IWD_OrderManager_Model_Confirm_Options_Type::BILLING_ADDRESS : IWD_OrderManager_Model_Confirm_Options_Type::SHIPPING_ADDRESS;

        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable($type, $order_id);
    }


    protected function estimateUpdateOrderAmounts()
    {
        if ($this->isNeedRecalculateOrderTotalAmount($this->address, $this->order_address))
        {
            $base_shipping_incl_tax = $this->recalculateOrderTotalAmount(true);

            if($base_shipping_incl_tax !== null) {
                $order_id = $this->order_address->getParentId();
                $order = Mage::getModel('sales/order')->load($order_id);

                $base_currency_code = $order->getBaseCurrencyCode();
                $order_currency_code = $order->getOrderCurrencyCode();
                $base_grand_total = $order->getBaseGrandTotal() - $order->getBaseShippingInclTax() + $base_shipping_incl_tax;
                $grand_total = Mage::helper('directory')->currencyConvert($base_grand_total, $base_currency_code, $order_currency_code);;
                $totals = array(
                    'grand_total' => $grand_total,
                    'base_grand_total' => $base_grand_total,
                );

                Mage::getSingleton('iwd_ordermanager/logger')->addNewTotalsToLog($totals);
            }

            return $base_shipping_incl_tax;
        }

        return true;
    }

    protected function updateOrderAmounts()
    {
        if ($this->isNeedRecalculateOrderTotalAmount($this->address, $this->order_address))
        {
            $order_id = $this->order_address->getParentId();
            $order = Mage::getModel('sales/order')->load($order_id);

            $base_shipping_incl_tax = $this->recalculateOrderTotalAmount(false);

            if($base_shipping_incl_tax !== null) {
                $order_edit = Mage::getModel('iwd_ordermanager/order_edit');
                $order_edit->collectOrderTotals($order_id);
                $order_edit->updateOrderPayment($order_id, $order);
            }

            return $base_shipping_incl_tax;
        }

        return true;
    }

    public function isNeedRecalculateOrderTotalAmount($address, $order_address)
    {
        if (isset($address['recalculate_amount']) && $address['recalculate_amount']) {
            $fields_which_affect_to_totals = array('street', 'city', 'region', 'country', 'region_id', 'country_id', 'postcode');

            foreach ($fields_which_affect_to_totals as $field) {
                if (isset($address[$field]) && !empty($address[$field])){
                    if($field == 'street'){
                        $address[$field] = $this->getStreetString($address[$field]);
                    }

                    if($address[$field] != $order_address->getData($field)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function recalculateOrderTotalAmount($estimate)
    {
        $order_id = $this->order_address->getParentId();
        $order = Mage::getModel('sales/order')->load($order_id);

        $shipping = Mage::getModel('iwd_ordermanager/shipping');
        $request = $shipping->prepareShippingRequest($order);

        $request
            ->setDestCountryId($this->address['country_id'])
            ->setDestRegionId($this->address['region_id'])
            ->setDestPostcode($this->address['postcode'])
            ->setDestCity($this->address['city']);

        $shipping_amount = $shipping->estimateShippingAmount($order, $request, $estimate);

        if (empty($shipping_amount)) {
            //TODO: show form with available shipping methods and change method
            $shipping_amount = $order->getBaseShippingAmount();
        }

        return $shipping_amount;
    }

    protected function updateOrderAddressFields()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $address_type = $this->order_address->getAddressType();

        $address_fields = $this->getOrderAddressFields();
        foreach ($address_fields as $field => $title) {
            if(!isset($this->address[$field])){
                continue;
            }

            $logger->addAddressFieldChangesToLog(
                $address_type, $field, $title,
                $this->order_address->getData($field),
                $this->address[$field]
            );

            $this->order_address->setData($field, $this->address[$field]);
        }

        $this->order_address->save();
    }

    protected function estimateUpdateOrderAddressFields()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $address_type = $this->order_address->getAddressType();

        $address_fields = $this->getOrderAddressFields();
        foreach ($address_fields as $field => $title) {
            if(!isset($this->address[$field])){
                continue;
            }
            $logger->addAddressFieldChangesToLog(
                $address_type, $field, $title,
                $this->order_address->getData($field),
                $this->address[$field]
            );
         }
    }

    protected function updateQuoteAddress($quote)
    {
        $address = $quote->getShippingAddress();

        if ($address) {
            $address_fields = $this->getOrderAddressFields();
            foreach ($address_fields as $field => $title) {
                if(!isset($this->address[$field])){
                    continue;
                }
                $address->setData($field, $this->address[$field]);
            }
            $address->save();
        }
    }
}
