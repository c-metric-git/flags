<?php

global $all_scripts;  

$_SERVER['HTTP_HOST'] = "www.stripedsocks.com";

if($all_scripts != 'Yes') {

    include 'config.php';

}        

error_reporting(0);

ini_set('max_execution_time', 0);

$serviceUrl = "https://mws.amazonservices.com";

$config = array (

  'ServiceURL' => $serviceUrl,

  'ProxyHost' => null,

  'ProxyPort' => -1,

  'MaxErrorRetry' => 3,

);

if($all_scripts != 'Yes') {

    function __autoload($className)

    {

        $_SERVER['DOCUMENT_ROOT'] = "/home/stripedsocks/public_html";

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

$submissionProduct_list = $mysqli->query('SELECT product_id, sku FROM error_reporting WHERE processed="0" AND (feed_type = "Product Posting" OR feed_type = "Variation Posting")  AND `isAmazonCANProduct?` != "1" AND `isAmazonUKProduct?` != "1" GROUP BY sku');
$TDProductId = array();
while($rows_getProduct_list = $submissionProduct_list->fetch_array(MYSQLI_ASSOC)){
    $SKU = $rows_getProduct_list['sku'];
    $TDProductId["$SKU"] = $rows_getProduct_list['product_id'];
}                                       

$submission_list = $mysqli->query('SELECT id, product_id, sku, feed_type, feed_id FROM error_reporting WHERE processed="0" AND feed_type = "Product Posting"  AND `isAmazonCANProduct?` != "1" AND `isAmazonUKProduct?` != "1" GROUP BY feed_id');
  
$error_desc = '';

$update_arr = array();

while($rows_submission_list = $submission_list->fetch_array(MYSQLI_ASSOC))

{         

    $DATE_FORMAT            = 'Y-m-d\TH:i:s\Z';

    $AWS_ACCESS_KEY_ID      = AWS_ACCESS_KEY_ID;

    $AWS_SECRET_ACCESS_KEY  = AWS_SECRET_ACCESS_KEY;

    $APPLICATION_NAME       = 'MyInventories';

    $APPLICATION_VERSION    = 'ver1.1';

    $MERCHANT_ID            = MERCHANT_ID;

    $MARKETPLACE_ID         = MARKETPLACE_ID;

    $SERVICE_URL            = SERVICE_URL;

      

    $service = new MarketplaceWebService_Client(

    $AWS_ACCESS_KEY_ID,

    $AWS_SECRET_ACCESS_KEY, 

    $config,

    $APPLICATION_NAME,

    $APPLICATION_VERSION);    

    $file = sha1(uniqid(mt_rand(), true));

    $file_handle = fopen($file.'.xml', 'w');

    $feed_id = $rows_submission_list['feed_id'];

         

    if($feed_id)

    {

        echo '<br/>Feed Id: ',$feed_id,'<br/>';

        $parameters = array (

        'Merchant' => $MERCHANT_ID,

        'FeedSubmissionId' => $feed_id,

        'FeedSubmissionResult' => $file_handle);



        $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

        $response = invokeGetFeedSubmissionResult($service, $request,$parameters);

        sleep(2);

        $feed = simplexml_load_file($file.'.xml');

        

        $error_desc = '';

         

        if($feed->Message->ProcessingReport->Result)

        {     

            foreach($feed->Message->ProcessingReport->Result as $rep)

            {
                $sku = $rep->AdditionalInfo->SKU;
                if(!$sku || $sku==""){$sku = $rows_submission_list['sku'];}     
                 
                if($rep->ResultCode == 'Error')

                {
                    $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'" AND sku = "'.$sku.'"');

                    $update_arr["$sku"]['error_text'] .= "<br>".$rows_submission_list['feed_type']." : ".$rep->ResultDescription;

                    echo "SKU : ".$sku."<br/>Record updated successfully<br/>";

                }

                else if($rep->ResultCode == 'Warning')

                {
                    $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "Entry Insert: Entry Created"), processed = "1" WHERE feed_id = "'.$feed_id.'" AND sku = "'.$sku.'"');

                    $update_arr["$sku"]['error_text'] .= "<br>".$rows_submission_list['feed_type']." : ".$rep->ResultDescription;

                    echo "SKU : ".$sku."<br/>Record updated successfully<br/>";

                }

                else

                {

                    $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "Entry Insert: Entry Created"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                    $update_arr["$sku"]['error_text'] .= "Entry Insert: Entry Created";

                    echo "SKU : ".$sku."<br/>Record updated successfully<br/>";

                }

            }

        }

        else 

        {  

            foreach($feed->Message->ProcessingReport as $rep)

            {
                $sku = $rep->AdditionalInfo->SKU;
                if(!$sku || $sku==""){$sku = $rows_submission_list['sku'];}

                if($rep->ProcessingSummary->MessagesProcessed == $rep->ProcessingSummary->MessagesSuccessful && $rows_submission_list['feed_type'] == 'Product Posting')

                {                

                    $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "<br/>'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                    $update_arr["$sku"]['error_text'] = "Entry Insert: Entry Created";  

                    echo "SKU : ".$sku."<br/>Record updated successfully<br/>";

                }

                else if($rep->ProcessingSummary->MessagesProcessed == $rep->ProcessingSummary->MessagesSuccessful && $rows_submission_list['feed_type'] == 'Entry Removal')

                {                

                    $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "<br/>'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                    $update_arr["$sku"]['error_text'] = "<br>".$rows_submission_list['feed_type']." : Removal Successful"; 

                    echo "SKU : ".$sku."<br/>Record updated successfully<br/>";

                }

            }

        }

        sleep(2);

        fclose($file_handle);

        unlink($file.'.xml');

    }    

}



function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request,$parameters) 

{

    try 

    {        
         $response = $service->getFeedSubmissionResult($request);
         return $response;              

    } 

    catch (MarketplaceWebService_Exception $ex) 

    {

         echo "Caught Exception: ",$ex->getMessage(),"<br/>";

         sleep(30);

         invokeGetFeedSubmissionResult($service, $request,$parameters);

     }

 }    

 

$api = new TDAPI();

$login = $api->login();

$dataset = $api->GetSchema("Amazon Entry",array('recordHistory','isReadyForUpdate?','isReadyForDeletion?','ASIN','amazonErrorReason','isVisibleOnAmazon?'));



foreach($update_arr as $key=>$updates)

{
    $sku = $key;
    $product_id = $TDProductId[$sku];

    if($product_id)

    {        

        $updates['error_text'] = trim(preg_replace('/\s+/',' ', $updates['error_text']));

        

        $mysqli->query('UPDATE ready_for_amazon SET amazonErrorReason = "'.$updates['error_text'].'",recordHistory = "'.$updates['error_text'].'" WHERE `@id` = "'.$product_id.'"');

        $dataset->Rows[0]["@id"] = $product_id;

        $dataset->Rows[0]["amazonErrorReason"] = trim($updates['error_text']);

        $dataset->Rows[0]["isVisibleOnAmazon?"] = 0;

        //$dataset->Rows[0]["ASIN"] = '';

        $dataset->Rows[0]["isReadyForUpdate?"] = 0;

        $dataset->Rows[0]["isReadyForDeletion?"] = 0;

        $dataset->Rows[0]["recordHistory"] = trim($updates['error_text']);

        $update_arr = $api->Upsert("Amazon Entry", $dataset);

        echo "<pre>";

        print_r($update_arr);

        echo "SKU: ".$sku."<br/>Team desk details updated successfully for id: ".$product_id."<br/>";

    }

}