<?php

include 'uk_config.php';

error_reporting(0);

ini_set('max_execution_time', 0);

$serviceUrl = "https://mws.amazonservices.co.uk";

$config = array (

  'ServiceURL' => $serviceUrl,

  'ProxyHost' => null,

  'ProxyPort' => -1,

  'MaxErrorRetry' => 3,

);



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



$submission_list = $mysqli->query('SELECT id, product_id, sku, feed_type, feed_id FROM error_reporting WHERE processed="0" AND feed_type = "Entry Removal" AND `isAmazonUKProduct?` = "1"');



$error_desc = '';

$update_arr = array();



while($rows_submission_list = $submission_list->fetch_array(MYSQLI_ASSOC))

{

    $DATE_FORMAT            = 'Y-m-d\TH:i:s\Z';

    $AWS_ACCESS_KEY_ID      = AWS_ACCESS_KEY_ID;

    $AWS_SECRET_ACCESS_KEY  = AWS_SECRET_ACCESS_KEY;

    $APPLICATION_NAME       = APPLICATION_NAME;

    $APPLICATION_VERSION    = APPLICATION_VERSION;

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

    $file_handle = fopen('report.xml', 'w');

    

    $feed_id = $rows_submission_list['feed_id'];

    $parameters = array (

    'Merchant' => $MERCHANT_ID,

    'FeedSubmissionId' => $feed_id,

    'FeedSubmissionResult' => $file_handle);

    

    $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

    $response = invokeGetFeedSubmissionResult($service, $request,$parameters);

    sleep(2);

    $feed = simplexml_load_file('report.xml');

    

    $error_desc = '';



    if($feed->Message->ProcessingReport->Result)

    {

        foreach($feed->Message->ProcessingReport->Result as $rep)

        {   

            if($rep->ResultCode == 'Error')

            {

                $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                $update_arr[$rows_submission_list['product_id']]['error_text'] .= "<br>".$rows_submission_list['feed_type']." : ".$rep->ResultDescription;

                echo "Record updated successfully<br/>";

            }

	    else if($rep->ResultCode == 'Error')

            {

                $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "<br/>'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                $update_arr[$rows_submission_list['product_id']]['error_text'] .= "<br>".$rows_submission_list['feed_type']." : ".$rep->ResultDescription;

                echo "SKU : ",$rows_submission_list['product_id'],"<br/>Record updated successfully<br/>";

            }

            else

            {

                $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, ""), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                $update_arr[$rows_submission_list['product_id']]['error_text'] .= "";

                echo "SKU : ",$rows_submission_list['product_id'],"<br/>Record updated successfully<br/>";

            }

        }

    }

    else 

    {

        foreach($feed->Message->ProcessingReport as $rep)

        {

            if($rep->ProcessingSummary->MessagesProcessed == $rep->ProcessingSummary->MessagesSuccessful && $rows_submission_list['feed_type'] == 'Entry Removal')

            {                

                $mysqli->query('UPDATE error_reporting SET error_desc = CONCAT(error_desc, "<br/>'.$rep->ResultDescription.'"), processed = "1" WHERE feed_id = "'.$feed_id.'"');

                $update_arr[$rows_submission_list['product_id']]['error_text'] .= "<br>".$rows_submission_list['feed_type']." : Removal Successful";

                echo "SKU : ",$rows_submission_list['product_id'],"<br/>Record updated successfully<br/>";

            }

        }

    }

    sleep(2);

    fclose($file_handle);    

}



function invokeGetFeedSubmissionResult(MarketplaceWebService_Interface $service, $request,$parameters)

{

    try 

    {        

        $response = $service->getFeedSubmissionResult($request);              

    } 

    catch (MarketplaceWebService_Exception $ex) 

    {

         echo "Caught Exception: " ,$ex->getMessage(),"<br>";

         sleep(5);

         invokeGetFeedSubmissionResult($service, $request,$parameters);

    }

 }

 

$api = new TDAPI();

$login = $api->login();

$dataset = $api->GetSchema("Amazon Entry",array('recordHistory','isReadyForUpdateToUkAmz?','isReadyForDeletionToUkAmz?','UK Amz ASIN','amazonErrorReason','isVisibleOnAmazonUK?'));



foreach($update_arr as $key=>$updates)

{    

    $product_id = explode('|', $key);

    $product_id = (isset($product_id[0])&& $product_id[0]!='') ? $product_id[0] : '';

    if($product_id)

    {        

        $updates['error_text'] = trim(preg_replace('/\s+/',' ', $updates['error_text']));        

        $mysqli->query('UPDATE ready_for_amazon SET amazonErrorReason = "'.$updates['error_text'].'" WHERE `@id` = "'.$product_id.'"');

        $dataset->Rows[0]["@id"] = $product_id;

        $dataset->Rows[0]["amazonErrorReason"] = trim($updates['error_text']);

        $dataset->Rows[0]["isVisibleOnAmazonUK?"] = 0;

        $dataset->Rows[0]["UK Amz ASIN"] = '';

        $dataset->Rows[0]["isReadyForUpdateToUkAmz?"] = 0;

        $dataset->Rows[0]["isReadyForDeletionToUkAmz?"] = 0;

        $dataset->Rows[0]["recordHistory"] = $updates['error_text'];

        $api->Upsert("Amazon Entry", $dataset);

        echo "Team desk details updated successfully for id: ",$product_id,"<br/>";        

    }

}