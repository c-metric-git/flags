<?php

class Potato_CheckoutCouponCode_Helper_Config extends Mage_Core_Helper_Data
{
    const GENERAL_IS_ENABLED = 'po_ccc/general/is_enabled';
    const GENERAL_CAN_SHOW_DISCOUNT_IN_MESSAGE = 'po_ccc/general/can_show_discount_in_message';
    const GENERAL_IS_ENABLED_UNOBTRUSIVE_INTERFACE = 'po_ccc/general/is_enabled_unobtrusive_interface';

    /**
     * @param null|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::GENERAL_IS_ENABLED, $store);
    }

    /**
     * @param null|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function canShowDiscountInMessage($store = null)
    {
        return Mage::getStoreConfigFlag(
            self::GENERAL_CAN_SHOW_DISCOUNT_IN_MESSAGE, $store
        );
    }

    /**
     * @param null|Mage_Core_Model_Store $store
     *
     * @return bool
     */
    public function isEnabledUnobtrusiveInterface($store = null)
    {
        return Mage::getStoreConfigFlag(
            self::GENERAL_IS_ENABLED_UNOBTRUSIVE_INTERFACE, $store
        );
    }
}