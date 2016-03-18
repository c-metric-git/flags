<?php
class IWD_OrderManager_Model_Payment_Paypal_Payflowpro extends Mage_Paypal_Model_Payflowpro
{
    //transstate:
    //0: General success state.
    //1: General error state.
    //3: Authorization approved.
    //6: Settlement pending (transaction is scheduled to be settled).
    //7: Settlement in progress (transaction involved in a currently ongoing settlement).
    //8: Settled successfully.
    //9: Authorization captured (once an authorization type transaction is captured, its TRANSTATE becomes 9).
    //10: Capture failed (an error occurred while trying to capture an authorization because the transaction was already captured).
    //11: Failed to settle (transactions fail settlement usually because of problems with the merchant's processor or because the card type is not set up with the merchant's processor).
    //12: Unsettled transaction because of incorrect account information.
    //14: For various reasons, the batch containing this transaction failed settlement.
    //16: Merchant ACH settlement failed. It needs to be corrected manually.

    public function reauthorize(Varien_Object $payment, $amount)
    {
        try{
            $this->__refund($payment);
            $this->__authorize($payment, $amount);
        }catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return false;
        }

        return true;
    }

    protected function __refund(Varien_Object $payment)
    {
        $order = $payment->getOrder();

        $transactions = Mage::getModel('sales/order_payment_transaction')->getCollection()
            ->addFieldToFilter('order_id', $order->getId())
            ->addFieldToFilter('is_closed', 0);

        foreach ($transactions as $transaction) {
            $can_void = false;
            if($transaction->getTxnType() == Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE){

                $txn_info = $this->fetchTransactionInfo($payment, $transaction->getTxnId());
                if(isset($txn_info["transstate"])){
                    if(in_array($txn_info["transstate"], array(7,8))){
                        //TODO: refund
                        /*$this->refund($payment, ); !!!
                        $message = Mage::helper('sales')->__('Refunded amount of %s.', $transaction->getTxnId());
                        $order->addStatusHistoryComment($message)->setIsVisibleOnFront(true)->setIsCustomerNotified(false)->save();
                        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, null, true, $message);
                        $payment->save();*/
                    } else if(in_array($txn_info["transstate"], array(6,3,9))){
                        $can_void = true;
                    }
                }
            }

            $can_void = $can_void || ($transaction->getTxnType() == Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);

            if($can_void){
                $payment->setParentTransactionId($transaction->getTxnId());
                $this->void($payment);
                $message = Mage::helper('sales')->__('Voided transaction %s.', $transaction->getTxnId());
                $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, null, true, $message);
                $payment->save();
            }

            $payment->setParentTransactionId(null);
        }
    }

    protected function __authorize(Varien_Object $payment, $amount)
    {
        $order = $payment->getOrder();

        $request = $this->_buildPlaceRequest($payment, $amount);

        if($order->hasInvoices()){
            $request->setTrxtype(self::TRXTYPE_SALE);
            $message = Mage::helper('sales')->__('Captured amount of %s.', $order->getBaseCurrency()->formatTxt($amount));
            $transaction_type = Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
        } else {
            $request->setTrxtype(self::TRXTYPE_AUTH_ONLY);
            $request->setTender('C');
            $message = Mage::helper('sales')->__('Authorized amount of %s.', $order->getBaseCurrency()->formatTxt($amount));
            $transaction_type = Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH;
        }

        $lastTransId = $payment->getLastTransId();
        $request->setOrigid($lastTransId);

        $request->setDoreauthorization(1);
        $this->_setReferenceTransaction($payment, $request);
        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->addTransaction($transaction_type, null, true, $message);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }

        $order->save();
        $payment->save();

        return $this;
    }
}
