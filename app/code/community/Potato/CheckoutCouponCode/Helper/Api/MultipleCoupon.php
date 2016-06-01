<?php

class Potato_CheckoutCouponCode_Helper_Api_MultipleCoupon
    extends Potato_CheckoutCouponCode_Helper_Api_Abstract
{
    public function isEnabled($store = null)
    {
        if (!$this->isModuleEnabled('Potato_MultipleCoupon')) {
            return false;
        }
        if (!Mage::helper('po_mcoupon')->isEnabled()) {
            return false;
        }
        if ($this->_getQuote()->getIsMultiShipping()) {
            return false;
        }
        return true;
    }

    public function hasAppliedCouponCode($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $appliedCouponCodeList = $this->getAppliedCouponCodeList($quote);
        return (bool)count($appliedCouponCodeList);
    }

    public function applyCouponCode($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $this->_getApi()->applyCouponCode($couponCode, $quote);
        return $this;
    }

    public function cancelCouponCode($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $this->_getApi()->removeCouponCode($couponCode, $quote);
        return $this;
    }

    public function isCouponCodeApplied($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $discountQuote = $this->_getApi()->getDiscountQuote($couponCode, $quote);

        if ($discountQuote->getId()) {
            return true;
        }
        return false;
    }

    public function getCouponCodeDiscountAmount($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $discountQuote = $this->_getApi()->getDiscountQuote($couponCode, $quote);
        if (!$discountQuote->getId()) {
            return 0;
        }
        return $discountQuote->getDiscountAmount();
    }

    public function canShowCancelButton()
    {
        return false;
    }

    public function isCleanCouponCodeAfterApply()
    {
        return true;
    }

    public function isCleanCouponCodeAfterCancel()
    {
        return true;
    }

    public function canRenderAppliedCouponCodeList()
    {
        return true;
    }

    public function getAppliedCouponCodeList($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        $appliedCouponCodeList = array();
        $appliedDiscountQuoteList = $this->_getApi()->getAppliedDiscountQuoteList($quote);
        foreach ($appliedDiscountQuoteList as $discountQuote) {
            if (!$discountQuote->getCouponCode()) {
                continue;
            }
            $appliedCouponCodeList[$discountQuote->getCouponCode()] = $discountQuote->getDiscountAmount();
        }
        return $appliedCouponCodeList;
    }

    public function canRenderRemoveLink()
    {
        return true;
    }

    public function getRemoveLink($couponCode = null)
    {
        $isSecure = Mage::app()->getRequest()->isSecure();
        return Mage::getUrl(
            'po_ccc/onepage/cancelCoupon',
            array(
                'coupon_code' => $couponCode,
                '_secure'     => $isSecure,
            )
        );
    }

    /**
     * @return Potato_MultipleCoupon_Helper_Api
     */
    protected function _getApi()
    {
        return Mage::helper('po_mcoupon/api');
    }
}