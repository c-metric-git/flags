<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Backup_Sales_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('backupsCommentsGrid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_backup_sales';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/backup_sales')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('iwd_ordermanager')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
            'filter_index' => 'id',
            'type' => 'number',
            'sortable' => true
        ));

        $this->addColumn('admin_user_id', array(
            'header' => Mage::helper('iwd_ordermanager')->__('Admin ID'),
            'align' => 'left',
            'index' => 'admin_user_id',
            'type' => 'text',
            'filter_index' => 'admin_user_id',
            'width' => '50px',
            'sortable' => true
        ));


        $this->addColumn('object_type', array(
            'header' => Mage::helper('iwd_ordermanager')->__('By Object'),
            'align' => 'left',
            'index' => 'object_type',
            'width' => '100px',
            'filter_index' => 'object_type',
            'sortable' => true
        ));

        $this->addColumn('object', array(
            'header' => Mage::helper('iwd_ordermanager')->__('Information'),
            'align' => 'left',
            'index' => 'object',
            'filter_index' => 'object',
            'type' => 'text',
            'sortable' => false,
            'filter' => false,
            'renderer' => 'iwd_ordermanager/adminhtml_sales_order_backup_sales_renderer_object',
        ));

        $this->addColumn('object_items', array(
            'header' => Mage::helper('iwd_ordermanager')->__('Items information'),
            'align' => 'left',
            'index' => 'object_items',
            'filter_index' => 'object_items',
            'type' => 'text',
            'sortable' => false,
            'filter' => false,
            'renderer' => 'iwd_ordermanager/adminhtml_sales_order_backup_sales_renderer_items',
        ));

        $this->addColumn('deletion_at', array(
            'header' => Mage::helper('iwd_ordermanager')->__('Deleted at'),
            'align' => 'left',
            'index' => 'deletion_at',
            'filter_index' => 'deletion_at',
            'type' => 'datetime',
            'sortable' => true
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('backup');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('iwd_ordermanager')->__('Delete backup'),
            'url' => $this->getUrl('*/*/salesMassDelete'),
            'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}