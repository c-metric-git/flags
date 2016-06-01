<?php

class Potato_CheckoutCouponCode_Helper_Api extends Potato_CheckoutCouponCode_Helper_Api_Abstract
{
    protected $_apiEntityList = array(
        'po_mcoupon' => 'po_ccc/api_multipleCoupon',
        'coupon'     => 'po_ccc/api_coupon',
    );

    protected $_api = null;

    public function isEnabled($store = null)
    {
        return true;
    }

    public function hasAppliedCouponCode($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        return $this->_getApi()->hasAppliedCouponCode($quote);
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
        $this->_getApi()->cancelCouponCode($couponCode, $quote);
        return $this;
    }

    public function isCouponCodeApplied($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        return $this->_getApi()->isCouponCodeApplied($couponCode, $quote);
    }

    public function getCouponCodeDiscountAmount($couponCode, $quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        return $this->_getApi()->getCouponCodeDiscountAmount($couponCode, $quote);
    }

    public function canShowCancelButton()
    {
        return $this->_getApi()->canShowCancelButton();
    }

    public function isCleanCouponCodeAfterApply()
    {
        return $this->_getApi()->isCleanCouponCodeAfterApply();
    }

    public function isCleanCouponCodeAfterCancel()
    {
        return $this->_getApi()->isCleanCouponCodeAfterCancel();
    }

    public function canRenderAppliedCouponCodeList()
    {
        return $this->_getApi()->canRenderAppliedCouponCodeList();
    }

    public function getAppliedCouponCodeList($quote = null)
    {
        if ($quote === null) {
            $quote = $this->_getQuote();
        }
        return $this->_getApi()->getAppliedCouponCodeList($quote);
    }

    public function canRenderRemoveLink()
    {
        return $this->_getApi()->canRenderRemoveLink();
    }

    public function getRemoveLink($couponCode = null)
    {
        return $this->_getApi()->getRemoveLink($couponCode);
    }

    protected function _getApi()
    {
        if ($this->_api === null) {
            foreach ($this->_apiEntityList as $apiEntityClass) {
                try {
                    $api = Mage::helper($apiEntityClass);
                } catch (Exception $e) {
                    continue;
                }
                if ($api->isEnabled()) {
                    $this->_api = $api;
                    break;
                }
            }
        }
        return $this->_api;
    }
}