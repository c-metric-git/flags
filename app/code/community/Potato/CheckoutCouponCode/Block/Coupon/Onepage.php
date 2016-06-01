<?php

class Potato_CheckoutCouponCode_Block_Coupon_Onepage
    extends Potato_CheckoutCouponCode_Block_Coupon
{
    /**
     * @return string
     */
    public function getApplyCouponUrl()
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl(
            'po_ccc/onepage/applyCoupon', array('_secure' => $isSecure)
        );
    }

    /**
     * @return string
     */
    public function getCancelCouponUrl()
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl(
            'po_ccc/onepage/cancelCoupon', array('_secure' => $isSecure)
        );
    }
}