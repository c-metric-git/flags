<?php

class Potato_CheckoutCouponCode_Block_Coupon_Multishipping
    extends Potato_CheckoutCouponCode_Block_Coupon
{
    /**
     * @return string
     */
    public function getApplyCouponUrl()
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl(
            'po_ccc/multishipping/applyCoupon', array('_secure' => $isSecure)
        );
    }

    /**
     * @return string
     */
    public function getCancelCouponUrl()
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl(
            'po_ccc/multishipping/cancelCoupon', array('_secure' => $isSecure)
        );
    }
}