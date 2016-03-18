<?php   
   
set_time_limit(0);
ini_set("memory_limit","128M");
//ini_set("display_errors",1);
require_once("cron_init.php");
require_once(BASE_PATH."lib/Teamdesk/class.export_teamdesk_user.php");
require_once(BASE_PATH."lib/Teamdesk/class.export_teamdesk_orders.php");

$strReturn ='';       
$objTeamDeskOrder = new TeamDeskOrder($db);   
$strReturn = $objTeamDeskOrder->exportOrdersToTeamDesk(); 

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$subject = "StripedSocks - Exporting orders to teamdesk ".date('Y-m-d h:i:s',time());
//mail(TD_UPDATE_MAIL_ID , $subject, $strReturn, $headers); //QUICKBASE_UPDATE_MAIL_ID  

if($strReturn == '')
    echo "No more orders to export into teamdesk";
else    
    echo $strReturn;
$db->done();    

?>

