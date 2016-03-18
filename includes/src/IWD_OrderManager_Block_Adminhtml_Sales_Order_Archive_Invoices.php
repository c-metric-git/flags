<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Invoices extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_invoices';
        $this->_headerText = Mage::helper('iwd_ordermanager')->__('Archive Invoices');

        parent::__construct();
        $this->_removeButton('add');
    }
}