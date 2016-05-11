<?php
define(BASE_PATH,"/home/stripedsocks/beta/");
$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::app();
ini_set('memory_limit','256M');

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
        $developerKey = 'ef67593b-9813-432b-a25d-f6a9c9bb9387';
        $password = 'Clown@123';                                           
        $headersData = array('DeveloperKey' => $developerKey, 'Password' => $password);
        $head = new SoapHeader("http://api.channeladvisor.com/webservices/","APICredentials",$headersData);
        $client->__setSoapHeaders($head);
        
        //To get necessary order details, we need to set order criteria. Following is the example of order criteria.
        $no_of_days=7;
        $seconds_calc = $no_of_days * (24*60*60);  
        $start_date = date("Y-m-d",time() - $seconds_calc);  
        $end_date = date("Y-m-d"); // End Date
        $accountID = '96133f54-ded0-4134-a2fd-d1ec9bfa88e2';
        $OrderCriteria = array(
                       'OrderCreationFilterBeginTimeGMT'=> $start_date,
                       'OrderCreationFilterEndTimeGMT'=> $end_date,
                       'StatusUpdateFilterBeginTimeGMT' => $start_date,
                       'StatusUpdateFilterEndTimeGMT' => $end_date,
                       'DetailLevel' => 'Complete',
                       'ExportState' => 'NotExported',
                       'OrderStateFilter' => 'Active',
                       'PaymentStatusFilter' => 'Cleared',
                       'CheckoutStatusFilter'  => 'Completed',
                       'ShippingStatusFilter'  =>'Shipped',
                       'PageNumberFilter'=>1,
                       'PageSize'=>30
                    );

        //Here date1 and date2 variable specify date range for orders. After this call function of API to get order data.

        $arrData = array(
                       'accountID'=>$accountID,
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
            echo "<br />No more orders to import into magento";
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
    echo "ERROR:".$f->faultstring."\n\n";
    echo "Request :\n".showNiceXML( $client->__getLastRequest() )."\n\n";
    echo "Response:\n".showNiceXML( $client->__getLastResponse() )."\n\n";
}
exit;
?>

<?php

function insertOrder($order_details) {         
    $channel_advisor_orderid = $order_details->ClientOrderIdentifier;
    $order_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('channel_advisor_orderid', $channel_advisor_orderid);
    if ($order_collection->count()==0) {
            //echo "<br />Processing Order : ".$channel_advisor_orderid; 
            $email = $order_details->BuyerEmailAddress;  
            $productids=array();
            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();  
             // Start New Sales Order Quote
            $quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
             // Set Sales Order Quote Currency
             $quote->setCurrency($order->AdjustmentAmount->currencyID);
             $customer = Mage::getModel('customer/customer')
                         ->setWebsiteId($websiteId)
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
             $quote->setIsSuperMode(true);
             $quote->setChannelAdvisorOrderid($channel_advisor_orderid);
             $quote->setCustomerNote("Channel Advisor Order id : ".$channel_advisor_orderid);
             /*echo '<pre>';
             print_R($quote);
             exit;       */
             //$quote->channel_advisor_orderid = $channel_advisor_orderid; 
                 // Configure Notification
            // $quote->setSendCconfirmation(1);
             $shipping_cost=0;
             if(isset($order_details->ShoppingCart->LineItemSKUList)) {
                 foreach($order_details->ShoppingCart->LineItemSKUList as $item_details){
                     $product=Mage::getModel('catalog/product')->loadByAttribute('sku',$item_details->SKU);
                     $quote->addProduct($product,new Varien_Object(array('qty'   => (int) $item_details->Quantity)));
                     $shipping_cost += $item_details->ShippingCost;
                 }
             }
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
                 $shipmethod='freeshipping_freeshipping';
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
             $increment_id = $service->getOrder()->getRealOrderId();
             
             // Resource Clean-Up
             $quote = $customer = $service = null;
             
             // Finished
             echo "order created successfully =". $increment_id;
             exit;
    }
}    
/*Mage::app();
Mage::setIsDeveloperMode(true);
umask(0);
Mage::register('isSecureArea', 1);
set_time_limit(0);     */

/***************** UTILITY FUNCTIONS ********************/
/*function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}
 
function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
} */



?>