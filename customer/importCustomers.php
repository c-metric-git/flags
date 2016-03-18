<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
include_once "../app/Mage.php";
Mage::init();
$app = Mage::app('default');
$f = fopen('csv/customer.csv', 'r');
$res = fgetcsv($f, null, ',', '"');    
//$qty = count($names);
//$uniqueQty = count(array_unique($names));
echo "<pre>";
print_r($res);
//printf("%d records, %d unique records\n", $qty, $uniqueQty);
?>