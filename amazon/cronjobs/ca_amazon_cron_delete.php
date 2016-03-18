<?php

include 'ca_config.php';

error_reporting(E_ALL);

ini_set('max_execution_time', 0);



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



$sql = 'SELECT `@id`, `Product - SKU`, `sku`, `parentage` FROM ready_for_deletion WHERE `isReadyForDeletionToCanAmz?` = "1"';

if(isset($_REQUEST['id']) && $_REQUEST['id'] != '')

    $sql .= 'AND `@id` = "'.trim($_REQUEST['id']).'"';



$query = $mysqli->query($sql);



$key = 0;

$feed_arr = array();



while($feeds = $query->fetch_array(MYSQLI_ASSOC))

{

    $feed_arr[$key]['product_id']   = (isset($feeds['@id']) && $feeds['@id'] != '') ? $feeds['@id'] : $feeds['@id'];

    $feed_arr[$key]['SKU']          = (isset($feeds['sku']) && $feeds['sku'] != '') ? $feeds['sku'] : $feeds['Product - SKU'];

    $feed_arr[$key]['parentage']    = (isset($feeds['parentage']) && $feeds['parentage'] != '') ? $feeds['parentage'] : '';

    if($feed_arr[$key]['parentage'] == 'parent')

    {

        $sql_child = 'SELECT `@id`, `Product - SKU`, `sku`, `parentage` FROM ready_for_deletion WHERE `parent-sku` = "'.$feed_arr[$key]['SKU'].'" OR `Related Amazon entry` = "'.$feed_arr[$key]['SKU'].'" OR `Amazon Entry sku` = "'.$feed_arr[$key]['SKU'].'"';

        $query_child = $mysqli->query($sql_child);

        $child = 0;

        while($feeds_child = $query_child->fetch_array(MYSQLI_ASSOC))

        {

            $feed_arr[$key]['variations'][$child]['product_id'] = (isset($feeds_child['@id']) && $feeds_child['@id'] != '') ? $feeds_child['@id'] : $feeds_child['@id'];

            $feed_arr[$key]['variations'][$child]['SKU']        = (isset($feeds_child['sku']) && $feeds_child['sku'] != '') ? $feeds_child['sku'] : $feeds_child['Product - SKU'];

            $child++;

        }

    }

    $key ++;

}

if($feed_arr)

{

    foreach ($feed_arr as $product_feed)

    {    

        delete_inv_amzn($product_feed);

    }

}

else

    echo "No records for deletion";



function delete_inv_amzn($product_feed)

{	



    $serviceUrl = SERVICE_URL;



    $config = array (

      'ServiceURL' => $serviceUrl,

      'ProxyHost' => null,

      'ProxyPort' => -1,

      'MaxErrorRetry' => 3,

    );                    

    $service = new MarketplaceWebService_Client(

            AWS_ACCESS_KEY_ID, 

            AWS_SECRET_ACCESS_KEY, 

            $config,

            APPLICATION_NAME,

            APPLICATION_VERSION);        



    $feed = "<?xml version='1.0' encoding='UTF-8'?>

                <AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>

                    <Header>

                            <DocumentVersion>1.02</DocumentVersion>

                            <MerchantIdentifier>M_TOPSTECHNO_1216044</MerchantIdentifier>

                    </Header>

                    <MessageType>Product</MessageType>

                     <PurgeAndReplace>false</PurgeAndReplace>

                     <Message>

                            <MessageID>1</MessageID>

                            <OperationType>Delete</OperationType>

                            <Product>

                                    <SKU>".$product_feed['SKU']."</SKU>

                            </Product>

                    </Message>";

    if(isset($product_feed['variations']) && count($product_feed['variations']))

    {

        foreach($product_feed['variations'] as $var_key=>$var_val)

        {

            $msgid = $var_key+2;

            $feed .= "<Message>

                        <MessageID>".$msgid."</MessageID>

                        <OperationType>Delete</OperationType>

                        <Product>

                            <SKU>".$var_val['SKU']."</SKU>

                        </Product>

                </Message>";   

        }

    }

    $feed .= "</AmazonEnvelope>";

    

    $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

    $tmpfname = tempnam("/tmp", "FOO");

    $feedHandle = fopen($tmpfname,'rw+');

    fwrite($feedHandle, $feed);

    rewind($feedHandle);



    $request = new MarketplaceWebService_Model_SubmitFeedRequest();

    $request->setMerchant(MERCHANT_ID);

    $request->setMarketplaceIdList($marketplaceIdArray);

    $request->setFeedType('_POST_PRODUCT_DATA_');                            

    $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));

    rewind($feedHandle);

    $request->setPurgeAndReplace(false);

    $request->setFeedContent($feedHandle);

    rewind($feedHandle);

    invokeSubmitFeed_delete($service, $request,$product_feed);

    @fclose($feedHandle);

}

    

function invokeSubmitFeed_delete(MarketplaceWebService_Interface $service, $request,$product_feed) 

{ 

    try 

    {

        $response = $service->submitFeed($request);

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $mysqli->query('INSERT INTO error_reporting VALUES (null, "'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Entry Removal","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"1","")');

        if ($response->isSetSubmitFeedResult()) 

        {

            $submitFeedResult = $response->getSubmitFeedResult();

            if ($submitFeedResult->isSetFeedSubmissionInfo()) 

            {

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();

                if($feedSubmissionInfo->isSetFeedSubmissionId())

                {

                    if($response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId != '')

                        echo json_encode(array('error'=>"0","msg"=>"Product Deleted Successfully from Amazon wait for status.."));                        

                    else

                        echo json_encode(array('error'=>"1","msg"=>"Product not Deleted from Amazon Error: Requestid not found please solve errors and resubmit feed."));                        

                }                    

            }

            else

            {

                echo json_encode(array('error'=>"1","msg"=>"Product not Deleted from Amazon Error: Requestid not found please solve errors and resubmit."));                    

            }                

        } 

        else

        {

            echo json_encode(array('error'=>"1","msg"=>"Product not Deleted from Amazon Error: Requestid not found please solve errors and resubmit."));

        }

    } 

    catch (MarketplaceWebService_Exception $ex) 

    {

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);        

        $mysqli->query('INSERT INTO error_reporting VALUES (null, "'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Entry Removal",null,"503 error on product delete call","1",null,"1","")');

        echo("Caught Exception: " . $ex->getMessage() . "\n");

        sleep(5);

        invokeSubmitFeed_delete($service, $request, $product_feed);

    }	

}

