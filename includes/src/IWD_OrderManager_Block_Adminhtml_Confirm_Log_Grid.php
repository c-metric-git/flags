<?php
class IWD_OrderManager_Block_Adminhtml_Confirm_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('orderManagerLogGrid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_confirm_log';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/confirm_logger')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('iwd_ordermanager');

        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
            'filter_index' => 'id',
            'type' => 'number',
            'sortable' => true
        ));

        $this->addColumn('order_id', array(
            'header' => $helper->__('Order ID'),
            'align' => 'left',
            'index' => 'order_id',
            'type' => 'text',
            'filter_index' => 'order_id',
            'width' => '150px',
            'sortable' => true
        ));

        $this->addColumn('admin_name', array(
            'header' => $helper->__('Admin Name'),
            'align' => 'left',
            'index' => 'admin_name',
            'type' => 'text',
            'filter_index' => 'admin_name',
            'width' => '200px',
            'sortable' => true
        ));

        $this->addColumn('log_operations', array(
            'header' => $helper->__('Info'),
            'align' => 'left',
            'index' => 'log_operations',
            'type' => 'text',
            'filter_index' => 'log_operations',
            'sortable' => true,
            'renderer'  => new IWD_OrderManager_Block_Adminhtml_Confirm_Log_Renderer_Description(),
        ));

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created At'),
            'align' => 'left',
            'index' => 'created_at',
            'type' => 'datetime',
            'filter_index' => 'created_at',
            'width' => '160px',
            'sortable' => true
        ));

        $this->addColumn('updated_at', array(
            'header' => $helper->__('Updated At'),
            'align' => 'left',
            'index' => 'updated_at',
            'type' => 'datetime',
            'filter_index' => 'updated_at',
            'width' => '160px',
            'sortable' => true
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'sortable' => true,
            'width' => '120px',
            'options' => Mage::getModel('iwd_ordermanager/confirm_options_status')->toOption(),
        ));

        $this->addColumn('action',
            array(
                'header'    => $helper->__('Action'),
                'width'     => '120',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => $helper->__('Confirm'),
                        'url'     => array(
                            'base'=>'*/confirm/edit',
                            'params'=>array('action'=>'confirm')
                        ),
                        'confirm' => $helper->__('Are you sure?'),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => $helper->__('Cancel'),
                        'url'     => array(
                            'base'=>'*/confirm/edit',
                            'params'=>array('action'=>'cancel')
                        ),
                        'confirm' => $helper->__('Are you sure?'),
                        'field'   => 'id'
                    )
                ),
                'column_css_class' => 'select-confirm-action',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'renderer'  => new IWD_OrderManager_Block_Adminhtml_Confirm_Log_Renderer_Actions()
            )
        );

        $this->addColumn('status-row', array(
            'index' => 'status',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'width' => '0px',
            'column_css_class' => 'no-display status-row',
            'header_css_class' => 'no-display',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();

        $append_block = $this->getLayout()
            ->createBlock('core/template')
            ->setTemplate('iwd/ordermanager/log/grid/append.phtml')
            ->toHtml();

        return $html . $append_block;
    }
}