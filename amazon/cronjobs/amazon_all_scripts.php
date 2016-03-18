<?php        

set_time_limit(0);

include 'config.php';



//ini_set("display_errors",1);   

$_SERVER['DOCUMENT_ROOT'] = "/home/stripedsocks/public_html";

$base_path = "/home/stripedsocks/public_html";

 

echo "<br />Start Time :".date("Y-m-d H:i:s");

$message = "<br />Start Time :".date("Y-m-d H:i:s");



mail("lakshmi@clownantics.com","Amazon Product Posting Started",$meesage);  



$all_scripts = "Yes";



echo "<br />First Script <br />";

$message .= "<br />First Script <br />";



include_once "database_sync.php";

  

echo " <br />Second Script<br /> ";

$message .= "<br />Second Script <br />";



include_once "amazon_cron_delay.php";



sleep(20);  



echo " <br />Third Script<br /> ";

$message .= "<br />Third Script <br />"; 



include_once "amazon_error_reporting_delay.php";



sleep(20);



echo "<br />Fourth Script<br /> ";

$message .= "<br />Fourth Script <br />";  



include_once "amazon_product_asin_update.php";



echo "<br />End Time :".date("Y-m-d H:i:s");

$message .= "<br />End Time :".date("Y-m-d H:i:s");

mail("lakshmi@clownantics.com","Amazon Product Posting Completed",$message);

?>