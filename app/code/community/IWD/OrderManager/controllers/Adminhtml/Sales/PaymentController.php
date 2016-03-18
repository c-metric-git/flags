<?php
class IWD_OrderManager_Adminhtml_Sales_PaymentController extends IWD_OrderManager_Controller_Abstract
{
    protected function getForm(){
        $result = array('status' => 1);

        $order = $this->getOrder();

        $form = $this->getLayout()
            ->createBlock('iwd_ordermanager/adminhtml_sales_order_payment_form')
            ->setData('order', $order)
            ->toHtml();

        $result['form'] = preg_replace('/(<input.+value)="[0-9]+"(.*>)/i', '$1$2', $form);

        return $result;
    }

    protected function updateInfo(){
        $result = array('status' => 1);

        $params = $this->getRequest()->getParams();
        Mage::getModel('iwd_ordermanager/payment_payment')->updateOrderPayment($params);

        return $result;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_payment');
    }
}