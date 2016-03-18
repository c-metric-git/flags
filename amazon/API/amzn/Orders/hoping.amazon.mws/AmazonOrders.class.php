<?php

require_once 'Amazon.php';

require_once 'MarketplaceWebServiceOrders/Client.php';

require_once 'MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrderItemsResponse.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrderItemsByNextTokenRequest.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrderItemsByNextTokenResponse.php';

require_once 'MarketplaceWebServiceOrders/Model/ListOrdersRequest.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrdersResponse.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrdersByNextTokenRequest.php';
require_once 'MarketplaceWebServiceOrders/Model/ListOrdersByNextTokenResponse.php';
require_once 'MarketplaceWebServiceOrders/Model/MarketplaceIdList.php';

require_once 'MarketplaceWebServiceOrders/Model/GetOrderRequest.php';
require_once 'MarketplaceWebServiceOrders/Model/GetOrderResponse.php';
require_once 'MarketplaceWebServiceOrders/Model/OrderIdList.php';
require_once 'MarketplaceWebServiceOrders/Model/OrderStatusList.php';

class AmazonOrder
{
    public $orderId;
    public $purchaseDate;
    public $orderStatus;
    public $salesChannel;
    public $shipToName;
    public $shipToAddressLine1;
    public $shipToAddressLine2;
    public $shipToAddressLine3;
    public $shipToCity;
    public $shipToCounty;
    public $shipToDistrict;
    public $shipToStateOrRegion;
    public $shipToPostalCode;
    public $shipToCountryCode;
    public $shipToPhone;
    public $orderTotalCurrency;
    public $orderTotalAmount;
    public $numberOfItemsShipped;
    public $buyerEmail;
    public $buyerName;
    public $shipmentServiceLevelCategory;
}

class AmazonOrderItem
{
    public $orderId;
    public $title;
}

class AmazonMWSOrders
{
    private $sec;
    private $impl;
    
    public static function create($security_credentials)
    {
        if ($security_credentials && $security_credentials->mwsMarketplaceID && $security_credentials->mwsMerchantID && $security_credentials->mwsAccessKey && $security_credentials->mwsSecretKey) {
            return new AmazonMWSOrders($security_credentials);
        }
        
        return null;
    }
    
    private function __construct($sec)
    {
        $this->sec = $sec;
    }
    
    public function getUnshippedOrders()
    {
        return $this->impl->getUnshippedOrders();
    }
    
    public function listOrderItems($amazonOrderIds)
    {
        if (!$amazonOrderIds) {
            return false;
        }
        $items = false;
        if ( ($service = $this->sec->getOrderService()) === false) {
            throw new Exception('No service');
        } else {
            $items = array();
            foreach($amazonOrderIds as $amazonOrderId) {
                sleep(4);
                $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
                $request->setSellerId($this->sec->mwsMerchantID);
                $request->setAmazonOrderId($amazonOrderId);

                $newItems = $this->_invokeListOrderItems($this->sec, $service, $request);
                if ($newItems) {
                    $items = array_merge($items, $newItems);
                }
            }
        }
        return $items;
    }
    
    public function listOrders($lastUpdatedAfter, $lastUpdatedBefore = false, $orderStatus = false)
    {
        if (!$lastUpdatedAfter && !$lastUpdatedBefore) {
            return false;
        }
        
        $orders = false;
        if ( ($service = $this->sec->getOrderService()) === false) {
            throw new Exception('No service');
        } else {
            $orders = array();
            
            //sleep(300);
            $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
            $request->setSellerId($this->sec->mwsMerchantID);

            if ($lastUpdatedAfter) {
                $request->setLastUpdatedAfter($lastUpdatedAfter);
            }
            if ($lastUpdatedBefore) {
                $request->setLastUpdatedBefore($lastUpdatedBefore);
            }
            if ($orderStatus) {
                $orderStatusList = new MarketplaceWebServiceOrders_Model_OrderStatusList();
                $orderStatusList->setStatus(!is_array($orderStatus) ? array($orderStatus) : $orderStatus);
                $request->setOrderStatus($orderStatusList);
            }

            $marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
            $marketplaceIdList->setId(array($this->sec->mwsMarketplaceID));
            $request->setMarketplaceId($marketplaceIdList);

            $orders = $this->_invokeListOrders($this->sec, $service, $request);
        }
        return $orders;
    }
    
    private function _invokeListOrders($sec, MarketplaceWebServiceOrders_Interface $service, $request, $retry = 0) 
    {
        $orders = array();
        
        try {
            //App::debug($request);
            $response = $service->listOrders($request);
            //App::debug($response);
            
            $nextToken = false;
            $hasNext = false;
            
            if ($response->isSetListOrdersResult()) { 
                $listOrdersResult = $response->getListOrdersResult();
                if ($listOrdersResult->isSetNextToken()) {
                    $nextToken = $listOrdersResult->getNextToken();
                }
                if ($listOrdersResult->isSetOrders()) { 
                    $orderList = $listOrdersResult->getOrders()->getOrder();
                    foreach ($orderList as $order) {
                        $o = $this->_parseAmazonOrder($order);
                        if ($o) {
                            $orders[] = $o;
                        }
                    }
                } 
            } 
            
            if ($nextToken !== false) { 
                $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
                $request->setSellerId($sec->mwsMerchantID);
                $request->setNextToken($nextToken);
                if ( ($nextOrders = $this->_invokeListOrdersByNextToken($sec, $service, $request)) !== false) {
                    $orders = array_merge($orders, $nextOrders);
                }
            }
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            App::ex($ex, 'getListOrders');
            $orders = false;
            if ($retry < 3) {
                sleep(60);
                $orders = $this->_invokeListOrders($sec, $service, $request, $retry+1);
            }
        }
        
        return $orders;
    }
    
    function _invokeListOrdersByNextToken($sec, MarketplaceWebServiceOrders_Interface $service, $request, $retry = 0) 
    {
        $orders = array();
        
        try {
            $nextToken = false;
            
            //App::debug($request);
            $response = $service->listOrdersByNextToken($request);
            //App::debug($response);
            
            if ($response->isSetListOrdersByNextTokenResult()) { 
                $listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();
                if ($listOrdersByNextTokenResult->isSetNextToken()) {
                    $nextToken = $listOrdersByNextTokenResult->getNextToken();
                }
                if ($listOrdersByNextTokenResult->isSetOrders()) { 
                    $orderList = $listOrdersByNextTokenResult->getOrders()->getOrder();
                    foreach ($orderList as $order) 
                        $o = $this->_parseAmazonOrder($order);
                    if ($o) {
                        $orders[] = $o;
                    }
                }
            } 
            
            if ($nextToken !== false) { 
                sleep(60);
                $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
                $request->setSellerId($sec->mwsMerchantID);
                $request->setNextToken($nextToken);
                if ( ($nextOrders = $this->_invokeListOrdersByNextToken($sec, $service, $request)) !== false) {
                    $orders = array_merge($orders, $nextOrders);
                }
            }
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            App::ex($ex, 'getListOrdersByNextToken');
            $orders = false;
            if ($retry < 3) {
                sleep(60);
                $orders = $this->_invokeListOrdersByNextToken($sec, $service, $request, $retry+1);
            }
        }
        
        return $orders;
    }
    
    function _invokeListOrderItems($sec, MarketplaceWebServiceOrders_Interface $service, $request, $retry = 0) 
    {
        $orderItems = false;
        
        try {
            //App::debug($request);
            $response = $service->listOrderItems($request);
            //App::debug($response);
            
            $nextToken = false;
            
            $orderItems = array();
            
            if ($response->isSetListOrderItemsResult()) { 
                $listOrderItemsResult = $response->getListOrderItemsResult();
                if ($listOrderItemsResult->isSetNextToken()) {
                    $nextToken = $listOrderItemsResult->getNextToken();
                }
                $orderId = false;
                if ($listOrderItemsResult->isSetAmazonOrderId()) {
                    $orderId = $listOrderItemsResult->getAmazonOrderId();
                }
                if ($listOrderItemsResult->isSetOrderItems()) { 
                    $orderItemList = $listOrderItemsResult->getOrderItems()->getOrderItem();
                    
                    foreach ($orderItemList as $orderItem) {
                        $o = $this->_parseAmazonOrderItem($orderItem);
                        if ($o) {
                            $o->orderId = $orderId;
                            $orderItems[] = $o;
                        }
                    }
                } 
            } 
            
            if ($nextToken !== false) {
                $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest();
                $request->setSellerId($sec->mwsMerchantID);
                $request->setNextToken($nextToken);

                if ( ($nextOrderItems = $this->_invokeListOrderItemsByNextToken($sec, $service, $request, $orderItems)) !== false) {
                    $orderItems = array_merge($nextOrderItems, $orderItems);
                }
            }
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            App::ex($ex, "invokeGetOrderItemList");
            $orderItems = false;
            if ($retry < 3) {
                sleep(4);
                $orderItems = $this->_invokeListOrderItems($sec, $service, $request);
            }
        }
        
        return $orderItems;
    }
    
    function invokeListOrderItemsByNextToken($sec, MarketplaceWebServiceOrders_Interface $service, $request, $orderItems, $retry = 0) 
    {
        $orderItems = array();
        
        try {
            sleep(4);
            
            //App::debug($request);
            $response = $service->listOrderItemsByNextToken($request);
            //App::debug($response);
            
            $nextToken = false;
            
            if ($response->isSetListOrderItemsByNextTokenResult()) { 
                $listOrderItemsByNextTokenResult = $response->getListOrderItemsByNextTokenResult();
                if ($listOrderItemsByNextTokenResult->isSetNextToken()) {
                    $nextToken = $listOrderItemsByNextTokenResult->getNextToken();
                }
                if ($listOrderItemsByNextTokenResult->isSetAmazonOrderId()) {
                    $orderId = $listOrderItemsByNextTokenResult->getAmazonOrderId();
                }
                
                if ($listOrderItemsByNextTokenResult->isSetOrderItems()) { 
                    $orderItemList = $listOrderItemsByNextTokenResult->getOrderItems()->getOrderItem();
                    foreach ($orderItemList as $orderItem) {
                        $o = $this->_parseAmazonOrderItem($orderItem);
                        if ($o) {
                            $o->orderId = $orderId;
                            $orderItems[] = $o;
                        }
                    }
                } 
            } 
        } catch (MarketplaceWebServiceOrders_Exception $ex) {
            App::exception($ex, 'invokeGetOrderItemsByNextToken');
            $orderItems = false;
            if ($retry < 3) {
                sleep(4);
                $orderItems = $this->_invokeListOrdersByNextToken($sec, $service, $request, $retry+1);
            }
        }
        
        return $orderItems;
    }
    
    function _parseAmazonOrder($order)
    {
        $o = new AmazonOrder();
        
        if ($order->isSetAmazonOrderId()) {
            $o->orderId = $order->getAmazonOrderId();
        }
        if ($order->isSetPurchaseDate()) {
            $o->purchaseDate = $order->getPurchaseDate();
        }
        if ($order->isSetOrderStatus()) {
            $o->orderStatus = $order->getOrderStatus();
        }
        if ($order->isSetShipServiceLevel()) {
            $o->shipServiceLevel = $order->getShipServiceLevel();
        }
        if ($order->isSetSalesChannel()) {
            $o->salesChannel = $order->getSalesChannel();
        }
        if ($order->isSetShippingAddress()) { 
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress->isSetName()) {
                $o->shipToName = $shippingAddress->getName();
            }
            if ($shippingAddress->isSetAddressLine1()) {
                $o->shipToAddressLine1 = $shippingAddress->getAddressLine1();
            }
            if ($shippingAddress->isSetAddressLine2()) {
                $o->shipToAddressLine2 = $shippingAddress->getAddressLine2();
            }
            if ($shippingAddress->isSetAddressLine3()) {
                $o->shipToAddressLine3 = $shippingAddress->getAddressLine3();
            }
            if ($shippingAddress->isSetCity()) {
                $o->shipToCity = $shippingAddress->getCity();
            }
            if ($shippingAddress->isSetCounty()) {
                $o->shipToCounty = $shippingAddress->getCounty();
            }
            if ($shippingAddress->isSetDistrict()) {
                $o->shipToDistrict = $shippingAddress->getDistrict();
            }
            if ($shippingAddress->isSetStateOrRegion()) {
                $o->shipToStateOrRegion = $shippingAddress->getStateOrRegion();
            }
            if ($shippingAddress->isSetPostalCode()) {
                $o->shipToPostalCode = $shippingAddress->getPostalCode();
            }
            if ($shippingAddress->isSetCountryCode()) {
                $o->shipToCountryCode = $shippingAddress->getCountryCode();
            }
            if ($shippingAddress->isSetPhone()) {
                $o->shipToPhone = $shippingAddress->getPhone();
            }
        } 
        
        if ($order->isSetOrderTotal()) { 
            $orderTotal = $order->getOrderTotal();
            if ($orderTotal->isSetCurrencyCode()) {
                $o->orderTotalCurrency = $orderTotal->getCurrencyCode();
            }
            if ($orderTotal->isSetAmount()) {
                $o->orderTotalAmount = $orderTotal->getAmount();
            }
        } 
        if ($order->isSetNumberOfItemsShipped()) {
            $o->numberOfItemsShipped = $order->getNumberOfItemsShipped();
        }
        if ($order->isSetBuyerEmail()) {
            $o->buyerEmail = $order->getBuyerEmail();
        }
        if ($order->isSetBuyerName()) {
            $o->buyerName = $order->getBuyerName();
        }
        if ($order->isSetShipmentServiceLevelCategory()) {
            $o->shipmentServiceLevelCategory = $order->getShipmentServiceLevelCategory();
        }
        
        return $o;
    }
    
    private function _parseAmazonOrderItem($orderItem)
    {
        $o = new AmazonOrderItem();
        $o->orderId = $orderId;
        
        if ($orderItem->isSetASIN()) {
            $o->asin = $orderItem->getASIN();
        }
        if ($orderItem->isSetSellerSKU()) {
            $o->sku = $orderItem->getSellerSKU();
        }
        if ($orderItem->isSetOrderItemId()) {
            $o->orderItemId = $orderItem->getOrderItemId();
        }
        if ($orderItem->isSetTitle()) {
            $o->title = $orderItem->getTitle();
        }
        if ($orderItem->isSetQuantityOrdered()) {
            $o->quantityOrdered = $orderItem->getQuantityOrdered();
        }
        if ($orderItem->isSetQuantityShipped()) {
            $o->quantityShipped = $orderItem->getQuantityShipped();
        }
        if ($orderItem->isSetItemPrice()) { 
            $itemPrice = $orderItem->getItemPrice();
            if ($itemPrice->isSetCurrencyCode()) {
                $o->itemPriceCurrency = $itemPrice->getCurrencyCode();
            }
            if ($itemPrice->isSetAmount()) {
                $o->itemPriceAmount = $itemPrice->getAmount();
            }
        } 
        if ($orderItem->isSetShippingPrice()) { 
            $shippingPrice = $orderItem->getShippingPrice();
            if ($shippingPrice->isSetCurrencyCode()) {
                $o->shippingPriceCurrency = $shippingPrice->getCurrencyCode() ;
            }
            if ($shippingPrice->isSetAmount()) {
                $o->shippingPriceAmount = $shippingPrice->getAmount() ;
            }
        } 
        if ($orderItem->isSetGiftWrapPrice()) { 
            $giftWrapPrice = $orderItem->getGiftWrapPrice();
            if ($giftWrapPrice->isSetCurrencyCode()) {
                $o->giftWrapCurrency = $giftWrapPrice->getCurrencyCode();
            }
            if ($giftWrapPrice->isSetAmount()) {
                $o->giftWrapAmount = $giftWrapPrice->getAmount();
            }
        } 
        if ($orderItem->isSetItemTax()) { 
            $itemTax = $orderItem->getItemTax();
            if ($itemTax->isSetCurrencyCode()) {
                $o->itemTaxCurrency = $itemTax->getCurrencyCode();
            }
            if ($itemTax->isSetAmount()) {
                $o->itemTaxAmount = $itemTax->getAmount();
            }
        } 
        if ($orderItem->isSetShippingTax()) { 
            $shippingTax = $orderItem->getShippingTax();
            if ($shippingTax->isSetCurrencyCode()) {
                $o->shippingTaxCurrency = $shippingTax->getCurrencyCode();
            }
            if ($shippingTax->isSetAmount()) {
                $o->shippingTaxAmount = $shippingTax->getAmount();
            }
        } 
        if ($orderItem->isSetGiftWrapTax()) { 
            $giftWrapTax = $orderItem->getGiftWrapTax();
            if ($giftWrapTax->isSetCurrencyCode()) {
                $o->giftWrapCurrency = $giftWrapTax->getCurrencyCode();
            }
            if ($giftWrapTax->isSetAmount()) {
                $o->giftWrapAmount = $giftWrapTax->getAmount();
            }
        } 
        if ($orderItem->isSetShippingDiscount()) { 
            $shippingDiscount = $orderItem->getShippingDiscount();
            if ($shippingDiscount->isSetCurrencyCode()) {
                $o->shippingDiscountCurrency = $shippingDiscount->getCurrencyCode();
            }
            if ($shippingDiscount->isSetAmount()) {
                $o->shippingDiscountAmount = $shippingDiscount->getAmount();
            }
        } 
        if ($orderItem->isSetPromotionDiscount()) { 
            $promotionDiscount = $orderItem->getPromotionDiscount();
            if ($promotionDiscount->isSetCurrencyCode()) {
                $o->promotionDiscountCurrency = $promotionDiscount->getCurrencyCode();
            }
            if ($promotionDiscount->isSetAmount()) {
                $o->promotionDiscountCurrency = $promotionDiscount->getAmount();
            }
        } 
        if ($orderItem->isSetPromotionIds()) { 
            $promotionIds = $orderItem->getPromotionIds();
            $promotionIdList  =  $promotionIds->getPromotionId();
            $o->promotionIds = $promotionIdList;
        } 
        if ($orderItem->isSetCODFee()) { 
            $CODFee = $orderItem->getCODFee();
            if ($CODFee->isSetCurrencyCode()) {
                $o->codFeeCurrency = $CODFee->getCurrencyCode() ;
            }
            if ($CODFee->isSetAmount()) {
                $o->codFeeAmount = $CODFee->getAmount();
            }
        } 
        if ($orderItem->isSetCODFeeDiscount()) { 
            $CODFeeDiscount = $orderItem->getCODFeeDiscount();
            if ($CODFeeDiscount->isSetCurrencyCode()) {
                $o->codFeeDiscountCurrency = $CODFeeDiscount->getCurrencyCode() ;
            }
            if ($CODFeeDiscount->isSetAmount()) {
                $o->codFeeDiscountAmount = $CODFeeDiscount->getAmount() ;
            }
        } 
        if ($orderItem->isSetGiftMessageText()) {
            $o->giftMessageText = $orderItem->getGiftMessageText();
        }
        if ($orderItem->isSetGiftWrapLevel()) {
            $o->giftWrapLevel = $orderItem->getGiftWrapLevel();
        }
        
        return $o;
    }
    
}
