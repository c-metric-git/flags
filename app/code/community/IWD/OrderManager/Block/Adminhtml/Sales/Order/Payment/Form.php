<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Payment_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/payment/form.phtml');
    }

    public function getOrder()
    {
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        return Mage::getModel("sales/order")->load($order_id);
    }

    public function getQuote()
    {
        $order_id = Mage::app()->getRequest()->getParam('order_id');
        if (!isset($order_id) || empty($order_id)) {
            return parent::getQuote();
        }

        $order = Mage::getModel("sales/order")->load($order_id);
        if (empty($order)){
            return null;
        }

        $data = $order->getData();
        if(empty($data)){
            return null;
        }

        $quote = Mage::getModel('sales/quote')->setStore($order->getStore())->load($order->getQuoteId());

        if (!empty($quote))
        {
            $data = $quote->getEntityId();
            if(!empty($data)) {
                return $quote;
            }
        }

        $quote = Mage::getModel('iwd_ordermanager/order_converter')->convertOrderToQuote($order_id);

        if (empty($quote)){
            return null;
        }

        $quote->setBaseSubtotal($order->getBaseSubtotal());

        return $quote;
    }

    public function _toHtml(){
        return $form = parent::_toHtml();
        if(Mage::app()->getRequest()->getActionName() == 'loadBlock'){
            return $form;
        }
        return preg_replace('/(<input.+value)="[0-9]+"(.*>)/i', '$1$2', $form);
    }
}