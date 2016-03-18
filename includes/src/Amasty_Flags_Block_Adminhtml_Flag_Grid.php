<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Flag_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('amflagsFlagGrid');
        $this->setDefaultSort('priority');
        $this->setDefaultDir('DESC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amflags/flag')->getCollection();
        /* @var $collection Amasty_Flags_Model_Mysql4_Flag_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('icon', array(
            'header'    => Mage::helper('amflags')->__('Flag Icon'),
            'index'       => 'flag_id',
            'width'       => '80px',
            'align'       => 'center',
            'filter'      => false,
            'sortable'    => false,
            'renderer'    => 'amflags/adminhtml_flag_grid_renderer_flag',
        ));
        
        $this->addColumn('priority', array(
            'header'    => Mage::helper('amflags')->__('Priority'),
            'width'     => '80px',
            'index'     => 'priority',
        ));
        
        $this->addColumn('alias', array(
            'header'    => Mage::helper('amflags')->__('Flag Alias'),
            'index'     => 'alias',
        ));
        
        $this->addColumn('comment', array(
            'header'    => Mage::helper('amflags')->__('Comments'),
            'index'     => 'comment',
        ));
        
        $this->addColumn('apply_status', array(
            'header'    => Mage::helper('amflags')->__('Auto Apply on Order Status Changed to'),
            'index'     => 'apply_status',
            'filter'    => false,
            'sortable'  => false,
            'renderer'    => 'amflags/adminhtml_flag_grid_renderer_status',
        ));
        
        $this->addColumn('apply_shipping', array(
            'header'    => Mage::helper('amflags')->__('Auto Apply on Order Shipping Method Chosen as'),
            'index'     => 'apply_shipping',
            'filter'    => false,
            'sortable'  => false,
            'renderer'    => 'amflags/adminhtml_flag_grid_renderer_shipping',
        ));
        
        $this->addColumn('apply_payment', array(
            'header'    => Mage::helper('amflags')->__('Auto Apply on Order Payment Method Chosen as'),
            'index'     => 'apply_payment',
            'filter'    => false,
            'sortable'  => false,
            'renderer'    => 'amflags/adminhtml_flag_grid_renderer_payment',
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('flag_id' => $row->getId()));
    }
}