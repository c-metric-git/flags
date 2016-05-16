<?php
ob_start(); 
define(BASE_PATH,"/home/stripedsocks/beta/");
require_once BASE_PATH.'cron/ca_config.php'; 
$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::app();
ini_set('memory_limit','256M');   

$websiteId = 2;//Mage::app()->getWebsite()->getId();
$storeId = 2;//Mage::app()->getStore();      
Mage::app()->setCurrentStore($storeId);
$store = Mage::app()->getStore();
            
//for debug
function showNiceXML($xml){
    return htmlspecialchars( str_replace("<", "\n<", $xml) );
}
    //setup for the api calls
    //url to the wsdl describing the service
    $urlToWsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/V7/FulfillmentService.asmx?WSDL";
    try {
        //create SOAP object that will let us send calls, we need to pass the url of the WSDL
        $client = new SoapClient($urlToWsdl, array("trace" => 1, "exception" => 0) );
        $headersData = array('DeveloperKey' => DeveloperKey, 'Password' => CAPassword);
        $head = new SoapHeader("http://api.channeladvisor.com/webservices/","APICredentials",$headersData);
        $client->__setSoapHeaders($head);
        //To get necessary order details, we need to set order criteria. Following is the example of order criteria.
        $no_of_days=7;
        $seconds_calc = $no_of_days * (24*60*60);  
        $start_date = date("Y-m-d",time() - $seconds_calc);  
        $end_date = date("Y-m-d"); // End Date
        $page_number=1;
        $page_size = 50;  
        $counter=1;
        
        $orderCollection = Mage::getModel('sales/order')->getCollection()
                 ->addFieldToFilter('channel_advisor_orderid',array('neq' => '')) 
                 ->addFieldToFilter('channel_advisor_tracking_updated',"No")
                 ->addFieldToFilter('status',"complete")
                 ->addFieldToSelect('*');
        if(isset($orderCollection) && $orderCollection->count()>0) {      
            foreach($orderCollection as $order){
                $increment_id = $order['increment_id'];
                foreach($order->getShipmentsCollection() as $shipment)
                {
                    $tracking_number='';
                    $carrier_code='';
                    $shipping_method='';
                    $ship_date='';
                    foreach($shipment->getAllTracks() as $tracknum) {
                          $tracking_number = $tracknum->getNumber();
                          $carrier_code = $tracknum->getCarrierCode(); 
                          $shipping_method = $tracknum->getTitle();
                          $ship_date = $tracknum->getCreatedAt();
                          $ship_date = new DateTime($ship_date, new DateTimeZone('UTC'));     
                    }
                    foreach($shipment->getAllItems() as $itemdet) {
                         $qty_shipped[$itemdet['sku']] = $itemdet['qty'];
                    }    
                }
                //Here date1 and date2 variable specify date range for orders. After this call function of API to get order data.
                $Orderid = $order->channel_advisor_orderid;
                $arrData = array(
                               'accountID'=>CAAccountID,
                               'orderIDList'=>array($Orderid)
                      );
                $result=$client->GetOrderFulfillmentDetailList($arrData); 
                if($result->GetOrderFulfillmentDetailListResult->Status == 'Success') {
                    foreach($result->GetOrderFulfillmentDetailListResult->ResultData->OrderFulfillmentResponse->FulfillmentList->FulfillmentDetailResponse->ItemList as $item_detail_list) {
                        $item_shipping_sku = $item_detail_list->SKU;
                        $itemlist = array(
                                    'FulfillmentItemSubmit' => array(
                                        'FulfillmentItemID'=>$item_detail_list->FulfillmentItemID,
                                        'Quantity'=>$qty_shipped[$item_shipping_sku]
                                        ));
                    }       
                    $fulfillmentList[0] = array(
                                   'FulfillmentType'=> 'Ship',
                                   'FulfillmentStatus'=> 'Complete',
                                   'CarrierCode'=>$carrier_code,
                                   'ClassCode'=>$shipping_method,
                                   'TrackingNumber'=>$tracking_number,
                                   'DistributionCenterCode'=>'New York',
                                   'ShippedDateGMT'=>$ship_date->date,
                                   'ItemList'=>$itemlist    
                                   );
                                   
                    $arrData = array(
                                   'accountID'=>CAAccountID,
                                   'orderID'=>$Orderid,
                                   'fulfillmentList'=>$fulfillmentList 
                          );
                     $result=$client->CreateOrderFulfillments($arrData);  
                     if($result->CreateOrderFulfillmentsResult->Status == 'Success') {   
                           $orderModel = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
                           $orderModel->setChannelAdvisorTrackingUpdated("Yes")
                                ->save();
                           echo "<br /> Tracking info updated for the order : ".$increment_id;
                     }
                     else {
                         echo "<br />Error updating tracking info for channel advisor order id : ".$Orderid; 
                     }    
                }     
            }  
        }
        else {
             echo "<br />No more orders to update tracking info on channel advisor";
        }             
 } catch (SOAPFault $e) {
    echo "ERROR:".$e->faultstring."\n\n";
    //echo "Request :\n".showNiceXML( $client->__getLastRequest() )."\n\n";
    //echo "Response:\n".showNiceXML( $client->__getLastResponse() )."\n\n";
}
exit;
ob_end_flush(); 
?>