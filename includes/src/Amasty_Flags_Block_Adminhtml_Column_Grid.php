<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
class Amasty_Flags_Block_Adminhtml_Column_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('amflagsColumnGrid');
        $this->setDefaultSort('pos');
        $this->setDefaultDir('ASC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amflags/column')->getCollection();
        /* @var $collection Amasty_Flags_Model_Mysql4_Column_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
         $this->addColumn('pos', array(
            'header'    => Mage::helper('amflags')->__('Position'),
            'index'     => 'pos',
            'width'     => '80px',
            'align'     => 'center',
        ));
        
        $this->addColumn('alias', array(
            'header'    => Mage::helper('amflags')->__('Column Alias'),
            'index'     => 'alias',
        ));
        
        $this->addColumn('comment', array(
            'header'    => Mage::helper('amflags')->__('Comments'),
            'index'     => 'comment',
        ));
        
        $this->addColumn('apply_flag', array(
            'header'    => Mage::helper('amflags')->__('Applied Flags'),
            'index'     => 'apply_flag',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'amflags/adminhtml_column_grid_renderer_flag',
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('column_id' => $row->getId()));
    }
}
