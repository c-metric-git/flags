<?php

class Potato_CheckoutCouponCode_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        $isEnabled = Mage::helper('po_ccc/config')->isEnabled();
        $isModuleEnabled = $this->isModuleEnabled();
        $isModuleOutputEnabled = $this->isModuleOutputEnabled();
        return $isEnabled && $isModuleEnabled && $isModuleOutputEnabled;
    }
}