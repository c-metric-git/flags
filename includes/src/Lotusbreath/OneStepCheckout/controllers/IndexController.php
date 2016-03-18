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
class Lotusbreath_OneStepCheckout_IndexController extends Lotusbreath_OneStepCheckout_Controller_Action
{

    protected $_isLoadedOneStepLayout = false;
    protected $_isRequireUpdateQuote = false;

    protected $_isCalculatedQuote = false;

    protected function getOnepage()
    {
        return Mage::getSingleton('lotusbreath_onestepcheckout/type_onepage');
    }

    protected function getSession()
    {
        return Mage::getSingleton('lotusbreath_onestepcheckout/session');
    }

    public function indexAction()
    {


        Mage::dispatchEvent('controller_action_predispatch_onestepcheckout_index_index',
            array('request' => $this->getRequest(),
                'quote' => $this->getOnepage()->getQuote()));

        if (!Mage::getStoreConfig('lotusbreath_onestepcheckout/general/enabled')) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error);
            $this->_redirect('checkout/cart');
            return;
        }
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure' => true)));

        $this->getOnepage()->initCheckout();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($customerAddressId = $quote->getCustomerId()) {
            $defaultShippingA = $quote->getCustomer()->getPrimaryShippingAddress();
            if ($defaultShippingA
                && $countryCode = $defaultShippingA->getCountryId()
            )
                //$quote->getShippingAddress()->setCountryId($countryCode)->save();
            $quote->getShippingAddress()->setCollectShippingRates(true);
            //$saveShippingMethodResult = $this->getOnepage()->saveOnlyOneShippingMethod();

        } else {
            //clear country
            $countryCode = Mage::getStoreConfig('lotusbreath_onestepcheckout/general/defaultcountry');
            if ($countryCode) {
                $this->getOnepage()->getQuote()->getShippingAddress()->setCountryId($countryCode)->save();
                $this->getOnepage()->getQuote()->getBillingAddress()->setCountryId($countryCode)->save();
                $saveShippingMethodResult = $this->getOnepage()->saveOnlyOneShippingMethod();
            }

        }
        $defaultpaymentCode = Mage::getStoreConfig('lotusbreath_onestepcheckout/general/defaultpayment');
        if (!$defaultpaymentCode) {
            $allActivePaymentMethods = Mage::getModel('payment/config')->getActiveMethods();
            foreach ($allActivePaymentMethods as $method => $methodInfo) {
                $defaultpaymentCode = $method;
                break;
            }
        }
        $payment = $quote->getPayment();
        $payment->setMethod($defaultpaymentCode)->save();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();

    }


    public function saveStepAction()
    {
        $step = $this->getRequest()->getParam('step', '');
        $updateItems = array();
        $htmlUpdates = array();
        $results = array();
        $previousData = $this->getRequest()->getPost();

        switch ($step) {
            case 'shipping_method':
                $results = $this->process(
                    array(
                        'update_billing' => array(),
                        'update_shipping_address' => array(),
                        'shipping_method' => array(),
                        'payment_method' => array(),
                        'update_quote' => array('force' => true)
                    )
                );
                $htmlUpdates['review_partial'] = $this->_getReviewHtml();
                if (Mage::getStoreConfigFlag('lotusbreath_onestepcheckout/checkout_process/shipping_method_is_loading_payment')){
                    $htmlUpdates['payment_partial'] = $this->_getPaymentHtml();
                    $updateItems[] = 'payment_partial';
                }

                $updateItems[] = 'review_partial';

                break;

            case 'payment_method':
                $results = $this->process(
                    array(
                        'shipping_method' => array(),
                        'payment_method' => array(),
                        'update_quote' => array('force' => true)
                    )
                );
                $htmlUpdates['review_partial'] = $this->_getReviewHtml();
                $updateItems[] = 'review_partial';
                if (Mage::getStoreConfigFlag('lotusbreath_onestepcheckout/checkout_process/payment_is_loading_shipping_method')){
                    $htmlUpdates['shipping_partial'] = $this->_getShippingMehodHtml();
                    $updateItems[] = 'shipping_partial';
                }
                //$htmlUpdates['payment_partial'] = $this->_getPaymentHtml();

                break;
            case 'update_location_billing' :
                $results = $this->process(
                    array(
                        'update_billing' => array(),
                        'payment_method' => array(),
                        'update_quote' => array('force' => true)
                    )
                );

                $htmlUpdates['review_partial'] = $this->_getReviewHtml();
                $updateItems[] = 'review_partial';
                $htmlUpdates['payment_partial'] = $this->_getPaymentHtml();
                $updateItems[] = 'payment_partial';
                break;
            case  'update_location':
                $results = $this->process(
                    array(
                        'update_billing' => array(),
                        'update_shipping_address' => array(),
                        'shipping_method' => array(),
                        'update_quote' => array('force' => true)
                    )
                );
                $htmlUpdates['review_partial'] = $this->_getReviewHtml();
                $updateItems[] = 'review_partial';
                $htmlUpdates['shipping_partial'] = $this->_getShippingMehodHtml();
                $updateItems[] = 'shipping_partial';
                break;
            case 'update_location_billing_shipping':

                $results = $this->process(
                    array(
                        'update_billing' => array(),
                        'update_shipping_address' => array(),
                        //'shipping_method' => array('force' => false),
                        //'payment_method' => array('force' => false),
                        'update_quote' => array('force' => true)
                    )
                );

                $htmlUpdates['review_partial'] = $this->_getReviewHtml();
                $updateItems[] = 'review_partial';
                $htmlUpdates['shipping_partial'] = $this->_getShippingMehodHtml();
                $updateItems[] = 'shipping_partial';
                $htmlUpdates['payment_partial'] = $this->_getPaymentHtml();
                $updateItems[] = 'payment_partial';
                break;
            default :
                return;
        }
        $return = array(

            'update_items' => $updateItems,
            'htmlUpdates' => $htmlUpdates,
            'results' => $results,
            'previous_data' => $previousData
        );
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($return));

    }


    public function process($steps = null, $skipWhenError  = false){
        $result = array();
        if ($steps){

            foreach ($steps as $stepIdx => $step){
                $force = !empty($step['force']) ? $step['force'] : true;
                switch($stepIdx){
                    case 'shipping_method':
                        $data = !empty($step['data']) ? $step['data'] : null;
                        $result['shipping_method'] = $this->_saveShippingMethod($data, $force);
                        break;
                    case 'payment_method':
                        $justSaveMethod = !empty($step['just_save_method']) ? $step['just_save_method'] : false;
                        $result['payment'] = $this->_savePayment($justSaveMethod);
                        break;
                    case 'update_billing':
                        $result['billing'] = $this->_updateBillingAddress($force);
                        break;
                    case 'update_shipping_address':
                        $result['shipping_address'] = $this->_updateShippingAddress($force);
                        break;
                    case 'billing':
                        $result['billing'] = $this->_saveBillingAddress();
                        break;
                    case 'shipping_address':
                        $result['shipping_address'] = $this->_saveShippingAddress();
                        break;
                    case 'update_quote':
                        $result['quote'] = $this->_updateQuote($force);
                        break;
                }
            }
        }
        return $result;
    }


    public function savePostAction()
    {
        $helper = Mage::helper('checkout');
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $updateItems = array();
        $previousData = $this->getRequest()->getPost();
        if (!Mage::helper('customer')->isLoggedIn()) {
            if ($helper->isAllowedGuestCheckout($quote)) {
                $quote->setCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST);
            }
            if (!empty($_POST["billing"]["create_new_account"]) || !$helper->isAllowedGuestCheckout($quote)) {
                $quote->setCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
            }
        }

        $results = $this->process(array(
            'billing' => array(),
            'shipping' => array(),
            'shipping_method' => array(),
            'payment_method' => array()
           ), true );
        $isHasErrors = false;
        foreach ($results as $stepIdx =>  $stepResult){
            if (!empty($stepResult['error'])){
                $isHasErrors = true;
                switch ($stepIdx){
                    case 'shipping_method':
                        $updateItems[] = 'shipping_partial';
                        break;
                    case 'payment':
                        $updateItems[] = 'payment_partial';
                        break;
                    case 'billing':
                        $updateItems[] = 'billing_partial';
                        break;
                    case 'shipping_address':
                        $updateItems[] = 'shipping_address_partial';
                        break;
                }
            }
        }
        if (!$isHasErrors){
            if (!empty($results['payment']['redirect'])) {
                //do not save order
                if ($data = $this->getRequest()->getPost('payment', false)) {
                    $this->getOnepage()->getQuote()->getPayment()->importData($data);
                }
                $this->_updateQuote(true);
            }else{
                $saveOrderResult = $this->_saveOrder();
                $results['save_order'] = $saveOrderResult;
                if ($saveOrderResult['success'] == false) {
                    $updateItems[] = "review_partial";
                }
            }


        }else{
            $this->_updateQuote();
        }


        $return = array(
            'results' => $results,
            'previous_data' => $previousData,
            'update_items' => $updateItems,
            'success' => !empty($saveOrderResult['success']) ? $saveOrderResult['success'] : false,
            'error' => !empty($saveOrderResult['error']) ? $saveOrderResult['error'] : false,
        );
        if (count($updateItems)) {
            foreach ($updateItems as $updateItem) {
                $return['htmlUpdates'][$updateItem] = $this->_getUpdateItem($updateItem);
            }
        }
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($return));
    }

    protected function _getUpdateItem($itemName = null)
    {
        switch ($itemName) {
            case 'shipping_partial':
                return $this->_getShippingMehodHtml();
            case 'payment_partial':
                return $this->_getPaymentHtml();
            case 'review_partial':
                return $this->_getReviewHtml();
            default:
                return '';
        }
    }



    protected function _updateBillingAddress($force = true)
    {

        $billingAddressId = $this->getRequest()->getPost('billing_address_id', null);
        $data = $this->getRequest()->getPost('billing', null);
        $isUseForShipping = !empty($data['use_for_shipping']) ? $data['use_for_shipping'] : null;

        if (!$force){
            if ($this->getSession()->compareOldData('data_billing', $data)) {
                return true;
            }
        }

        if ($billingAddressId) {
            $this->getOnepage()->saveBilling(array('use_for_shipping' => $isUseForShipping), $billingAddressId);
        } else {
            $this->getOnepage()->updateAddress('billing', $data);
        }
        $this->getSession()->setData('saved_billing', 1);
        $this->getSession()->setData('data_billing', $data);

    }

    protected function _updateShippingAddress($force = true)
    {

        $billingAddressId = $this->getRequest()->getPost('billing_address_id', null);
        $data = $this->getRequest()->getPost('billing', null);
        $isUseForShipping = !empty($data['use_for_shipping']) ? $data['use_for_shipping'] : null;
        if ($isUseForShipping){
            $shippingData = $data;
        }else{
            $shippingData = $this->getRequest()->getPost('shipping', null);
        }
        $address = $this->getOnepage()->getQuote()->getShippingAddress();
        $isReCalculateShippingRates = $this->getOnepage()->isReCalculateShippingRates($shippingData, $address);

        if (!$force ){
            if ($this->getSession()->compareOldData('data_shipping', $data)) {
                return true;
            }
        }
        $shippingAddressId = $isUseForShipping ? $billingAddressId : ($this->getRequest()->getPost('shipping_address_id', null));
        $saveShippingResult = false;
        if ($shippingAddressId) {
            $this->getOnepage()->saveShipping(array('same_as_billing' => $isUseForShipping), $shippingAddressId);
            $address = $this->getOnepage()->getQuote()->getShippingAddress();
            if ($this->getOnepage()->isReCalculateShippingRates($shippingData, $address)){
                $address->setCollectShippingRates(true)
                    //->collectShippingRates()
                ;
            }
            $saveShippingResult = true;
        } else {
            if (empty($data['use_for_shipping'])) {
                $data = $shippingData;
            } else {
                $data = $this->getRequest()->getPost('billing', null);
            }
            $saveShippingResult = $this->getOnepage()->updateAddress('shipping', $data);

        }
        $address = $this->getOnepage()->getQuote()->getShippingAddress();
        if ($isReCalculateShippingRates){
            $address->setCollectShippingRates(true)
                //->collectShippingRates()
            ;
            //echo 1; exit;
        }


        $this->getSession()->setData('saved_shipping', 1);
        $this->getSession()->setData('data_shipping', $data);
        return $saveShippingResult;
    }


    protected function _getLocaleData($data)
    {
        $locationInfo = array();
        if ($data) {
            $locationInfo['country_id'] = !empty($data['country_id']) ? $data['country_id'] : null;
            $locationInfo['postcode'] = !empty($data['postcode']) ? $data['postcode'] : null;
            $locationInfo['region'] = !empty($data['region']) ? $data['region'] : null;;
            $locationInfo['region_id'] = !empty($data['region_id']) ? $data['region_id'] : null;
            $locationInfo['city'] = !empty($data['city']) ? $data['city'] : null;
        }
        return $locationInfo;

    }

    protected function _loadLayout()
    {

        if (!$this->_isLoadedOneStepLayout) {
            $this->loadLayout('lotusbreath_onestepcheckout_index_index');
            $this->_isLoadedOneStepLayout = true;
        }
    }

    protected function _getReviewHtml()
    {

        $this->_loadLayout();
        if ($reviewBlock = $this->getLayout()->getBlock('checkout.onepage.review')) {
            return $reviewBlock->toHtml();
        }
        return null;
    }

    protected function _getShippingMehodHtml()
    {
        $this->_loadLayout();
        if ($shippingMethodBlock = $this->getLayout()->getBlock('checkout.onepage.shipping_method')) {
            return $shippingMethodBlock->toHtml();
        }
        return null;
    }

    protected function _getPaymentHtml()
    {
        $this->_loadLayout();
        if ($paymentMethodBlock = $this->getLayout()->getBlock('checkout.onepage.payment')) {
            return $paymentMethodBlock->toHtml();
        }
        return null;
    }

    /**
     * Save billing Address
     * @return mixed
     */
    protected function _saveBillingAddress($force = true)
    {

        $request = $this->getRequest();
        $billingData = $request->getPost('billing');

        /*if (!$force){
            if ($this->getSession()->compareOldData('data_billing', $billingData)) {
                return true;
            }
        }*/

        $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
        if (isset($billingData['email'])) {
            $billingData['email'] = trim($billingData['email']);
        }
        $result = $this->getOnepage()->saveBilling($billingData, $customerAddressId);
        $this->getSession()->setData('saved_billing', 1);
        $this->getSession()->setData('data_billing', $billingData);
        return $result;
    }

    protected function _saveShippingAddress($force = true)
    {
        $data = null;
        $dataBilling = $this->getRequest()->getPost('billing', null);
        if (!empty($dataBilling['use_for_shipping'])){
            $data = $dataBilling;
        } else{
            $data = $this->getRequest()->getPost('shipping', array());
        }

        /*if (!$force){
            if ($this->getSession()->compareOldData('data_shipping', $data)) {
                return true;
            }
        }*/

        if (Mage::getStoreConfig('lotusbreath_onestepcheckout/shippingaddress/useshortshippingaddress')) {
            $billingData = $this->getRequest()->getPost('billing', array());
            $billingAddressId = $this->getRequest()->getPost('billing_address_id', null);
            if ($billingAddressId) {
                $billingAddress = $this->getOnepage()->getQuote()->getBillingAddress();
                $relatedFields = array('firstname', 'lastname', 'company');
                $billingData = array();
                foreach ($relatedFields as $field) {
                    $billingData[$field] = $billingAddress->getData($field);
                }
            }
            $data = array_merge($billingData, $data);
        }
        if ($data) {
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);
            $this->getSession()->setData('saved_shipping', 1);
            $this->getSession()->setData('data_shipping', $data);
            return $result;
        }
        return null;


    }

    /**
     * save payment
     * @return mixed
     */
    protected function _savePayment($justSaveMethod = false, $force = false)
    {

        $data = $this->getRequest()->getPost('payment');
        if (empty($data['method'])){
            return false;
        }
        if ($force == false && $this->getSession()->compareOldData('data_payment', $data)) {
            $result = $this->getSession()->getData('saved_payment_result');
            if (!empty($result) && empty($result['error'])){
                return $this->getSession()->getData('saved_payment_result');
            }

        }
        try {
            $result = $this->getOnepage()->savePayment($data, $justSaveMethod);

            //$this->_requireUpdateQuote();
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

            $this->getSession()->setData('saved_payment', 1);
            $this->getSession()->setData('data_payment', $data);

        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = 1;
            $result['message'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = 1;
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = 1;
            $result['message'] = $this->__('Unable to set Payment Method.');
        }
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
        $this->getSession()->setData('saved_payment_result', $result);

        return $result;
    }

    /**
     * Save shipping method
     * @return mixed
     */
    protected function _saveShippingMethod($data = null, $force = true)
    {
        if (!$data)
            $data = $this->getRequest()->getPost('shipping_method', '');
        if (!$force){
            if ($this->getSession()->compareOldData('data_shipping_method', $data)) {
                $result = $this->getSession()->getData('saved_shipping_method_result');
                if (!empty($result) && empty($result['error'])){
                    return $this->getSession()->getData('saved_shipping_method_result');
                }
            }
        }

        if (empty($data)) {
            $result = $this->getOnepage()->saveOnlyOneShippingMethod();
        } else {
            $result = $this->getOnepage()->saveShippingMethod($data);
        }
        /*
        $result will have erro data if shipping method is empty
        */
        if (!$result) {
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                array('request' => $this->getRequest(),
                    'quote' => $this->getOnepage()->getQuote()));

        }
        $this->getSession()->setData('saved_shipping_method', 1);
        $this->getSession()->setData('data_shipping_method', $data);
        //$this->_requireUpdateQuote();
        $this->getSession()->setData('saved_shipping_method_result', $data);
        return $result;
    }

    /**
     * Save order
     * @return mixed
     */
    protected function _saveOrder()
    {
        try {

            $data = $this->getRequest()->getPost('payment', array());
            if ($data) {
                $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
                    | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
                    | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
                    | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
            }


            //save comment
            if (Mage::getStoreConfig('lotusbreath_onestepcheckout/general/allowcomment')) {
                Mage::getSingleton('customer/session')->setOrderCustomerComment($this->getRequest()->getPost('order_comment'));
            }
            $this->_subscribeNewsletter();

            $this->getOnepage()->saveOrder();

            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
            $result['success'] = true;
            $result['error'] = false;

        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            $result['success'] = false;
            $result['error'] = true;
            if (!empty($message)) {
                $result['error_messages'] = $message;
            }
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

        } catch (Exception $e) {
            Mage::logException($e);
            //echo $e->getMessage();
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }
        return $result;
        //$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Login action
     *
     */
    public function loginAction()
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $errorMessages = array();
        $success = false;
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                    $success = true;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    //$session->addError($message);
                    $session->setUsername($login['username']);
                    $errorMessages[] = $message;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $errorMessages[] = $this->__('Login and password are required.');
                //$session->addError($this->__('Login and password are required.'));
            }
        }
        echo json_encode(array(
            'success' => $success,
            'messages' => $errorMessages
        ));

    }


    public function applyCouponAction()
    {

        $this->_savePayment();
        $this->_saveShippingMethod();
        $saveCouponResult = array();
        $quote = $this->getOnepage()->getQuote();
        $couponCode = (string)$this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $quote->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $saveCouponResult['success'] = false;
            $saveCouponResult['message'] = Mage::helper('onestepcheckout')->__('Coupon code is required');
        }
        try {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            //$quote->setCouponCode(strlen($couponCode) ? $couponCode : '');
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = true;
            if (defined(Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH)) {
                $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
            }

            $quote->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->setTotalsCollectedFlag(false)
                ->collectTotals()
                ->save();
            //$this->_requireUpdateQuote();

            if (strlen($couponCode)) {
                if ($isCodeLengthValid && $couponCode == $quote->getCouponCode()) {

                    $saveCouponResult['success'] = true;
                    $saveCouponResult['message'] = Mage::helper('checkout/cart')->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                } else {
                    $saveCouponResult['success'] = false;
                    $saveCouponResult['message'] = Mage::helper('checkout/cart')->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                }
            } else {
                $saveCouponResult['success'] = true;
                $saveCouponResult['message'] = Mage::helper('checkout/cart')->__('Coupon code was canceled.');
            }
        } catch (Mage_Core_Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
            $saveCouponResult['success'] = false;
            $saveCouponResult['message'] = $e->getMessage();

        } catch (Exception $e) {
            $saveCouponResult['success'] = false;
            $saveCouponResult['message'] = Mage::helper('checkout/cart')->__('Cannot apply the coupon code.');
            Mage::logException($e);
        }

        $return = array(
            'results' => $saveCouponResult,
            //'update_items' => array('shipping_partial', 'payment_partial', 'review_partial' ),
            'update_items' => array('review_partial', 'payment_partial', 'shipping_partial'),
            'htmlUpdates' => array(
                'review_partial' => $this->_getReviewHtml(),
                'shipping_partial' => $this->_getShippingMehodHtml(),
                'payment_partial' => $this->_getPaymentHtml(),
            )
        );
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($return));
    }

    public function updateCartAction()
    {
        $this->_saveShippingMethod();
        $this->_savePayment();

        $checkoutSession = Mage::getSingleton('checkout/session');
        $cartData = $this->getRequest()->getParam('cart');
        if (is_array($cartData)) {
            $filter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );
            foreach ($cartData as $index => $data) {
                if (isset($data['qty'])) {
                    $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                }
            }
            $cart = Mage::getSingleton('checkout/cart');
            $cartData = $cart->suggestItemsQty($cartData);
            $cart->updateItems($cartData)
                ->save();
        }
        $checkoutSession->setCartWasUpdated(true);

        $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
        $this->getOnepage()->getQuote()->save();

        $return = array(
            'results' => true,
            'update_items' => array('review_partial', 'shipping_partial', 'payment_partial'),
            'htmlUpdates' => array(
                'review_partial' => $this->_getReviewHtml(),
                'shipping_partial' => $this->_getShippingMehodHtml(),
                'payment_partial' => $this->_getPaymentHtml(),
            )
        );
        $this->getResponse()
             ->clearHeaders()
             ->setHeader('Content-Type', 'application/json')
             ->setBody(Mage::helper('core')->jsonEncode($return));

    }

    public function clearCartItemAction()
    {
        $id = (int)$this->getRequest()->getPost('id');
        if ($id) {
            $cart = Mage::getSingleton('checkout/cart');
            $checkoutSession = Mage::getSingleton('checkout/session');
            try {
                $cart->removeItem($id)
                    ->save();
                $checkoutSession->setCartWasUpdated(true);
                //$this->_requireUpdateQuote();
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }

        }

        if ($cart && $cart->getQuote()->getItemsCount() == 0) {
            $return = array(
                'results' => false,
                'cart_is_empty' => true,
            );
        } else {
            $return = array(
                'results' => true,
                'update_items' => array('review_partial', 'payment_partial', 'shipping_partial'),
                'htmlUpdates' => array(
                    'review_partial' => $this->_getReviewHtml(),
                    'payment_partial' => $this->_getPaymentHtml(),
                    'shipping_partial' => $this->_getShippingMehodHtml()
                )
            );
        }

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($return));
    }

    protected function _subscribeNewsletter()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('newsletter')) {
            $customerSession = Mage::getSingleton('customer/session');

            if ($customerSession->isLoggedIn())
                $email = $customerSession->getCustomer()->getEmail();
            else {
                $bill_data = $this->getRequest()->getPost('billing');
                $email = $bill_data['email'];
            }

            try {
                if (!$customerSession->isLoggedIn() && Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1)
                    Mage::throwException($this->__('Sorry, subscription for guests is not allowed. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));

                $ownerId = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email)->getId();

                if ($ownerId !== null && $ownerId != $customerSession->getId())
                    Mage::throwException($this->__('Sorry, you are trying to subscribe email assigned to another user.'));

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
            } catch (Mage_Core_Exception $e) {
            }
            catch (Exception $e) {
            }
        }
    }

    public function checkExistsEmailAction()
    {
        $email = $this->getRequest()->getParam('email', null);
        $response = array('success' => true, 'message' => '');
        if ($email) {
            if ($this->getOnepage()->customerEmailExists($email, Mage::app()->getWebsite()->getId())) {
                $response = array('success' => false, 'message' => '');
            } else {

            }
        }
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($response));

    }

    protected function _requireUpdateQuote()
    {
        $this->_isRequireUpdateQuote = true;
    }



    protected function _updateQuote($force = false)
    {
        //if ($this->_isRequireUpdateQuote) {
            if ($force == true || $this->_isCalculatedQuote == false) {
                $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false)->collectTotals();
                $this->getOnepage()->getQuote()->save();
                $this->_isCalculatedQuote = true;
            }
        //}
    }
}