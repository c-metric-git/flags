<?php
class IWD_OrderManager_Model_Mysql4_Archive_Creditmemo extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_ordermanager/archive_creditmemo', 'entity_id');
    }
}
