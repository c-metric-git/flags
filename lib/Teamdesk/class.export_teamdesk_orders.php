<?php                      
/**                    
* @desc This class has functions to export the order details from Magento to TeamDesk. 
* @package  TeamDesk  
* @author Dinesh Nagdev
* @since 29-July-2015                      
*/
class TeamDeskOrder {
    private $db;
    private $arrTDFields;
    private $arrTDOrderSchema;
    private $arrOrdDetailTDFields;
    private $arrSpclOrdTDFields;
    private $arrErrorLog;    
    private $totalOrderAdded;
    private $totalOrderUpdateFromTD;
    public $orderID;
    private $orderset; 
    private $orderlineitemdataset;
    private $tdErrorLog;    
    private $arrBulkProductSKU;
    /* 
       CONSTRUCTOR
    */
    function __construct(&$db){    
        $this->db = $db;
        $this->totalOrderAdded = '';
        $this->totalOrderUpdateFromTD = '';
        $this->orderID = 0;
    } 
    /**
    * @desc Function to connect to TeamDesk Orders table
    * @return array
    * @since 12-Feb-2013
    * @author Dinesh Nagdev
    */
    private function connectToTeamDesk()
    {
        /**
        * @desc code to create the teamdesk api object with the domain name and database id.
        */
        try {
            $td_api = new API(TD_DOMAIN,TD_DB_ID,array("trace" => true)); 
            /**
            * @desc code to check the user login and session
            */
            $td_api->login(TD_USERNAME, TD_PASSWORD);  
            $this->api = $td_api;   
        }
        catch (Exception $e) {
            echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
            return $this->tdErrorLog;            
        }            
    } 
    /**
    * @desc Function to export Pinnacle orders and invoice items into 
    * TeamDesk Invoice and Invoice Line Items table respectively
    * @since 12-Feb-2013  
    * @author Dinesh Nagdev
    */
    public function exportOrdersToTeamDesk($channel_advisor='')
    {                               
        $arrOrders = array();
        $this->totalOrderUpdateFromTD = '';
        $this->connectToTeamDesk();
        $where_channel='';
        if ($this->api !='') {     
            if($channel_advisor=='yes') {
                 $where_channel = " AND channel_advisor_orderid!='' ";
            }                                                        
            $orderSQL = "SELECT *, created_at AS order_date, DATE_FORMAT(created_at, '%H:%i') AS order_time                     
                         FROM sales_flat_order 
                         WHERE (status = 'processing' || status = 'pending' || status ='holded' || status ='fraud' || status ='pending_payment' || status ='pending_paypal' || status ='payment_review')
                         AND order_teamdesk_id = 0 $where_channel ORDER BY sales_flat_order.entity_id ASC";
            /** 
            * get list of all the new users registered with the site (pinnacle)   
            */
            $this->db->query($orderSQL);   
            $arrOrders = $this->db->getRecords();   
            $count_of_order = count($arrOrders);
            if($count_of_order > 0) { 
              /**
               * @desc code to get the schema of orders table  from teamdesk
               */
              try {
                 $this->orderset = $this->api->GetSchema("Order",array('Related Contact','OrderNumber','Date','ShippingCharged','TrackingNumber','TotalSmallOrderFee','TotalProductCost','TotalCustomerTax','TotalCost','Special Instructions','Ship To First Name','Ship To Company','Ship To Address 1','Ship To Address 2','Ship To City','Ship To State','Ship To Province','Ship To Zip','Ship To Country','Bill To First Name','Bill To Last Name','Bill To Company','Bill To Address 1','Bill To Address 2','Bill To City','Bill To State','Bill To Zip','Bill To Country','Time','Status','Bill To Province','pinnacleId','Email','Phone','Shipping Method','Authcode','AVSZipMatch','Credit Card Transaction ID','CCType','promo_discount_amount','promo_discount_value','promo_discount_type','Promotion Code - Record ID#','shipping - Shipping Method Code','discount_amount','discount_value','discount_type','TotalTaxEntered','Ship To Last Name','gift_cert_first_name','gift_cert_last_name','gift_cert_voucher','gift_cert_amount','Gift Message','Ship To Zip Plus Four','CVV Match','Estimated Shipping Date','Tracking_info','is_correct_address','Coupon Code','ShipDate','PaymentStatus','sendToPinnacle','RecordHistory','OrderSource','PayPalProMsg','AVSAddressMatch','CC First Name','CC Last Name','IntlAVSMatch','PayPalProReason','AVS Code','isCallOrder?','Case Type','OrderSourceText'));  
              } 
              catch (Exception $e) {
                      echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of order table '.$e->getMessage(). "<br/>";
                      $strReturn .= $this->tdErrorLog;           
              }
              /**
               * @desc code to get the schema of orders line items table  from teamdesk
               */
             try {  
                $this->orderlineitemdataset = $this->api->GetSchema("Invoice Line Item",array('SKU','Quantity','Price','SKU - Weight','Order Number','pinnacleId','free_shipping','PinnacleSku','PinnacleAttributeSku','Related Web Profile','RecordHistory')); 
             }
             catch (Exception $e) {
                      echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of order line item table '.$e->getMessage(). "<br/>";
                      $strReturn .= $this->tdErrorLog;           
             }   
             foreach ($arrOrders as $key => $orderData) {  
                $this->orderset->Rows = array();   
                $handlingAmount = 0;
                $shippingAmount = 0;
                $transaction_id  = '';
                $paymentMethod = '';
                $ship_to_zip_plus_four = '';
                $PayPalProMsg='';
                $AVSAddressMatch='';
                $AVSZipMatch='';   
                $CVVMatch='';  
                $Authcode='';
                $CCFirstName='';   
                $CCLastName='';  
                $IntlAVSMatch='';
                $PayPalProReason='';
                echo "<br /><br />**************************Order Number:".$orderData['increment_id']."***************************************<br /><br />";
                /**
                @desc update the use information in TeamDesk 
                */
                /*$objTeamDeskUser = new TeamDeskUser($this->db);   
                $objTeamDeskUser->userID  = $orderData['customer_id']; 
                if($orderData['customer_id'] > 0) {
                    $recAdded = $objTeamDeskUser->exportUserToTeamDesk();      
                }    */
                /**
                @desc if the customer was not previously sent to TeamDesk
                */
                if ($orderData['customer_id'] > 0) { 
                     $objTeamDeskUser = new TeamDeskUser($this->db);
                     $recAdded = $objTeamDeskUser->exportUserToTeamDesk('No',$orderData['customer_id']);  
                     $orderData['td_user_id'] = $recAdded;
                } 
                else {
                   $orderData['td_user_id'] =NULL;
                } 
                /**
               * @desc code to get the billing and shipping address of user  
               */
               $orderSQL = "SELECT *       
                            FROM sales_flat_order_address      
                            WHERE sales_flat_order_address.parent_id = '".addslashes($orderData['entity_id'])."'"; 
                $this->db->query($orderSQL);
                while($this->db->moveNext()) {
                      if($this->db->col['address_type'] == 'billing') {
                           $billing_address = $this->db->col; 
                      }
                      else {
                           $shipping_address = $this->db->col;
                      }   
                }   
                /*****************end of code added for billing & shipping address *************>
                /**
                * @desc code to fetch the state name based on US country
                */
                if ($billing_address['country_id'] == 'US') { 
                    $billing_address['stateName'] = getStateNameByID($this->db, $billing_address['region_id']); 
                    $billing_address['province'] = '';  
                }
                /**
                * @desc code to fetch the state name based on canada country
                */
                else if ($billing_address['country_id'] == 'CA') { 
                    $billing_address['province'] = getStateNameByID($this->db, $billing_address['region_id']); 
                    $billing_address['stateName'] = '';  
                } else {
                    $billing_address['province'] = $billing_address['region'];
                    $billing_address['stateName'] = '';
                } 
                /**
                * @desc code to fetch the state name based on US country
                */
                if ($shipping_address['country_id'] == 'US') { 
                    $shipping_address['stateName'] = getStateNameByID($this->db, $shipping_address['region_id']);   
                    $shipping_address['province']  = '';
                }
                /**
                * @desc code to fetch the state name based on Canada country
                */ 
                else if ($shipping_address['country_id'] == 'CA') { 
                    $shipping_address['province'] = getStateNameByID($this->db, $shipping_address['region_id']); 
                    $shipping_address['stateName'] = '';  
                } else {
                    $shipping_address['province'] = $shipping_address['region'];
                    $shipping_address['stateName'] = '';  
                }    
                /**
                @desc get the shipping method for the order 
                */ 
                if($orderData['shipping_description'] == 'Standard Shipping - First Class') {
                    //$shippingMethod = "USPS - First Class Parcel";  
                    $shippingMethod =  "UPS - Mail Innovations";
                }
                else if($orderData['shipping_description'] == 'Standard Shipping - Priority') {  
                    $shippingMethod = "USPS - Priority"; 
                }
                else if($orderData['shipping_description'] == 'Standard Shipping - First Class International') {  
                    $shippingMethod = "USPS - Package - First Class Mail International Package";
                }
                else if($orderData['shipping_description'] == 'Standard Shipping - Priority International') {  
                     $shippingMethod = "USPS - Package - Priority Mail International, (6 - 10 Days)";
                }
                else {
                     $shippingMethod = $orderData['shipping_method'];           
                }    
                $orderSQL = "SELECT * FROM sales_flat_order_payment  
                             WHERE parent_id = '".addslashes($orderData['entity_id'])."'"; 
                $this->db->query($orderSQL);     
                while($this->db->moveNext()){  
                    $payment_data = $this->db->col;
                }   
                /**
                * @desc  payment method
                */
                if ($payment_data['method'] == 'paypalipn') {
                    $paymentMethod = 'PayPal';
                }   
                else if ($payment_data['method'] == 'verisign') {
                    if(strtolower($payment_data['cc_type'])=='vi') {
                        $paymentMethod = "VISA";
                    } 
                    else if(strtolower($payment_data['cc_type'])=='mc') {
                        $paymentMethod = "MC";
                    } 
                    else if(strtolower($payment_data['cc_type'])=='di') {
                        $paymentMethod = "DISC";
                    } 
                    else if(strtolower($payment_data['cc_type'])=='ae') {
                        $paymentMethod = "AMEX";
                    }    
                    $transaction_id = $payment_data['last_trans_id'];
                    $payment_additional_information = unserialize($payment_data['additional_information']); 
                    $AVSAddressMatch = $payment_additional_information['paypal_avs_code']=='Y'?'Y':'Y'; //N  
                    $CVVMatch = $payment_additional_information['paypal_cvv2_match']=='M'?'Y':'Y'; //N  
                    $PayPalProMsg = $payment_additional_information['paypal_payment_status']=='completed'?'Completed':'Completed'; //Pending 
                    $PayPalProReason = $payment_additional_information['paypal_pending_reason'];  
                }
                else if ($payment_data['method'] == 'authorizenet') {
                    $payment_additional_information = unserialize($payment_data['additional_information']); 
                    foreach($payment_additional_information['authorize_cards'] as $pay_key => $pay_detail) {
                        if(strtolower($pay_detail['cc_type'])=='vi' || strtolower($pay_detail['cc_type'])=='visa') {
                            $paymentMethod = "VISA";
                        } 
                        else if(strtolower($pay_detail['cc_type'])=='mc' || strtolower($pay_detail['cc_type'])=='master card') {
                            $paymentMethod = "MC";
                        } 
                        else if(strtolower($pay_detail['cc_type'])=='di' || strtolower($pay_detail['cc_type'])=='discover') {
                            $paymentMethod = "DISC";
                        } 
                        else if(strtolower($pay_detail['cc_type'])=='ae' || strtolower($pay_detail['cc_type'])=='american express') {
                            $paymentMethod = "AMEX";
                        }    
                    }   
                    $transaction_id = $payment_additional_information['otherinfo']['trans_id'];
                    $Authcode = $payment_additional_information['otherinfo']['auth_code']; 
                    $AVSAddressMatch = $payment_additional_information['otherinfo']['avs_addmatch']=='Y'?'Y':'N'; //N  
                    $AVSZipMatch = $payment_additional_information['otherinfo']['avs_zipmatch']=='Y'?'Y':'N';
                    $CVVMatch = $payment_additional_information['otherinfo']['cvv_match']=='M'?'Y':'N'; //N  
                    $PayPalProMsg = $payment_additional_information['otherinfo']['response_code']!='Approved'?'Pending':'Completed'; //Pending 
                    $PayPalProReason = $payment_additional_information['otherinfo']['response_code'];
                    $CCFirstName = $payment_additional_information['otherinfo']['first_name'];  
                    $CCLastName = $payment_additional_information['otherinfo']['last_name'];
                }
                else if ($payment_data['method'] == 'paypaluk_express' || $payment_data['method'] == 'paypal_express') {
                    $paymentMethod = 'PayPal Express';  
                    $payment_additional_information = unserialize($payment_data['additional_information']);
                    $transaction_id = $payment_additional_information['paypal_correlation_id'];
                    $Authcode = $payment_data['last_trans_id'];
                    $AVSAddressMatch = $payment_additional_information['paypal_address_status']=='confirmed'?'Y':'N'; 
                    $PayPalProMsg = $payment_additional_information['paypal_payment_status']=='completed'?'Approved':'Pending';  
                }   
                elseif($payment_data['method'] == 'Gift Certificate') {
                    $paymentMethod = 'Gift Certificate';
                }
                else {
                    $paymentMethod = "Other";
                }
                if(strstr($orderData['increment_id'], 'CA')) {
                   $order_source = "CA";   
                }
                elseif(strstr($orderData['increment_id'], 'FP')) {
                   $order_source = "FP";   
                }
                elseif(strstr($orderData['increment_id'], 'HM')) {
                   $order_source = "HM";   
                }
                elseif(strstr($orderData['increment_id'], 'FL')) {
                   $order_source = "FL";   
                }
                elseif(strstr($orderData['increment_id'], 'SS')) {
                   $order_source = "SS";   
                } 
                else {   
                    $order_source = "SS";   
                }
                $order_channel='';
                $ordersourcetext='';
                if($channel_advisor=='yes') {
                    $order_channel_arr = explode("Order id : ",$orderData['customer_note']);     
                    $order_channel = trim($order_channel_arr[0]); 
                    $paymentMethod = $order_channel;  
                    $ordersourcetext = $order_source." ".$paymentMethod; 
                } 
                /**
                * @desc code added by dinesh for checking the order status and assigning the same.
                */
                if($orderData['status'] == 'Canceled') {
                    $order_status = 'Cancelled';
                }
                else {
                    $order_status = 'New';
                }
                if($orderData['status'] == 'processing') {  
                    $payment_status = 'Received';
                }   
                else {
                    $payment_status = 'Pending';
                } 
                $order_date = new DateTime($orderData['order_date']);
                $order_time = strtotime($orderData['order_time']);
                
                // code added buy lav to update the call orders flag
                if(strpos($orderData['customer_note'], '202')!==false){
                    $isCallOrder = 1;
                    $CaseType = "Customer Request: Phone Order";
                }
                else{
                    $isCallOrder = 0;
                    $CaseType = "";
                }
                      
                $arrRecords = array(
                                    'pinnacleId' => $orderData['entity_id'],   
                                    'OrderNumber' =>  $orderData['increment_id'],
                                    'Related Contact' => $orderData['td_user_id'],      
                                    'Date' => $order_date,
                                    'Time' => $order_time,
                                    'Status' => $order_status,  
                                    'Ship To First Name' => ucfirst($shipping_address['firstname']),
                                    'Ship To Last Name' => ucfirst($shipping_address['lastname']),
                                    'Ship To Company' => ucfirst($shipping_address['company']),
                                    'Ship To Address 1' => ucfirst($shipping_address['street']),
                                    'Ship To Address 2' => '',
                                    'Ship To City' => ucfirst($shipping_address['city']),
                                    'Ship To State' => ucfirst($shipping_address['stateName']),
                                    'Ship To Province' => ucfirst($shipping_address['province']),
                                    'Ship To Zip' => ucfirst($shipping_address['postcode']),
                                    'Ship To Zip Plus Four' => '',
                                    'Ship To Country' => ucfirst($shipping_address['country_id']),
                                    'Bill To First Name' => ucfirst($billing_address['firstname']),
                                    'Bill To Last Name' => ucfirst($billing_address['lastname']),
                                    'Bill To Company' => ucfirst($billing_address['company']),
                                    'Bill To Address 1' => ucfirst($billing_address['street']),
                                    'Bill To Address 2' => '',
                                    'Bill To City' => ucfirst($billing_address['city']),
                                    'Bill To State' => ucfirst($billing_address['stateName']),
                                    'Bill To Province' => ucfirst($billing_address['province']),
                                    'Bill To Zip' => ucfirst($billing_address['postcode']),
                                    'Bill To Country' => ucfirst($billing_address['country_id']),
                                    'Email' => $shipping_address['email']!=''?$shipping_address['email']:$billing_address['email'],
                                    'Phone' => $shipping_address['telephone']!=''?$shipping_address['telephone']:$billing_address['telephone'],
                                    'TotalProductCost' => $orderData['subtotal'],
                                    'TotalSmallOrderFee' => (float) "0.00",
                                    'TotalTaxEntered' => $orderData['tax_amount'],
                                    'ShippingCharged' => $orderData['shipping_amount'],      
                                    'TotalCost' => $orderData['grand_total'],                                        
                                    'Special Instructions' => $orderData['customer_note'],                                        
                                    'isCallOrder?' => $isCallOrder,                                        
                                    'Case Type' => $CaseType,                                        
                                    'Shipping Method' => $shippingMethod,   
                                    'shipping - Shipping Method Code' => $shippingMethod,   
                                    'promo_discount_amount' => (float)$orderData['discount_amount'],
                                    'promo_discount_value' => NULL,
                                    'promo_discount_type' => NULL,
                                    'promo_campaign_id' => $orderData['discount_description'],
                                    'Coupon Code' => $orderData['discount_description'],                                        
                                    'discount_amount' => $orderData['discount_description']==''?$orderData['discount_amount']:"0.00",
                                    'discount_value' => NULL,
                                    'discount_type' => NULL,
                                    'gift_cert_first_name' => NULL,                  
                                    'gift_cert_last_name' => NULL,                  
                                    'gift_cert_voucher' => $orderData['gift_card_no'],                  
                                    'gift_cert_amount' => $orderData['gift_card_value'],                  
                                    'Gift Message' => $orderData['gift_message_id'],
                                    'Tracking_info' => NULL, 
                                    'Credit Card Transaction ID' => $transaction_id,                                        
                                    'CCType' => $paymentMethod,   
                                    'PayPalProMsg' => $PayPalProMsg,
                                    'AVSAddressMatch' => $AVSAddressMatch,
                                    'AVSZipMatch' => $AVSZipMatch,   
                                    'CVV Match' => $CVVMatch,  
                                    'Authcode' => $Authcode,
                                    'CC First Name' => $CCFirstName,   
                                    'CC Last Name' => $CCLastName,  
                                    'IntlAVSMatch' => NULL,
                                    'AVSCode' => NULL,
                                    'PayPalProReason' => $PayPalProReason,
                                    'RecordHistory' => date("Y-m-d H:i:s").' - '.'Record added in TeamDesk',
                                    'PaymentStatus' => $payment_status,
                                    'OrderSource' => $order_source, 
                                    'OrderSourceText' => $ordersourcetext,
                                    'sendToPinnacle' =>1
                                );    
                array_push($this->orderset->Rows, $arrRecords);  
                 try {      
                    $order_id = $this->api->Create("Order", $this->orderset); // returns ids of the new record     
                    $order_id = $order_id[0];
                    if ($order_id <= 0) {                   
                        $this->arrErrorLog[] = array("orderID"  => ucwords($orderData['increment_id']),
                                                     "Error_Msg" => "Order Import Error");
                    } else {      
                        $boolOrdDetail = $this->exporOrderDetailsToTeamDesk($orderData['entity_id'], $orderData['increment_id'], $order_id,'');                                   /**
                        * @desc  save the order details
                        */
                        if ($boolOrdDetail) {
                            $this->totalOrderAdded++;
                        }
                        /**
                        * @desc  save the teamdesk record in the orders table
                        */
                        $this->db->reset();
                        $this->db->assignStr("order_teamdesk_id", $order_id);          
                        $this->db->update("sales_flat_order", "WHERE entity_id='".addslashes($orderData['entity_id'])."'");
                    } 

                 }
                 catch (Exception $e) {
                        echo $this->tdErrorLog .= '<br/>Caught exception: Error in order number :'.$orderData['increment_id']." ".$e->getMessage(). "<br/>";  
                        $strReturn .= $this->tdErrorLog;           
                 }
              }    
              /**
                * @desc  Save log
                */
                $strReturn = $this->saveExportLog($this->totalOrderAdded, $this->totalOrderUpdateFromTD);                        
                if ($this->orderID > 0) {
                    return $this->totalOrderAdded;   
                }
                else {    
                    return $strReturn;  
                }

            }  
            else {
                $strReturn = "No orders to export into teamdesk";
                return $strReturn;
            }
        }
        else {
            return $this->tdErrorLog;      
        }

    }
    /**
    * @desc Function to export order details of the order to TeamDesk Invoice Line Items table
    * @param integer $pinnacleOrderID id of the order in Pinnacle database
    * @param string $orderNumber order number in pinnacle
    * @param integer record id of the order in TeamDesk
    * @return boolean true on success/ false on failure
    * @since 13-Feb-2013
    * @author Dinesh Nagdev
    */
    function exporOrderDetailsToTeamDesk($pinnacleOrderID=0, $orderNumber='', $tdOrderID=0,$order_from='Site')
    {
        $isError = false;
        $arrOrderDetail = array();
        $product_added = array();
        /**
        * @desc  get the order contents by order id
        */
        $this->db->query("SELECT sales_flat_order_item.* FROM sales_flat_order_item WHERE order_id='".$pinnacleOrderID."' ORDER BY sales_flat_order_item.item_id");    
        $arrOrderDetail = $this->db->getRecords();  
        /** create an array of all parent product sku and attribute product sku **/
        foreach ($arrOrderDetail as $key => $orderDetail) {
            if (!in_array($orderDetail['sku'],$product_added)) {
                if($orderDetail['product_type'] == 'configurable') {
                    $arrAttributeSKU[] = strtolower(trim($orderDetail['sku']));  
                }
                else {
                    $arrProductSKU[] = strtolower(trim($orderDetail['sku']));
                }
                $product_added[] = $orderDetail['sku']; 
            }      
        } 
        /** get the related parent product for the product_id **/
        if(count($arrProductSKU) > 0) {
            $arrRelatedParentSKU = $this->getRelatedProductSKUFromTeamDesk($arrProductSKU, 'parent'); 
        }
        /** get the related parent product for the product_id **/
        if(count($arrAttributeSKU) > 0) {
            $arrRelatedParentSKU_Attribute =  $this->getRelatedProductSKUFromTeamDesk($arrAttributeSKU, 'attribute');   
        }
        /*echo '<pre>';
        print_R($arrRelatedParentSKU);
        print_R($this->arrBulkProductSKU); */
        $product_added = array();  
        foreach ($arrOrderDetail as $key => $orderDetail) {  
           if (!in_array($orderDetail['sku'],$product_added)) {    
            $this->orderlineitemdataset->Rows = array();      
            if ($orderDetail['product_type'] == 'gift') {
               $pinnacleSKU = 'GIFT';
               $productSKU = 'GIFT';   
               $pinnacleAttributeSKU = '';
            } 
            else {
                /**
                * @desc  if parent product - use product_id , if product attribute use product_sub_id
                */
                if($orderDetail['product_type'] == 'configurable') {
                    $pinnacleAttributeSKU = trim($orderDetail['sku']);
                    $pinnacleSKU = trim($orderDetail['sku']);
                    /**
                    * @desc code to check if related product is blank then assign related product.
                    */
                    if($arrRelatedParentSKU_Attribute[strtolower($orderDetail['sku'])] != '') {
                        $productSKU =  (string)$arrRelatedParentSKU_Attribute[strtolower($orderDetail['sku'])];
                    }
                    else {
                         $productSKU =  $orderDetail['sku'];
                    }   
                }
                else {
                    $pinnacleAttributeSKU = '';
                    $pinnacleSKU = trim($orderDetail['sku']);
                    /**
                    * @desc code to check if related product is blank then assign related product.
                    */
                    if($arrRelatedParentSKU[strtolower($orderDetail['sku'])] != '') {
                        $productSKU =  (string)$arrRelatedParentSKU[strtolower($orderDetail['sku'])];
                    }
                    else {
                         $productSKU =  $orderDetail['sku'];
                    }    
                }     
            }  
            if(isset($this->arrBulkProductSKU[strtolower($orderDetail['sku'])]['kitType'])) {
               if(($this->arrBulkProductSKU[strtolower($orderDetail['sku'])]['kitType']!='') && ($this->arrBulkProductSKU[strtolower($orderDetail['sku'])]['quantityPerPack']>'0'))         
               {
                   $qty_ordered = $orderDetail['qty_ordered']*$this->arrBulkProductSKU[strtolower($orderDetail['sku'])]['quantityPerPack'];
                   $price = $orderDetail['price']/$this->arrBulkProductSKU[strtolower($orderDetail['sku'])]['quantityPerPack'];
               }
            }
            else {
               $qty_ordered = $orderDetail['qty_ordered'];
               $price = $orderDetail['price'];
            }        
            $arrOrderRec = array(                                                               
                                'Order Number'=> $orderNumber,   
                                'SKU'=> $productSKU,   
                                'Quantity'=> $qty_ordered,   
                                'Price'=> $price,
                                'free_shipping'=> $orderDetail['free_shipping'],
                                'pinnacleId'=> $orderDetail['item_id'], 
                                'PinnacleSku'=> $pinnacleSKU, 
                                'PinnacleAttributeSku'=> $pinnacleAttributeSKU, 
                                'Related Web Profile'=> $pinnacleSKU, 
                                'RecordHistory'=> date("Y-m-d H:i:s").' - '.'Record added in TeamDesk' 
                              );
            array_push($this->orderlineitemdataset->Rows, $arrOrderRec);
            try { 
                $order_line_item_id = $this->api->Create("Invoice Line Item", $this->orderlineitemdataset); // returns ids of the new record
                $order_line_item_id = $order_line_item_id[0];
                if ($order_line_item_id <= 0) {                   
                   $this->arrErrorLog[] = array("orderID"  =>  $orderNumber.", Product SKU - ".$productSKU ,
                                             "Error_Msg" => "Order Detail Import Error");
                   $isError = true;
                } else {  
                    /**
                    * @desc  update the teamdesk record id in the teamdesk_id in the shipping address table of pinnacle
                    */
                    $this->db->reset();
                    $this->db->assignStr("oc_teamdesk_id", $order_line_item_id);          
                    $this->db->update("sales_flat_order_item", "WHERE item_id='".addslashes($orderDetail['item_id'])."'");
                } 
             }
             catch (Exception $e) {
                   echo $this->tdErrorLog .= '<br/>Caught exception: Error in order number :'.$orderData['td_order_number']." "."Order Line Item :".$pinnacleSKU." ".$e->getMessage(). "<br/>";
                    $strReturn .= $this->tdErrorLog;           
             }
             $product_added[] = $orderDetail['sku'];
           }
         }
         if ($this->tdErrorLog)  {
            return false;
         }
         else {
            return true;
         }

    } 

    /**                                                                        
    * @desc Function to save the export log 
    * @param integer $totalOrderAdded total new orders added  
    * @since 14-Feb-2013
    * @author Dinesh Nagdev
    */
    function saveExportLog($totalOrderAdded=0, $totalOrderUpdateFromTD=0,$totalOrderUpdateInTD=0)
    {                                                                                               
        $strComment = '';               
        if ($totalOrderAdded != '') {
            $strRec  = 'Total Orders added in TeamDesk :- <b>'.$totalOrderAdded.'</b> <br/> <br/> ';                                                                                 }   
        if ($totalOrderUpdateFromTD != '') {
            $strRec .= 'Total Orders updated from TeamDesk :- <b>'.$totalOrderUpdateFromTD.'</b> <br/> <br/> ';   
        }
        if ($totalOrderUpdateInTD != '') {
            $strRec  = 'Total Orders updated in TeamDesk :- <b>'.$totalOrderUpdateInTD.'</b> <br/> <br/> ';                                                                          } 
        if ($this->tdErrorLog !='') {
           $strComment = '<br>'; 
           $strComment .= '<table cellpadding="0" cellspacing="1" width="95%" bgcolor="#cccccc">';  
           $strComment .= '<tr>
                                <td height="20" width="30%" style="padding-left:5px"><b>Order ID</b></td>
                                <td height="20"width="70%" style="padding-left:5px" align="left"><b>Error Message</b></td>
                            </tr>';  
            $strComment .= '<tr>
                                <td height="20" width="30%" style="padding-left:5px"><b>Order ID</b></td>
                                <td height="20"width="70%" style="padding-left:5px" align="left"><b>'.$this->tdErrorLog.'</b></td>
                            </tr>';  
           foreach($this->tdErrorLog as $error) {   
              $strComment .= '<tr>
                               <td width="30%" height="20" style="background-color:#FFFFFF;padding-left:5px">'.$error["orderID"].'</td>'.
                             ' <td width="70%" height="20" style="background-color:#FFFFFF;padding-left:5px">'.$error["Error_Msg"].'</td>'.
                             '</tr>';  
           }
           $strComment .= '</table>';        
        }
        $this->db->reset();              
        $this->db->assignStr("user_id", "1");
        $this->db->assignStr("records_added", $totalOrderAdded);
        $this->db->assignStr("records_updated", $totalOrderUpdateFromTD);
        $this->db->assignStr("export_type", "order");
        $this->db->assignStr("comment", $strComment);
        $this->db->assignStr("log_date", date('Y-m-d h:i:s',time()));  
        $new_log_id = $this->db->insert("tblteamdesk_export_log");  
        if (is_array($this->tdErrorLog))
        {
            $subject = "SS: "."Order Export Status On ".date('Y-m-d h:i:s',time());  
            mail(TD_UPDATE_MAIL_ID, $subject, $strRec.$strComment.$this->tdErrorLog);
            /**
            * @desc code to insert the entry into the logs table
            * date : 9-October-2012
            */
            require_once("/home/myclown/public_html/ca_fulex/includes/common_func.php");
            $message =  "SS - Error exporting orders to teamdesk ".date('Y-m-d h:i:s',time());
            $message .= "<br />".$strRec.$strComment.$this->tdErrorLog;
            insertEntryInLogDatabase("ss_orders_export_process",$message,$totalOrderAdded,$totalOrderUpdateFromTD,"Yes");      
        }    
        return $strRec; 
    }

    /**                                                                        
    * @desc Function to get the fetch order information from TeamDesk and update the same in Pinnacle
    * @since 15-Feb-2013
    * @author Dinesh Nagdev
    */
    function updateOrdersFromTeamDesk()
    {   
        $arrOrders = array();  
        $totalOrderUpdateFromTD = 0;
        $totalOrdDetailsUpdateFromTD = 0;
        $this->connectToTeamDesk();
        if ($this->api !='') { 
            /**
            * @desc code to get the schema of orders table  from teamdesk
            */
            try {
                $this->orderset = $this->api->GetSchema("Order",array('sendToPinnacle')); 
            }
            catch (Exception $e) {
                      echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of order table '.$e->getMessage(). "<br/>";
                      $strReturn .= $this->tdErrorLog;           
            }
            /**
            * @desc code to get the schema of orders line items table  from teamdesk
            */
            try {
                $this->orderlineitemdataset = $this->api->GetSchema("Invoice Line Item",array('pinnacleId')); 
            }
            catch (Exception $e) {
                      echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of invoice line items table '.$e->getMessage(). "<br/>";
                      $strReturn .= $this->tdErrorLog;           
            }
            /**
            * @desc  get all the records with sendToPinnacle marked as checked
            */
            $arrQueries = "WHERE [sendToPinnacle] AND ([OrderSource]='SS' OR [OrderSourceText]='FL Jet') AND ([Status]='Shipped' OR [Status]='Cancelled')";     
            try {
                $strColumns =  "[pinnacleId],[Status],[OrderNumber],[ShipDateCalced],[TrackingNumber],[WRHS Freight Carrier/Service],[sendToPinnacle],[Shipping Method],[Ship To Full Name Calc]";
                $arrResults = $this->api->Query("SELECT $strColumns FROM [Order] ".$arrQueries);
                if (isset($arrResults->Rows)) {
                    /**
                    * @desc  loop through each user and update the details in pinnacle and retreive the shipping address
                    */
                    foreach ($arrResults->Rows as $tdOrder) { 
                            echo "<br /><br />**************************Order Number:".$tdOrder['OrderNumber']."***************************************<br /><br />";   
                            $this->orderset->Rows = array();                                               
                            $errorCount = 0;
                            $handling_separated = '';   
                            $arrUpdateRecord = array();
                            $arrTeamDeskOrders = array();
                            $pinnacleOrderID = (int)$tdOrder['pinnacleId'];
                            $tdRecordID = (int)$tdOrder['@id'];
                            $message = '';  
                            /**
                            * @desc  order charges like sub total amount, tax, shipping , handling and total amount
                            */
                            $this->db->reset();   
                            /**
                            * @desc  order status and shipment tracking number, tracking number type    
                            */
                            if (($tdOrder['Status'] == 'Shipped') || ($tdOrder['Status'] == 'Cancelled')) {
                                /**
                                * @desc  Status
                                */
                                if ($tdOrder['Status'] == 'Shipped') {
                                    $this->db->assignStr("status", 'complete');  
                                    $this->db->assignStr("state", 'complete');                     
                                }
                                elseif ($tdOrder['Status'] == 'Cancelled') {
                                   $this->db->assignStr("status", 'canceled'); 
                                   $this->db->assignStr("state", 'canceled'); 
                                }    
                                $this->db->assignStr("updated_at", date('Y-m-d h:i:s',time())); 
                                /**
                                * @desc  update the order details
                                */
                                $this->db->update("sales_flat_order","WHERE entity_id = '".$tdOrder['pinnacleId']."' ");  
                                
                                $this->db->reset(); 
                                if ($tdOrder['Status'] == 'Shipped') {
                                    $this->db->assignStr("status", 'complete');                       
                                }
                                elseif ($tdOrder['Status'] == 'Cancelled') {
                                   $this->db->assignStr("status", 'canceled');  
                                }
                                $this->db->update("sales_flat_order_grid","WHERE entity_id = '".$tdOrder['pinnacleId']."' ");  
                                if($tdOrder['TrackingNumber'] != '') {
                                    /**
                                    * @desc code added by dinesh to create the shipping record in magento
                                    */
                                    $this->db->reset(); 
                                    $this->db->assign("store_id", "1");
                                    $this->db->assign("order_id", $tdOrder['pinnacleId']);   
                                    $this->db->assignStr("increment_id", $tdOrder['OrderNumber']);  
                                    $this->db->assignStr("email_sent", "0"); 
                                    $this->db->assignStr("created_at", date('Y-m-d h:i:s',time())); 
                                    $this->db->assignStr("updated_at", date('Y-m-d h:i:s',time()));
                                    $shipping_id = $this->db->insert("sales_flat_shipment");  
                                
                                    if($shipping_id > 0) {
                                        $shipDate = $tdOrder['ShipDateCalced'];
                                        $this->db->reset(); 
                                        $this->db->assign("parent_id", $shipping_id);
                                        $this->db->assign("order_id", $tdOrder['pinnacleId']);   
                                        $this->db->assignStr("created_at", $shipDate); 
                                        $this->db->assignStr("updated_at", $shipDate);
                                        /**
                                        * @desc  tracking number
                                        */
                                        $this->db->assignStr("track_number", $tdOrder['TrackingNumber']); 
                                        /**
                                        * @desc  tracking number type 
                                        */
                                        if ((preg_match('/^UPS/i', $tdOrder['WRHS Freight Carrier/Service']) > 0) || ($tdOrder['WRHS Freight Carrier/Service']=='M6')) {
                                            $tracking_number_type = 'ups';   
                                        }     
                                        elseif (preg_match('/^USPS/i', $tdOrder['WRHS Freight Carrier/Service']) > 0) {
                                            $tracking_number_type = 'usps';  
                                        }
                                        elseif (preg_match('/^FEDEX/i', $tdOrder['WRHS Freight Carrier/Service']) > 0) {
                                            $tracking_number_type = 'FedEx';  
                                        }  
                                        elseif (preg_match('/^UP/i', $tdOrder['WRHS Freight Carrier/Service']) > 0) {
                                            $tracking_number_type = 'ups';  
                                        } 
                                        elseif (preg_match('/^US/i', $tdOrder['WRHS Freight Carrier/Service']) > 0) {
                                            $tracking_number_type = 'usps';  
                                        }     
                                        if($tdOrder['Shipping Method']=='freeshipping_freeshipping') {
                                            if($tdOrder['WRHS Freight Carrier/Service']=='M6') {
                                                $tdOrder['Shipping Method']='Mail Innovations';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='USPS1') {                                  
                                                $tdOrder['Shipping Method']='First Class Parcel';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='USPP') {
                                                $tdOrder['Shipping Method']='Priority';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='UPSG') {
                                                $tdOrder['Shipping Method']='Ground';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='UP3R') {
                                                $tdOrder['Shipping Method']='3 Day Select';
                                            } 
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='UP2R') {
                                                $tdOrder['Shipping Method']='2nd Day Air';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='UPND') {
                                                $tdOrder['Shipping Method']='Next Day Air';
                                            }
                                            elseif($tdOrder['WRHS Freight Carrier/Service']=='USEX') {
                                                $tdOrder['Shipping Method']='Express (1-2 Days)';
                                            }   
                                        }                     
                                        $this->db->assignStr("carrier_code", $tracking_number_type); 
                                        $this->db->assignStr("title", $tdOrder['Shipping Method']);  
                                        $shipping_tracking_id = $this->db->insert("sales_flat_shipment_track");  
                                    }  
                                    $arrOrdLineQueries = "WHERE [Order Number] = '".$tdOrder['OrderNumber']."'";   
                                    try {
                                         $total_qty=0; 
                                         $arrOrdstrColumns  = "[pinnacleId],[SKU],[Quantity],[Price],[SKU - Weight],[Quantity Shipped]";
                                          $arrOrderDetails = $this->api->Query("SELECT $arrOrdstrColumns FROM [Invoice Line Item] ".$arrOrdLineQueries);    
                                          if (isset($arrOrderDetails->Rows)) {
                                             /**
                                             * @desc  loop through each user and update the details in pinnacle and retreive the shipping address
                                             */
                                             foreach ($arrOrderDetails->Rows as $tdOrderDetail) {    
                                                  $productSKU = $tdOrderDetail['SKU'];
                                                  $this->db->query("SELECT entity_id from catalog_product_entity where sku='".$productSKU."'");
                                                  if($this->db->moveNext()) {
                                                        $product_id = $this->db->col['entity_id'];
                                                  } 
                                                  $this->db->query("SELECT value from catalog_product_entity_varchar where attribute_id=71 AND entity_id='".$product_id."'");
                                                  if($this->db->moveNext()) {
                                                        $name = $this->db->col['value']; 
                                                  }
                                                  if($tdOrderDetail['pinnacleId']=='') {
                                                      $this->db->query("SELECT item_id from sales_flat_order_item where order_id=".$tdOrder['pinnacleId']." Limit 0,1");
                                                      if($this->db->moveNext()) {
                                                            $tdOrderDetail['pinnacleId'] = $this->db->col['item_id'];
                                                      } 
                                                  }    
                                                  $this->db->reset(); 
                                                  $this->db->assign("parent_id", $shipping_id);
                                                  $this->db->assignStr("price", $tdOrderDetail['Price']);   
                                                  $this->db->assignStr("weight", $tdOrderDetail['SKU - Weight']);  
                                                  $this->db->assign("qty", $tdOrderDetail['Quantity Shipped']);
                                                  $this->db->assign("product_id", $product_id);
                                                  $this->db->assign("order_item_id", $tdOrderDetail['pinnacleId']);  
                                                  $this->db->assignStr("name", $name);   
                                                  $this->db->assignStr("sku", $productSKU);
                                                  $this->db->insert("sales_flat_shipment_item");  
                                                  $total_qty += $tdOrderDetail['Quantity Shipped'];
                                             }   
                                          }
                                    }
                                    catch (Exception $e) {
                                           echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                                           $strReturn .= $this->tdErrorLog;           
                                    }  
                                    $this->db->reset();    
                                    $this->db->assignStr("entity_id", $shipping_id);     
                                    $this->db->assignStr("store_id", "1"); 
                                    $this->db->assignStr("order_id", $tdOrder['pinnacleId']);  
                                    $this->db->assignStr("increment_id", $tdOrder['OrderNumber']); 
                                    $this->db->assignStr("order_increment_id", $tdOrder['OrderNumber']); 
                                    $this->db->assignStr("created_at", date('Y-m-d h:i:s',time()));   
                                    $this->db->assignStr("shipping_name", $tdOrder['Ship To Full Name Calc']); 
                                    $this->db->assignStr("total_qty", $total_qty); 
                                    $this->db->insert("sales_flat_shipment_grid");  
                                }    
                                /**                                                                  
                                * @desc  uncheck the sendToPinnacle 
                                */       
                                $this->orderset->Rows[0]["@id"] = $tdRecordID; 
                                $this->orderset->Rows[0]["sendToPinnacle"] = "0";
                            }
                            /**
                            * @desc  increment the total orders updated count
                            */
                            $this->totalOrderUpdateFromTD++;                                    
                            /**
                            * @desc save record history in user   
                            */
                            try {
                                $update_rec = $this->api->Update("Order", $this->orderset);  
                            }
                            catch (Exception $e) {
                                  echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                                  $strReturn .= $this->tdErrorLog;           
                            }    
                        }

                    } 
            } 
           catch (Exception $e) {
                echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";
                $strReturn .= $this->tdErrorLog;           
           }     
            /**
            * @desc  Save log
            */
            $strReturn = $this->saveExportLog($this->totalOrderAdded, $this->totalOrderUpdateFromTD);
            return $strReturn;  
        }
        else {
            return $this->tdErrorLog;      
        }
    } // end of updateRecordsFromTD function     

    /** 
    * @desc Function to get the related product sku for the product or attribute
    * @param $arrSKU array of sku for which the parent must be returned
    * @param $producType - parent or attribute
    * @since 13-Feb-2013
    * @author Dinesh Nagdev
    */
    function getRelatedProductSKUFromTeamDesk($arrSKU = array(), $productType='parent')
    {
        $arrRelatedProductSKU = array(); 
        $arrQueries = array();
        $cnt=0;
        /**
        * @desc Create an array for query for all the sku whose parent sku is to be found
        */
        if ($productType == 'parent') {    
            foreach ($arrSKU as $key => $arrValue) {  
                if ($cnt == 0) {      
                    /**
                    * @desc  create the Teamdesk query, to fetch all the categories that are marked as sendToPinnacle
                    */ 
                    $arrQueries = "WHERE [PinnacleSKU] = '".$arrValue."'";   
                }
                else {  
                    $arrQueries .= " OR [PinnacleSKU] = '".$arrValue."'";   
                }
                if($cnt == 5)
                {
                    $strColumns = "[PinnacleSKU],[Related Product],[kitType],[quantityPerPack]";
                    $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Web Profile] ".$arrQueries);   
                    $arrQueries='';
                    $cnt=0;
                    if (isset($arrResults->Rows)) {  
                        foreach ($arrResults->Rows as $tdProductSKU) {
                            $tmpPinnacleSKU = (string)$tdProductSKU['PinnacleSKU'];
                            $tmpPinnacleSKU = strtolower($tmpPinnacleSKU); 
                            $arrRelatedProductSKU[$tmpPinnacleSKU] = $tdProductSKU['Related Product'];
                            $this->arrBulkProductSKU[$tmpPinnacleSKU]['kitType'] = $tdProductSKU['kitType']; 
                            $this->arrBulkProductSKU[$tmpPinnacleSKU]['quantityPerPack'] = $tdProductSKU['quantityPerPack'];      
                        }
                    }
                }
                else {    
                    $cnt++;
                }                      
            }
            if($arrQueries !='')
            {     
                $strColumns = "[PinnacleSKU],[Related Product],[kitType],[quantityPerPack]";
                $arrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Web Profile] ".$arrQueries); 
                /**
                * @desc Create an array for parent SKU
                */   
                if (isset($arrResults->Rows)) {  
                    foreach ($arrResults->Rows as $tdProductSKU) {
                        $tmpPinnacleSKU = (string)$tdProductSKU['PinnacleSKU'];
                        $tmpPinnacleSKU = strtolower($tmpPinnacleSKU); 
                        $arrRelatedProductSKU[$tmpPinnacleSKU] = $tdProductSKU['Related Product'];
                        $this->arrBulkProductSKU[$tmpPinnacleSKU]['kitType'] = $tdProductSKU['kitType']; 
                        $this->arrBulkProductSKU[$tmpPinnacleSKU]['quantityPerPack'] = $tdProductSKU['quantityPerPack'];
                    }
                }
            }
            foreach ($arrSKU as $key => $arrValue) {
                if(strtolower($arrValue) != strtolower($arrRelatedProductSKU[$tmpPinnacleSKU])) {
                     $arrQueries = "WHERE [PinnacleAttributeSKU] = '".$arrValue."'";
                     $strColumns = "[PinnacleAttributeSku],[Related Product]";  
                     $arrAttrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Attribute] ".$arrQueries); 
                     if (isset($arrAttrResults->Rows)) {  
                        foreach ($arrAttrResults->Rows as $tdAttrSKU) {  
                            $tmpPinnacleSKU = $tdAttrSKU['PinnacleAttributeSku'];
                            $tmpPinnacleSKU = strtolower($tmpPinnacleSKU);
                            $tdAttrSKU['Related Product'];
                            $arrRelatedProductSKU[$tmpPinnacleSKU] = $tdAttrSKU['Related Product'];
                        }
                     }
                }   
            }
                     
        }
        else {
            /**
            * @desc 
            *  FID 15  - pinnacleAttributeSKU, FID 18  - related product
            */
            foreach ($arrSKU as $key => $arrValue) {  
                if ($cnt == 0) {
                    /**
                    * @desc  create the Teamdesk query, to fetch all the categories that are marked as sendToPinnacle
                    */ 
                    $arrQueries = "WHERE [PinnacleAttributeSku] = '".$arrValue."'"; 
                } 
                else {
                    $arrQueries .= " OR [PinnacleAttributeSku] = '".$arrValue."'";
                }
                if($cnt == 5)
                {
                    $strColumns = "[PinnacleAttributeSku],[Related Product]";  
                    $arrAttrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Attribute] ".$arrQueries); 
                    $arrQueries='';
                    $cnt=0;
                    if (isset($arrAttrResults->Rows)) {  
                        foreach ($arrAttrResults->Rows as $tdAttrSKU) {  
                            $tmpPinnacleAttributeSKU = (string)$tdAttrSKU['PinnacleAttributeSku'];
                            $tmpPinnacleAttributeSKU = strtolower($tmpPinnacleAttributeSKU);
                            $arrRelatedProductSKU[$tmpPinnacleAttributeSKU] = $tdAttrSKU['Related Product'];
                        }
                    }
                } 
                else {    
                    $cnt++;
                }
            } 
            if($arrQueries !='')
            {     
                $strColumns = "[PinnacleAttributeSku],[Related Product]";    
                $arrAttrResults = $this->api->Query("SELECT ".$strColumns." FROM [SS Attribute] ".$arrQueries);   
                /**
                * @desc Create an array for parent SKU
                */
                if (isset($arrAttrResults->Rows)) {  
                    foreach ($arrAttrResults->Rows as $tdAttrSKU) {  
                        $tmpPinnacleAttributeSKU = (string)$tdAttrSKU['PinnacleAttributeSku'];
                        $tmpPinnacleAttributeSKU = strtolower($tmpPinnacleAttributeSKU);
                        $arrRelatedProductSKU[$tmpPinnacleAttributeSKU] = $tdAttrSKU['Related Product'];
                    }
                }
            }   
        }
        return $arrRelatedProductSKU;
    }

    

    /** 

    * @desc Function to export the special order request from Pinnacle to TeamDesk      

    * @since 03-July-2013

    * @author Dinesh Nagdev

    */

    function exportProductRequestToTeamDesk()

    {

        // get the field mapping for the special order form

        $this->db->query("SELECT pinnacle_field_name, teamdesk_field_name FROM tblspecial_orders_teamdesk_mapping ORDER BY map_id ASC");        

        while ($this->db->moveNext()) {

            $pinnacleFieldName = $this->db->col["pinnacle_field_name"];    

            $TDFieldName = $this->db->col["teamdesk_field_name"];    

            $this->arrSpclOrdTDFields[$pinnacleFieldName] = $TDFieldName; 

        }

        $this->connectToTeamDesk();

        

        if ($this->api !='') {  

           

             /**

            * @desc code to get the schema of orders table  from teamdesk

            */

            $orderSQL = "SELECT *, 

                         DATE_FORMAT(request_date, '%H:%i') AS order_time   

                         FROM tblspecial_orders WHERE sendToTeamDesk = 'Yes' AND order_teamdesk_id = 0 

                         ORDER BY sporder_id";

            $this->db->query($orderSQL);   

            $arrOrders = $this->db->getRecords();     

           

            if(count($arrOrders) > 0) {

                try {

                    $this->orderset = $this->api->GetSchema("Order",array('Bill To First Name','Bill To Last Name','Email','Phone','Date','Time','OrderNumber','Case Type',

                                                        'Next Step','Case Followup Date','Comments','specialOrderDateNeededBy','Status','Bill To Country','Ship To Country'));  

                }

                catch (Exception $e) {

                          echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of order table '.$e->getMessage(). "<br/>";

                            $strReturn .= $this->tdErrorLog;           

                }

                /**

                * @desc code to get the schema of orders line items table  from teamdesk

                */

                try {

                    $this->orderlineitemdataset = $this->api->GetSchema("Invoice Line Item",array('SKU','Quantity','Price','Order Number')); 

                }

                catch (Exception $e) {

                          echo $this->tdErrorLog = '<br/>Caught exception: Error fetching schema of order line item table '.$e->getMessage(). "<br/>";

                            $strReturn .= $this->tdErrorLog;           

                }

            

                foreach ($arrOrders as $key => $orderData) {  

                    $this->orderset->Rows = array();  

                    $this->orderlineitemdataset->Rows = array();

                    $case_followup_date = new DateTime();          

                    $orderNumber = 'SS-PR-'.date("ym")."-".vsprintf("%04s", (string)$orderData['sporder_id']);

                    $order_date = new DateTime($orderData['request_date']);

                    $order_time = strtotime($orderData['order_time']);  

                    $date_needed_by = new DateTime($orderData['date_needed_by']);

                    $billCountryName = getCountryNameByID($this->db, $orderData['country'], 'country_code'); 
                    
                    $arrRecords = array(

                                        $this->arrSpclOrdTDFields['first_name'] => $orderData['first_name'],   

                                        $this->arrSpclOrdTDFields['last_name'] => $orderData['last_name'],                                                                                                           $this->arrSpclOrdTDFields['email'] => $orderData['email'],      

                                        $this->arrSpclOrdTDFields['phone'] => $orderData['phone'],

                                        $this->arrSpclOrdTDFields['date'] => $order_date,

                                        $this->arrSpclOrdTDFields['time'] => $order_time,

                                        $this->arrSpclOrdTDFields['order_number'] => $orderNumber,

                                        $this->arrSpclOrdTDFields['Comments'] => $orderData['comments'],      

                                        $this->arrSpclOrdTDFields['date_needed_by'] => $date_needed_by, 

                                        $this->arrSpclOrdTDFields['Bill_To_Country'] => ucfirst($billCountryName),      

                                        $this->arrSpclOrdTDFields['shipping_country'] => ucfirst($billCountryName), 

                                        $this->arrSpclOrdTDFields['case_type'] => 'Customer Request: Product Request',

                                        $this->arrSpclOrdTDFields['next_step'] => 'Talk to Customer',

                                        $this->arrSpclOrdTDFields['case_followup_date'] => $case_followup_date,

                                        $this->arrSpclOrdTDFields['status'] => 'On Hold'

                                       );

                    

                    array_push($this->orderset->Rows, $arrRecords);

                    try {                                            

                         $order_id = $this->api->Create("Order", $this->orderset); // returns ids of the new record     

                         $order_id = $order_id[0];

                         if ($order_id <= 0) {                   

                            $this->arrErrorLog[] = array("orderID"  => ucwords($orderNumber),

                                                         "Error_Msg" => "Order Import Error");

                         } else {             

                              $arrOrderRec = array(                                                               

                                        $this->arrSpclOrdTDFields['invoice_order_number']  => $orderNumber,   

                                        $this->arrSpclOrdTDFields['productSKU'] => $orderData['product_SKU'],   

                                        $this->arrSpclOrdTDFields['quantity_needed'] => $orderData['quantity_needed'], 

                                        $this->arrSpclOrdTDFields['price'] => 0

                                      );

                              array_push($this->orderlineitemdataset->Rows, $arrOrderRec);             

                              try { 

                                    $order_line_item_id = $this->api->Create("Invoice Line Item", $this->orderlineitemdataset); // returns ids of the new record

                                    $order_line_item_id = $order_line_item_id[0];

                                    if ($order_line_item_id <= 0) {                   

                                       $this->arrErrorLog[] = array("orderID"  =>  $orderNumber.", Product SKU - ".$orderData['product_SKU'] ,

                                                                 "Error_Msg" => "Order Detail Import Error");

                                       $isError = true;

                                    }

                              }

                              catch (Exception $e) {

                                      echo $this->tdErrorLog = '<br/>Caught exception: Error inserting order line item for order :'.$orderNumber." "."Order Line Item :".$orderData['product_SKU']." ".$e->getMessage(). "<br/>";

                                        $strReturn .= $this->tdErrorLog;           

                              }     

                              $this->totalOrderAdded++; 

                              /**

                              * @desc  save the teamdesk record in the orders table

                              */

                              $this->db->reset();

                              $this->db->assignStr("order_teamdesk_id", $order_id);          

                              $this->db->assignStr("sendToTeamDesk ", "No");          

                              $this->db->update("tblspecial_orders", "WHERE sporder_id='".addslashes($orderData['sporder_id'])."'");

                          }

                     }               

                     catch (Exception $e) {

                            echo $this->tdErrorLog = '<br/>Caught exception: Error inserting order number :'.$orderNumber." into teamdesk ".$e->getMessage(). "<br/>";  

                            $strReturn .= $this->tdErrorLog;           

                     }           

                } // end of foreach   

            } // end of if arrorders 

        } // end of if $this->connTDOrder

        

    } // end of function



    /**

    * @desc function added by dinesh for updating is_correct_shipping_address in teamdesk

    * date : 14th October, 2013 

    */

    public function updateflagInTeamDesk($orderdataset='',$field_array) 

    {

        $this->connectToTeamDesk();

        

        if ($this->api !='') {  

            

            if (count($orderdataset) > 0) { 

               

                try {

                    $this->orderset = $this->api->GetSchema("Order",$field_array);  

                }

                catch (Exception $e) {

                        echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";

                        $strReturn .= $this->tdErrorLog;           

                }     

                $this->orderset->Rows = $orderdataset; 

                try {

                    $orderresults = $this->api->Upsert("Order", $this->orderset);

                }

                catch (Exception $e) {

                     echo $this->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";

                     $strReturn .= $this->tdErrorLog;           

                } 

                if (count($orderresults) > 0) {

                   $totalOrderRecords =  $totalOrderRecords + count($orderresults); 

               }

            }

        }    

        return $totalOrderRecords;   

    }  

    

     /**

    * @desc function added by dinesh for fetching the sku from amazon entries table of teamdesk

    * date : 14-Feb-2012

    */

    function get_product_sku_from_amazon_entries_table($pinnacleSKU,$type='')

    {

            /**

            * @desc code added by dinesh for searching the amazon sku from amazon entries table 

            */   

            if($pinnacleSKU != '')

            {

                 if($type == 'amazon')

                 { 

                     $arrAmazonQueries = "WHERE [sku] = '".$pinnacleSKU."'"; 

                     /**

                     * @desc amazon sku columns

                     */

                      $strAmazonColumns = "[sku],[Related Product]"; 

                      $arrAmazonResults = $this->api->Query("SELECT ".$strAmazonColumns." FROM [Amazon Entry] ".$arrAmazonQueries);   

                      if (isset($arrAmazonResults->Rows)) {  

                          foreach ($arrAmazonResults->Rows as $tdProductSKU) {

                                $productSKU = (string)$tdProductSKU['Related Product'];

                                if($productSKU == '') 

                                {

                                    $productSKU = (string)$tdProductSKU['sku'];

                                }    

                            }

                      }

                 }

                 else if($type == 'ebay')

                 {

                     $arrEbayQueries = "WHERE [Related Web Profile] = '".$pinnacleSKU."'";   

                      /**

                      * @desc ebay entries columns

                      */

                      $strEbayColumns = "[Related Web Profile],[Related Product]"; 

                      $arrEbayResults = $this->api->Query("SELECT ".$strEbayColumns." FROM [Ebay Entry] ".$arrEbayQueries); 

                      if (isset($arrEbayResults->Rows)) {  

                          foreach ($arrEbayResults->Rows as $tdProductSKU) {

                                $productSKU = (string)$tdProductSKU['Related Product'];

                                if($productSKU == '') 

                                {

                                    $productSKU = (string)$tdProductSKU['Related Web Profile'];

                                }    

                            }

                      }

                 }      

                 return $productSKU; 

            } 

            else

            {

                return false;

            }    

    }  

    function search_string($string,$search_str)

    {                             

       $output_str = stristr($string,$search_str);

       if($output_str !='') {

            $output_arr = explode("\n",$output_str);  

            $output_string = explode("=",$output_arr[0]);   

            return trim($output_string[1]);

       }

       else

            return '';     

    }    

}        

?>                                           

