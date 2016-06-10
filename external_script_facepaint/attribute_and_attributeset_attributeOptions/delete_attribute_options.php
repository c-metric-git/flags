<?php
require_once '../../app/Mage.php';
Mage::app();
$attribute_code = 'fp_size';
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
echo $attribute_code." done!";