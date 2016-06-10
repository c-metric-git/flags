<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../../app/Mage.php';
umask(0);
Mage::app();
// Now you can run ANY Magento code you want

// Change 12 to the ID of the product you want to load
$_product = Mage::getModel('catalog/product')->load(12);

echo "<pre>";
print_r($_product);
exit;
//echo $_product->getName();

