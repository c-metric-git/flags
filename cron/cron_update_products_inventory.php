<?php     

//ini_set("display_errors",1);
/**
 * @author      MagePsycho <info@magepsycho.com>
 * @website     http://www.magepsycho.com
 * @category    Export / Import
 */
require_once("cron_init.php");
  
require_once(BASE_PATH."lib/Teamdesk/class.import_teamdesk_webprofiles.php"); 
ini_set("display_errors",1);
error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
$objTDProduct = new TeamDeskWebprofiles(); 
$strReturn = $objTDProduct->updateProductInventory($db);  
$strMessage = "<br><b>Stripedsocks</b></br><br /><br />Total product inventory updated :- <b>".$strReturn["totalProductsUpdated"]."</b><br>
               Total product attributes updated :- <b>".$strReturn["totalAttributesUpdate"]."</b><br>"; 
echo $strMessage;

require_once(BASE_PATH."lib/Teamdesk/class.FL_import_teamdesk_webprofiles.php"); 
$objTDProduct_fl = new FLTeamDeskWebprofiles(); 
$strReturn_fl = $objTDProduct_fl->updateProductInventory($db);  
$strMessage_fl = "<br><b>Flagsrus</b></br><br /><br />Total product inventory updated :- <b>".$strReturn_fl["totalProductsUpdated"]."</b><br>"; 
echo $strMessage_fl;


require_once(BASE_PATH."lib/Teamdesk/class.FP_import_teamdesk_webprofiles.php"); 

$objTDProduct_fp = new FPTeamDeskWebprofiles(); 
$strReturn_fp = $objTDProduct_fp->updateProductInventory($db);  
$strMessage_fp = "<br><b>Facepaint</b></br><br /><br />Total product inventory updated :- <b>".$strReturn_fp["totalProductsUpdated"]."</b><br>"; 
echo $strMessage_fp;
$db->done();

$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::app();
Mage::setIsDeveloperMode(true);
umask(0);
Mage::register('isSecureArea', 1);
 //echo "here";exit;
set_time_limit(0);

/***************** UTILITY FUNCTIONS ********************/
function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}
 
function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}

$process = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
//echo "<pre>";
//print_r($process);
//exit;
$process->reindexAll();

//$process = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
//$process->reindexAll();

echo "<br>indexing done successfully";

?>



