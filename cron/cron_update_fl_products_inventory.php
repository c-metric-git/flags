<?php     

//ini_set("display_errors",1);
/**
 * @author      MagePsycho <info@magepsycho.com>
 * @website     http://www.magepsycho.com
 * @category    Export / Import
 */
require_once("cron_init.php");  
require_once(BASE_PATH."lib/Teamdesk/class.FL_import_teamdesk_webprofiles.php"); 
ini_set("display_errors",1);           
$objTDProduct = new FLTeamDeskWebprofiles(); 
$strReturn = $objTDProduct->updateProductInventory($db);  
$strMessage = "<br /><br />Total product inventory updated :- <b>".$strReturn["totalProductsUpdated"]."</b><br>
               Total product attributes updated :- <b>".$strReturn["totalAttributesUpdate"]."</b><br>"; 
echo $strMessage;
$db->done();

$mageFilename = BASE_PATH.'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
set_time_limit(0);
/***************** UTILITY FUNCTIONS ********************/
function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}
 
function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}
$process = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
$process->reindexAll();


?>



