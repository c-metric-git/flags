<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Shipments_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_shipments_archive_grid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_shipments';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/archive')->getArchiveShipmentsCollection();
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}