<?php
class IWD_OrderManager_Adminhtml_Sales_ShippingController extends IWD_OrderManager_Controller_Abstract
{
    protected function getForm(){
        $result = array('status' => 1);
        $order = $this->getOrder();

        $result['form'] = $this->getLayout()
            ->createBlock('iwd_ordermanager/adminhtml_sales_order_shipping_form')
            ->setData('order', $order)
            ->toHtml();

        return $result;
    }

    protected function updateInfo(){
        $result = array('status' => 1);

        $params = $this->getRequest()->getParams();
        Mage::getModel('iwd_ordermanager/shipping')->updateOrderShipping($params);

        return $result;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_shipping');
    }
}