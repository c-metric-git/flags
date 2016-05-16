<?php
ob_start(); 
define(BASE_PATH,"/home/stripedsocks/beta/");
require_once BASE_PATH.'cron/ca_config.php';     
$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::app();
ini_set('memory_limit','256M');   

Mage::app()->setCurrentStore(FLStoreId);
$store = Mage::app()->getStore();
            
//for debug
function showNiceXML($xml){
    return htmlspecialchars( str_replace("<", "\n<", $xml) );
}
    //setup for the api calls
    //url to the wsdl describing the service
    $urlToWsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/v7/OrderService.asmx?WSDL";
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
        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date = date("Y-m-d"); // End Date
        $end_date = new DateTime($end_date, new DateTimeZone('UTC')); 
        $page_number=1;
        $page_size = 50;  
        $counter=1;
        $OrderCriteria = array(
                       /*'OrderCreationFilterBeginTimeGMT'=> $start_date,
                       'OrderCreationFilterEndTimeGMT'=> $end_date,   */
                       'StatusUpdateFilterBeginTimeGMT' => $start_date->date,
                       'StatusUpdateFilterEndTimeGMT' => $end_date->date,
                       'DetailLevel' => 'Complete',
                       /*'ExportState' => 'NotExported',  */
                       'OrderStateFilter' => 'Active',
                       'PaymentStatusFilter' => 'Cleared',
                       'CheckoutStatusFilter'  => 'Completed',
                       'ShippingStatusFilter'  =>'Unshipped',  //Unshipped
                       'PageNumberFilter'=>$page_number,
                       'PageSize'=>$page_size
                    );
        //Here date1 and date2 variable specify date range for orders. After this call function of API to get order data.
        $arrData = array(
                       'accountID'=>CAAccountID,
                       'orderCriteria'=>$OrderCriteria  
              );  
        $result=$client->GetOrderList($arrData);    
        $total_orders_count = isset($result->GetOrderListResult->ResultData->OrderResponseItem->NumberOfMatches)?$result->GetOrderListResult->ResultData->OrderResponseItem->NumberOfMatches:$result->GetOrderListResult->ResultData->OrderResponseItem[0]->NumberOfMatches;
        $page_count = ceil($total_orders_count/$page_size);
        $count_of_orders = count($result->GetOrderListResult->ResultData->OrderResponseItem);
        if(isset($result->GetOrderListResult->ResultData->OrderResponseItem) && $count_of_orders > 0) {
            if($count_of_orders == 1) {
                 insertOrder($result->GetOrderListResult->ResultData->OrderResponseItem);
            }
            else {    
                foreach($result->GetOrderListResult->ResultData->OrderResponseItem as $key => $order_details) {
                    insertOrder($order_details);
                }
            }       
        }
        else {
            echo "<br />No more orders to import into magento";
        }
        while($counter<$page_count) {    
                $page_number++;
                $OrderCriteria = array(
                           /*'OrderCreationFilterBeginTimeGMT'=> $start_date,
                           'OrderCreationFilterEndTimeGMT'=> $end_date,    */
                           'StatusUpdateFilterBeginTimeGMT' => $start_date->date,
                           'StatusUpdateFilterEndTimeGMT' => $end_date->date,
                           'DetailLevel' => 'Complete',
                           'ExportState' => 'NotExported',
                           'OrderStateFilter' => 'Active',
                           'PaymentStatusFilter' => 'Cleared',
                           'CheckoutStatusFilter'  => 'Completed',
                           'ShippingStatusFilter'  =>'Unshipped',
                           'PageNumberFilter'=>$page_number,
                           'PageSize'=>$page_size
                        ); 
                //Here date1 and date2 variable specify date range for orders. After this call function of API to get order data.
                $arrData = array(
                               'accountID'=>CAAccountID,
                               'orderCriteria'=>$OrderCriteria  
                      );
                $result=$client->GetOrderList($arrData);  
                $count_of_orders = count($result->GetOrderListResult->ResultData->OrderResponseItem); 
                if(isset($result->GetOrderListResult->ResultData->OrderResponseItem) && $count_of_orders > 0) {
                    if($count_of_orders == 1) {
                         insertOrder($result->GetOrderListResult->ResultData->OrderResponseItem);
                    }
                    else {    
                        foreach($result->GetOrderListResult->ResultData->OrderResponseItem as $key => $order_details) {
                            insertOrder($order_details);
                        }
                    }       
                }
                else {
                    echo "<br />Got here.No more orders to import into magento";
                }
                $counter++;
        } 
        //the request object and actual request XML
        //print_r( $header );
        /*echo '<pre>';
        print_r( "\n\n\n".showNiceXML($client->__getLastRequest())."\n\n\n" );
        */
        //the result object and the actual XML that we got
        // print_r( "\n\n\n".showNiceXML($client->__getLastResponse())."\n\n\n" );

        //$result = $client->__soapCall("Ping", array("Ping" => array()), NULL, $oHeader);
 } catch (SOAPFault $e) {
    echo "ERROR:".$e->faultstring."\n\n";
    //echo "Request :\n".showNiceXML( $client->__getLastRequest() )."\n\n";
    //echo "Response:\n".showNiceXML( $client->__getLastResponse() )."\n\n";
}
exit;
?>

<?php

function insertOrder($order_details) { 
    global $store;
    $channel_advisor_orderid = $order_details->OrderID;
    //$insert=1;
    $order_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('channel_advisor_orderid', $channel_advisor_orderid);
    if ($order_collection->count()==0) {
            $error_order=0; 
            echo "<br />Processing Order : ".$channel_advisor_orderid; 
            $email = $order_details->BuyerEmailAddress;  
            $productids=array();
             // Start New Sales Order Quote
            $quote = Mage::getModel('sales/quote')->setStoreId(FLStoreId);
             // Set Sales Order Quote Currency
            $quote->setCurrency($order->AdjustmentAmount->currencyID);
            $quote->setIsSuperMode(true);
                  
             $shipping_cost=0;
             if(isset($order_details->ShoppingCart->LineItemSKUList)) {
                 foreach($order_details->ShoppingCart->LineItemSKUList as $item_details){   
                        $product=Mage::getModel('catalog/product')->loadByAttribute('sku',$item_details->SKU);
                        if($product!='') {
                            $quoteItem = Mage::getModel('sales/quote_item')->setProduct($product);
                            $quoteItem->setQuote($quote);
                            $quoteItem->setQty((int) $item_details->Quantity);
                            $quoteItem->setLineItemId($item_details->LineItemID);
                            $quoteItem->setStoreId(FLStoreId);
                            $quote->addItem($quoteItem);
                            //$quote->setLineItemID($item_details->LineItemID);
                        }
                        else { 
                            echo $message = "     Channel Advisor Order : $channel_advisor_orderid Product not found on magento :".$item_details->SKU;
                            mail("dhiraj@clownantics.com","Channel Advisor Order : $channel_advisor_orderid Error",$message);
                            $error_order=1;
                        }    
                     $shipping_cost += $item_details->ShippingCost;
                 }
             }
             
             if($error_order==0) {
                 $customer = Mage::getModel('customer/customer')
                             ->setWebsiteId(FLWebsiteId)
                             ->loadByEmail($email);
                 if($customer->getId()==""){
                     $customer = Mage::getModel('customer/customer');
                     $customer->setWebsiteId($websiteId)
                             ->setStore($store)
                             ->setFirstname($order_details->BillingInfo->FirstName)
                             ->setLastname($order_details->BillingInfo->LastName)
                             ->setEmail($email)
                             ->setPassword("password");
                     $customer->save();
                 }
                 
                 // Assign Customer To Sales Order Quote
                 $quote->assignCustomer($customer);
                 $quote->setChannelAdvisorOrderid($channel_advisor_orderid);
                 $quote->setCustomerNote("Channel Advisor Order id : ".$channel_advisor_orderid);
                     // Configure Notification
                // $quote->setSendCconfirmation(1);
            
                 // Set Sales Order Billing Address
                 $billingAddress = $quote->getBillingAddress()->addData(array(
                     'customer_address_id' => '',
                     'prefix' => '',
                     'firstname' => $order_details->BillingInfo->FirstName,
                     'middlename' => '',
                     'lastname' =>$order_details->BillingInfo->LastName,
                     'suffix' => $order_details->BillingInfo->Suffix,
                     'company' =>$order_details->BillingInfo->CompanyName, 
                     'street' => array(
                             '0' => $order_details->BillingInfo->AddressLine1,
                             '1' => $order_details->BillingInfo->AddressLine2
                         ),
                     'city' => $order_details->BillingInfo->City,
                     'country_id' => $order_details->BillingInfo->CountryCode,
                     'region' => $order_details->BillingInfo->Region,
                     'postcode' => $order_details->BillingInfo->PostalCode,
                     'telephone' => $order_details->BillingInfo->PhoneNumberDay,
                     'fax' => '',
                     'vat_id' => '',
                     'save_in_address_book' => 1
                 ));
                 
                 // Set Sales Order Shipping Address
                $shippingAddress = $quote->getShippingAddress()->addData(array(
                     'customer_address_id' => '',
                     'prefix' => '',
                     'firstname' => $order_details->ShippingInfo->FirstName,
                     'middlename' => '',
                     'lastname' =>$order_details->ShippingInfo->LastName,
                     'suffix' => $order_details->ShippingInfo->Suffix,
                     'company' =>'', 
                     'street' => array(
                             '0' => $order_details->ShippingInfo->AddressLine1,
                             '1' => $order_details->ShippingInfo->AddressLine2
                         ),
                     'city' => $order_details->ShippingInfo->City,
                     'country_id' => $order_details->ShippingInfo->CountryCode,
                     'region' => $order_details->ShippingInfo->Region,
                     'postcode' => $order_details->ShippingInfo->PostalCode,
                     'telephone' => $order_details->ShippingInfo->PhoneNumberDay,
                     'fax' => '',
                     'vat_id' => '',
                     'save_in_address_book' => 1
                 ));
                 if($shipping_cost==0){
                     $shipmethod='freeshipping_freeshipping';
                 }
                 else {
                     $shipmethod='usps_0_FCP';
                 }    
                 
                 // Collect Rates and Set Shipping & Payment Method
                 $shippingAddress->setCollectShippingRates(true)
                                 ->collectShippingRates()
                                 ->setShippingMethod($shipmethod)
                                 ->setPaymentMethod('checkmo');
                 
                 // Set Sales Order Payment
                 $quote->getPayment()->importData(array('method' => 'checkmo'));
                 
                 // Collect Totals & Save Quote
                 $quote->collectTotals()->save();
                 
                 // Create Order From Quote
                 $service = Mage::getModel('sales/service_quote', $quote);
                 $service->submitAll();
                 //Setup order object and gather newly entered order
                 $order = $service->getOrder();
             
                //Now set newly entered order's status to complete so customers can enjoy their goods. 
                    //(optional of course, but most would like their orders created this way to be set to complete automagicly)
                 $order->setStatus('processing');
             
                //Finally we save our order after setting it's status to complete.
                 $order->save();     
                 $increment_id = $service->getOrder()->getRealOrderId();
                 
                 // Resource Clean-Up
                 $quote = $customer = $service = null;
                 
                 // Finished
                 echo "        Order Created Successfully =". $increment_id;
           }       
    }
    else {
        echo "<br />Order : ".$channel_advisor_orderid; 
    }    
}    
ob_end_flush(); 
?>