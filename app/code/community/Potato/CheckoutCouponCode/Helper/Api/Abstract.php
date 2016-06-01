<?php

abstract class Potato_CheckoutCouponCode_Helper_Api_Abstract
    extends Mage_Core_Helper_Data
    implements Potato_CheckoutCouponCode_Helper_Api_Interface
{
    /**
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}