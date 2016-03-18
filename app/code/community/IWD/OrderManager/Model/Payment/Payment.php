<?php
class IWD_OrderManager_Model_Payment_Payment extends Mage_Core_Model_Abstract
{
    protected $params;
    protected $_realTransactionIdKey = 'real_transaction_id';

    protected function init($params)
    {
        if (!isset($params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
        $this->params = $params;
    }

    public function updateOrderPayment($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editPaymentMethod($params['payment'], $params['order_id']);
            $this->addChangesToLog();
            $this->notifyEmail();
        }
    }

    public function execUpdatePaymentMethod($payment_data, $order_id){
        $this->editPaymentMethod($payment_data, $order_id);
        $this->notifyEmail();
    }

    protected function editPaymentMethod($payment_data, $order_id)
    {
        try{
            if ($order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                if (!empty($order) && $order->getEntityId()) {
                    $old_payment = $order->getPayment()->getMethodInstance()->getTitle();

                    if($order->getPayment()->getMethod() == "iwd_authorizecim"){
                        $transactions= Mage::getModel('sales/order_payment_transaction')->getCollection()
                            ->addAttributeToFilter('order_id', array('eq' => $order->getEntityId()))
                            ->addAttributeToFilter('txn_type', array('eq' => Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH));

                        $card_id = $order->getPayment()->getIwdAuthorizecimCardId();
                        $method = $order->getPayment()->getMethodInstance();
                        $card = $method->loadCard($card_id);
                        $gateway = $method->gateway();
                        $gateway->setCard($card);

                        foreach($transactions as $transaction){
                            $txn_id = $transaction->getTxnId();
                            $delimiter = strpos($txn_id, "-");
                            $txn_id = $delimiter ? substr($txn_id, 0, $delimiter) : $txn_id;

                            $gateway->void($order->getPayment(), $txn_id);
                            $transaction->setOrderPaymentObject($order->getPayment());
                            $transaction->close(false)->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID)->save();
                        }
                    }

                    $payment = $order->getPayment();
                    $payment->addData($payment_data)->save();
                    $method = $payment->getMethodInstance();
                    $method->prepareSave()->assignData($payment_data);
                    $order->getPayment()->save();
                    $order->getPayment()->getOrder()->save();

                    $order = Mage::getModel('sales/order')->load($order_id);
                    $payment = $order->getPayment();
                    $payment->addData($payment_data)->save();
                    $payment->unsMethodInstance();
                    $method = $payment->getMethodInstance();
                    $method->prepareSave()->assignData($payment_data);

                    if($order->getPayment()->getMethod() == "iwd_authorizecim"){
                        $card_id = $order->getPayment()->getIwdAuthorizecimCardId();
                        $order->place();
                        $order->getPayment()->setIwdAuthorizecimCardId($card_id);
                    } else {
                        $order->place();
                    }

                    $order->getPayment()->save();
                    $order->getPayment()->getOrder()->save();
                    $new_payment = Mage::getModel('sales/order')->load($order_id)->getPayment()->getMethodInstance()->getTitle();
                    Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("payment_method", $old_payment, $new_payment);

                    return 1;
                }
            }
        }catch(Exception $e){
            echo ($e->getMessage());
            return -1;
        }

        return 0;
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

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT, $order_id);
    }

    protected function addChangesToConfirm()
    {
        $this->estimatePaymentMethod($this->params['order_id'], $this->params['payment']);

        Mage::getSingleton('iwd_ordermanager/logger')->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT, $this->params['order_id'], $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Order update not yet applied. Customer has been sent an email with a confirmation link. Updates will be applied after confirmation.');

        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }


    public function isAllowEditPayment()
    {
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_payment');
        return $permission_allow;
    }

    public function reauthorizePayment($order_id, $old_order)
    {
        try {
            $order = Mage::getModel('sales/order')->load($order_id);
            $payment = $order->getPayment();
            $order_method = $payment->getMethod();

            $old_amount_authorize = $payment->getBaseAmountAuthorized();
            $amount = $order->getBaseGrandTotal();

            // authorized (but do not captured) more then we need now (authorized $100, need $80)
            if(!$order->hasInvoices() && $old_amount_authorize >= $amount){
                return 1;
            }

            switch ($order_method) {
                case 'free':
                case 'checkmo':
                case 'purchaseorder':
                    return 1;

                case 'authorizenet':
                    return $this->reauthorizeAuthorizeNet($order, $old_order);

                case 'iwd_authorizecim':
                case 'iwd_authorizecim_echeck':
                    return $this->reauthorizeIWDAuthorizeNetCIM($order, $old_order);

                case Mage_Paypal_Model_Config::METHOD_PAYFLOWPRO:
                    return $this->reauthorizePayPalPayflowPro($order);


                /* *
                 * Paradox Labs Authorize.net CIM
                 * Please, do not uncomment this code. It will not enable re-authorization via Paradox Labs Authorize.net CIM.
                 * You need add changes to Paradox Labs Authorize.net CIM extension for correct work of Order Manager
                 * */
                /*case 'authnetcim':
                    return $this->reauthorizeParadoxAuthorizeNetCIM($order, $old_order);*/

                default:
                    return 1;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in update payment: " . $e->getMessage()));
            return -1;
        }
    }



    /* * * * Authorize.net * * * */
    protected function reauthorizeAuthorizeNet($order, $old_order)
    {
        /* @var $payment_authorizenet IWD_OrderManager_Model_Payment_Authorizenet */
        /* @var $old_order Mage_Sales_Model_Order */
        /* @var $order Mage_Sales_Model_Order */

        $amount = $order->getGrandTotal();
        $payment = $order->getPayment();
        $authorized = $payment->getBaseAmountAuthorized();

        $payment_authorizenet = Mage::getModel('iwd_ordermanager/payment_authorizenet');

        // authorized (but do not captured) more then we need now (authorized $100, need $80)
        if(!$order->hasInvoices() && $authorized < $amount) {
            // void + authorize
            $payment_authorizenet->voidAuthorizeNet($order);
            return $payment_authorizenet->authorizeAuthorizeNet($order, $amount);
        }

        $new_total = $order->getGrandTotal() - $order->getBaseTotalRefunded();
        $old_total = $old_order->getGrandTotal() - $old_order->getBaseTotalRefunded();

        if($old_total > $new_total){
            // amount was decreased (-)
            $refund_amount = $old_total - $new_total;
            return $this->refundAuthorizenetLogic($order, $refund_amount);
        } else {
            // amount was increased (+)
            $additional_amount = $new_total - $old_total;
            return $payment_authorizenet->captureAuthorizeNet($order, $additional_amount);
        }
    }

    protected function refundAuthorizenetLogic($order, $refund_amount)
    {
        $transactions = $this->preparePaymentTransactions($order);

        /* @var $payment_authorizenet IWD_OrderManager_Model_Payment_Authorizenet */
        $payment_authorizenet = Mage::getModel('iwd_ordermanager/payment_authorizenet');

        $captured = array_sum($transactions['captured']);
        $settled = array_sum($transactions['settled']);

        if($captured > $refund_amount){
            foreach($transactions['captured'] as $trx_id => $trx_amount){
                $payment_authorizenet->voidAuthorizeNetTransaction($order, $trx_id);

                $refund_amount -= $trx_amount;
                if($refund_amount == 0){
                    return 1;
                }
                if($refund_amount < 0){
                    $capture_amount = abs($refund_amount);
                    return $payment_authorizenet->captureAuthorizeNet($order, $capture_amount);
                }
            }

            throw new Exception("We can not refund more than captured");
        } else {
            if($captured + $settled > $refund_amount){
                // VOID ALL CAPTURED TRANSACTIONS
                foreach($transactions['captured'] as $trx_id => $trx_amount){
                    if($refund_amount == 0){
                        return 1;
                    }

                    $result = $payment_authorizenet->voidAuthorizeNetTransaction($order, $trx_id);
                    if(is_string($result)) {
                        Mage::getSingleton('adminhtml/session')->addError($result);
                    }
                    $refund_amount -= $trx_amount;
                }

                // REFUND SETTLED TRANSACTIONS
                foreach($transactions['settled'] as $trx_id => $trx_amount){
                    if($refund_amount == 0){
                        return 1;
                    }

                    if($refund_amount < $trx_amount){
                        $refund = $refund_amount;
                        $refund_amount = 0;
                    } else {
                        $refund = $trx_amount;
                        $refund_amount -= $trx_amount;
                    }

                    $result = $payment_authorizenet->refundAuthorizeNet($order, $trx_id, $refund);
                    if(is_string($result)){
                        Mage::getSingleton('adminhtml/session')->addError($result);
                    }
                }

                return 0;
            } else {
                throw new Exception("We can not refund more than captured");
            }
        }
    }

    protected function preparePaymentTransactions($order)
    {
        $transactions = Mage::getModel('sales/order_payment_transaction')->getCollection()
            ->addFieldToFilter('order_id', $order->getId());

        /* @var $payment_authorizenet IWD_OrderManager_Model_Payment_Authorizenet */
        $payment_authorizenet = Mage::getModel('iwd_ordermanager/payment_authorizenet');

        $transactions_captured = array();
        $transactions_settled = array();
        foreach ($transactions as $transaction) {
            $trx_info = $payment_authorizenet->fetchTrxInfo($transaction->getTransactionId());
            $id = $trx_info->getData('transaction_id');

            $information = $trx_info->getData('additional_information');
            $status = false;
            if (isset($information["raw_details_info"]["transaction_status"])) {
                $status = $information["raw_details_info"]["transaction_status"];
            }
            $auth_amount = 0;
            if (isset($information["raw_details_info"]["auth_amount"])) {
                $auth_amount = $information["raw_details_info"]["auth_amount"];
            }

            if ($status == "capturedPendingSettlement" ) {
                $transactions_captured[$id] = $auth_amount;
            }

            if ($status == "settledSuccessfully" && $transaction->getTxnType() == 'capture') {
                $transactions_settled[$id] = $auth_amount;
            }
        }

        return array(
            'captured' => $transactions_captured,
            'settled' => $transactions_settled,
        );
    }



    /* * * * IWD Authorize.net CIM * * * */
    protected function reauthorizeIWDAuthorizeNetCIM($order, $old_order)
    {
        /* @var $old_order Mage_Sales_Model_Order */
        /* @var $order Mage_Sales_Model_Order */
        $amount = $order->getGrandTotal();
        $payment = $order->getPayment();
        $authorized = $payment->getBaseAmountAuthorized();
        $payment_authorizenet = $payment->getMethodInstance();
        $new_total = $order->getGrandTotal() - $order->getBaseTotalRefunded();
        $old_total = $old_order->getGrandTotal() - $old_order->getBaseTotalRefunded();

        // authorized (but do not captured) more then we need now (authorized $100, need $80)
        if(!$order->hasInvoices() && $authorized < $amount) {
            $additional_amount = $new_total - $old_total;
            if (!$payment_authorizenet->authorize($payment, $additional_amount)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                return 0;
            }
            $this->savePayment($payment);
            return 1;
        }

        if($old_total > $new_total){
            // amount was decreased (-)
            $refund_amount = $old_total - $new_total;
            return $this->refundAuthorizenetNetCIMLogic($order, $refund_amount);
        } else {
            // amount was increased (+)
            Mage::register('iwd_order_manager_authorize', true);
            $additional_amount = $new_total - $old_total;
            $payment_authorizenet->capture($payment, $additional_amount, true);
            $this->savePayment($payment);
            return 1;
        }
    }

    protected function savePayment($payment)
    {
        $payment->getOrder()->save();
        $payment->save();
    }

    protected function refundAuthorizenetNetCIMLogic($order, $refund_amount)
    {
        $transactions = $this->preparePaymentNetCIMTransactions($order);
        $payment = $order->getPayment();
        $payment_authorizenet = $payment->getMethodInstance();

        $captured = array_sum($transactions['captured']);
        $settled = array_sum($transactions['settled']);

        if($captured > $refund_amount){
            foreach($transactions['captured'] as $trx_id => $trx_amount){
                $payment_authorizenet->void($payment, $trx_id);

                $refund_amount -= $trx_amount;
                if($refund_amount == 0){
                    $this->savePayment($payment);
                    return 1;
                }
                if($refund_amount < 0){
                    $capture_amount = abs($refund_amount);
                    $payment_authorizenet->capture($payment, $capture_amount, true);
                    $this->savePayment($payment);
                    return 1;
                }
            }

            throw new Exception("We can not refund more than captured");
        } else {
            if($captured + $settled > $refund_amount){
                // VOID ALL CAPTURED TRANSACTIONS
                foreach($transactions['captured'] as $trx_id => $trx_amount){
                    if($refund_amount == 0){
                        return 1;
                    }

                    $payment_authorizenet->void($payment, $trx_id);
                    $refund_amount -= $trx_amount;
                }

                // REFUND SETTLED TRANSACTIONS
                foreach($transactions['settled'] as $trx_id => $trx_amount){
                    if($refund_amount == 0){
                        $this->savePayment($payment);
                        return 1;
                    }

                    if($refund_amount < $trx_amount){
                        $refund = $refund_amount;
                        $refund_amount = 0;
                    } else {
                        $refund = $trx_amount;
                        $refund_amount -= $trx_amount;
                    }

                    $result = $payment_authorizenet->refund($payment, $refund, $trx_id);
                    if(is_string($result)){
                        Mage::getSingleton('adminhtml/session')->addError($result);
                    }
                }

                $this->savePayment($payment);
                return 1;
            } else {
                throw new Exception("We can not refund more than captured");
            }
        }
    }

    protected function preparePaymentNetCIMTransactions($order)
    {
        $payment = $order->getPayment();

        $transactions = Mage::getModel('sales/order_payment_transaction')->getCollection()
            ->addFieldToFilter('order_id', $order->getId());

        /* @var $payment_authorizenet IWD_OrderManager_Model_Payment_Authorizenet */
        $payment_authorizenet = $payment->getMethodInstance()->gateway();

        $transactions_captured = array();
        $transactions_settled = array();
        foreach ($transactions as $transaction) {
            $txn_id = $transaction->getTxnId();
            $delimiter = strpos($txn_id, "-");
            $txn_id = $delimiter ? substr($txn_id, 0, $delimiter) : $txn_id;

            $trx_info = $payment_authorizenet
                ->setParameter('transId', $txn_id)
                ->getTransactionDetails();

            $id = $trx_info['transaction']['transId'];

            $status = false;
            if (isset($trx_info['transaction']["transactionStatus"])) {
                $status = $trx_info['transaction']["transactionStatus"];
            }
            if ($status == "capturedPendingSettlement" ) {
                $transactions_captured[$id] = $trx_info['transaction']["authAmount"];
            }
            if ($status == "settledSuccessfully" && $transaction->getTxnType() == 'capture') {
                $transactions_settled[$id] = $trx_info['transaction']["settleAmount"];
            }
        }

        return array(
            'captured' => $transactions_captured,
            'settled' => $transactions_settled,
        );
    }

    /* * * * PayPal Payflow Pro Gateway * * * */
    protected function reauthorizePayPalPayflowPro($order)
    {
        $payment = $order->getPayment();
        $amount = $order->getGrandTotal();

        /* @var $method IWD_OrderManager_Model_Payment_Paypal_Payflowpro */
        $method = $payment->getMethodInstance()->setStore($order->getStoreId());

        if (!$method->reauthorize($payment, $amount)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
            return -1;
        }

        $this->savePayment($payment);
        return 1;
    }



    public function GetActivePaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getActiveMethods();
        return $this->getMethodsTitle($payments);
    }

    public function GetAllPaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getAllMethods();
        return $this->getMethodsTitle($payments);
    }

    private function getMethodsTitle($payments)
    {
        $methods = array();

        foreach ($payments as $paymentCode => $paymentModel) {
            $methods[$paymentCode] = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
        }

        return $methods;
    }

    public function GetPaymentMethods()
    {
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('sales/order_payment');
        $results = $resource->fetchAll("SELECT DISTINCT `method` FROM `{$tableName}`");

        $methods = array();

        foreach ($results as $paymentCode) {
            $code = $paymentCode['method'];
            $methods[$code] = Mage::getStoreConfig('payment/' . $code . '/title');
        }

        return $methods;
    }


    public function canUpdatePaymentMethod($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);
        if (empty($order)) {
            return false;
        }

        return !$order->hasInvoices();
    }

    public function estimatePaymentMethod($order_id, $payment_data)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        $old_payment = $order->getPayment()->getMethodInstance()->getTitle();
        $new_payment = Mage::helper('payment')->getMethodInstance($payment_data['method'])->getTitle();

        $totals = array(
            'grand_total' => $order->getGrandTotal(),
            'base_grand_total' => $order->getBaseGrandTotal(),
        );

        $log = Mage::getSingleton('iwd_ordermanager/logger');
        $log->addNewTotalsToLog($totals);
        $log->addChangesToLog("payment_method", $old_payment, $new_payment);
        $log->addCommentToOrderHistory($order_id, 'wait');
    }



    /* * * * Paradox Lab Authorize.net CIM * * * */
    protected function reauthorizeParadoxAuthorizeNetCIM($order, $old_order)
    {
        $payment = $order->getPayment();

        $tax_amount = $order->getTaxAmount();
        $base_tax_amount = $order->getBaseTaxAmount();

        $total_due = $order->getGrandTotal() - $order->getTotalRefunded() - $payment->getAmountAuthorized();
        $base_total_due = $order->getBaseGrandTotal() - $order->getBaseTotalRefunded() - $payment->getBaseAmountAuthorized();

        $new_tax_amount = $order->getTaxAmount() - $old_order->getTaxAmount();
        $new_base_tax_amount = $order->getBaseTaxAmount() - $old_order->getBaseTaxAmount();

        $new_shipping_amount = $order->getShippingAmount() - $old_order->getShippingAmount();
        $new_base_shipping_amount = $order->getBaseShippingAmount() - $old_order->getBaseShippingAmount();

        $old_amount_authorize = $payment->getAmountAuthorized();
        $old_base_amount_authorize = $payment->getBaseAmountAuthorized();

        $old_amount_paid = $payment->getAmountPaid();
        $old_base_amount_paid = $payment->getBaseAmountPaid();


        if(isset($old_amount_paid) && !empty($old_amount_paid)){
            $total_due = $order->getGrandTotal() - $order->getTotalRefunded() - $payment->getAmountPaid();
            $base_total_due = $order->getBaseGrandTotal() - $order->getBaseTotalRefunded() - $payment->getBaseAmountPaid();
        }

        // capture
        if ($base_total_due > 0) {
            $__new_tax_amount = $new_tax_amount > 0 ? $new_tax_amount : 0;

            $payment->getOrder()->setTaxAmount($__new_tax_amount);

            $__new_base_tax_amount = $new_base_tax_amount > 0 ? $new_base_tax_amount : 0;
            $payment->getOrder()->setBaseTaxAmount($__new_base_tax_amount);

            $__new_shipping_amount = $new_shipping_amount > 0 ? $new_shipping_amount : 0;
            $payment->setShippingAmount($__new_shipping_amount);

            $__new_base_shipping_amount = $new_base_shipping_amount > 0 ? $new_base_shipping_amount : 0;
            $payment->setBaseShippingAmount($__new_base_shipping_amount);

            $payment->setAmountPaid($old_amount_authorize);
            $payment->setBaseAmountPaid($old_amount_authorize);

            Mage::register('iwd_order_manager_authorize', true);

            if (!$payment->authorize(1, $base_total_due)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                return -1;
            }

            if(empty($old_amount_paid)){
                $payment->setAmountPaid($old_amount_paid);
                $payment->setBaseAmountPaid($old_base_amount_paid);
            }

            if($payment->getAmountAuthorized() < $payment->getAmountPaid()){
                $payment->setAmountAuthorized($payment->getAmountPaid());
                $payment->setBaseAmountAuthorized($payment->getBaseAmountPaid());
            } else {
                $payment->setAmountAuthorized($old_amount_authorize + $total_due);
                $payment->setBaseAmountAuthorized($old_base_amount_authorize + $base_total_due);
            }
        }
        // refund
        else if($base_total_due < 0 && $payment->getOrder()->getBaseTotalPaid() > 0){
            Mage::register('iwd_order_manager_authorize', true);

            $refund = abs($base_total_due);
            $payment->getMethodInstance()->refund($payment, $refund);
        }

        $payment->setAmountOrdered($payment->getAmountOrdered() + $total_due);
        $payment->setBaseAmountOrdered($payment->getBaseAmountOrdered() + $base_total_due);

        $payment->setShippingAmount($order->getShippingAmount());
        $payment->setBaseShippingAmount($order->getBaseShippingAmount());

        $payment->save();

        $order->setBaseTaxAmount($base_tax_amount)->setTaxAmount($tax_amount)->save();

        return 1;
    }
}
