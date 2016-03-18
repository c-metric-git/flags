<?php

require_once 'MarketplaceWebService/Client.php';
require_once 'MarketplaceWebService/Model.php';
require_once 'MarketplaceWebService/Interface.php';
require_once 'MarketplaceWebService/RequestType.php';
require_once 'MarketplaceWebService/Exception.php';
require_once 'MarketplaceWebService/Model/IdList.php';
require_once 'MarketplaceWebService/Model/SubmitFeedRequest.php';
require_once 'MarketplaceWebService/Model/SubmitFeedResponse.php';
require_once 'MarketplaceWebService/Model/RequestReportRequest.php';
require_once 'MarketplaceWebService/Model/RequestReportResponse.php';
require_once 'MarketplaceWebService/Model/GetReportRequestListRequest.php';
require_once 'MarketplaceWebService/Model/GetReportRequestListResponse.php';
require_once 'MarketplaceWebService/Model/GetReportRequestListByNextTokenRequest.php';
require_once 'MarketplaceWebService/Model/GetReportRequestListByNextTokenResponse.php';
require_once 'MarketplaceWebService/Model/GetReportListByNextTokenRequest.php';
require_once 'MarketplaceWebService/Model/GetReportListByNextTokenResponse.php';
require_once 'MarketplaceWebService/Model/GetReportListRequest.php';
require_once 'MarketplaceWebService/Model/GetReportListResponse.php';
require_once 'MarketplaceWebService/Model/GetReportRequest.php';
require_once 'MarketplaceWebService/Model/GetReportResponse.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionListRequest.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionListResponse.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionListByNextTokenRequest.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionListByNextTokenResponse.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php';
require_once 'MarketplaceWebService/Model/GetFeedSubmissionResultResponse.php';

class AmazonShipment
{
    public $orderId;
    public $shippingCarrier;
    public $shippingMethod;
    public $items;
    
    public function __construct($orderId, $shippingCarrier, $shippingMethod = 'Standard', $items = false)
    {
        $this->orderId = $orderId;
        $this->shippingCarrier = $shippingCarrier;
        $this->shippingMethod = $shippingMethod;
        $this->items = $items;
    }
    
    public function toXml()
    {
        date_default_timezone_set('UTC');
        $dateTime = strftime("%Y-%m-%dT%H:%M:%S");
        
        $xml = '';
        $xml .= '<OrderFulfillment>' . PHP_EOL;
        $xml .= "<AmazonOrderID>{$this->orderId}</AmazonOrderID>" . PHP_EOL;
        $xml .= "<FulfillmentDate>$dateTime</FulfillmentDate>" . PHP_EOL;
        $xml .= "<FulfillmentData>" . PHP_EOL;
        $xml .= "<CarrierName>{$this->shippingCarrier}</CarrierName>" . PHP_EOL;
        $xml .= "<ShippingMethod>{$this->shippingMethod}</ShippingMethod>" . PHP_EOL;
        if ($this->trackingNumber) {
            $xml .= "<ShipperTrackingNumber>{$this->trackingNumber}</ShipperTrackingNumber>" . PHP_EOL;
        }
        $xml .= "</FulfillmentData>" . PHP_EOL;
        if ($this->items) {
            foreach($this->items as $oneItem) {
                $xml .= $oneItem->toXml();
            }
        }
        $xml .= "</OrderFulfillment>" . PHP_EOL;
        
        return $xml;
    }
    
    public function isValid()
    {
        return !empty($this->orderId) && !empty($this->shippingCarrier);
    }
}

class AmazonShipmentItem
{
    public $orderItemId;
    public $quantity;
    
    public function __construct($orderItemId, $quantity = false)
    {
        $this->orderItemId = $orderItemId;
        $this->quantity = $quantity;
    }
    
    public function toXml()
    {
        $xml = '';
        $xml .= "<Item>" . PHP_EOL;
        $xml .= "<AmazonOrderItemCode>{$this->orderItemId}</AmazonOrderItemCode>" . PHP_EOL;
        if ($this->quantity > 0) {
            $xml .= "<Quantity>{$this->quantity}</Quantity>" . PHP_EOL;
        }
        $xml .= "</Item>" . PHP_EOL;
    }
    
}

class AmazonSummary
{
    public $totalMessages;
    public $totalErrors;
    public $totalUpdates;
    public $totalWarnings;
    public $errorsMessages;
    
    public function __construct()
    {
        $this->totalMessages = 0;
        $this->totalErrors = 0;
        $this->totalUpdates = 0;
        $this->totalWarnings = 0;
        $this->errorMessages = array();
    }
    
    public function fatalError()
    {
        $this->totalErrors = $this->totalMessages;
        $this->totalUpdates = 0;
        $this->totalWarnings = 0;
        
        return $this;
    }
    
    public function parseResult($xml)
    {
        if (!$xml) {
            throw new Exception('No xml');
        }
        App::debug($xml);
        
        $processingReport = $xml->Message->ProcessingReport;
        if ( ($summary = $processingReport->ProcessingSummary) )
        {
            $this->totalErrors = (int)$summary->MessagesWithError;
            $this->totalUpdates = (int)$summary->MessagesSuccessful; 
        }
        
        $messages = $processingReport->Result;
        if ($messages) {
            foreach($messages as $message) {
                if ($message->ResultCode == "Error") {
                    $this->errorMessages[] = (string)$message->ResultDescription . (string)$message->AdditionalInfo;
                }
            }
        }
        
        return $this;
    }
    
}

class AmazonMWSFeeds
{
    private $sec;
    private $impl;
    
    public static function create($security_credentials)
    {
        if ($security_credentials && $security_credentials->mwsMarketplaceID && $security_credentials->mwsMerchantID && $security_credentials->mwsAccessKey && $security_credentials->mwsSecretKey) {
            return new AmazonMWSFeeds($security_credentials);
        }
        throw new Exception('Invalid security credentials');
    }
    
    private function __construct($sec)
    {
        $this->sec = $sec;
    }
    
    public function updateShipments($shipments)
    {
        $summary = new AmazonSummary();
        if (!$shipments) {
            return $summary;
        }
        if (!is_array($shipments)) {
            throw new Exception('No shipment array');
        }
        $stream = $this->_toXmlStream($summary, $shipments, 'OrderFulfillment');
        if (!$stream) {
            throw new Exception('No XML for Amazon feed');
        }
        return $this->_submit($summary, $stream, '_POST_ORDER_FULFILLMENT_DATA_');
    }
    
    private function _submit($summary, $stream, $feedType)
    {
        $marketplaceIdArray = array("Id" => array($sec->mwsMarketplaceID));
        
        $sec = $this->sec;
        $service = $sec->getFeedService();
        //App::debug($service);
        
        rewind($stream);
        App::debug(stream_get_contents($stream));
        
        rewind($stream);
        
        $parameters = array (
            'Merchant' => $sec->mwsMerchantID,
            'Marketplace' => $sec->mwsMarketplaceID,
            'FeedType' => $feedType,
            'FeedContent' => $stream,
            'PurgeAndReplace' => false,
            'ContentMd5' => base64_encode(md5(stream_get_contents($stream), true)),
            );
        
        rewind($stream);
        
        $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        
        if ( ($feedSubmissionId = $this->_invokeSubmitFeed($service, $request)) !== false) {
            $parameters = array ('Merchant' => $sec->mwsMerchantID, 'FeedSubmissionIdList' => array('FieldValue' => $feedSubmissionId, 'FieldType' => 'MarketplaceWebService_Model_IdList'));
            
            $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters);
            $feedSubmissionIdList = new MarketplaceWebService_Model_IdList();
            $feedSubmissionIdList->setId(array($feedSubmissionId));
            $request->setFeedSubmissionIdList($feedSubmissionIdList);
            
            do {
                sleep(45);
                
                $processingStatus = $this->_invokeGetFeedSubmissionList($sec, $service, $request, $feedSubmissionId);
            } while ($processingStatus && strtoupper($processingStatus) !== "_DONE_" && strtoupper($processingStatus) !== "_DONE_NO_DATA_");
            
            if ($processingStatus) {
                if ( ($streamTwo = @fopen('php://temp', 'rw+')) !== false) {
                    $parameters = array ( 'Merchant' => $sec->mwsMerchantID, 'FeedSubmissionId' => $feedSubmissionId, 'FeedSubmissionResult' => $streamTwo, );

                    $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

                    if ( ($res = $this->_invokeGetFeedSubmissionResult($service, $request)) ) {
                        $content = stream_get_contents($streamTwo);
                        
                        $xml = simplexml_load_string($content);
                        
                        $summary->parseResult($xml);
                    }
                    
                    @fclose($streamTwo);
                } else {
                    $summary->fatalError();
                }
            } else {
                $summary->fatalError();
            }
        } else {
            $summary->fatalError();
        }
        
        @fclose($stream);

        return $summary;
    }
    
    private function _toXmlStream($summary, $amazonObjects, $messageType)
    {
        if (!$amazonObjects) {
            throw new Exception('No amazon objects');
        }
        if (!$messageType) {
            throw new Exception('No message type');
        }
        if ( ($stream = @fopen('php://temp', 'rw+')) !== false) {
            $this->_writeHeader($stream);
            fwrite($stream, "<MessageType>$messageType</MessageType>");
            
            $messageId = 1;
            
            date_default_timezone_set('UTC');
            $dateTime = strftime("%Y-%m-%dT%H:%M:%S");
            
            foreach($amazonObjects as $oneObject) {
                if ($oneObject->isValid()) {
                    $xml = '<Message>';
                    $xml .= "<MessageID>$messageId</MessageID>";
                    $xml .= "<OperationType>Update</OperationType>";
                    $xml .= $oneObject->toXml();
                    $xml .= '</Message>';
                    $xml .= "\n";
                    
                    fwrite($stream, $xml);
                    fflush($stream);
                    
                    $messageId++;
                } else {
                    $summary->totalErrors++;
                }
            }
            $this->_writeFooter($stream);
        } else {
            throw new Exception('No PHP stream for amazon feed');
        }
        $summary->totalMessages = $messageId - 1 + $summary->totalErrors;
        
        return $stream;
    }
    
    private function _writeHeader($stream)
    {
        fwrite($stream, '<?xml version="1.0" encoding="UTF-8"?>');
        fwrite($stream, '<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">');
        fwrite($stream, '<Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>various</MerchantIdentifier></Header>');
        fflush($stream);
    }
    
    private function _writeFooter($stream)
    {
        fwrite($stream, '</AmazonEnvelope>');
        fflush($stream);
    }
    
    private function _invokeSubmitFeed(MarketplaceWebService_Interface $service, $request, $retries = 0) 
    {
        $res = false;
        
        try {
            $response = $service->submitFeed($request);
            
            if ($response->isSetSubmitFeedResult()) { 
                $submitFeedResult = $response->getSubmitFeedResult();
                
                if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                    $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                    
                    if ($feedSubmissionInfo->isSetFeedSubmissionId()) {
                        $res = $feedSubmissionInfo->getFeedSubmissionId();
                    }
                } 
            } 
        } catch (MarketplaceWebService_Exception $ex) {
            if ($retries < 3) {
                sleep(60);
                $res = $this->_invokeSubmitFeed($service, $request, $retries+1);
            } else {
                $res = false;
            }
        }
        
        return $res;
    }
    
    private function _invokeGetFeedSubmissionList($sec, MarketplaceWebService_Interface $service, $request, $feedSubmissionId, $retries = 0) 
    {
        $res = false;
        
        try {
            $response = $service->getFeedSubmissionList($request);
            
            $nextToken = false;
            $hasNext = false;
            
            if ($response->isSetGetFeedSubmissionListResult()) { 
                $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
                
                if ($getFeedSubmissionListResult->isSetNextToken()) {
                    $nextToken = $getFeedSubmissionListResult->getNextToken();
                }
                if ($getFeedSubmissionListResult->isSetHasNext()) {
                    $hasNext = $getFeedSubmissionListResult->getHasNext();
                }
                
                $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
                
                foreach ($feedSubmissionInfoList as $feedSubmissionInfo) {
                    $id = false;
                    $status = false;
                    
                    if ($feedSubmissionInfo->isSetFeedSubmissionId()) {
                        $id = $feedSubmissionInfo->getFeedSubmissionId();
                    }
                    if ($feedSubmissionInfo->isSetFeedProcessingStatus()) {
                        $status = $feedSubmissionInfo->getFeedProcessingStatus();
                    }
                    
                    if ($id && $id === $feedSubmissionId) {
                        $res = $status;
                        break;
                    }
                }
                
                if ($res === false) {
                    if ($nextToken && $hasNext) {
                        /** we had no feed submission id in this list **/
                        $parameters = array ('Merchant' => $sec->mwsMerchantID, 'NextToken' => $nextToken, );

                        $request = new MarketplaceWebService_Model_GetFeedSubmissionListByNextTokenRequest($parameters);

                        $res = $this->invokeGetFeedSubmissionListByNextToken($sec, $service, $request, $feedSubmissionId);
                    } else {
                        throw new Exception('No feed submission entry for ' . $feedSubmissionId);
                    }
                }
            } 
        } catch (MarketplaceWebService_Exception $ex) {
            if ($retries < 3) {
                sleep(45);
                $res = $this->_invokeGetFeedSubmissionList($sec, $service, $request, $feedSubmissionId, $retries+1);
            } else {
                $res = false;
            }
        }
        
        return $res;
    }
    
    function _invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request, $retries = 0) 
    {
        $res = false;
        
        try {
            $response = $service->getFeedSubmissionResult($request);
            
            if ($response->isSetGetFeedSubmissionResultResult()) {
                $res = true;
            }
        } catch (MarketplaceWebService_Exception $ex) {
            if ($retries < 3) {
                sleep(45);
                $res = $this->_invokeGetFeedSubmissionResult($service, $requests, $retries+1);
            } else {
                $res = false;
            }
        }
        
        return $res;
    }
    
}
