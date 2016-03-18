<?php

class Stripedsocks_Authextrainfo_Model_Authorizenet extends Mage_Paygate_Model_Authorizenet
{
    /**
     * Send request with new payment to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @param string $requestType
     * @return Mage_Paygate_Model_Authorizenet
     * @throws Mage_Core_Exception
     */
    protected function _place($payment, $amount, $requestType)
    {
        $payment->setAnetTransType($requestType);
        $payment->setAmount($amount);
        $request= $this->_buildRequest($payment);
        $result = $this->_postRequest($request);
        switch ($requestType) {
            case self::REQUEST_TYPE_AUTH_ONLY:
                $newTransactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH;
                $defaultExceptionMessage = Mage::helper('paygate')->__('Payment authorization error.');
                break;
            case self::REQUEST_TYPE_AUTH_CAPTURE:
                $newTransactionType = Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
                $defaultExceptionMessage = Mage::helper('paygate')->__('Payment capturing error.');
                break;
        }

        switch ($result->getResponseCode()) {
            case self::RESPONSE_CODE_APPROVED:
                $this->getCardsStorage($payment)->flushCards();
                $card = $this->_registerCard($result, $payment);
				
				$otherinfo = $this->_getCustomtransactiondetail($card->getLastTransId());
				$infoobj = $this->getInfoInstance();
				$infoobj->setAdditionalInformation('otherinfo', $otherinfo);
                
				$this->_addTransaction(
                    $payment,
                    $card->getLastTransId(),
                    $newTransactionType,
                    array('is_transaction_closed' => 0),
                    array($this->_realTransactionIdKey => $card->getLastTransId()),
                    Mage::helper('paygate')->getTransactionMessage(
                        $payment, $requestType, $card->getLastTransId(), $card, $amount
                    )
                );
                if ($requestType == self::REQUEST_TYPE_AUTH_CAPTURE) {
                    $card->setCapturedAmount($card->getProcessedAmount());
                    $this->getCardsStorage($payment)->updateCard($card);
                }
                return $this;
            case self::RESPONSE_CODE_HELD:
                if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PENDING_REVIEW_AUTHORIZED
                    || $result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PENDING_REVIEW
                ) {
                    $card = $this->_registerCard($result, $payment);
					
					$otherinfo = $this->_getCustomtransactiondetail($card->getLastTransId());
					$infoobj = $this->getInfoInstance();
					$infoobj->setAdditionalInformation('otherinfo', $otherinfo);
                    
					$this->_addTransaction(
                        $payment,
                        $card->getLastTransId(),
                        $newTransactionType,
                        array('is_transaction_closed' => 0),
                        array(
                            $this->_realTransactionIdKey => $card->getLastTransId(),
                            $this->_isTransactionFraud => true
                        ),
                        Mage::helper('paygate')->getTransactionMessage(
                            $payment, $requestType, $card->getLastTransId(), $card, $amount
                        )
                    );
                    if ($requestType == self::REQUEST_TYPE_AUTH_CAPTURE) {
                        $card->setCapturedAmount($card->getProcessedAmount());
                        $this->getCardsStorage()->updateCard($card);
                    }
                    $payment
                        ->setIsTransactionPending(true)
                        ->setIsFraudDetected(true);
                    return $this;
                }
                if ($result->getResponseReasonCode() == self::RESPONSE_REASON_CODE_PARTIAL_APPROVE) {
                    $checksum = $this->_generateChecksum($request, $this->_partialAuthorizationChecksumDataKeys);
                    $this->_getSession()->setData($this->_partialAuthorizationChecksumSessionKey, $checksum);
                    if ($this->_processPartialAuthorizationResponse($result, $payment)) {
                        return $this;
                    }
                }
                Mage::throwException($defaultExceptionMessage);
            case self::RESPONSE_CODE_DECLINED:
            case self::RESPONSE_CODE_ERROR:
                Mage::throwException($this->_wrapGatewayError($result->getResponseReasonText()));
            default:
                Mage::throwException($defaultExceptionMessage);
        }
        return $this;
    }
    protected function _getCustomtransactiondetail($transId)
    {
		//$autobj = Mage::getModel('paygate/authorizenet');
 		$requestBody = sprintf(
            '<?xml version="1.0" encoding="utf-8"?>'
            . '<getTransactionDetailsRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">'
            . '<merchantAuthentication><name>%s</name><transactionKey>%s</transactionKey></merchantAuthentication>'
            . '<transId>%s</transId>'
            . '</getTransactionDetailsRequest>',
            $this->getConfigData('login'),
            $this->getConfigData('trans_key'),
            $transId
        );
/* 		$requestBody = sprintf(
            '<?xml version="1.0" encoding="utf-8"?>'
            . '<getTransactionDetailsRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">'
            . '<merchantAuthentication><name>%s</name><transactionKey>%s</transactionKey></merchantAuthentication>'
            . '<transId>%s</transId>'
            . '</getTransactionDetailsRequest>',
            '9Q5CmzXFz4qC',
            '62yDvtk2yG9S4A7g',
            '8009454879'
        ); */

        $client = new Varien_Http_Client();
        $uri = $this->getConfigData('cgi_url_td');
		/*$client->setUri($uri ? $uri : self::CGI_URL_TD);*/
 		if(!$this->getConfigData('test'))
		{
			$client->setUri('https://api.authorize.net/xml/v1/request.api');	
		}
		else
		{
			$client->setUri('https://apitest.authorize.net/xml/v1/request.api');
		}
		//$client->setUri('https://api.authorize.net/xml/v1/request.api');	
        $client->setConfig(array('timeout'=>45));
        $client->setHeaders(array('Content-Type: text/xml'));
        $client->setMethod(Zend_Http_Client::POST);
        $client->setRawData($requestBody);

        $debugData = array('request' => $requestBody);

        try {
            $responseBody = $client->request()->getBody();
			$responseXmlDocument = new Varien_Simplexml_Element($responseBody);
			$result = array();
			$result['trans_id'] = (string)$responseXmlDocument->transaction->transId;
			$result['first_name'] = (string)$responseXmlDocument->transaction->billTo->firstName;
			$result['last_name'] = (string)$responseXmlDocument->transaction->billTo->lastName;
			$result['auth_code'] = (string)$responseXmlDocument->transaction->authCode;
			$avsresponse = (string)$responseXmlDocument->transaction->AVSResponse;
			if($avsresponse == 'Y')
			{
				$result['avs_addmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;
				$result['avs_zipmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;
			}
			elseif($avsresponse == 'N')
			{
				$result['avs_addmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;
				$result['avs_zipmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;				
			}
			elseif($avsresponse == 'A')
			{
				$result['avs_addmatch'] = 'Y';
				$result['avs_zipmatch'] = 'N';
			}
			elseif($avsresponse == 'Z')
			{
				$result['avs_addmatch'] = 'N';
				$result['avs_zipmatch'] = 'Y';
			}
			else
			{
				$result['avs_addmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;
				$result['avs_zipmatch'] = (string)$responseXmlDocument->transaction->AVSResponse;	
			}
			$result['cvv_match'] = (string)$responseXmlDocument->transaction->cardCodeResponse;
			$result['avs_match'] = (string)$responseXmlDocument->transaction->AVSResponse;
			return $result;
			
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('paygate')->__('Payment updating error.'));
        }
	}
}