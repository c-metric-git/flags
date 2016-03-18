<?php
class IWD_OrderManager_Block_Adminhtml_Confirm_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_confirm_log';
        $this->_headerText = Mage::helper('iwd_ordermanager')->__('Confirmations and Logs Operations');

        parent::__construct();
        $this->_removeButton('add');
    }
}