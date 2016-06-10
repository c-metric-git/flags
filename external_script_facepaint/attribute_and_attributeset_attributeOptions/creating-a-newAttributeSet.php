<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../../app/Mage.php';
Mage::app();
$entityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
$cloneSetId = 4; // Default Attribute set
//make sure an attribute set with the same name doesn't already exist

$model =Mage::getModel('eav/entity_attribute_set')
		->getCollection()
		->setEntityTypeFilter($entityTypeId)
		->addFieldToFilter('attribute_set_name','Brand & Size/Brush/FP Color/FP Design/Face Paint Kit Accessory/Kit Accessory/FP Size/Skin Color/Stencil Kit Accessory/FP Style/Title/Type')
		->getFirstItem();
//print_r($model);		
//echo "adf";
//exit;		
if(!is_object($model)){
	$model = Mage::getModel('eav/entity_attribute_set');
}
if(!is_numeric($model->getAttributeSetId())){
	$new = true;
}
$model->setEntityTypeId($entityTypeId);
						
$model->setAttributeSetName('Brand & Size/Brush/FP Color/FP Design/Face Paint Kit Accessory/Kit Accessory/FP Size/Skin Color/Stencil Kit Accessory/FP Style/Title/Type');
$model->validate();
$model->save();						
//if the set is new use the Magento Default Attributeset as a skeleton template for the default attributes						
if($new){
	$model->initFromSkeleton($cloneSetId)->save();
	echo "Save successfully!.";
}
