<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
$attribute_code = 'theme_subthemes';
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
$options = $attribute->getSource()->getAllOptions();
$optionsDelete = array();
foreach($options as $option) {
    if ($option['value'] != "") {
        $optionsDelete['delete'][$option['value']] = true;
        $optionsDelete['value'][$option['value']] = true;
    }
}
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->addAttributeOption($optionsDelete);
echo "done!";