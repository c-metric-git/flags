<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Payment_Authorizenet_Info_Cc extends Mage_Paygate_Block_Authorizenet_Info_Cc
{
    public function __construct()
    {
        parent::__construct();
        $this->_isCheckoutProgressBlockFlag = false;
    }
}