<?php

require_once 'App.php';

require_once 'AmazonOrders.class.php';
require_once 'AmazonFeeds.class.php';

class AmazonMWSSec
{
    public $mwsMerchantID;
    public $mwsMarketplaceID;
    public $mwsAccessKey;
    public $mwsSecretKey;
    public $mwsServiceName; // com, de, uk, ...
    
    public function __construct($mws_merchant_id, $mws_marketplace_id, $mws_accesskey, $mws_secretkey, $mws_service_name)
    {
        $this->mwsMerchantID = $mws_merchant_id;
        $this->mwsMarketplaceID = $mws_marketplace_id;
        $this->mwsAccessKey = $mws_accesskey;
        $this->mwsSecretKey = $mws_secretkey;
        $this->mwsServiceName = $mws_service_name;
    }
    
    public function getOrderServiceUrl()
    {
        if ($this->mwsServiceName)
        {
            if (stripos(strtoupper($this->mwsServiceName), "COM") !== false) {
                return "https://mws.amazonservices.com/Orders/2011-01-01";
            } else {
                throw new Exception('Unknown service name');
            }
        }
        
        return false;	
    }
    
    public function getOrderService()
    {
        $config = array ( 'ServiceURL' => $this->getOrderServiceUrl(), 'ProxyHost' => null, 'ProxyPort' => -1, 'MaxErrorRetry' => 3, );
        
        return new MarketplaceWebServiceOrders_Client($this->mwsAccessKey, $this->mwsSecretKey, "amzlibphp", "V1.0", $config);
    }
    
    public function getFeedServiceUrl()
    {
        if ($this->mwsServiceName) {
            if (stripos(strtoupper($this->mwsServiceName), "COM") !== false) {
                return "https://mws.amazonservices.com";
            } else {
                throw new Exception('Unknown service name');
            }
        }
        
        return false;	
    }
    
    public function getFeedService()
    {
        $config = array ( 'ServiceURL' => $this->getFeedServiceUrl(), 'ProxyHost' => null, 'ProxyPort' => -1, 'MaxErrorRetry' => 3, );
        
        return new MarketplaceWebService_Client($this->mwsAccessKey, $this->mwsSecretKey, $config, "amzlibphp", "V1.0");
    }
}

class AmazonMWS
{
    public static function createOrders($security_credentials)
    {
        return AmazonMWSOrders::create($security_credentials);;
    }
    
    public static function createFeeds($sec)
    {
        return AmazonMWSFeeds::create($sec);
    }
}