<?php
class IWD_OrderManager_Model_Confirm_Options_Type extends IWD_OrderManager_Model_Confirm_Options_Abstract
{
    const PAYMENT = 1;
    const SHIPPING = 2;
    const ITEMS = 3;
    const ORDER_INFO = 4;
    const CUSTOMER_INFO = 5;
    const BILLING_ADDRESS = 6;
    const SHIPPING_ADDRESS = 7;

    public function toOption()
    {
        $helper = Mage::helper('iwd_ordermanager');
        return array(
            self::PAYMENT           => $helper->__('Payment'),
            self::SHIPPING          => $helper->__('Shipping'),
            self::ITEMS             => $helper->__('Items'),
            self::ORDER_INFO        => $helper->__('Order Info'),
            self::CUSTOMER_INFO     => $helper->__('Customer Info'),
            self::BILLING_ADDRESS   => $helper->__('Billing Address'),
            self::SHIPPING_ADDRESS  => $helper->__('Shipping Address'),
        );
    }
}
