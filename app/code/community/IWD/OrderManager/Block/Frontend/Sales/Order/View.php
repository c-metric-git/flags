<?php
class IWD_OrderManager_Block_Frontend_Sales_Order_View extends Mage_Sales_Block_Order_View
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iwd/ordermanager/sales/order/view.phtml');
    }
}