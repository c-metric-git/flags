<?php
class IWD_OrderManager_Model_Mysql4_Confirm_Logger_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_ordermanager/confirm_logger');
    }
}