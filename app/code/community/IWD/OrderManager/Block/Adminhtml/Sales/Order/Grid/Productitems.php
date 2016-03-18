<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid_Productitems extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/grid/product_items.phtml');
    }
    public function getOrderIncrementId()
    {
        return Mage::getModel('sales/order')->load($this->order_id)->getIncrementId();
    }
}