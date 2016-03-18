<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Account_Form extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/account/form.phtml');
    }
}