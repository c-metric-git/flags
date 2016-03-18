<?php        

set_time_limit(0);
ini_set("memory_limit","256M");

include 'ca_config.php';



//ini_set("display_errors",1);   

$_SERVER['DOCUMENT_ROOT'] = "/home/stripedsocks/public_html";

$base_path = "/home/stripedsocks/public_html";

 

echo "<br />Start Time :".date("Y-m-d H:i:s");

$message = "<br />Start Time :".date("Y-m-d H:i:s");



//mail("lakshmi@clownantics.com","CAN Amazon Product Posting Started",$meesage);  
mail("uzzal@clownantics.com","CAN Amazon Product Posting Started",$meesage);  



$all_scripts = "Yes";



echo "<br />First Script <br />";

$message .= "<br />First Script <br />";



include_once "ca_database_sync.php";

  

echo " <br />Second Script<br /> ";

$message .= "<br />Second Script <br />";



include_once "ca_amazon_cron_delay.php";



sleep(20);  



echo " <br />Third Script<br /> ";

$message .= "<br />Third Script <br />"; 



include_once "ca_amazon_error_reporting_delay.php";



sleep(20);



echo "<br />Fourth Script<br /> ";

$message .= "<br />Fourth Script <br />";  



include_once "ca_amazon_product_asin_update.php";



echo "<br />End Time :".date("Y-m-d H:i:s");

$message .= "<br />End Time :".date("Y-m-d H:i:s");

mail("uzzal@clownantics.com","CAN Amazon Product Posting Completed",$message);

?>