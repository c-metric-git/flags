<?php

//ini_set("display_errors",1); 

global $all_scripts,$base_path;

                             

if($all_scripts != 'Yes') {

    include 'config.php';

} 

error_reporting(E_ALL);

ini_set('max_execution_time', 0);


// code added by lav butala
$TDObject = new TDDataGeneral();
$TDObject->Login();
   

function __autoload($className) 

{   

    global $all_scripts,$base_path;

    if($all_scripts != 'Yes') {   

        $_SERVER['DOCUMENT_ROOT'] = "/home/stripedsocks/public_html";

        $filePath = $_SERVER['DOCUMENT_ROOT'] .'/'.FOLDER_NAME. '/API/amzn/' . str_replace('_', '/', $className) . '.php';    

    }

    else {    

       $filePath = $_SERVER['DOCUMENT_ROOT'] .'/'.FOLDER_NAME. '/API/amzn/' . str_replace('_', '/', $className) . '.php'; 

    }    

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
  
$serviceUrl = SERVICE_URL;

$config = array(

    'ServiceURL' => $serviceUrl,

    'ProxyHost' => null,

    'ProxyPort' => -1,

    'MaxErrorRetry' => 3,

);

$service = new MarketplaceWebService_Client(AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);



$sql = 'SELECT * FROM ready_for_amazon WHERE is_visible = "1" AND `isReadyForUpdate?` = "1"';



if(isset($_REQUEST['id']) && $_REQUEST['id'] != '')

    $sql .= 'AND `@id` = "'.trim($_REQUEST['id']).'"';



$query = $mysqli->query($sql);



$key = 0;

$feed_arr = array();



while($feeds = $query->fetch_array(MYSQLI_ASSOC))

{    

    $feed_arr[$key]['product_id']       = (isset($feeds['@id']) && $feeds['@id'] != '') ? $feeds['@id'] : $feeds['@id'];

    $feed_arr[$key]['SKU']              = (isset($feeds['sku']) && $feeds['sku'] != '') ? $feeds['sku'] : $feeds['Product - SKU'];

    $feed_arr[$key]['ASIN']             = (isset($feeds['ASIN']) && $feeds['ASIN'] != '') ? $feeds['ASIN'] : '';

    $feed_arr[$key]['model']             = (isset($feeds['Style_Number']) && $feeds['Style_Number'] != '') ? $feeds['Style_Number'] : ''; 

    $feed_arr[$key]['launch_date']      = (isset($feeds['launch-date']) && $feeds['launch-date'] != '0.000000-00-00') ? $feeds['launch-date'] : date('Y-m-d').'T11:00:01';

    $feed_arr[$key]['product_tax_code'] = (isset($feeds['Amazon category - product-tax-code']) && $feeds['Amazon category - product-tax-code'] != '') ? $feeds['Amazon category - product-tax-code'] : '';

    $feed_arr[$key]['item_condition']   = (isset($feeds['condition-type']) && $feeds['condition-type'] != '') ? $feeds['condition-type'] : 'New';    

    $feed_arr[$key]['Title']            = (isset($feeds['title']) && $feeds['title'] != '') ? $feeds['title'] : $feeds['title'];    

    $feed_arr[$key]['Brand']            = (isset($feeds['brand']) && $feeds['brand'] != '') ? $feeds['brand'] : '';

    $feed_arr[$key]['Description']      = (isset($feeds['description']) && $feeds['description'] != '') ? $feeds['description'] : $feeds['product-description'];

    $feed_arr[$key]['BulletPoint1']     = (isset($feeds['bullet-point1']) && $feeds['bullet-point1'] != '') ? $feeds['bullet-point1'] : '';

    $feed_arr[$key]['BulletPoint2']     = (isset($feeds['bullet-point2']) && $feeds['bullet-point2'] != '') ? $feeds['bullet-point2'] : '';

    $feed_arr[$key]['BulletPoint3']     = (isset($feeds['bullet-point3']) && $feeds['bullet-point3'] != '') ? $feeds['bullet-point3'] : '';

    $feed_arr[$key]['BulletPoint4']     = (isset($feeds['bullet-point4']) && $feeds['bullet-point4'] != '') ? $feeds['bullet-point4'] : '';

    $feed_arr[$key]['BulletPoint5']     = (isset($feeds['bullet-point5']) && $feeds['bullet-point5'] != '') ? $feeds['bullet-point5'] : '';

    $feed_arr[$key]['MSRP']             = (isset($feeds['item-price']) && $feeds['item-price'] != '') ? $feeds['item-price'] : '';

    $feed_arr[$key]['StandardPrice']    = (isset($feeds['item-price']) && $feeds['item-price'] != '') ? $feeds['item-price'] : '';

    $feed_arr[$key]['Manufacturer']     = (isset($feeds['manufacturer']) && $feeds['manufacturer'] != '') ? $feeds['manufacturer'] : '';

    $feed_arr[$key]['MfrPartNumber']    = (isset($feeds['mfr-part-number']) && $feeds['mfr-part-number'] != '') ? $feeds['mfr-part-number'] : '';

    $feed_arr[$key]['amzn_UnitQTY']     = (isset($feeds['Product - Quantity Available']) && $feeds['Product - Quantity Available'] != '') ? (int)$feeds['Product - Quantity Available'] : (($feeds['quantity'])? (int)$feeds['quantity'] : '0');

    $feed_arr[$key]['upc']              = (isset($feeds['Product - UPC Code']) && $feeds['Product - UPC Code'] != '') ? $feeds['upc code'] : '';

    $feed_arr[$key]['main-image-url']   = (isset($feeds['main-image-url']) && $feeds['main-image-url'] != '') ? $feeds['main-image-url'] : '';

    $feed_arr[$key]['product_type']     = (isset($feeds['Amazon category - Type']) && $feeds['Amazon category - Type'] != '') ? $feeds['Amazon category - Type'] : $feeds['Type2'];

    $feed_arr[$key]['product_theme']    = (isset($feeds['Amazon category - theme']) && $feeds['Amazon category - theme'] != '') ? $feeds['Amazon category - theme'] : '';    

    $feed_arr[$key]['department']       = (isset($feeds['Amazon category - department']) && $feeds['Amazon category - department'] != '') ? $feeds['Amazon category - department'] : '';

    $feed_arr[$key]['product-parent']   = (isset($feeds['Amazon Category - ClothingType']) && $feeds['Amazon Category - ClothingType'] != '') ? $feeds['Amazon Category - ClothingType'] : ((isset($feeds['product_type']) && $feeds['product_type'] != '') ? $feeds['product_type'] : 'Accessory');    

    $feed_arr[$key]['item-type']        = (isset($feeds['item-type']) && $feeds['item-type'] != '') ? $feeds['item-type'] : $feeds['Amazon category - Type2'];

    $feed_arr[$key]['search_terms1']                 = (isset($feeds['Amazon category - search-terms1']) && $feeds['Amazon category - search-terms1'] != '') ? $feeds['Amazon category - search-terms1'] : $feeds['search-terms1'];

    $feed_arr[$key]['search_terms2']                 = (isset($feeds['Amazon category - search-terms2']) && $feeds['Amazon category - search-terms2'] != '') ? $feeds['Amazon category - search-terms2'] : $feeds['search-terms2'];

    $feed_arr[$key]['search_terms3']                 = (isset($feeds['Amazon category - search-terms3']) && $feeds['Amazon category - search-terms3'] != '') ? $feeds['Amazon category - search-terms3'] : $feeds['search-terms3'];

    $feed_arr[$key]['search_terms4']                 = (isset($feeds['Amazon category - search-terms4']) && $feeds['Amazon category - search-terms4'] != '') ? $feeds['Amazon category - search-terms4'] : $feeds['search-terms4'];

    $feed_arr[$key]['search_terms5']                 = (isset($feeds['Amazon category - search-terms5']) && $feeds['Amazon category - search-terms5'] != '') ? $feeds['Amazon category - search-terms5'] : $feeds['search-terms5'];

    

    $feed_arr[$key]['platinum_1']                    = (isset($feeds['platinum-1']) && $feeds['platinum-1'] != '') ? $feeds['platinum-1'] : '';

    $feed_arr[$key]['platinum_2']                    = (isset($feeds['platinum-2']) && $feeds['platinum-2'] != '') ? $feeds['platinum-2'] : '';

    $feed_arr[$key]['platinum_3']                    = (isset($feeds['platinum-3']) && $feeds['platinum-3'] != '') ? $feeds['platinum-3'] : '';

    $feed_arr[$key]['platinum_4']                    = (isset($feeds['platinum-4']) && $feeds['platinum-4'] != '') ? $feeds['platinum-4'] : '';

    $feed_arr[$key]['platinum_5']                    = (isset($feeds['platinum-5']) && $feeds['platinum-5'] != '') ? $feeds['platinum-5'] : '';

    $feed_arr[$key]['material']                      = (isset($feeds['material-fabric1 entered']) && $feeds['material-fabric1 entered'] != '') ? $feeds['material-fabric1 entered'] : '';    

    $feed_arr[$key]['product_size']                  = (isset($feeds['Product - Size']) && $feeds['Product - Size'] != '') ? $feeds['Product - Size'] : $feeds['size'];

    $feed_arr[$key]['product_color']                 = (isset($feeds['color']) && $feeds['color'] != '') ? $feeds['color'] : $feeds['color-map'];

    $feed_arr[$key]['shipping_weight']               = (isset($feeds['shipping-weight']) && $feeds['shipping-weight'] != '') ? $feeds['shipping-weight'] : '1';

    $feed_arr[$key]['shipping_weight_unit_measure']  = (isset($feeds['shipping-weight-unit-measure']) && $feeds['shipping-weight-unit-measure'] != '') ? $feeds['shipping-weight-unit-measure'] : 'LB';

    $feed_arr[$key]['target_audience1']              = (isset($feeds['target-audience1']) && $feeds['target-audience1'] != '') ? $feeds['target-audience1'] : '';

    $feed_arr[$key]['target_audience2']              = (isset($feeds['target-audience2']) && $feeds['target-audience2'] != '') ? $feeds['target-audience2'] : '';

    $feed_arr[$key]['target_audience3']              = (isset($feeds['target-audience3']) && $feeds['target-audience3'] != '') ? $feeds['target-audience3'] : '';

    $feed_arr[$key]['age_recommended']               = (isset($feeds['minimum-manufacturer-age-recommended']) && $feeds['minimum-manufacturer-age-recommended'] != '') ? $feeds['minimum-manufacturer-age-recommended'] : '';

    $feed_arr[$key]['age_unit_of_measurement']       = (isset($feeds['minimum-manufacturer-age-recommended-unit-of-meas']) && $feeds['minimum-manufacturer-age-recommended-unit-of-meas'] != '') ? strtolower($feeds['minimum-manufacturer-age-recommended-unit-of-meas']) : 'months';

    $feed_arr[$key]['parentage']                     = (isset($feeds['parentage']) && $feeds['parentage'] != '') ? $feeds['parentage'] : $feeds['parent-child'];

    $feed_arr[$key]['variation_theme']               = (isset($feeds['Amazon entry - variation-theme entered']) && $feeds['Amazon entry - variation-theme entered'] != '') ? $feeds['Amazon entry - variation-theme entered'] : 'Size';

    $feed_arr[$key]['variation_theme']               = (isset($feeds['variation-theme']) && $feeds['variation-theme'] != '') ? $feeds['variation-theme'] : ( isset($feeds['Amazon entry - variation-theme entered']) ? $feeds['Amazon entry - variation-theme entered'] : 'Size');

    

    

    if($feed_arr[$key]['product-parent'] == 'BeautyMisc')

        $feed_arr[$key]['product_type'] = 'Beauty';

    

    //Toys should be ToysBaby for amazon

    if($feed_arr[$key]['product_type'] == 'Toys')

        $feed_arr[$key]['product_type'] = 'ToysBaby';

    

    if($feed_arr[$key]['product_type'] == 'Makeup')

        $feed_arr[$key]['product_type'] = 'Beauty';

    

    //For getting variation childs    

    if($feed_arr[$key]['parentage'] == 'parent')

    {

        $variation = 'SELECT * FROM ready_for_amazon_child WHERE 1 ';

        $variation .= ' AND `Related Amazon entry` = "'.$feed_arr[$key]['SKU'].'" OR `parent-sku` = "'.$feed_arr[$key]['SKU'].'" OR `tmpParent` = "'.$feed_arr[$key]['SKU'].'"';

        $variations = $mysqli->query($variation);

        $count = 0;

        while($var = $variations->fetch_array(MYSQLI_ASSOC))

        {            

            $feed_arr[$key]['variations'][$count]['product_id']                    = (isset($var['@id']) && $var['@id'] != '') ? $var['@id'] : $var['@id'];

            $feed_arr[$key]['variations'][$count]['SKU']                           = (isset($var['sku']) && $var['sku'] != '') ? $var['sku'] : $var['Product - SKU'];

            $feed_arr[$key]['variations'][$count]['Title']                         = (isset($var['title']) && $var['title'] != '') ? $var['title'] : $var['title'];

            $feed_arr[$key]['variations'][$count]['Brand']                         = (isset($var['brand']) && $var['brand'] != '') ? $var['brand'] : '';

            $feed_arr[$key]['variations'][$count]['model']                         = (isset($var['Style_Number']) && $var['Style_Number'] != '') ? $var['Style_Number'] : '';    

            $feed_arr[$key]['variations'][$count]['Description']                   = (isset($var['description']) && $var['description'] != '') ? $var['description'] : $var['product-description'];

            $feed_arr[$key]['variations'][$count]['BulletPoint1']                  = (isset($var['bullet-point1']) && $var['bullet-point1'] != '') ? $var['bullet-point1'] : '';

            $feed_arr[$key]['variations'][$count]['BulletPoint2']                  = (isset($var['bullet-point2']) && $var['bullet-point2'] != '') ? $var['bullet-point2'] : '';

            $feed_arr[$key]['variations'][$count]['BulletPoint3']                  = (isset($var['bullet-point3']) && $var['bullet-point3'] != '') ? $var['bullet-point3'] : '';

            $feed_arr[$key]['variations'][$count]['BulletPoint4']                  = (isset($var['bullet-point4']) && $var['bullet-point4'] != '') ? $var['bullet-point4'] : '';

            $feed_arr[$key]['variations'][$count]['BulletPoint5']                  = (isset($var['bullet-point5']) && $var['bullet-point5'] != '') ? $var['bullet-point5'] : '';

            $feed_arr[$key]['variations'][$count]['MSRP']                          = (isset($var['item-price']) && $var['item-price'] != '') ? $var['item-price'] : '';

            $feed_arr[$key]['variations'][$count]['StandardPrice']                 = (isset($var['item-price']) && $var['item-price'] != '') ? $var['item-price'] : '';

            $feed_arr[$key]['variations'][$count]['Manufacturer']                  = (isset($var['manufacturer']) && $var['manufacturer'] != '') ? $var['manufacturer'] : '';

            $feed_arr[$key]['variations'][$count]['MfrPartNumber']                 = (isset($var['mfr-part-number']) && $var['mfr-part-number'] != '') ? $var['mfr-part-number'] : '';

            $feed_arr[$key]['variations'][$count]['amzn_UnitQTY']                  = (isset($var['Product - Quantity Available']) && $var['Product - Quantity Available'] != '') ? (int)$var['Product - Quantity Available'] : '0';

            $feed_arr[$key]['variations'][$count]['upc']                           = (isset($var['Product - UPC Code']) && $var['Product - UPC Code'] != '') ? $var['upc code'] : '';

            $feed_arr[$key]['variations'][$count]['main-image-url']                = (isset($var['main-image-url']) && $var['main-image-url'] != '') ? $var['main-image-url'] : '';

            $feed_arr[$key]['variations'][$count]['product_type']                  = (isset($var['Amazon category - Type']) && $var['Amazon category - Type'] != '') ? $var['Amazon category - Type'] : $var['Type2'];

            $feed_arr[$key]['variations'][$count]['product_theme']                 = (isset($var['Amazon category - theme']) && $var['Amazon category - theme'] != '') ? $var['Amazon category - theme'] : '';    

            $feed_arr[$key]['variations'][$count]['department']                    = (isset($var['Amazon category - department']) && $var['Amazon category - department'] != '') ? $var['Amazon category - department'] : '';

            $feed_arr[$key]['variations'][$count]['product-parent']                = (isset($feeds['Amazon Category - ClothingType']) && $feeds['Amazon Category - ClothingType'] != '') ? $feeds['Amazon Category - ClothingType'] : ((isset($feeds['product_type']) && $feeds['product_type'] != '') ? $feeds['product_type'] : 'Accessory');

            $feed_arr[$key]['variations'][$count]['item-type']                     = (isset($var['item-type']) && $var['item-type'] != '') ? $var['item-type'] : $var['Amazon category - Type2'];

            $feed_arr[$key]['variations'][$count]['platinum_1']                            = (isset($var['platinum-1']) && $var['platinum-1'] != '') ? $var['platinum-1'] : '';

            $feed_arr[$key]['variations'][$count]['platinum_2']                            = (isset($var['platinum-2']) && $var['platinum-2'] != '') ? $var['platinum-2'] : '';

            $feed_arr[$key]['variations'][$count]['platinum_3']                            = (isset($var['platinum-3']) && $var['platinum-3'] != '') ? $var['platinum-3'] : '';

            $feed_arr[$key]['variations'][$count]['platinum_4']                            = (isset($var['platinum-4']) && $var['platinum-4'] != '') ? $var['platinum-4'] : '';

            $feed_arr[$key]['variations'][$count]['platinum_5']                            = (isset($var['platinum-5']) && $var['platinum-5'] != '') ? $var['platinum-5'] : '';

            $feed_arr[$key]['variations'][$count]['search_terms1']                 = (isset($var['Amazon category - search-terms1']) && $var['Amazon category - search-terms1'] != '') ? $var['Amazon category - search-terms1'] : $var['search-terms1'];

            $feed_arr[$key]['variations'][$count]['search_terms2']                 = (isset($var['Amazon category - search-terms2']) && $var['Amazon category - search-terms2'] != '') ? $var['Amazon category - search-terms2'] : $var['search-terms2'];

            $feed_arr[$key]['variations'][$count]['search_terms3']                 = (isset($var['Amazon category - search-terms3']) && $var['Amazon category - search-terms3'] != '') ? $var['Amazon category - search-terms3'] : $var['search-terms3'];

            $feed_arr[$key]['variations'][$count]['search_terms4']                 = (isset($var['Amazon category - search-terms4']) && $var['Amazon category - search-terms4'] != '') ? $var['Amazon category - search-terms4'] : $var['search-terms4'];

            $feed_arr[$key]['variations'][$count]['search_terms5']                 = (isset($var['Amazon category - search-terms5']) && $var['Amazon category - search-terms5'] != '') ? $var['Amazon category - search-terms5'] : $var['search-terms5'];

            $feed_arr[$key]['variations'][$count]['material']                      = (isset($var['Amazon category - search-terms5']) && $var['material-fabric1 entered'] != '') ? $var['material-fabric1 entered'] : '';    

            $feed_arr[$key]['variations'][$count]['product_size']                  = (isset($var['Product - Size']) && $var['Product - Size'] != '') ? $var['Product - Size'] : $var['size'];

            $feed_arr[$key]['variations'][$count]['product_color']                 = (isset($var['color']) && $var['color'] != '') ? $var['color'] : $var['color-map'];

            $feed_arr[$key]['variations'][$count]['shipping_weight']               = (isset($var['shipping-weight']) && $var['shipping-weight'] != '') ? $var['shipping-weight'] : '1';

            $feed_arr[$key]['variations'][$count]['shipping_weight_unit_measure']  = (isset($var['shipping-weight-unit-measure']) && $var['shipping-weight-unit-measure'] != '') ? $var['shipping-weight-unit-measure'] : 'LB';

            $feed_arr[$key]['variations'][$count]['target_audience1']              = (isset($var['target-audience1']) && $var['target-audience1'] != '') ? $var['target-audience1'] : '';

            $feed_arr[$key]['variations'][$count]['target_audience2']              = (isset($var['target-audience2']) && $var['target-audience2'] != '') ? $var['target-audience2'] : '';

            $feed_arr[$key]['variations'][$count]['target_audience3']              = (isset($var['target-audience3']) && $var['target-audience3'] != '') ? $var['target-audience3'] : '';

            $feed_arr[$key]['variations'][$count]['age_recommended']               = (isset($var['minimum-manufacturer-age-recommended']) && $var['minimum-manufacturer-age-recommended'] != '') ? $var['minimum-manufacturer-age-recommended'] : '';

            $feed_arr[$key]['variations'][$count]['age_unit_of_measurement']       = (isset($var['minimum-manufacturer-age-recommended-unit-of-meas']) && $var['minimum-manufacturer-age-recommended-unit-of-meas'] != '') ? strtolower($var['minimum-manufacturer-age-recommended-unit-of-meas']) : 'months';

            $feed_arr[$key]['variations'][$count]['item_condition']                = (isset($var['condition-type']) && $var['condition-type'] != '') ? $var['condition-type'] : 'New';

            $feed_arr[$key]['variations'][$count]['product_size']                  = (isset($var['size']) && $var['size'] != '') ? $var['size'] : $var['Product - Size'];

            $feed_arr[$key]['variations'][$count]['product_quantity']              = (isset($var['quantity']) && $var['quantity'] != '') ? (int)$var['quantity'] : '';            

            $count++;

        }

    }

    $key ++;

}


$totalProductEvents = 0; // aded by lav for product event
$sendToProductEvent = array(); // aded by lav for product event

$feedVar = '';

$feed = '<?xml version="1.0" encoding="utf-8" ?>';

$feed .= '<AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>M_TOPSTECHNO_1216044</MerchantIdentifier></Header><MessageType>Product</MessageType><PurgeAndReplace>false</PurgeAndReplace>';

    $msgIdCounter = 0;
    $msgIdCounterVariants = 0;
foreach($feed_arr as $key=>$product_feed)

{
    $msgIdCounter = $msgIdCounter+1;

    $feed .= '<Message><MessageID>'.$msgIdCounter.'</MessageID>';

    $feed .= '<OperationType>Update</OperationType>';

    $feed .= "<Product><SKU>" . $product_feed['SKU'] . "</SKU>";

    

    if($product_feed['ASIN'])

        $feed .= "<StandardProductID><Type>ASIN</Type><Value>".$product_feed['ASIN']."</Value></StandardProductID>";

    else

    {

        if($product_feed['upc'])

            $feed .= "<StandardProductID><Type>UPC</Type><Value>".$product_feed['upc']."</Value></StandardProductID>";

        else

        {

            if(isset($product_feed['variations']) && count($product_feed['variations']))

                $feed .= "";

            else

                $feed .= "<StandardProductID><Type>GTIN</Type><Value>".generate_upc()."</Value></StandardProductID>";

        }

    }

    

    if($product_feed['product_tax_code'])

        $feed .= "<ProductTaxCode>".$product_feed['product_tax_code']."</ProductTaxCode>";

    

    $feed .= "<LaunchDate>".$product_feed['launch_date']."</LaunchDate>";

    $feed .= "<Condition><ConditionType>".$product_feed['item_condition']."</ConditionType></Condition>";

    $feed .= "<DescriptionData><Title>" . $product_feed['Title'] . "</Title>";

    

    if ($product_feed['Brand'] != '')

        $feed .= "<Brand>" . $product_feed['Brand'] . "</Brand>";

    if ($product_feed['Description'] != '')

        $feed .= "<Description>" . strip_tags($product_feed['Description']) . "</Description>";

    if ($product_feed['BulletPoint1'] != '')

        $feed .= "<BulletPoint>" . $product_feed['BulletPoint1'] . "</BulletPoint>";

    if ($product_feed['BulletPoint2'] != '')

        $feed .= "<BulletPoint>" . $product_feed['BulletPoint2'] . "</BulletPoint>";

    if ($product_feed['BulletPoint3'] != '')

        $feed .= "<BulletPoint>" . $product_feed['BulletPoint3'] . "</BulletPoint>";

    if ($product_feed['BulletPoint4'] != '')

        $feed .= "<BulletPoint>" . $product_feed['BulletPoint4'] . "</BulletPoint>";

    if ($product_feed['BulletPoint5'] != '')

        $feed .= "<BulletPoint>" . $product_feed['BulletPoint5'] . "</BulletPoint>";

    

    if ($product_feed['shipping_weight'] != '' && $product_feed['shipping_weight_unit_measure'])

        $feed .= "<ShippingWeight unitOfMeasure='".strtoupper($product_feed['shipping_weight_unit_measure'])."'>".$product_feed['shipping_weight']."</ShippingWeight>";

    

    if ($product_feed['MSRP'] != '')

        $feed .= "<MSRP currency='USD'>" . $product_feed['MSRP'] . "</MSRP>";

    if ($product_feed['Manufacturer'] != '')

        $feed .= "<Manufacturer>" . $product_feed['Manufacturer'] . "</Manufacturer>";

    if ($product_feed['MfrPartNumber'] != '')

        $feed .= "<MfrPartNumber>" . $product_feed['MfrPartNumber'] . "</MfrPartNumber>";

    

    if ($product_feed['search_terms1'] != '')

        $feed .= "<SearchTerms>" . $product_feed['search_terms1'] . "</SearchTerms>";

    

    if ($product_feed['search_terms2'] != '')

        $feed .= "<SearchTerms>" . $product_feed['search_terms2'] . "</SearchTerms>";

    

    if ($product_feed['search_terms3'] != '')

        $feed .= "<SearchTerms>" . $product_feed['search_terms3'] . "</SearchTerms>";

    

    if ($product_feed['search_terms4'] != '')

        $feed .= "<SearchTerms>" . $product_feed['search_terms4'] . "</SearchTerms>";

    

    if ($product_feed['search_terms5'] != '')

        $feed .= "<SearchTerms>" . $product_feed['search_terms5'] . "</SearchTerms>";

    

    if ($product_feed['platinum_1'] != '')

        $feed .= "<PlatinumKeywords>" . $product_feed['platinum_1'] . "</PlatinumKeywords>";

    

    if ($product_feed['platinum_2'] != '')

        $feed .= "<PlatinumKeywords>" . $product_feed['platinum_2'] . "</PlatinumKeywords>";

    

    if ($product_feed['platinum_3'] != '')

        $feed .= "<PlatinumKeywords>" . $product_feed['platinum_3'] . "</PlatinumKeywords>";

    

    if ($product_feed['platinum_4'] != '')

        $feed .= "<PlatinumKeywords>" . $product_feed['platinum_4'] . "</PlatinumKeywords>";

    

    if ($product_feed['platinum_5'] != '')

        $feed .= "<PlatinumKeywords>" . $product_feed['platinum_5'] . "</PlatinumKeywords>";


    /**

    * @desc code added by dhiraj for checking the product parent blank and assigning default to accessory

    */

    if($product_feed['product-parent'] == '') {

        $product_feed['product-parent'] = 'Accessory';

    }

    if($product_feed['item-type'] == '') {

        $product_feed['item-type'] = 'Accessory';

    } 

    /***************** End of code added by dhiraj**********************************/        

    $feed .= "<ItemType>".$product_feed['item-type']."</ItemType>";

    

    if ($product_feed['target_audience1'] != '')

        $feed .= "<TargetAudience>" . $product_feed['target_audience1'] . "</TargetAudience>";

    if ($product_feed['target_audience2'] != '')

        $feed .= "<TargetAudience>" . $product_feed['target_audience3'] . "</TargetAudience>";

    if ($product_feed['target_audience3'] != '')

        $feed .= "<TargetAudience>" . $product_feed['target_audience3'] . "</TargetAudience>";

    

    $feed .= "</DescriptionData>";

    

    $feed .= "<ProductData>";

    $feed .= "<".$product_feed['product_type'].">";

    switch($product_feed['product_type'])

    {

        default:

        case 'Clothing':

        {

            if($product_feed['product_size'] != '' && $product_feed['product_color'] != '')

            {

                $feed .= "<VariationData>

                            <Size>".$product_feed['product_size']."</Size>

                            <Color>".$product_feed['product_color']."</Color>

                         </VariationData>";

            }            

            if(isset($product_feed['variations']) && count($product_feed['variations']))        

                $feed .= "<VariationData><Parentage>parent</Parentage><VariationTheme>".$product_feed['variation_theme']."</VariationTheme></VariationData>";

            $feed .= "<ClassificationData>";

            

            $feed .= "<ClothingType>".$product_feed['product-parent']."</ClothingType>";

            $feed .= "<Department>".$product_feed['department']."</Department>";

            

            if($product_feed['material'] != '')

                $feed .= "<MaterialAndFabric>".$product_feed['material']."</MaterialAndFabric>";

            

            if($product_feed['model'] != '')

                $feed .= "<ModelNumber>".$product_feed['model']."</ModelNumber>";

            //else

                //$feed .= "<MaterialAndFabric>Other</MaterialAndFabric>";

            $feed .= "</ClassificationData>";

            break;

        }

        case 'Toys':

        case 'ToysBaby':

        {

            $feed .= '<ProductType>ToysAndGames</ProductType>';

            if($product_feed['age_recommended']!= '')

            {

                $feed .= '<AgeRecommendation>

                            <MinimumManufacturerAgeRecommended unitOfMeasure="'.$product_feed['age_unit_of_measurement'].'">'.$product_feed['age_recommended'].'</MinimumManufacturerAgeRecommended>

                          </AgeRecommendation>';

            }
            break;

        }

        case 'Beauty':

        case 'Makeup':

        {
            /**
            * @desc code added by dinesh for updating color on amazon
            */
            /*if($product_feed['product_color'] != '')
            {
                $feed .= "<VariationData>
                            <Color>".$product_feed['product_color']."</Color>
                         </VariationData>";
            }*/
            /********************end of code added by dinesh *************/
            $feed .= '<ProductType><BeautyMisc/></ProductType>';

            break;

        }

    }

    $feed .= "</".$product_feed['product_type']."></ProductData></Product></Message>";

    

    if(isset($product_feed['variations']) && count($product_feed['variations']))

    {

        foreach($product_feed['variations'] as $var_key=>$var_val)

        {
            $msgIdCounter = $msgIdCounter+1;

            $feed .= "<Message><MessageID>".$msgIdCounter."</MessageID><OperationType>Update</OperationType>";

            $feed .= "<Product><SKU>".$var_val['SKU']."</SKU>";

            

            if($var_val['upc'] != '')

                $feed .= "<StandardProductID><Type>UPC</Type><Value>".$var_val['upc']."</Value></StandardProductID>";

            else

                $feed .= "<StandardProductID><Type>GTIN</Type><Value>".generate_upc()."</Value></StandardProductID>";

            $feed .= "<LaunchDate>".date('Y-m-d').'T11:00:01'."</LaunchDate>";

            $feed .= "<Condition><ConditionType>".$var_val['item_condition']."</ConditionType></Condition>";

            $feed .= "<DescriptionData>";

            $feed .= "<Title>" . $var_val['Title'] . "</Title>";



            if ($product_feed['Brand'] != '')

            $feed .= "<Brand>" . $product_feed['Brand'] . "</Brand>";



            if ($product_feed['Description'] != '')

                $feed .= "<Description>" . strip_tags($product_feed['Description']) . "</Description>";



            if ($product_feed['BulletPoint1'] != '')

                $feed .= "<BulletPoint>" . $product_feed['BulletPoint1'] . "</BulletPoint>";



            if ($product_feed['BulletPoint2'] != '')

                $feed .= "<BulletPoint>" . $product_feed['BulletPoint2'] . "</BulletPoint>";



            if ($product_feed['BulletPoint3'] != '')

                $feed .= "<BulletPoint>" . $product_feed['BulletPoint3'] . "</BulletPoint>";



            if ($product_feed['BulletPoint4'] != '')

                $feed .= "<BulletPoint>" . $product_feed['BulletPoint4'] . "</BulletPoint>";

            

            if ($product_feed['BulletPoint5'] != '')

                $feed .= "<BulletPoint>" . $product_feed['BulletPoint5'] . "</BulletPoint>";

            

            if ($var_val['MSRP'] != '')

                $feed .= "<MSRP currency='USD'>" . $var_val['MSRP'] . "</MSRP>";



            if ($product_feed['Manufacturer'] != '')

                $feed .= "<Manufacturer>" . $product_feed['Manufacturer'] . "</Manufacturer>";


            /**

            * @desc code added by dhiraj for checking the product parent blank and assigning default to accessory

            */

            if($product_feed['product-parent'] == '') {

                $product_feed['product-parent'] = 'Accessory';

            }

            if($product_feed['item-type'] == '') {

                $product_feed['item-type'] = 'Accessory';

            } 

            /***************** End of code added by dhiraj**********************************/        

            

            if($var_val['item-type']!='')

                $feed .= "<ItemType>".$var_val['item-type']."</ItemType>";

            

            if ($product_feed['target_audience1'] != '')

                $feed .= "<TargetAudience>" . $product_feed['target_audience1'] . "</TargetAudience>";

            

            if ($product_feed['target_audience2'] != '')

                $feed .= "<TargetAudience>" . $product_feed['target_audience2'] . "</TargetAudience>";

            

            if ($product_feed['target_audience3'] != '')

                $feed .= "<TargetAudience>" . $product_feed['target_audience3'] . "</TargetAudience>";

            

            $feed .= "</DescriptionData>";

            

            $feed .= "<ProductData>";

            $feed .= "<".$product_feed['product_type'].">";

            

            switch($product_feed['product_type'])

            {

                default:

                case 'Clothing':

                {

                    $var_val['product_color'] = (isset($var_val['product_color']) && $var_val['product_color']!='') ? $var_val['product_color'] : 'White';

                    $var_val['product_size'] = (isset($var_val['product_size']) && $var_val['product_size']!='') ? $var_val['product_size'] : 'S';

                    

                    if($var_val['product_size'] != '' && $var_val['product_color'] != '')

                    {

                        //$feed .= "<Size>".$var_val['product_size']."</Size></VariationData>";

                        $feed .= "<VariationData>

                                    <Parentage>child</Parentage>";                        

                        $feed .= "<Size>".$var_val['product_size']."</Size>";                        

                        $feed .= "<Color>".$var_val['product_color']."</Color>";

                        

                        $feed .= "</VariationData>";

                    }



                    $feed .= "<ClassificationData>";            

                    $feed .= "  <ClothingType>".$product_feed['product-parent']."</ClothingType>

                                <Department>".$product_feed['department']."</Department>";



                    if($product_feed['material'] != '')

                        $feed .= "<MaterialAndFabric>".$product_feed['material']."</MaterialAndFabric>";

                    

                    if($product_feed['model'] != '')

                        $feed .= "<ModelNumber>".$var_val['model']."</ModelNumber>";

                    //else

                        //$feed .= "<MaterialAndFabric>Other</MaterialAndFabric>";

                    $feed .= "</ClassificationData>";

                    break;

                }

                case 'Toys':

                case 'ToysBaby':

                {

                    $feed .= '<ProductType>ToysAndGames</ProductType>';

                    if($var_val['age_recommended']!= '')                

                    {

                        $feed .= '<AgeRecommendation>

                                    <MinimumManufacturerAgeRecommended unitOfMeasure="'.$var_val['age_unit_of_measurement'].'">'.$var_val['age_recommended'].'</MinimumManufacturerAgeRecommended>

                                  </AgeRecommendation>';

                    }
                    break;

                }

                case 'Beauty':

                case 'Makeup':

                {

                    $feed .= '<ProductType><BeautyMisc/></ProductType>';
                    /**
                    * @desc code added by dinesh for updating color field on amazon                    
                    */
                    /*if($var_val['product_color'] != '')
                    {

                        $feed .= "<VariationData>
                                    <Parentage>child</Parentage>";                        
                        $feed .= "<Color>".$var_val['product_color']."</Color>";
                        $feed .= "</VariationData>";
                    } */
                    /******************End of code added by dinesh **************/
                    break;

                }

            }            

            $feed .= "</".$product_feed['product_type'].">";

            $feed .= "</ProductData></Product></Message>";

        }            

    }

    

    if(isset($product_feed['variations']) && count($product_feed['variations']))

    {

        foreach($product_feed['variations'] as $var_key=>$var_val)

        {
            $msgIdCounterVariants = $msgIdCounterVariants + 1;                    

            $feedVar .= '<Message><MessageID>'.$msgIdCounterVariants.'</MessageID>

                     <OperationType>Update</OperationType>

                     <Relationship>

                        <ParentSKU>'.$product_feed['SKU'].'</ParentSKU>

                        <Relation><SKU>'.$var_val['SKU'].'</SKU>
                        
                        <Type>Variation</Type></Relation>

                     </Relationship></Message>';

        }

    }
    
    // code added by lav butala to add product event for start new product in TD
    if(!isProductEventExist($product_feed['SKU'], $TDObject)){
        $sendToProductEvent[$totalProductEvents]['Related Product'] = $product_feed['SKU'];
        $sendToProductEvent[$totalProductEvents]['Label'] = 'Amazon Start Record';
        $sendToProductEvent[$totalProductEvents]['Related Website'] = 'Amz';
        $sendToProductEvent[$totalProductEvents]['Start Date'] = new DateTime(date('Y-m-d'));
        $sendToProductEvent[$totalProductEvents]['End Date'] = NULL;
        $sendToProductEvent[$totalProductEvents]['isNewProduct?'] = 1;
        $totalProductEvents++;
    }

}

    /******************* process products ********************************/
    $feed .= "</AmazonEnvelope>";

    $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID)); 

    $cwd = getcwd(); 

    $tmpfname = $_SERVER['DOCUMENT_ROOT'] .'/'.FOLDER_NAME."/cronjobs/product_xml_files/product_update_".date('Y').date('m').date('d')."_".date('H').date("i").".xml";  

    //$tmpfname = tempnam("/tmp", "FOO");

    $feedHandle = fopen($tmpfname, 'w+');

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

    invokeSubmitFeed_amzn($service, $request, $feed_arr);    

    @fclose($feedHandle);

    sleep(20);
    /******************* process products end ********************************/
     
    /******************* process products variants ********************************/
    
    if($feedVar!=""){
        $feedVariant = '<?xml version="1.0" encoding="UTF-8"?><AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">';

        $feedVariant .= '<Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>M_TOPSTECHNO_1216044</MerchantIdentifier></Header>';

        $feedVariant .= '<MessageType>Relationship</MessageType><PurgeAndReplace>false</PurgeAndReplace>';
        
        $feedVariant .= $feedVar;
        
        $feedVariant .= '</AmazonEnvelope>';
        

        $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

        $tmpfname = $_SERVER['DOCUMENT_ROOT'] .'/'.FOLDER_NAME."/cronjobs/product_varaiation_xml_files/product_update_".date('Y').date('m').date('d')."_".date('H').date("i").".xml";  

        //$tmpfname = tempnam("/tmp", "FOO");

        $feedHandle = fopen($tmpfname, 'w+');

        fwrite($feedHandle, $feedVariant);

        rewind($feedHandle);



        $request = new MarketplaceWebService_Model_SubmitFeedRequest();

        $request->setMerchant(MERCHANT_ID);

        $request->setMarketplaceIdList($marketplaceIdArray);

        $request->setFeedType('_POST_PRODUCT_RELATIONSHIP_DATA_');            

        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));

        rewind($feedHandle);

        $request->setPurgeAndReplace(false);

        $request->setFeedContent($feedHandle);

        rewind($feedHandle);        

        invokeSubmitFeed_amzn_relations($service, $request,$feed_arr);

        @fclose($feedHandle);
    } 
    /******************* process products variants end ********************************/ 

// added by lav to import in product event
if($sendToProductEvent && sizeof($sendToProductEvent)>0){
    $TDTable = "Product Events";
    $schemaArray = array("Related Product", "Label", "Related Website", "Start Date", "End Date", "isNewProduct?");
    $record_inserted = $TDObject->insertTDData($TDTable, $sendToProductEvent, $schemaArray);
}

echo "<br><hr>Total Product Event Added: <b>".$totalProductEvents."</b><hr><br>"; 


// function added by lav butala to check same product event exist or not
function isProductEventExist($sku, $TDObject){
    $arrResults = $TDObject->api->Query('SELECT [Related Product] FROM [Product Events] WHERE [Label]="Amazon Start Record" AND [Related Website]="Amz" AND [isNewProduct?]=true AND [Related Product]="'.$sku.'"');
    if (isset($arrResults->Rows) && sizeof($arrResults->Rows)>0) {
        return true;
    }
    return false;
}





function invokeSubmitFeed_amzn(MarketplaceWebService_Interface $service, $request, $feed_arr)

{

    try

    {            

        $response = $service->submitFeed($request);

        $parameters = array (

            'Merchant' => MERCHANT_ID,

            'FeedSubmissionIdList' => array('FieldValue' => $response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId, 'FieldType' => 'MarketplaceWebService_Model_IdList'),

        );

        $config = array(

        'ServiceURL' => SERVICE_URL,

        'ProxyHost' => null,

        'ProxyPort' => -1,

        'MaxErrorRetry' => 3,

        );

        $service = new MarketplaceWebService_Client(

            AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);

        $request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters);

        

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        foreach($feed_arr as $key=>$product_feed){        
         
            $mysqli->query('INSERT INTO error_reporting VALUES (null, "'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Product Posting","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"","")');
        }

                

        if ($response->isSetSubmitFeedResult()) 

        {                

            $submitFeedResult = $response->getSubmitFeedResult();

            if ($submitFeedResult->isSetFeedSubmissionInfo())

            {                    

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();                    

                if ($feedSubmissionInfo->isSetFeedSubmissionId()) 

                {                    

                    echo json_encode(array('error' => "0", "msg" => "Products Posted Successfully to Amazon wait for status.."));                    

                }



                if ($feedSubmissionInfo->isSetFeedType()) 

                    $feedSubmissionInfo->getFeedType();



                if ($feedSubmissionInfo->isSetSubmittedDate())

                    $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT);



                if ($feedSubmissionInfo->isSetFeedProcessingStatus())

                    $feedSubmissionInfo->getFeedProcessingStatus();



                if ($feedSubmissionInfo->isSetStartedProcessingDate())

                    $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT);



                if ($feedSubmissionInfo->isSetCompletedProcessingDate())

                    $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT);
                    

                    sleep(5);

                    update_image($feed_arr);

                    sleep(5);

                    update_qnt_crons($feed_arr);

                    sleep(5);

                    update_price($feed_arr);                

            } 

            else

                echo json_encode(array('error' => "1", "msg" => "Products Not Posted to Amazon Error: Requestid not found please solve errors and resubmit product data."));

        }

        else

            echo json_encode(array('error' => "1", "msg" => "Products Not Posted to Amazon Error: Requestid not found please solve errors and resubmit product data."));

    }

    catch (MarketplaceWebService_Exception $ex) 

    {      

        echo("Caught Exception: " . $ex->getMessage() . "\n");

        sleep(20);

        invokeSubmitFeed_amzn($service, $request, $feed_arr);        

    }

}



function update_image($feed_arr) 

{

    $serviceUrl = SERVICE_URL;



    $config = array(

        'ServiceURL' => $serviceUrl,

        'ProxyHost' => null,

        'ProxyPort' => -1,

        'MaxErrorRetry' => 3,

    );

    $service = new MarketplaceWebService_Client(

            AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);


    $feedImage = '';

    $feedImage .= "<?xml version='1.0' encoding='UTF-8'?>

                        <AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>

                            <Header>

                                <DocumentVersion>1.02</DocumentVersion>

                                <MerchantIdentifier>M_TOPSTECHNO_1216044</MerchantIdentifier>

                            </Header>

                            <MessageType>ProductImage</MessageType>";
                        
    $MessageID = 0;
                            
    foreach($feed_arr as $key=>$product_feed){
        
        if (count($product_feed) > 0) 

        {
            $image = $product_feed['main-image-url'];
            
            $MessageID = $MessageID+1;

            $feedImage .= "<Message>

                                <MessageID>".$MessageID."</MessageID>

                                <OperationType>Update</OperationType>

                                <ProductImage>

                                    <SKU>" . $product_feed['SKU']. "</SKU> 

                                    <ImageType>Main</ImageType> 

                                    <ImageLocation>" . $image . "</ImageLocation>

                                </ProductImage>
                            </Message>";

            if(isset($product_feed['variations']) && count($product_feed['variations']))

            {

                foreach($product_feed['variations'] as $var_key=>$var_val)

                {
                    $MessageID = $MessageID+1;

                    $feedImage .= "<Message>

                                <MessageID>".$MessageID."</MessageID>

                                <OperationType>Update</OperationType>

                                <ProductImage>

                                        <SKU>".$var_val['SKU']."</SKU> 

                                        <ImageType>Main</ImageType> 

                                        <ImageLocation>".$var_val['main-image-url']."</ImageLocation>     

                                </ProductImage>

                        </Message>";

                }                

            }

        } 

    }
    
    $feedImage .= "</AmazonEnvelope>";

            echo "<br>---Image Data--<br>";

            $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

            $tmpfname = tempnam("/tmp", "FOO");

            $feedHandle = fopen($tmpfname, 'rw+');

            fwrite($feedHandle, $feedImage);

            rewind($feedHandle);


            $request = new MarketplaceWebService_Model_SubmitFeedRequest();

            $request->setMerchant(MERCHANT_ID);

            $request->setMarketplaceIdList($marketplaceIdArray);

            $request->setFeedType('_POST_PRODUCT_IMAGE_DATA_');

            $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));

            rewind($feedHandle);

            $request->setPurgeAndReplace(true);

            $request->setFeedContent($feedHandle);

            rewind($feedHandle);

            invokeSubmitFeed_image($service, $request, $feed_arr);

            @fclose($feedHandle);

}



function invokeSubmitFeed_image(MarketplaceWebService_Interface $service, $request, $feed_arr)

{

    try 

    {

        $response = $service->submitFeed($request);



        if ($response->isSetSubmitFeedResult()) 

        {

            $submitFeedResult = $response->getSubmitFeedResult();

            if ($submitFeedResult->isSetFeedSubmissionInfo()) 

            {

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();

                if ($feedSubmissionInfo->isSetFeedSubmissionId()) 

                {
                    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 
                    foreach($feed_arr as $key => $product_feed){       

                        $mysqli->query('INSERT INTO error_reporting VALUES (null,"'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Image Posting","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"","")');
                    }

                    echo json_encode(array('error' => "0", "msg" => "Images Posted Successfully to Amazon wait for status.."));                    

                }                    

            } 

            else

                echo json_encode(array('error' => "1", "msg" => "Image not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

        } 

        else

            echo json_encode(array('error' => "1", "msg" => "Image not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

    } 

    catch (MarketplaceWebService_Exception $ex) 

    {

        echo json_encode(array("Caught_Exception" => $ex->getMessage(), "Response_Status_Code" => $ex->getStatusCode(), "Error_Code" => $ex->getErrorCode(), "Error_Type" => $ex->getErrorType(), "Request_ID" => $ex->getRequestId(), "XML" => $ex->getXML(), "ResponseHeaderMetadata" => $ex->getResponseHeaderMetadata()));        

        sleep(90);

        invokeSubmitFeed_image($service, $request, $feed_arr);

    }

}

    

function invokeSubmitFeed_amzn_relations(MarketplaceWebService_Interface $service, $request,$feed_arr)

{

    try

    {

        $response = $service->submitFeed($request);

        

        if ($response->isSetSubmitFeedResult()) 

        {                

            $submitFeedResult = $response->getSubmitFeedResult();
            
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            foreach($feed_arr as $key => $product_feed){
                if(isset($product_feed['variations']) && count($product_feed['variations'])>0){
                    foreach($product_feed['variations'] as $key => $product_var_feed){
                        $mysqli->query('INSERT INTO error_reporting VALUES (null, "'.$product_var_feed['product_id'].'", "'.$product_var_feed['SKU'].'","Variation Posting","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"","")');   
                    }
                }
            }

            if ($submitFeedResult->isSetFeedSubmissionInfo()) 

            {                    

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();

                if ($feedSubmissionInfo->isSetFeedSubmissionId()) {



                }

                if ($feedSubmissionInfo->isSetFeedType()) {

                    $feedSubmissionInfo->getFeedType();

                }

                if ($feedSubmissionInfo->isSetSubmittedDate()) {

                    $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT);

                }

                if ($feedSubmissionInfo->isSetFeedProcessingStatus()) {

                    $feedSubmissionInfo->getFeedProcessingStatus();

                }

                if ($feedSubmissionInfo->isSetStartedProcessingDate()) {

                    $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT);

                }

                if ($feedSubmissionInfo->isSetCompletedProcessingDate()) {

                    $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT);

                }

            }

        } 

        else         

            echo json_encode(array('error'=>"1","msg"=>"Product Not Posted to Amazon Error: Requestid not found please solve errors and resubmit product data."));

    }

    catch (MarketplaceWebService_Exception $ex) 

    {

        echo("Caught Exception: " . $ex->getMessage() . "\n");      

        sleep(20);

        invokeSubmitFeed_amzn_relations($service, $request,$feed_arr);

    }

}



function update_qnt_crons($feed_arr)

{    

    $serviceUrl = SERVICE_URL;

    $config = array(

        'ServiceURL' => $serviceUrl,

        'ProxyHost' => null,

        'ProxyPort' => -1,

        'MaxErrorRetry' => 3,

    );



    $service = new MarketplaceWebService_Client(

            AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);

    
    $feedInv = '';

    $feedInv .= "<?xml version='1.0' encoding='UTF-8'?><AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>

                    <Header>

                        <DocumentVersion>1.02</DocumentVersion>

                        <MerchantIdentifier>M_TOPSTECHNO_1216044</MerchantIdentifier>

                    </Header><MessageType>Inventory</MessageType>";
                    
    $MessageID = 0;
                    
    foreach($feed_arr as $key => $product_feed){

        if (count($product_feed) > 0) 

        {
            $SKU = $product_feed['SKU'];
            $qunt = $product_feed['amzn_UnitQTY'];
            
            $MessageID = $MessageID+1;

            $feedInv .= "<Message>

                        <MessageID>".$MessageID."</MessageID>

                        <OperationType>Update</OperationType>

                        <Inventory>

                            <SKU>".$SKU."</SKU>";            

            $feedInv .= "<Quantity>".($qunt ? $qunt : 0)."</Quantity>";



            $feedInv .= "  </Inventory>

                    </Message>";        

            

            if(isset($product_feed['variations']) && count($product_feed['variations']))

            {            

                foreach($product_feed['variations'] as $var_key=>$var_val)

                {
                    $MessageID = $MessageID+1;

                    $feedInv .= "<Message>

                                <MessageID>".$MessageID."</MessageID>

                                <OperationType>Update</OperationType>

                                <Inventory>

                                        <SKU>".$var_val['SKU']."</SKU>";

                    if(isset($product_feed['amzn_ship_method']) && $product_feed['amzn_ship_method'] == 'Fullfillment by Amazon')

                    {

                        $feedInv .= "<FulfillmentCenterID>AMAZON_NA</FulfillmentCenterID>

                            <Lookup>FulfillmentNetwork</Lookup>

                            <SwitchFulfillmentTo>AFN</SwitchFulfillmentTo>";

                    }

                    else

                        $feedInv .= "<Quantity>".($var_val['product_quantity'] ? $var_val['product_quantity'] : 0)."</Quantity>";

                    $feedInv .= "</Inventory>

                        </Message>";

                }

            }
        }
    }

        $feedInv .= "</AmazonEnvelope>";

        echo "<br>---Quantity Data--<br>";

        $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));



        $tmpfname = tempnam("/tmp", "FOO");

        $feedHandle = fopen($tmpfname, 'rw+');

        fwrite($feedHandle, $feedInv);

        rewind($feedHandle);


        $request = new MarketplaceWebService_Model_SubmitFeedRequest();

        $request->setMerchant(MERCHANT_ID);

        $request->setMarketplaceIdList($marketplaceIdArray);

        $request->setFeedType('_POST_INVENTORY_AVAILABILITY_DATA_');

        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));

        rewind($feedHandle);

        $request->setPurgeAndReplace(false);

        $request->setFeedContent($feedHandle);


        rewind($feedHandle);

        invokeSubmitFeed_qnt($service, $request, $feed_arr);

        @fclose($feedHandle);

}



function invokeSubmitFeed_qnt(MarketplaceWebService_Interface $service, $request, $feed_arr)

{

    try 

    {

        $response = $service->submitFeed($request);            

        if ($response->isSetSubmitFeedResult()) 

        {

            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            
            foreach($feed_arr as $key => $product_feed){
                $mysqli->query('INSERT INTO error_reporting VALUES (null, "'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Quantity Posting","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"","")');
            }

            $submitFeedResult = $response->getSubmitFeedResult();

            if ($submitFeedResult->isSetFeedSubmissionInfo()) 

            {                    

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();

                if ($feedSubmissionInfo->isSetFeedSubmissionId()) 

                    echo json_encode(array('error' => "0", "msg" => "Quantity Posted Successfully to Amazon wait for status.."));

            }

            else 

            {

                echo json_encode(array('error' => "1", "msg" => "Quantity Not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

            }

        } 

        else 

        {

            echo json_encode(array('error' => "1", "msg" => "Quantity Not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

        }

    }

    catch (MarketplaceWebService_Exception $ex) 

    {

        echo json_encode(array("Caught_Exception" => $ex->getMessage(), "Response_Status_Code" => $ex->getStatusCode(), "Error_Code" => $ex->getErrorCode(), "Error_Type" => $ex->getErrorType(), "Request_ID" => $ex->getRequestId(), "XML" => $ex->getXML(), "ResponseHeaderMetadata" => $ex->getResponseHeaderMetadata()));        

        sleep(20);

        invokeSubmitFeed_qnt($service, $request, $feed_arr);

    }

}



function update_price($feed_arr) 

{        

    $serviceUrl = SERVICE_URL;



    $config = array(

        'ServiceURL' => $serviceUrl,

        'ProxyHost' => null,

        'ProxyPort' => -1,

        'MaxErrorRetry' => 3,

    );        

    $service = new MarketplaceWebService_Client(

            AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION);



    if (count($feed_arr) > 0) 

    {
        $feedPrice = '';        

        $feedPrice .= "<?xml version='1.0' encoding='UTF-8'?>

            <AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>

                    <Header>

                        <DocumentVersion>1.02</DocumentVersion>

                        <MerchantIdentifier>M_CLOWNANTIC_1083404</MerchantIdentifier>

                    </Header>

                    <MessageType>Price</MessageType>";
                    
        $MessageID = 0;
                    
        foreach($feed_arr as $key => $product_feed){

            if($product_feed['StandardPrice'] != '' || $product_feed['StandardPrice'] > 0)

            {
                $MessageID = $MessageID+1;

                $feedPrice .= "<Message>

                        <MessageID>".$MessageID."</MessageID>

                        <Price>

                            <SKU>".$product_feed['SKU']."</SKU>

                            <StandardPrice currency='USD'>".$product_feed['StandardPrice']."</StandardPrice>

                        </Price>

                    </Message>";

            }

            if(isset($product_feed['variations']) && count($product_feed['variations']))

            {

                foreach($product_feed['variations'] as $var_key=>$var_val)

                {
                    $MessageID = $MessageID+1;

                    $feedPrice .= "<Message>

                                <MessageID>".$MessageID."</MessageID>                            

                                <Price>

                                    <SKU>".$var_val['SKU']."</SKU>

                                    <StandardPrice currency='USD'>".$var_val['MSRP']."</StandardPrice>

                                </Price>

                        </Message>";

                }

            }
        }

        $feedPrice .= "</AmazonEnvelope>";

        $marketplaceIdArray = array("Id" => array(MARKETPLACE_ID));

        echo "<br>---Price Data--<br>";

        $tmpfname = tempnam("/tmp", "FOO");

        $feedHandle = fopen($tmpfname, 'rw+');

        fwrite($feedHandle, $feedPrice);

        rewind($feedHandle);



        $request = new MarketplaceWebService_Model_SubmitFeedRequest();

        $request->setMerchant(MERCHANT_ID);

        $request->setMarketplaceIdList($marketplaceIdArray);

        $request->setFeedType('_POST_PRODUCT_PRICING_DATA_');



        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));

        rewind($feedHandle);

        $request->setPurgeAndReplace(false);

        $request->setFeedContent($feedHandle);



        rewind($feedHandle);



        invokeSubmitFeed_price($service, $request, $feed_arr);



        @fclose($feedHandle);

    } else {

        echo json_encode(array('error' => "1", "msg" => "Price data not Posted..."));

    }    
    
    foreach($feed_arr as $key => $product_feed){
        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        if(isset($product_feed['lid']) && $product_feed['lid']!='')

        {

            $result2 = $mysqli->query('update item_lister set lister_status = "Completed" where lister_id = '.$product_feed['lid']);

        }
        
    }

}



function invokeSubmitFeed_price(MarketplaceWebService_Interface $service, $request, $feed_arr)

{

    try 

    {

        $response = $service->submitFeed($request);



        if ($response->isSetSubmitFeedResult()) 

        {

            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            
            foreach($feed_arr as $key => $product_feed){
                $mysqli->query('INSERT INTO error_reporting VALUES (null,"'.$product_feed['product_id'].'", "'.$product_feed['SKU'].'","Price Posting","'.$response->SubmitFeedResult->FeedSubmissionInfo->FeedSubmissionId.'",null,"0",null,"","")');
            }

            $submitFeedResult = $response->getSubmitFeedResult();

            if ($submitFeedResult->isSetFeedSubmissionInfo()) 

            {                    

                $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();

                if ($feedSubmissionInfo->isSetFeedSubmissionId()) 

                {                    

                    echo json_encode(array('error' => "0", "msg" => "Price Posted Successfully to Amazon wait for status.."));                    

                }                    

            }

            else

                echo json_encode(array('error' => "1", "msg" => "Price not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

        }

        else

            echo json_encode(array('error' => "1", "msg" => "Price not Posted to Amazon Error: Requestid not found please solve errors and resubmit."));

    } 

    catch (MarketplaceWebService_Exception $ex) 

    {

        echo json_encode(array("Caught_Exception" => $ex->getMessage(), "Response_Status_Code" => $ex->getStatusCode(), "Error_Code" => $ex->getErrorCode(), "Error_Type" => $ex->getErrorType(), "Request_ID" => $ex->getRequestId(), "XML" => $ex->getXML(), "ResponseHeaderMetadata" => $ex->getResponseHeaderMetadata()));

        sleep(20);

        invokeSubmitFeed_price($service, $request, $feed_arr);

    }

}

?>

