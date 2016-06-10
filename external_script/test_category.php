<?php 
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
set_time_limit(0);
$res = Mage::getModel('catalog/category')->load(11911);
echo "<pre>";
print_r($res->getData());
exit;
 ?>