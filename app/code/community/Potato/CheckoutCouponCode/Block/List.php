<?php

class Potato_CheckoutCouponCode_Block_List extends Mage_Core_Block_Template
{
    public function canShow()
    {
        return $this->_getApi()->canRenderAppliedCouponCodeList();
    }

    /**
     * @return array
     */
    public function getCouponList()
    {
        return $this->_getApi()->getAppliedCouponCodeList();
    }

    public function canRenderRemoveLink()
    {
        return $this->_getApi()->canRenderRemoveLink();
    }

    public function getRemoveLink($couponCode = null)
    {
        return $this->_getApi()->getRemoveLink($couponCode);
    }

    /**
     * @return Potato_CheckoutCouponCode_Helper_Api
     */
    protected function _getApi()
    {
        return Mage::helper('po_ccc/api');
    }
}