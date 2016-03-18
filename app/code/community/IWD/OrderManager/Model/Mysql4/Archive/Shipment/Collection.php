<?php
class IWD_OrderManager_Model_Mysql4_Archive_Shipment_Collection extends IWD_OrderManager_Model_Resource_Archive_Shipment_Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_ordermanager/archive_shipment');
    }
}
