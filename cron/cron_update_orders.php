<?php   

set_time_limit(0);
ini_set("memory_limit","128M");

require_once("cron_init.php");
require_once(BASE_PATH."lib/Teamdesk/class.export_teamdesk_orders.php");

$strReturn ='';       
$objTeamDeskOrder = new TeamDeskOrder($db);   
$strReturn = $objTeamDeskOrder->updateOrdersFromTeamDesk();
echo $strReturn;

$db->query("SELECT * from sales_flat_shipment where email_sent !='1'");  
while($db->moveNext()) {
     $order_array[] = $db->col['increment_id'];
}    
$db->done();

if(count($order_array) > 0) {
    $mageFilename = BASE_PATH.'app/Mage.php';
    require_once $mageFilename;
    Mage::setIsDeveloperMode(true);
    ini_set('display_errors', 1);
    umask(0);
    Mage::app('admin');
    Mage::register('isSecureArea', 1);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
     
    foreach($order_array as $orderId) { 
        $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($orderId); 
        $shipment->sendEmail(true, $comment)
                    ->setEmailSent(true)
                    ->save();
       echo "<br />Shipping email sent for order: ".$orderId;
    }
}    
?>

