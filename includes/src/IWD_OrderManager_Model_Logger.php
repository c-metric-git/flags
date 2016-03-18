<?php
class IWD_OrderManager_Model_Logger extends IWD_OrderManager_Model_Logger_Abstract
{
    const CONFIG_XML_PATH_CONFIRM_STATUS_CANCEL = 'iwd_ordermanager/edit/confirm_cancel_status';
    const CONFIG_XML_PATH_CONFIRM_STATUS_SUCCESS = 'iwd_ordermanager/edit/confirm_success_status';
    const CONFIG_XML_PATH_CONFIRM_STATUS_WAIT = 'iwd_ordermanager/edit/confirm_wait_status';

    public function addCommentToOrderHistory($order_id, $status = false, $is_customer_notified = false)
    {
        $this->getLogOutput($order_id);
        if(empty($this->log_output)){
            return;
        }
        $this->addOrderStatusHistoryComment($this->log_output, $order_id, $status, $is_customer_notified);
    }

    public function addCommentToOrderHistoryConfirmSuccess($order_id)
    {
        $message = Mage::helper('iwd_ordermanager')->__("Changes were applied.");
        $this->addOrderStatusHistoryComment($message, $order_id, "success");
    }

    public function addCommentToOrderHistoryConfirmCancel($order_id)
    {
        $message = Mage::helper('iwd_ordermanager')->__("Changes were canceled.");
        $this->addOrderStatusHistoryComment($message, $order_id, "cancel");
    }

    protected function addOrderStatusHistoryComment($comment, $order_id, $status = false, $is_customer_notified = false){
        $order = Mage::getModel('sales/order')->load($order_id);

        if ($status === 'wait') {
            $order_status = Mage::getStoreConfig(self::CONFIG_XML_PATH_CONFIRM_STATUS_WAIT, Mage::app()->getStore());
            $comment .= "<i>" . Mage::helper('iwd_ordermanager')->__("Wait confirm...") . "</i>";
            $is_customer_notified = true;
        } elseif($status === 'success'){
            $order_status = Mage::getStoreConfig(self::CONFIG_XML_PATH_CONFIRM_STATUS_SUCCESS, Mage::app()->getStore());
        } elseif($status === 'cancel'){
            $order_status = Mage::getStoreConfig(self::CONFIG_XML_PATH_CONFIRM_STATUS_CANCEL, Mage::app()->getStore());
        } else {
            $order_status = $order->getStatus();
        }

        if (empty($order_status) || $order_status === 'false' || $order_status == false) {
            $order_status = $order->getStatus();
        }

        $is_visible_on_front = Mage::getStoreConfig('iwd_ordermanager/edit/is_visible_comment_on_front', Mage::app()->getStore());

        $order->addStatusHistoryComment($comment, $order_status)
            ->setIsCustomerNotified($is_customer_notified)
            ->setIsVisibleOnFront($is_visible_on_front)
            ->save();

        $order->setData('status', $order_status)->save();
    }

    public function addLogToLogTable($type, $order_id, $params = null)
    {
        $this->getLogOutput($order_id);

        if(empty($this->log_output)){
            return;
        }

        if (empty($params)) {
            Mage::getModel('iwd_ordermanager/confirm_logger')->addOperationToLog($type, $this->log_output, $order_id);
        } else {
            Mage::getModel('iwd_ordermanager/confirm_logger')->addOperationForConfirm($type, $this->log_output, $params, $order_id);
        }
    }
}