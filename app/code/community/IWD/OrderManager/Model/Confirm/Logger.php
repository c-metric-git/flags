<?php
class IWD_OrderManager_Model_Confirm_Logger extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('iwd_ordermanager/confirm_logger');
    }

    public function addOperationToLog($type, $log, $order_id)
    {
        $data = array();

        $data['edit_type'] = $type;
        $data['params'] = null;
        $data['confirm_link'] = null;
        $data['log_operations'] = $log;
        $data['status'] = IWD_OrderManager_Model_Confirm_Options_Status::LOG;
        $data['order_id'] = $order_id;
        $data['customer_email'] = null;

        $data = $this->_setBaseInfo($data);

        $this->setData($data);
        $this->save();
    }

    public function addOperationForConfirm($type, $log, $params, $order_id = null)
    {
        /** save data **/
        $data = array();
        $data['edit_type'] = $type;
        $data['params'] = serialize($params);
        $data['confirm_link'] = date('sihdmY') . uniqid();
        $data['log_operations'] = $log;
        $data['status'] = IWD_OrderManager_Model_Confirm_Options_Status::WAIT_CONFIRM;
        $data['order_id'] = ($order_id == null) ? $params['order_id'] : $order_id;
        $data['customer_email'] = $this->_getCustomerEmailByOrder($data['order_id']);

        $data = $this->_setBaseInfo($data);

        $this->setData($data);
        $this->save();

        /** send confirm email **/
        Mage::getModel('iwd_ordermanager/notify_notification')->sendConfirmEmail($data['order_id'], $this, null);
    }

    protected function _setBaseInfo($data)
    {
        $user = Mage::getSingleton('admin/session')->getUser();

        $data['admin_id'] = $user->getId();
        $data['admin_name'] = "{$user->getFirstname()} {$user->getLastname()} ({$user->getUsername()})";
        $data['created_at'] = Mage::getModel('core/date')->gmtDate();
        $data['updated_at'] = null;
        $data['request_ip'] = Mage::helper('iwd_ordermanager')->getCurrentIpAddress();
        $data['confirm_ip'] = null;

        return $data;
    }

    protected function _getCustomerEmailByOrder($order_id)
    {
        try {
            return Mage::getModel('sales/order')->load($order_id)->getCustomerEmail();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            return '';
        }
    }
}