<?php
if(Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true') && class_exists("IWD_All_Model_Paygate_Authorizenet")){
    class IWD_OrderManager_Model_Payment_Authorizenet_Rewrite extends IWD_All_Model_Paygate_Authorizenet {}
} else {
    class IWD_OrderManager_Model_Payment_Authorizenet_Rewrite extends Mage_Paygate_Model_Authorizenet {}
}

class IWD_OrderManager_Model_Payment_Authorizenet extends IWD_OrderManager_Model_Payment_Authorizenet_Rewrite
{
    protected $_canUseInternal = true;
    protected $_canSaveCc = true;

    /* * * * * OVERRIDE * * * */
    protected function _registerCard(Varien_Object $response, Mage_Sales_Model_Order_Payment $payment)
    {
        $cardsStorage = $this->getCardsStorage($payment);
        $card = $cardsStorage->registerCard();
        $card
            ->setRequestedAmount($response->getRequestedAmount())
            ->setBalanceOnCard($response->getBalanceOnCard())
            ->setLastTransId($response->getTransactionId())
            ->setProcessedAmount($response->getAmount())
            ->setCcType($payment->getCcType())
            ->setCcOwner($payment->getCcOwner())
            ->setCcLast4($payment->getCcLast4())
            ->setCcExpMonth($payment->getCcExpMonth())
            ->setCcExpYear($payment->getCcExpYear())
            ->setCcSsIssue($payment->getCcSsIssue())
            ->setCcSsStartMonth($payment->getCcSsStartMonth())
            ->setCcSsStartYear($payment->getCcSsStartYear());

        $cardsStorage->updateCard($card);
        //$this->_clearAssignedData($payment);
        return $card;
    }

    protected function _parseTransactionDetailsXmlResponseToVarienObject(Varien_Simplexml_Element $responseXmlDocument)
    {
        $response = new Varien_Object;
        $responseTransactionXmlDocument = $responseXmlDocument->transaction;
        //main fields for generating order status:
        $response
            ->setResponseCode((string)$responseTransactionXmlDocument->responseCode)
            ->setResponseReasonCode((string)$responseTransactionXmlDocument->responseReasonCode)
            ->setTransactionStatus((string)$responseTransactionXmlDocument->transactionStatus)
        ;
        //some additional fields:
        isset($responseTransactionXmlDocument->responseReasonDescription) && $response->setResponseReasonDescription((string)$responseTransactionXmlDocument->responseReasonDescription);
        isset($responseTransactionXmlDocument->FDSFilterAction)           && $response->setFdsFilterAction((string)$responseTransactionXmlDocument->FDSFilterAction);
        isset($responseTransactionXmlDocument->FDSFilters)                && $response->setFdsFilters(serialize($responseTransactionXmlDocument->FDSFilters->asArray()));
        isset($responseTransactionXmlDocument->transactionType)           && $response->setTransactionType((string)$responseTransactionXmlDocument->transactionType);
        isset($responseTransactionXmlDocument->submitTimeUTC)             && $response->setSubmitTimeUtc((string)$responseTransactionXmlDocument->submitTimeUTC);
        isset($responseTransactionXmlDocument->submitTimeLocal)           && $response->setSubmitTimeLocal((string)$responseTransactionXmlDocument->submitTimeLocal);
        isset($responseTransactionXmlDocument->authAmount)           && $response->setAuthAmount((string)$responseTransactionXmlDocument->authAmount);
        isset($responseTransactionXmlDocument->settleAmount)         && $response->setSettleAmount((string)$responseTransactionXmlDocument->settleAmount);

        return $response;
    }

    /* * * * * CUSTOM * * * */
    public function fetchTrxInfo($txn_id){
        $txn = null;

        try {
            $txn = Mage::getModel('sales/order_payment_transaction')->load($txn_id);
            Mage::register('current_transaction', $txn);

            if ($txn->getId()) {
                $txn->getOrderPaymentObject()
                    ->setOrder($txn->getOrder())
                    ->importTransactionInfo($txn);
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Unable to fetch transaction details.')
            );
            Mage::log($e->getMessage(), null, 'iwd_order_manager.log');
        }
        Mage::unregister('current_transaction');
        return $txn;
    }

    public function refundAuthorizeNet($order, $trx_id, $amount)
    {
        try {
            $payment = $order->getPayment();
            $payment = $this->restorePaymentData($payment);
            $payment->setSkipTransactionCreation(false);
            $card = new Varien_Object($payment->getData());
            $card->setLastTransId($trx_id);
            $this->om_refundCardTransaction($payment, $amount, $card);
            $payment->save();
            $order->save();
        } catch (Exception $e) { return $e->getMessage(); }
        return 1;
    }

    public function voidAuthorizeNet($order)
    {
        try {
            $payment = $order->getPayment();
            $payment = $this->restorePaymentData($payment);
            $payment->setSkipTransactionCreation(false);
            $payment->void(new Varien_Object());

            $payment->setBaseAmountPaid(0)->setAmountPaid(0)->save();
            $order->setBaseAmountPaid(0)->setAmountPaid(0)->save();
        }catch (Exception $e){return $e->getMessage(); }
        return 1;
    }

    public function voidAuthorizeNetTransaction($order, $trx_id)
    {
        try {
            $payment = $order->getPayment();
            $payment = $this->restorePaymentData($payment);
            $payment->setSkipTransactionCreation(false);
            $card = new Varien_Object($payment->getData());
            $card->setLastTransId($trx_id);
            $this->om_voidCardTransaction($payment, $card);

            $payment->save();
            $order->save();
        }catch (Exception $e){return $e->getMessage();}
        return 1;
    }

    protected function om_voidCardTransaction($payment, $card)
    {
        $authTransactionId = $card->getLastTransId();
        $authTransaction = Mage::getModel('sales/order_payment_transaction')->load($authTransactionId);
        $realAuthTransactionId = $authTransaction->getAdditionalInformation($this->_realTransactionIdKey);

        $payment->setAnetTransType(self::REQUEST_TYPE_VOID);
        $payment->setXTransId($realAuthTransactionId);

        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_APPROVED) {
                    $voidTransactionId = $result->getTransactionId() . '-void';
                    $card->setLastTransId($voidTransactionId);
                    return $this->_addTransaction(
                        $payment,
                        $voidTransactionId,
                        Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID,
                        array(
                            'is_transaction_closed' => 1,
                            'should_close_parent_transaction' => 1,
                            'parent_transaction_id' => $authTransactionId
                        ),
                        array($this->_realTransactionIdKey => $result->getTransactionId()),
                        Mage::helper('paygate')->getTransactionMessage(
                            $payment, self::REQUEST_TYPE_VOID, $result->getTransactionId(), $card
                        )
                    );
                }
                $exceptionMessage = $this->_wrapGatewayError($result->getResponseReasonText());
                break;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_NOT_FOUND
                    && $this->_isTransactionExpired($realAuthTransactionId)
                ) {
                    $voidTransactionId = $realAuthTransactionId . '-void';
                    return $this->_addTransaction(
                        $payment,
                        $voidTransactionId,
                        Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID,
                        array(
                            'is_transaction_closed' => 1,
                            'should_close_parent_transaction' => 1,
                            'parent_transaction_id' => $authTransactionId
                        ),
                        array(),
                        Mage::helper('paygate')->getExtendedTransactionMessage(
                            $payment,
                            self::REQUEST_TYPE_VOID,
                            null,
                            $card,
                            false,
                            false,
                            Mage::helper('paygate')->__('Parent Authorize.Net transaction (ID %s) expired', $realAuthTransactionId)
                        )
                    );
                }
                $exceptionMessage = $this->_wrapGatewayError($result->getResponseReasonText());
                break;
            default:
                $exceptionMessage = Mage::helper('paygate')->__('Payment voiding error.');
                break;
        }

        $exceptionMessage = Mage::helper('paygate')->getTransactionMessage(
            $payment, self::REQUEST_TYPE_VOID, $realAuthTransactionId, $card, false, $exceptionMessage
        );
        Mage::throwException($exceptionMessage);
        return null;
    }

    protected function om_refundCardTransaction($payment, $amount, $card)
    {
        $captureTransactionId = $card->getLastTransId();
        $captureTransaction = Mage::getModel('sales/order_payment_transaction')->load($captureTransactionId);
        $realCaptureTransactionId = $captureTransaction->getAdditionalInformation($this->_realTransactionIdKey);

        $payment->setAnetTransType(self::REQUEST_TYPE_CREDIT);
        $payment->setXTransId($realCaptureTransactionId);
        $payment->setAmount($amount);

        $request = $this->_buildRequest($payment);
        $request->setXCardNum($card->getCcLast4());
        $result = $this->_postRequest($request);

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_APPROVED) {
                    $refundTransactionId = $result->getTransactionId() . '-refund';
                    $shouldCloseCaptureTransaction = 0;
                    /**
                     * If it is last amount for refund, transaction with type "capture" will be closed
                     * and card will has last transaction with type "refund"
                     */
                    if ($this->_formatAmount($card->getCapturedAmount() - $card->getRefundedAmount()) == $amount) {
                        $card->setLastTransId($refundTransactionId);
                        $shouldCloseCaptureTransaction = 1;
                    }
                    return $this->_addTransaction(
                        $payment,
                        $refundTransactionId,
                        Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND,
                        array(
                            'is_transaction_closed' => 1,
                            'should_close_parent_transaction' => $shouldCloseCaptureTransaction,
                            'parent_transaction_id' => $captureTransactionId
                        ),
                        array($this->_realTransactionIdKey => $result->getTransactionId()),
                        Mage::helper('paygate')->getTransactionMessage(
                            $payment, self::REQUEST_TYPE_CREDIT, $result->getTransactionId(), $card, $amount
                        )
                    );
                }
                $exceptionMessage = $this->_wrapGatewayError($result->getResponseReasonText());
                break;
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                $exceptionMessage = $this->_wrapGatewayError($result->getResponseReasonText());
                break;
            default:
                $exceptionMessage = Mage::helper('paygate')->__('Payment refunding error.');
                break;
        }

        $exceptionMessage = Mage::helper('paygate')->getTransactionMessage(
            $payment, self::REQUEST_TYPE_CREDIT, $realCaptureTransactionId, $card, $amount, $exceptionMessage
        );
        Mage::throwException($exceptionMessage);
        return null;
    }

    public function authorizeAuthorizeNet($order, $amount)
    {
        try {
            $payment = $order->getPayment();
            $payment = $this->restorePaymentData($payment);
            $payment->setSkipTransactionCreation(false);
            if (!$payment->authorize(1, $amount)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                return -1;
            }
            $payment->save();
            $order->save();
            $this->restoreAdditionalInformation($payment, $order);
        }catch (Exception $e){return $e->getMessage();}
        return 1;
    }

    public function captureAuthorizeNet($order, $amount)
    {
        try {
            /* @var $order Mage_Sales_Model_Order */
            /* @var $payment Mage_Sales_Model_Order_Payment */

            $payment = $order->getPayment();
            $payment = $this->restorePaymentData($payment);
            $payment->setSkipTransactionCreation(false);
            if (!$this->_place($payment, $amount, self::REQUEST_TYPE_AUTH_CAPTURE)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                return -1;
            }

            $payment->save();
            $order->save();
            $this->restoreAdditionalInformation($payment, $order);
        } catch(Exception $e) {return $e->getMessage(); }
        return 1;
    }

    /* Some extensions remove information that we need for edit order */
    protected function restorePaymentData($payment)
    {
        $info = $payment->getAdditionalInformation();

        if(isset($info['authorize_cards']) && count($info['authorize_cards'])){
            $info = array_shift($info['authorize_cards']);
            $data = $payment->getData('cc_exp_month');

            if(isset($info['cc_exp_month']) && empty($data['cc_exp_month'])) {
                $payment->setData('cc_exp_month', $info['cc_exp_month']);
            }
            if(isset($info['cc_last4']) && empty($data['cc_last4'])) {
                $payment->setData('cc_last4', $info['cc_last4']);
            }
            if(isset($info['cc_exp_year']) && empty($data['cc_exp_year'])) {
                $payment->setData('cc_exp_year', $info['cc_exp_year']);
            }
            if(isset($info['cc_type']) && empty($data['cc_type'])) {
                $payment->setData('cc_type', $info['cc_type']);
            }
            $payment->save();
        }

        return $payment;
    }

    protected function restoreAdditionalInformation($payment, $order)
    {
        $additional_information = $payment->getAdditionalInformation();
        foreach($additional_information['authorize_cards'] as $id => $information){
            $additional_information['authorize_cards'][$id]["captured_amount"] = $order->getPayment()->getBaseAmountPaid();
            $additional_information['authorize_cards'][$id]["processed_amount"] = $order->getBaseGrandTotal();
            break;
        }
        $payment->setAdditionalInformation($additional_information);
        $payment->save();
    }
}