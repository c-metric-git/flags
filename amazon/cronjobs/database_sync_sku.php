<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('max_execution_time', 0);

$api = new TDAPI();
$login = $api->login();
$dataset = $api->GetSchema("Amazon Entry",array('sku','isVisibleOnAmazon?','recordHistory','amazonErrorReason','ASIN'));

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//$mysqli->query('DELETE FROM ready_for_amazon');
$arr = array("F41025","K1140","K1442","L10781","L109743","M1870","M2170","M2332","M4410","L109779","L109787","L109755");
foreach($arr as $a)
{
$result = $api->Query("SELECT [platinum-5],[platinum-4],[platinum-3],[platinum-2],[platinum-1],
                       [amz shipping price],[Product - Package Width],[Product - Package Length],[Product - Package Height],
                       [item-height],[item-width],[item-length],[item-length-unit-of-measure],[item-weight],[item-weight-unit-of-measure],[package-weight-unit-of-measure],[package-weight],[package-length-unit-of-measure],[package-length],[package-width],[package-height],
                       [isReadyForDeletion?],[isReadyForUpdate?],[recordHistory],[amazonErrorReason],[# of Related Amazon Entries],[Related Amazon entry],[Product - isBannedFromAmazon?],[Product - Vendor Product URL],[reasonBadMatch],[minimum-manufacturer-age-recommended-unit-of-measu],[isVisibleOnAmazonAds?],[isBadTitleOnAmz?],
                       [tmpParent],[Amazon Entry Related Amazon entry],[Amazon Entry],[Amazon category - bullet-point1 entered],[Amazon category - character],[Amazon category - department],[Amazon category - genre],[Amazon category - isBrandRequiredInEntryLabel?],[Amazon category - isKansasCosmetics?],[Amazon category - item-type],
                       [Amazon category - product-tax-code],[Amazon category - search-terms1],[Amazon category - search-terms2],[Amazon category - search-terms3],[Amazon category - search-terms4],[Amazon category - search-terms5],[Amazon category - target-audience1],
                       [Amazon category - target-audience2],[Amazon category - target-audience3],[Amazon category - target-gender],[Amazon category - theme],[Amazon category - toe-style],[Amazon category - Type],[Amazon category - Type2],[Amz Competitors Below Map],
                       [Amazon entry - Amazon category - bullet-point1 en],[Amazon entry - Amazon category - character],[Amazon entry - Amazon category - department old],[Amazon entry - Amazon category - genre],[Amazon entry - Amazon category - item-type],[Amazon entry - Amazon category - product-tax-code],[Amazon entry - Amazon category - search-terms1],[Amazon entry - Amazon category - search-terms2],[Amazon entry - Amazon category - search-terms3],[Amazon entry - Amazon category - search-terms4],
                       [Amazon entry - Amazon category - search-terms5],[Amazon entry - Amazon category - target-audience1],[Amazon entry - Amazon category - target-audience2],[Amazon entry - Amazon category - target-audience3],[Amazon entry - Amazon category - target-gender],[Amazon entry - Amazon category - theme],[Amazon entry - Amazon category - toe-style],[Amazon entry - Amazon category - Type],[Amazon entry - is_visible],
                       [Amazon entry - material-fabric1 entered],[Amazon entry - material-fabric2 entered],[Amazon entry - material-fabric3 entered],[Amazon entry - material-type entered],[Amazon entry - product-description],[Amazon entry - product-name],[Amazon Entry sku],[Amazon entry - variation-theme entered],
                       [Amazon entry - Web Profile - Display Name],[Amazon entry - Web Profile - bullet-point5 entered],[Amazon entry - Web Profile - bullet-point4 entered],[Amazon entry - Web Profile - bullet-point3 entered],[Amazon entry - Web Profile - bullet-point2 entered],[Amazon entry - Web Profile - bullet-point1 entered],[Amazon entry - Web Profile - PinnacleSKU],[Amazon entry - Web Profile - Product - Description],[Amazon entry - Web Profile - Product - SKU],
                       [Amazon Launch Date],[Amz Price Difference Down],[Amz Price Difference Up],[Amazon Title],[amazonUrlDisplay],[amazonUrlEdit],[amazonUrlPrice],[animal-type],[ASIN],[brand],[bullet-point1],[bullet-point2],[bullet-point3],[bullet-point4],
                       [bullet-point5],[character],[color],[color-map],[condition-type],[coverage],[currency],[department],[description],[finish-type1],[finish-type2],[genre],[Image - imgLocation],[Image - imgLocationCloud500],
                       [imgPath],[is_visible],[isVisibleOnAmazon?],[item-condition],[item-form],[item-package-quantity],[item-price],[item-specialty1],[item-specialty2],[item-specialty3],[item-specialty4],[item-specialty5],
                       [item-type],[keyAttribute],[launch-date],[main-image-url],[manufacturer],[manufacturer-safety-warning],[material-fabric1],[material-fabric2],[material-fabric3],[material-fabric1 entered],[material-fabric2 entered],[material-fabric3 entered],[material-type],
                       [material-type entered],[maximum-manufacturer-age-recommended],[maximum-manufacturer-age-recommended-unit-of-meas],[mfr-part-number],
                       [minimum-manufacturer-age-recommended],[minimum-manufacturer-age-recommended-unit-of-meas],[msrp],[parentage],
                       [parent-child],[parent-sku],[price],[priceAmazon],[priceCalc],[Product - Description],[product-description],[product-id],[product-id-type],
                       [Product - Size],[Product - SKU],[Product - Status],[product-tax-code],[product_type],[Product - Type],[Product - UPC Code],[quantity],[Related Product],[relationship-type],[search-terms1],[search-terms2],[search-terms3],[search-terms4],[search-terms5],
                       [shippingCost],[shippingCostNew],[shipping-weight],[shipping-weight-unit-of-measure],[size],[size-map],[sku],[standard-product-id],[target-audience1],[target-audience2],[target-audience3],[target-gender],[theme],[title],[Type],
                       [Type2],[upc code],[variation-theme],[variation-theme entered] 
                       FROM [Amazon Entry] WHERE [sku] = '".$a."' ");

$arr_fields = array();
$arr_values = array();
$new_arr = array();
$count = count($result->Rows);
$product_count = 0;

for($i=0;$i<$count;$i++)
{
    foreach($result->Rows[$i] as $key=>$val)
    {
        $key = '`'.$key.'`';
        switch (gettype($val))
        {
            case 'boolean':
            {
                if($val)
                    $arr_fields [$key] = '"1"';
                else
                    $arr_fields [$key] = '"0"';
                break;
            }
            case 'object':
            {
                $arr_fields [$key]= '"'.$val->format( 'd-m-Y H:i:s').'"';
                break;
            }
            default:
            {
                $arr_fields [$key] = '"'.(($val != '') ? htmlspecialchars(str_replace('"', '\"', $val)) : NULL).'"';
                break;
            }
        }
    }
    $last_record = $arr_fields['`sku`'];            
    $arr_values = array_values($arr_fields);
    
    $arr_fields = array_keys($arr_fields);            
    $arr_fields = implode(',', $arr_fields);
    $arr_values = implode(',', $arr_values);
    $sql = 'INSERT INTO ready_for_amazon ('.$arr_fields.') VALUES ('.$arr_values.')';
    if($arr_values['74']!='')
    {
        $res = $api->Query("SELECT * FROM [Amazon Entry] WHERE [parent-sku] = '".$arr_values[74]."'");
        $arr_fields_var = array();
        $arr_values_var = array();
        $new_arr = array();
        $cou = count($res->Rows);

        for($j=0;$j<$cou;$j++)
        {
            foreach($res->Rows[$j] as $key=>$val)
            {
                $key = '`'.$key.'`';
                switch (gettype($val))
                {
                    case 'boolean':
                    {
                        if($val)
                            $arr_fields_var [$key] = '"1"';
                        else
                            $arr_fields_var [$key] = '"0"';
                        break;
                    }
                    case 'object':
                    {
                        $arr_fields_var [$key]= '"'.$val->format( 'd-m-Y H:i:s').'"';
                        break;
                    }
                    default:
                    {
                        $arr_fields_var [$key] = '"'.(($val != '') ? htmlspecialchars(str_replace('"', '\"', $val)) : NULL).'"';
                        break;
                    }
                }
            }
            $last_record = $arr_fields_var['`sku`'];            
            $arr_values_var = array_values($arr_fields_var);
            $arr_fields_var = array_keys($arr_fields_var);            
            $arr_fields_var = implode(',', $arr_fields_var);
            $arr_values_var = implode(',', $arr_values_var);
            $sql_var = 'INSERT INTO ready_for_amazon ('.$arr_fields_var.') VALUES ('.$arr_values_var.')';    
            $mysqli->query($sql_var);
            $product_count+=1;
            unset($arr_fields_var);
            unset($arr_values_var);
        }
    }
    $mysqli->query($sql);
    $product_count+=1;
    unset($arr_fields);
    unset($arr_values);
    echo $product_count, "Product(s) Added Successfully<br/>";
}
}
