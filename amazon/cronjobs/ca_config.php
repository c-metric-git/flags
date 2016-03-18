<?php
include 'tdapi/tdapi.php';
include 'tdapi/dataset.php';
include 'tdapi/class.TDDataGeneral.php';

define('DB_HOST', 'localhost');
define('DB_USER', 'myclown_user');
define('DB_PASSWORD', 'Myclownantics@1');
define('DB_NAME', 'myclown_amazon');
define('SERVER_URL',"http://".$_SERVER['HTTP_HOST'].'/');

define('DATE_FORMAT', 'Y-m-d\TH:i:s\Z');
 //Clownantics credentials
define('AWS_ACCESS_KEY_ID', 'AKIAJPDZOCDYWOSILEUQ');
define('AWS_SECRET_ACCESS_KEY', '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');
define('MERCHANT_ID', 'A1VEDA95APL570');
define('MARKETPLACE_ID', 'A2EUQ1WTGCTBG2'); // for canada
define('APPLICATION_NAME', 'Clownantics');
define('APPLICATION_VERSION', '2.0');
define('SERVICE_URL', 'https://mws.amazonservices.ca');

//Teamdesk API details
define('TD_EMAIL'   , 'blake@clownantics.com');      //kashyap.padh@tops-int.com
define('TD_PASSWORD', 'julian202');   //thirteen13
define('TD_DOMAIN'  , 'clownantics.teamdesk.net');
define('TD_API_KEY' , 27503);

$folder_name = explode('/',dirname($_SERVER['SCRIPT_NAME']));
$folder_name = $folder_name[0] ? $folder_name[0] : $folder_name[1];

define('FOLDER_NAME', 'amazon');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

function pr($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

    
function generate_upc()
{
    $st = '';
    $odd_sum = 0;
    $even_sum = 0;

    for($i=1;$i<=13;$i++)
    {
        $num = rand(0, 9);
        if($i%2 == 0)
            $even_sum +=$num;
        else
            $odd_sum +=$num;
        $st .= $num;
    }   

    $odd_sum *= 3;
    $mod = $even_sum+$odd_sum;
    $mod = $mod % 10;
    if($mod == 0)
        return $st.$mod;
    else
    {
        $mod = 10 - $mod;
        return $st.$mod;
    }
}
