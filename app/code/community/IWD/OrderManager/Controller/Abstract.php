<?php
class IWD_OrderManager_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    public function getFormAction()
    {
        try {
            $result = $this->getForm();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function updateInfoAction()
    {
        Mage::dispatchEvent('iwd_ordermanager_update_order_before', array('order_id'=>$this->getOrderId()));

        try {
            $result = $this->updateInfo();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        Mage::dispatchEvent('iwd_ordermanager_update_order_after', array('order_id'=>$this->getOrderId()));

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function getForm()
    {
        return array('status' => -1);
    }

    protected function updateInfo()
    {
        return array('status' => -1);
    }

    protected function getOrderId()
    {
        return $this->getRequest()->getPost('order_id');
    }

    protected function getOrder()
    {
        $order_id = $this->getOrderId();
        $order = Mage::getModel('sales/order')->load($order_id);
        return $order;
    }
}