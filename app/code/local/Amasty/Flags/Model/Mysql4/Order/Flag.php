<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Model_Mysql4_Order_Flag extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('amflags/order_flag', 'entity_id');
    }
    
    public function loadByColumnIdAndOrderId(Mage_Core_Model_Abstract $object, $orderId, $columnId)
    {
        $read = $this->_getReadAdapter();
        $select = $this->_getReadAdapter()
                       ->select()
                       ->from($this->getMainTable())
                       ->where('order_id = ?', $orderId)
                       ->where('column_id = ?', $columnId);
        
        $data = $read->fetchRow($select);
        if ($data)
            $object->setData($data);
            
        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }
}