<?php
class IWD_OrderManager_Model_Resource_Archive_Shipment_Collection extends Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection
{
    public function _construct(){
        parent::_construct();
        $this->setMainTable('iwd_ordermanager/archive_shipment');
    }
}
