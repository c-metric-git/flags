<?php

abstract class Potato_CheckoutCouponCode_Block_Coupon
    extends Mage_Checkout_Block_Cart_Coupon
{
    /**
     * @return bool
     */
    public function canShow()
    {
        return Mage::helper('po_ccc')->isEnabled();
    }

    /**
     * @return string
     */
    abstract public function getApplyCouponUrl();

    /**
     * @return string
     */
    abstract public function getCancelCouponUrl();

    /**
     * @return string
     */
    public function getCartUrl()
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl('checkout/cart', array('_secure' => $isSecure));
    }

    /**
     * @return bool
     */
    public function isCouponApplied()
    {
        return $this->_getApi()->hasAppliedCouponCode();
    }

    /**
     * @return bool
     */
    public function isEnabledUnobtrusiveInterface()
    {
        return $this->_getConfig()->isEnabledUnobtrusiveInterface();
    }

    /**
     * @return Potato_CheckoutCouponCode_Helper_Api
     */
    protected function _getApi()
    {
        return Mage::helper('po_ccc/api');
    }

    /**
     * @return Potato_CheckoutCouponCode_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('po_ccc/config');
    }
}