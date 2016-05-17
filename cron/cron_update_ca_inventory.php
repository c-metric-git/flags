<?php
ob_start(); 
require_once("cron_init.php");
require_once BASE_PATH.'cron/ca_config.php'; 
require_once(BASE_PATH."lib/Teamdesk/class.FL_import_teamdesk_webprofiles.php"); 
$db->done();
$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::app();
ini_set('memory_limit','256M');
ini_set("display_errors",1);   

//for debug
function showNiceXML($xml){
    return htmlspecialchars( str_replace("<", "\n<", $xml) );
}
    
    $objTDProduct = new FLTeamDeskWebprofiles(); 
    $TDProducts = $objTDProduct->getCATDProductsInventory();  
    //setup for the api calls
    //url to the wsdl describing the service
    $urlToWsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/v7/InventoryService.asmx?WSDL";
    try {
        //create SOAP object that will let us send calls, we need to pass the url of the WSDL
        $client = new SoapClient($urlToWsdl, array("trace" => 1, "exception" => 0) );
        $headersData = array('DeveloperKey' => DeveloperKey, 'Password' => CAPassword);
        $head = new SoapHeader("http://api.channeladvisor.com/webservices/","APICredentials",$headersData);
        $client->__setSoapHeaders($head);
        //To get necessary order details, we need to set order criteria. Following is the example of order criteria.
        $page_number=1;
        $page_size = 50;  
        $counter=0;     
        /*$collection = Mage::getModel('catalog/product')
                      ->getCollection()
                      ->setStoreId(FLStoreId)
                      ->addStoreFilter(FLStoreId)
                      ->addAttributeToSelect('*')
                      ->joinField('qty',
                                 'cataloginventory/stock_item',
                                 'qty',
                                 'product_id=entity_id',
                                 '{{table}}.stock_id=1',
                                 'left');
        if(isset($collection) && count($collection->getData())>0) {    
            foreach($collection->getData() as $product){  */
                //Here date1 and date2 variable specify date range for orders. After this call function of API to get order data.
        $inventory_update_arr='';
        $error_update_arr='';
        if(count($TDProducts)>0) {  
            foreach($TDProducts as $Productdata) {  
                /*echo '<pre>';
                print_R($Productdata);
                exit;  */
                $itemList[$counter]['Sku'] =  $Productdata['sku'];
                $itemList[$counter]['DistributionCenterCode'] =  'New York';
                $itemList[$counter]['Quantity'] =  $Productdata['quantity'];
                $itemList[$counter]['UpdateType'] =  'UnShipped';
                $itemList[$counter]['PriceInfo'] =  array('RetailPrice'=>$Productdata['item-price']);   
                
                if($counter==999) {      
                    $arrData = array(
                               'accountID'=>CAAccountID,
                               'itemQuantityAndPriceList'=>$itemList
                      );
                    $result=$client->UpdateInventoryItemQuantityAndPriceList($arrData); 
                    if($result->UpdateInventoryItemQuantityAndPriceListResult->Status == 'Success') {   
                          foreach($result->UpdateInventoryItemQuantityAndPriceListResult->ResultData->UpdateInventoryItemResponse as $key=> $items_updated) {
                               $update_sku = $items_updated->Sku;
                               if($items_updated->Result==1) {
                                    $inventory_update_arr[$update_sku] = $items_updated->Result;
                               }
                               else {
                                    $error_update_arr[$update_sku] = $items_updated->Result; 
                               }      
                          }    
                         //  echo "<br /> Inventory updated for items ".count($inventory_update_arr);
                     }
                     /*else {
                         echo "<br />Error updating inventory for this batch: "; 
                         echo '<pre>';
                         print_R($arrData);
                     } */  
                    $itemList='';
                    $counter=0;
                    $arrData='';
                }
                else {
                    $counter++;    
                }
            }
            if(count($itemList)>0) {
                 $arrData = array(
                               'accountID'=>CAAccountID,
                               'itemQuantityAndPriceList'=>$itemList
                      );
                    $result=$client->UpdateInventoryItemQuantityAndPriceList($arrData); 
                    if($result->UpdateInventoryItemQuantityAndPriceListResult->Status == 'Success') {   
                          foreach($result->UpdateInventoryItemQuantityAndPriceListResult->ResultData->UpdateInventoryItemResponse as $key=> $items_updated) {
                               $update_sku = $items_updated->Sku;
                               if($items_updated->Result==1) {
                                    $inventory_update_arr[$update_sku] = $items_updated->Result;
                               }
                               else {
                                    $error_update_arr[$update_sku] = $items_updated->Result; 
                               }      
                          }    
                      }
            }   
            echo "<br /> Inventory Updated Successfully : ".count($inventory_update_arr);
            echo "<br /> Error Inventory Update : ".count($error_update_arr);  
            if(count($error_update_arr)>0) {
                  echo '<pre>';
                  print_R($error_update_arr);
            }   
        }
        else {
             echo "<br />No more product to update inventory on channel advisor";
        }             
 } catch (SOAPFault $e) {
    echo "ERROR:".$e->faultstring."\n\n";
    //echo "Request :\n".showNiceXML( $client->__getLastRequest() )."\n\n";
    //echo "Response:\n".showNiceXML( $client->__getLastResponse() )."\n\n";
}
ob_end_flush(); 
?>