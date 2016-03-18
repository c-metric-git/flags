<?php
/*
Lotus Breath - One Step Checkout
Copyright (C) 2014  Lotus Breath
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class Lotusbreath_OneStepCheckout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

    public function initCheckout()
    {

        $customerSession = $this->getCustomerSession();
        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }
        /*
        * want to load the correct customer information by assigning to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }

    public function saveOnlyOneShippingMethod(){
        $result = null;
        $this->getQuote()->getShippingAddress()->save()->setCollectShippingRates(true)->collectShippingRates();
        $groupRates = $this->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
        if(count($groupRates) == 1){
            $_sole = count($groupRates) == 1;
            $_rates = $groupRates[key($groupRates)];
            $_sole = $_sole && count($_rates) == 1;
            if ($_sole){
                $result = $this->saveShippingMethod(reset($_rates)->getCode());
                if (!$result){
                    Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request' => Mage::app()->getRequest(),
                            'quote' => $this->getQuote()));
                }
                $this->getQuote()->collectTotals()->save();
            }

        }

        return $result;
    }

    public  function customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data, $justSaveMethod = false)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }


        $quote = $this->getQuote();

        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        /*
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }
        */
        //if (!$justSaveMethod){
            $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

            $payment = $quote->getPayment();
            $payment->importData($data);
        //}

        /*working*/
        //$quote->save();
    }

    public function updateAddress($type = 'billing', $data){
        $quote = $this->getQuote();
        if ($type == 'billing')
            $address = $quote->getBillingAddress();
        if ($type == 'shipping')
            $address = $quote->getShippingAddress();

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('customer/form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
        $addressForm->setEntity($address);
        // emulate request object
        $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
        //print_r($addressData);
        /*
        $addressErrors  = $addressForm->validateData($addressData);
        if ($addressErrors !== true) {
            return array('error' => 1, 'message' => array_values($addressErrors));
        }
        */
        $addressForm->compactData($addressData);
        //unset billing address attributes which were not shown in form
        foreach ($addressForm->getAttributes() as $attribute) {
            if (!isset($data[$attribute->getAttributeCode()])) {
                $address->setData($attribute->getAttributeCode(), NULL);
            }else{

            }
        }
        if ($type == 'billing'){
            // set email for newly created user
            if (!$address->getEmail() && $this->getQuote()->getCustomerEmail()) {
                $address->setEmail($this->getQuote()->getCustomerEmail());
            }
        }
        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }else{
            //$address->save();
        }
        $address->implodeStreetAddress();
        if($type == 'shipping'){
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        return $validateRes;
    }

    public function isReCalculateShippingRates($data, $address){

        $relatedLocationFields = Mage::getStoreConfig("lotusbreath_onestepcheckout/general/location_fields");
        if ($relatedLocationFields){
            $relatedLocationFields = explode(',',$relatedLocationFields);
        }else{
            $relatedLocationFields = array('postcode', 'country_id', 'region_id','city');
        }

        foreach($relatedLocationFields as $field){
            if (!empty($data[$field])){
                if ($address->getData($field) != $data[$field])
                    return true;
            }
        }
        return false;
    }




}