<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../../app/Mage.php';
Mage::app();

// This installer scripts adds a product attribute to Magento programmatically.

// Set data:
$attributeName  = 'Type'; // Name of the attribute
$attributeCode  = 'type'; // Code of the attribute
$attributeGroup = 'General';          // Group to add the attribute to
$attributeSetIds = array(51);          // Array with attribute set ID's to add this attribute to. (ID:4 is the Default Attribute Set)

// Configuration:
$data = array(
    'type'      => 'dropdown',       // Attribute type
    'input'     => 'select',          // Input type
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,    // Attribute scope
    'required'  => false,           // Is this attribute required?
    'user_defined' => false,
    'searchable' => true,
	'visible_in_advanced_search' => true,
    'filterable' => false,	
    'comparable' => true,
    'visible_on_front' => true,
    'unique' => false,
    'used_in_product_listing' => true,
    // Filled from above:
    'label' => $attributeName
);

// Create attribute:
// We create a new installer class here so we can also use this snippet in a non-EAV setup script.
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup'); 
$installer->startSetup();
$installer->addAttribute('catalog_product', $attributeCode, $data);

// Add the attribute to the proper sets/groups:
foreach($attributeSetIds as $attributeSetId)
{
    $installer->addAttributeToGroup('catalog_product', $attributeSetId, $attributeGroup, $attributeCode);
}

// Done:
$installer->endSetup();
echo "successful..";