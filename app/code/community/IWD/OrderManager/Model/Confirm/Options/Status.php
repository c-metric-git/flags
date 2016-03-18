<?php
class IWD_OrderManager_Model_Confirm_Options_Status extends IWD_OrderManager_Model_Confirm_Options_Abstract
{
    const LOG = 0;
    const WAIT_CONFIRM = 1;
    const CONFIRMED = 2;
    const CANCELED = 3;

    public function toOption()
    {
        $helper = Mage::helper('iwd_ordermanager');
        return array(
            self::LOG           => $helper->__('Log Info'),
            self::WAIT_CONFIRM  => $helper->__('Wait Confirm'),
            self::CONFIRMED     => $helper->__('Confirmed'),
            self::CANCELED      => $helper->__('Canceled'),
        );
    }
}
