<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_orders';
        $this->_headerText = Mage::helper('iwd_ordermanager')->__('Archive Orders');

        parent::__construct();
        $this->_removeButton('add');
    }
}