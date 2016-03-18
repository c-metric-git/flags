<?php

global $all_scripts;

if($all_scripts != 'Yes') {

    include 'config.php';

}     

//error_reporting(E_ALL);

ini_set('max_execution_time', 0);

ini_set('display_errors', 1);



$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($all_scripts != 'Yes') {

    function __autoload($className) 

    {

        $filePath = $_SERVER['DOCUMENT_ROOT'] .'/'.FOLDER_NAME. '/API/amzn/' . str_replace('_', '/', $className) . '.php';    

        $includePaths = explode(PATH_SEPARATOR, get_include_path());

        

        foreach ($includePaths as $includePath) 

        {        

            if (file_exists($filePath))

            {

                require_once $filePath;

                return;

            }

        }

    }

}    

//$sql = 'SELECT `@id`,`sku` FROM ready_for_amazon WHERE is_visible = "1" AND `isVisibleOnAmazon?` <> "1"';

$sql = 'SELECT `@id`,`sku`,`recordHistory`,`amazonErrorReason` FROM ready_for_amazon WHERE `isReadyForUpdate?` = "1"';

//$sql = 'SELECT `@id`,`sku` FROM ready_for_amazon WHERE `sku` IN ("F41025","K1140","K1442","L10781","L109743","M1870","M2170","M2332","M4410","L109779","L109787","L109755")';

            

if(isset($_REQUEST['id']) && $_REQUEST['id'] != '')

    $sql .= 'AND `@id` = "'.trim($_REQUEST['id']).'"';



$query = $mysqli->query($sql);

$key = 0;

$feed_arr = array();



while($feeds = $query->fetch_array(MYSQLI_ASSOC))

{

    $feed_arr[$key]['@id'] = (isset($feeds['@id']) && $feeds['@id'] != '') ? $feeds['@id'] : '';

    $feed_arr[$key]['sku'] = (isset($feeds['sku']) && $feeds['sku'] != '') ? $feeds['sku'] : '';

    $feed_arr[$key]['recordHistory'] = (isset($feeds['recordHistory']) && $feeds['recordHistory'] != '') ? $feeds['recordHistory'] : '';  

    $feed_arr[$key]['amazonErrorReason'] = (isset($feeds['amazonErrorReason']) && $feeds['amazonErrorReason'] != '') ? $feeds['amazonErrorReason'] : '';  

    $sql_var = 'SELECT `@id`,`sku`,`recordHistory`,`amazonErrorReason` FROM ready_for_amazon_child WHERE `parent-sku` = "'.$feeds['sku'].'"';

    $query_var = $mysqli->query($sql_var);

    $count = 0;

    while($feeds_var = $query_var->fetch_array(MYSQLI_ASSOC))

    {

        $feed_arr[$key]['variations'][$count]['@id'] = (isset($feeds_var['@id']) && $feeds_var['@id'] != '') ? $feeds_var['@id'] : '';

        $feed_arr[$key]['variations'][$count]['sku'] = (isset($feeds_var['sku']) && $feeds_var['sku'] != '') ? $feeds_var['sku'] : '';

        $feed_arr[$key]['variations'][$count]['recordHistory'] = (isset($feeds_var['recordHistory']) && $feeds_var['recordHistory'] != '') ? $feeds_var['recordHistory'] : '';  

        $feed_arr[$key]['variations'][$count]['amazonErrorReason'] = (isset($feeds_var['amazonErrorReason']) && $feeds_var['amazonErrorReason'] != '') ? $feeds_var['amazonErrorReason'] : '';  

    

        $count++;

    }

    $key++;

}

$api = new TDAPI();

$login = $api->login();

$dataset = $api->GetSchema("Amazon Entry",array('isVisibleOnAmazon?','isReadyForUpdate?','recordHistory','amazonErrorReason','ASIN','Amazon Title','Amazon_Style_Number'));



$serviceUrl = "https://mws.amazonservices.com/Products/2011-10-01";



$config = array (

   'ServiceURL' => $serviceUrl,

   'ProxyHost' => null,

   'ProxyPort' => -1,

   'MaxErrorRetry' => 3,

 );



$service = new MarketplaceWebServiceProducts_Client(

        AWS_ACCESS_KEY_ID, 

        AWS_SECRET_ACCESS_KEY,

        APPLICATION_NAME,

        APPLICATION_VERSION,

        $config);



     

if(isset($feed_arr))

{

    foreach($feed_arr as $key=>$val)

    {

        if(isset($val['variations']))

        {          

            foreach($val['variations'] as $key=>$variations_val)

            {            

                if(isset($variations_val['sku']))

                {                

                    $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();

                    $request->setSellerId(MERCHANT_ID);

                    $request->setMarketplaceId(MARKETPLACE_ID);

                    $request->setIdType('SellerSKU');

                    $request->setIdList($variations_val['sku']);

                    $asin = invokeGetMatchingProductForId($service, $request,$variations_val['sku']);                    

                    if($asin !='')

                    {

                        $asin_arr = explode("@#@",$asin);

                        $asin = $asin_arr[0];

                        $amazon_title = $asin_arr[1]; 

                        

                        /*$request = new MarketplaceWebServiceProducts_Model_GetMatchingProductRequest();

                        $request->setSellerId(MERCHANT_ID);

                        $request->setMarketplaceId(MARKETPLACE_ID);    

                        $request->setASINList($asin);    */ 

                        $model_number = $asin_arr[2];//invokeGetMatchingProduct($service, $request);      

                        $record_history = $variations_val['recordHistory']."<br />"."Entry Insert: Entry Created";

                        $amazon_error_reason = $variations_val['amazonErrorReason'];

                        $query = $mysqli->query('UPDATE ready_for_amazon_child SET ASIN = "'.$asin.'", `recordHistory` = "'.$record_history.'", `amazonErrorReason` = "'.$amazon_error_reason.'", `isVisibleOnAmazon?` = "1" WHERE `@id` = "'.$variations_val['@id'].'"');

                        $dataset->Rows[0]["@id"] = $variations_val['@id'];

                        $dataset->Rows[0]["amazonErrorReason"] = $amazon_error_reason;

                        $dataset->Rows[0]["isVisibleOnAmazon?"] = TRUE;

                        $dataset->Rows[0]["recordHistory"] = $record_history;

                        $dataset->Rows[0]["isReadyForUpdate?"] = FALSE;

                        $dataset->Rows[0]["ASIN"] = $asin;

                        $dataset->Rows[0]["Amazon Title"] = $amazon_title;  

                        $dataset->Rows[0]["Amazon_Style_Number"] = $model_number;  

                        $api->Upsert("Amazon Entry", $dataset);

                        echo "SKU : ",$variations_val['sku'],"<br/>Record with id:- ",$variations_val['@id']," updated successfully.<br/>";

                    }

                }

            }

        }

        if(isset($val['sku']))

        {

            $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();

            $request->setSellerId(MERCHANT_ID);

            $request->setMarketplaceId(MARKETPLACE_ID);

            $request->setIdType('SellerSKU');

            $request->setIdList($val['sku']); 

            $asin = invokeGetMatchingProductForId($service, $request,$val['sku']); 

            if($asin!='')

            {

                $asin_arr = explode("@#@",$asin);

                $asin = $asin_arr[0];

                $amazon_title = $asin_arr[1]; 

                

                 /*$request = new MarketplaceWebServiceProducts_Model_GetMatchingProductRequest();

                 $request->setSellerId(MERCHANT_ID);

                 $request->setMarketplaceId(MARKETPLACE_ID); 

                 $request->setASINList($asin);            */

                 $model_number = $asin_arr[2];//invokeGetMatchingProduct($service, $request);

                 $record_history = $val['recordHistory']."<br />"."Entry Insert: Entry Created";

                 $amazon_error_reason = $val['amazonErrorReason'];

                $query = $mysqli->query('UPDATE ready_for_amazon SET ASIN = "'.$asin.'", `recordHistory` = "'.$record_history.'", `amazonErrorReason` = "'.$amazon_error_reason.'", `isVisibleOnAmazon?` = "1" WHERE `@id` = "'.$val['@id'].'"');                

                $dataset->Rows[0]["@id"] = $val['@id'];

                $dataset->Rows[0]["amazonErrorReason"] = $amazon_error_reason;

                $dataset->Rows[0]["isVisibleOnAmazon?"] = TRUE;

                $dataset->Rows[0]["recordHistory"] = $record_history;

                $dataset->Rows[0]["isReadyForUpdate?"] = FALSE; 

                $dataset->Rows[0]["ASIN"] = $asin;

                $dataset->Rows[0]["Amazon Title"] = $amazon_title;  

                $dataset->Rows[0]["Amazon_Style_Number"] = $model_number; 

                try 

                {

                    $orderresults = $api->Upsert("Amazon Entry", $dataset);

                }

                catch (Exception $e)

                {

                     echo $api->tdErrorLog = '<br/>Caught exception: ' .$e->getMessage(). "<br/>";

                     $strReturn .= $api->tdErrorLog;

                }

                $api->Upsert("Amazon Entry", $dataset);                

                echo "SKU: ",$val['sku'],"<br/>Record with id:- ",$val['@id']," updated successfully.<br/>";                

            }

        }        

    }

}

else

    echo "No records to update";



function invokeGetMyPriceForSKU(MarketplaceWebServiceProducts_Interface $service, $request,$sku)

{

    try 

    {

        $response = $service->getMyPriceForSKU($request);        

        $getMyPriceForSKUResultList = $response->getGetMyPriceForSKUResult();

        foreach ($getMyPriceForSKUResultList as $getMyPriceForSKUResult) 

        {

            if ($getMyPriceForSKUResult->isSetProduct()) 

            {

                $product = $getMyPriceForSKUResult->getProduct();

                if ($product->isSetIdentifiers())

                {

                    $identifiers = $product->getIdentifiers();

                    if ($identifiers->isSetMarketplaceASIN())

                    {

                        $marketplaceASIN = $identifiers->getMarketplaceASIN();

                        if ($marketplaceASIN->isSetASIN())

                            return $marketplaceASIN->getASIN();

                        else

                            return 0;

                    }

                    else

                        return 0;

                }

                else

                    return 0;

            }

            else

                return 0;

        }

    }

    catch (MarketplaceWebServiceProducts_Exception $ex) 

    {

        echo "Caught Exception: ",$ex->getMessage(),"<br/>";

        sleep(30);

        invokeGetMyPriceForSKU($service, $request,$sku);

    }

}



function invokeGetMatchingProductForId(MarketplaceWebServiceProducts_Interface $service, $request,$sku)

{

      try {   

        $response = $service->GetMatchingProductForId($request);

        $getMatchingProductForIdResultList = $response->getGetMatchingProductForIdResult(); 

        foreach ($getMatchingProductForIdResultList as $getMatchingProductForIdResult) {

                    if ($getMatchingProductForIdResult->isSetProducts()) { 

                        $products = $getMatchingProductForIdResult->getProducts();

                        if($products->isSetProduct()) {

                            $product = $products->getProduct();

                            $product = $product[0];

                            if ($product->isSetIdentifiers()) { 

                                $identifiers = $product->getIdentifiers();

                                if ($identifiers->isSetMarketplaceASIN()) { 

                                    $marketplaceASIN = $identifiers->getMarketplaceASIN();

                                    if ($marketplaceASIN->isSetASIN()) 

                                    {

                                         $amazon_asin = $marketplaceASIN->getASIN();

                                    }

                                } 

                                 

                            } 

                            if ($product->isSetAttributeSets()) {

                                $attributeSets = $product->getAttributeSets();

                                if ($attributeSets->isSetAny()){

                                    $nodeList = $attributeSets->getAny();    

                                      $amazon_title = prettyPrint($nodeList,'Title');  

                                      $model_number = prettyPrint($nodeList,'Model'); 

                                }

                            }

                        }    

                    } 

                } 

           if($amazon_asin != '') {     

                return $amazon_asin."@#@".$amazon_title."@#@".$model_number; 

           }                         

     } catch (MarketplaceWebServiceProducts_Exception $ex) {

        echo("Caught Exception: " . $ex->getMessage() . "\n");

        echo("Response Status Code: " . $ex->getStatusCode() . "\n");

        echo("Error Code: " . $ex->getErrorCode() . "\n");

        echo("Error Type: " . $ex->getErrorType() . "\n");

        echo("Request ID: " . $ex->getRequestId() . "\n");

        echo("XML: " . $ex->getXML() . "\n");

        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");

        sleep(5);

        invokeGetMatchingProductForId($service, $request,$sku);

     }

}

function prettyPrint($nodeList,$field)

 {    

      foreach ($nodeList as $domNode){

         $domDocument =  new DOMDocument();

         $domDocument->preserveWhiteSpace = false;

         $domDocument->formatOutput = true;

         $nodeStr = $domDocument->saveXML($domDocument->importNode($domNode,true));   

         $domDocument->loadXML($nodeStr);       

         $title =  $domDocument->getElementsByTagName($field)->item(0)->nodeValue;    

         return $title;

     }

 } 

 function prettyPrint1($nodeList1,$field)

 {    

      foreach ($nodeList1 as $domNode1){

         $domDocument1 =  new DOMDocument();

         $domDocument1->preserveWhiteSpace = false;

         $domDocument1->formatOutput = true;

         $nodeStr1 = $domDocument1->saveXML($domDocument1->importNode($domNode1,true));

         $domDocument1->loadXML($nodeStr1);       

         $model =  $domDocument1->getElementsByTagName($field)->item(0)->nodeValue; 

         return $model;

     }

 }

 function invokeGetMatchingProduct(MarketplaceWebServiceProducts_Interface $service, $request) 

  {

      try {

            $response = $service->getMatchingProduct($request);

            $getMatchingProductResultList = $response->getGetMatchingProductResult(); 

            foreach ($getMatchingProductResultList as $getMatchingProductResult) {

                        if ($getMatchingProductResult->isSetProduct()) { 

                            $product = $getMatchingProductResult->getProduct(); 

                            if ($product->isSetAttributeSets()) {

                                $attributeSets1 = $product->getAttributeSets(); 

                                if ($attributeSets1->isSetAny()){

                                    $nodeList1 = $attributeSets1->getAny();    

                                    $model_number = prettyPrint1($nodeList1,'Model');  

                                }

                            }

                        } 

            }

            return $model_number!='' ? $model_number : ''; 

     } catch (MarketplaceWebServiceProducts_Exception $ex) {  

        echo("Caught Exception: " . $ex->getMessage() . "\n");

        echo("Response Status Code: " . $ex->getStatusCode() . "\n");

        echo("Error Code: " . $ex->getErrorCode() . "\n");

        echo("Error Type: " . $ex->getErrorType() . "\n");

        echo("Request ID: " . $ex->getRequestId() . "\n");

        echo("XML: " . $ex->getXML() . "\n");

        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");

     }

 } 