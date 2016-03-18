<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Backup_Sales extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_backup_sales';
        $this->_headerText = Mage::helper('iwd_ordermanager')->__('Backup Sales');

        parent::__construct();
        $this->_removeButton('add');
    }
}