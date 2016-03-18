<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Invoices_Grid extends Mage_Adminhtml_Block_Sales_Invoice_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_invoices_grid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_invoices';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/archive')->getArchiveInvoicesCollection();
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/invoice'))
            return $this->getUrl('*/sales_invoice/view', array('invoice_id'=> $row->getId()));

        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}