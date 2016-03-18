<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Sales_Order_Grid_Flag extends Mage_Adminhtml_Block_Template
{
    protected $_order = null;
    protected $_flagColumn = null;
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('amasty/amflags/flag.phtml');
        return $this;
    }
    
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }
    
    public function getOrder()
    {
        return $this->_order;
    }
    
    public function setCurrentColumn($column)
    {
        $this->_flagColumn = $column;
        return $this;
    }
    
    public function getCurrentColumn()
    {
        return $this->_flagColumn;
    }
    
    public function getApplyFlags()
    {
        $columnFlags = array();
        $column = Mage::getModel('amflags/column')->load(str_replace('priority', '', $this->getCurrentColumn()->getId()));
        $columnFlags = explode(',', $column->getApplyFlag());
        return $columnFlags;
    }
    
    public function getCurrentFlag()
    {
        $orderFlag = Mage::getModel('amflags/order_flag')->loadByColumnIdAndOrderId($this->getOrder()->getId(), str_replace('priority', '', $this->getCurrentColumn()->getId()));
        $flag = Mage::getModel('amflags/flag')->load($orderFlag->getFlagId());
        
        if (!$flag->getId())
            return null;

        if ($orderFlag->getColumnId() != str_replace('priority', '', $this->getCurrentColumn()->getId()))
            return null;
        
        // if specific comment was set to the order flag, replacing flag comment with order comment.
        if ($orderFlag->getComment())
            $flag->setComment($orderFlag->getComment());
        
        return $flag;
    }
    
    public function getEmptyFlagUrl()
    {
        return $this->getSkinUrl('images/amflags/empty.gif');
    }
    
    public function getDownArrowUrl()
    {
        return $this->getSkinUrl('images/amflags/arrow.gif');
    }
    
    public function getFlagCollection()
    {
        if (!Mage::registry('amflags_flag_collection'))
        {
            $flagCollection = Mage::getModel('amflags/flag')->getCollection();
            Mage::register('amflags_flag_collection', $flagCollection, true);
        }
        return Mage::registry('amflags_flag_collection');
    }
    
    public function getSetFlagUrl()
    {
        return $this->getUrl('adminhtml/amflagsassign/setFlag');
    }
}
