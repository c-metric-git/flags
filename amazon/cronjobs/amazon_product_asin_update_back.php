<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('max_execution_time', 0);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

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
$sql = 'SELECT `@id`,`sku` FROM ready_for_amazon WHERE is_visible = "1" AND `isVisibleOnAmazon?` <> "1"';

if(isset($_REQUEST['id']) && $_REQUEST['id'] != '')
    $sql .= 'AND `@id` = "'.trim($_REQUEST['id']).'"';

$query = $mysqli->query($sql);
$key = 0;
$feed_arr = array();

while($feeds = $query->fetch_array(MYSQLI_ASSOC))
{
    $feed_arr[$key]['@id'] = (isset($feeds['@id']) && $feeds['@id'] != '') ? $feeds['@id'] : '';
    $feed_arr[$key]['sku'] = (isset($feeds['sku']) && $feeds['sku'] != '') ? $feeds['sku'] : '';
    $sql_var = 'SELECT `@id`,`sku` FROM ready_for_amazon WHERE `parent-sku` = "'.$feeds['sku'].'"';
    $query_var = $mysqli->query($sql_var);
    $count = 0;
    while($feeds_var = $query_var->fetch_array(MYSQLI_ASSOC))
    {
        $feed_arr[$key]['variations'][$count]['@id'] = (isset($feeds_var['@id']) && $feeds_var['@id'] != '') ? $feeds_var['@id'] : '';
        $feed_arr[$key]['variations'][$count]['sku'] = (isset($feeds_var['sku']) && $feeds_var['sku'] != '') ? $feeds_var['sku'] : '';
        $count++;
    }
    $key++;
}

$api = new TDAPI();
$login = $api->login();
$dataset = $api->GetSchema("Amazon Entry",array('isVisibleOnAmazon?','isReadyForUpdate?','recordHistory','amazonErrorReason','ASIN'));

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

$request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest();
$request->setSellerId(MERCHANT_ID);
$request->setMarketplaceId(MARKETPLACE_ID);

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
                    $request->setSellerSKUList($variations_val['sku']);                
                    $asin = invokeGetMyPriceForSKU($service, $request);
                    if($asin)
                    {
                        $query = $mysqli->query('UPDATE ready_for_amazon SET ASIN = "'.$asin.'", `recordHistory` = "Entry Insert: Entry Created", `amazonErrorReason` = "", `isVisibleOnAmazon?` = "1" WHERE `@id` = "'.$val['@id'].'"');
                        $dataset->Rows[0]["@id"] = $val['@id'];
                        $dataset->Rows[0]["amazonErrorReason"] = "";
                        $dataset->Rows[0]["isVisibleOnAmazon?"] = TRUE;
                        $dataset->Rows[0]["recordHistory"] = "Entry Insert: Entry Created";
                        $dataset->Rows[0]["isReadyForUpdate?"] = FALSE;
                        $dataset->Rows[0]["ASIN"] = $asin;
                        $api->Upsert("Amazon Entry", $dataset);
                        echo "Record with id:- ",$val['@id']," updated successfully.<br/>";
                    }
                }
            }
        }
        if(isset($val['sku']))
        {
            $request->setSellerSKUList($val['sku']); 
            $asin = invokeGetMyPriceForSKU($service, $request);            
            if($asin)
            {
                $query = $mysqli->query('UPDATE ready_for_amazon SET ASIN = "'.$asin.'", `recordHistory` = "Entry Insert: Entry Created", `amazonErrorReason` = "", `isVisibleOnAmazon?` = "1" WHERE `@id` = "'.$val['@id'].'"');                
                $dataset->Rows[0]["@id"] = $val['@id'];
                $dataset->Rows[0]["amazonErrorReason"] = "";
                $dataset->Rows[0]["isVisibleOnAmazon?"] = TRUE;
                $dataset->Rows[0]["recordHistory"] = "Entry Insert: Entry Created";
                $dataset->Rows[0]["isReadyForUpdate?"] = FALSE;
                $dataset->Rows[0]["ASIN"] = $asin;
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
                echo "Record with id:- ",$val['@id']," updated successfully.<br/>";                
            }
        }    
    }
}
else
    echo "No records to update";

function invokeGetMyPriceForSKU(MarketplaceWebServiceProducts_Interface $service, $request) 
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
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    }
}