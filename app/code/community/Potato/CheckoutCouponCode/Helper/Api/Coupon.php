<?php

class Potato_CheckoutCouponCode_Helper_Api_Coupon
    extends Potato_CheckoutCouponCode_Helper_Api_Abstract
{
    public function isEnabled($store = null)
    {
        return true;
    }

    public function hasAppliedCouponCode($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        return (bool)$quote->getCouponCode();
    }

    public function applyCouponCode($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $quote->setCouponCode($couponCode);
        return $this;
    }

    public function cancelCouponCode($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $quote->setCouponCode('');
        return $this;
    }

    public function isCouponCodeApplied($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        if (is_string($couponCode) && $couponCode === $quote->getCouponCode()) {
            return true;
        }
        if (!$quote->getCouponCode()) {
            return false;
        }
        return false;
    }

    public function getCouponCodeDiscountAmount($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $totals = $quote->getTotals();
        if (!array_key_exists('discount', $totals)) {
            return 0;
        }
        $discountTotal = $totals['discount'];
        return $discountTotal->getValue();
    }

    public function canShowCancelButton()
    {
        return true;
    }

    public function isCleanCouponCodeAfterApply()
    {
        return false;
    }

    public function isCleanCouponCodeAfterCancel()
    {
        return true;
    }

    public function canRenderAppliedCouponCodeList()
    {
        return false;
    }

    public function getAppliedCouponCodeList($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $appliedCouponCodeList = array();
        if (!$quote->getCouponCode()) {
            return $appliedCouponCodeList;
        }
        $discountAmount = $quote->getData('subtotal') - $quote->getData('subtotal_with_discount');
        $appliedCouponCodeList[$quote->getCouponCode()] = $discountAmount;
        return $appliedCouponCodeList;
    }

    public function canRenderRemoveLink()
    {
        return false;
    }

    public function getRemoveLink($couponCode = null)
    {
        return null;
    }
}