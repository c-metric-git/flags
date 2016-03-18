<?php
class IWD_OrderManager_Model_Order_Customer extends Mage_Core_Model_Abstract
{
    protected $params;

    protected $customer_fields = array(
        'customer_group_id' => 'Group',
        'customer_prefix' => 'Prefix',
        'customer_firstname' => 'First Name',
        'customer_middlename' => 'Middle Name/Initial',
        'customer_lastname' => 'Last Name',
        'customer_suffix' => 'Suffix',
        'customer_email' => 'Email',
    );

    public function CustomerInfoOrderField($order)
    {
        return array(
            'customer_group_id' => array('value' => $order['customer_group_id'], 'title' => 'Group', 'required' => true),
            'customer_prefix' => array('value' => $order['customer_prefix'], 'title' => 'Prefix', 'required' => false),
            'customer_firstname' => array('value' => $order['customer_firstname'], 'title' => 'First Name', 'required' => true),
            'customer_middlename' => array('value' => $order['customer_middlename'], 'title' => 'Middle Name/Initial', 'required' => false),
            'customer_lastname' => array('value' => $order['customer_lastname'], 'title' => 'Last Name', 'required' => true),
            'customer_suffix' => array('value' => $order['customer_suffix'], 'title' => 'Suffix', 'required' => false),
            'customer_email' => array('value' => $order['customer_email'], 'title' => 'Email', 'required' => true),
        );
    }

    public function updateOrderCustomer($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editCustomerInfo();
            $this->updateOrderAmounts();
            $this->addChangesToLog();
            $this->notifyEmail();
        }
    }

    public function execUpdateOrderCustomer($params)
    {
        $this->init($params);
        $this->editCustomerInfo();
        $this->updateOrderAmounts();
        $this->notifyEmail();
    }

    protected function init($params)
    {
        $this->params = $params;

        if (empty($this->params) || !isset($this->params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
    }

    protected function addChangesToConfirm()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $this->editCustomerInfo(false);
        $this->estimateOrderAmounts();

        $logger->addCommentToOrderHistory($order_id, 'wait');
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::CUSTOMER_INFO, $order_id, $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Updates was not applied now! Customer get email with confirm link. Order will be updated after confirm.');

        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }

    protected function editCustomerInfo($save = true)
    {
        $order = Mage::getModel('sales/order')->load($this->params['order_id']);
        $logger = Mage::getSingleton('iwd_ordermanager/logger');

        foreach ($this->customer_fields as $field => $title) {
            if (isset($this->params[$field])) {
                if ($field == 'customer_group_id') {
                    $new_group_name = Mage::getModel('customer/group')->load($this->params[$field])->getCustomerGroupCode();
                    $old_group_name = Mage::getModel('customer/group')->load($order->getData($field))->getCustomerGroupCode();
                    $logger->addChangesToLog($field, $old_group_name, $new_group_name);
                } else {
                    $logger->addChangesToLog($field, $order->getData($field), $this->params[$field]);
                }

                if ($save) {
                    $order->setData($field, $this->params[$field]);
                }
            }
        }

        if ($save) {
            $order->save();
        }
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::CUSTOMER_INFO, $order_id);
    }

    protected function updateOrderAmounts()
    {
        if (isset($this->params['recalculate_amount']) && !empty($this->params['recalculate_amount'])) {
            //TODO: add!!!
        }
    }

    protected function estimateOrderAmounts()
    {
        if (isset($this->params['recalculate_amount']) && !empty($this->params['recalculate_amount'])) {
            $order_id = $this->params['order_id'];
            $order = Mage::getModel('sales/order')->load($order_id);
            Mage::getSingleton('adminhtml/session_quote')->clear();

            $sales_order_create = Mage::getModel('adminhtml/sales_order_create')->initFromOrder($order);
            $quote = $sales_order_create->getQuote();

            $quote->setData('store_id', $this->params['store_id'])->save();

            $quote = $quote->setTotalsCollectedFlag(false)->collectTotals();

            $totals = array(
                'grand_total' => $quote->getGrandTotal(),
                'base_grand_total' => $quote->getBaseGrandTotal(),
            );

            Mage::getSingleton('iwd_ordermanager/logger')->addNewTotalsToLog($totals);
        }
    }

    protected function notifyEmail(){
        $notify = isset($this->params['notify']) ? $this->params['notify'] : null;
        $order_id = $this->params['order_id'];

        if ($notify) {
            $message = isset($this->params['comment_text']) ? $this->params['comment_text'] : null;
            $email = isset($this->params['comment_email']) ? $this->params['comment_email'] : null;
            $result['notify'] = Mage::getModel('iwd_ordermanager/notify_notification')->sendNotifyEmail($order_id, $email, $message);
        }
    }
}
