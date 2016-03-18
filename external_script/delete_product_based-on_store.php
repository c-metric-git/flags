<?php
/* Delete all product based on specific store*/
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
umask(0);
ini_set('memory_limit', '-1');
set_time_limit(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
//$products = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('data_set', 1544);
$store_id = 2;//Mage::app()->getStore()->getId();
$products = Mage::getResourceModel('reports/product_collection')
->setStoreId($storeId)
->addStoreFilter($store_id)
->addAttributeToSelect('*');
$products->load();
//echo "<pre>";
//print_r($products);
//exit;
$sql = "";
$undoSql = "";

for ($i = 0; $i <= 8; $i++) {
    $sql.= "UPDATE index_process SET mode = 'manual' WHERE index_process.process_id =$i LIMIT 1;";
    $undoSql.= "UPDATE index_process SET mode = 'real_time' WHERE index_process.process_id =$i LIMIT 1;";
}

$mysqli = Mage::getSingleton('core/resource')->getConnection('core_write');
$mysqli->query($sql);
$total_products = count($products);
$count = 0;

foreach($products as $product) {
    $product->delete();
	//echo "fsda";
    if ($count++ % 100 == 0) {
        $cur = strtotime(date("d/m/y h:i:s")) - $time;
        $time = strtotime(date("d/m/y h:i:s"));
        echo round((($count / $total_products) * 100) , 2) . "% deleted ($count/$total_products) " . round(100 / $cur) . " p/s " . date("h:i:s") . "rn";
        flush();
    }
}

echo "Ended " . date("d/m/y h:i:s") . "rn";
$mysqli->query($undoSql);
exit();