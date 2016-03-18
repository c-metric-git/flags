<?php
class IWD_OrderManager_Model_Mysql4_Confirm_Logger extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_ordermanager/confirm_logger', 'id');
    }
}
