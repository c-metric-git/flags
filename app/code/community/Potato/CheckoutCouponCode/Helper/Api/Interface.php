<?php

interface Potato_CheckoutCouponCode_Helper_Api_Interface
{
    /**
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isEnabled($store = null);

    public function hasAppliedCouponCode($quote = null);

    /**
     * @param      $couponCode
     * @param null $quote
     *
     * @return mixed
     */
    public function applyCouponCode($couponCode, $quote = null);

    public function cancelCouponCode($couponCode, $quote = null);

    public function isCouponCodeApplied($couponCode, $quote = null);

    public function getCouponCodeDiscountAmount($couponCode, $quote = null);

    public function canShowCancelButton();

    public function isCleanCouponCodeAfterApply();

    public function isCleanCouponCodeAfterCancel();

    public function canRenderAppliedCouponCodeList();

    public function getAppliedCouponCodeList($quote = null);

    public function canRenderRemoveLink();

    public function getRemoveLink($couponCode = null);
}