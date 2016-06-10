<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../../app/Mage.php';
Mage::app();
$model = Mage::getResourceModel('catalog/setup','catalog_setup');
$model->removeAttribute('catalog_product','type');

echo "Deleted successful..";