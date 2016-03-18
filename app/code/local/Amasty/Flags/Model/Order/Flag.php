<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Model_Order_Flag extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('amflags/order_flag');
    }
    
    public function removeLinks($flag)
    {
        if ($flag->getId())
        {
            $collection = $this->getCollection()->addFieldToFilter('flag_id', array('eq' => $flag->getId()))->load();
            if ($collection->getSize())
            {
                foreach ($collection as $orderFlag)
                {
                    $orderFlag->delete();
                }
            }
        }
        return $this;
    }
    
    public function loadByColumnIdAndOrderId($orderId, $columnId)
    {
        $this->_getResource()->loadByColumnIdAndOrderId($this, $orderId, $columnId);
        return $this;
    }
    
    public function removeLinksByColumnId($column)
    {
        if ($column->getId())
        {
            $collection = $this->getCollection()->addFieldToFilter('column_id', array('eq' => $column->getId()))->load();
            if ($collection->getSize())
            {
                foreach ($collection as $orderColumn)
                {
                    $orderColumn->delete();
                }
            }
        }
        return $this;
    }
}