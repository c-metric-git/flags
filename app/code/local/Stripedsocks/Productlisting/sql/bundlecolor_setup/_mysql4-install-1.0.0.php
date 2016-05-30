<?php
$attributeName  = 'Bundle Option Title'; 
$attributeCode  = 'bundle_option_title'; 
$attributeGroup = 'General';          
$attributeSetIds = array(50,57,16,4);         

// Configuration:
$data = array(
    'type'      => 'int',       
    'input'     => 'select',          
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,    
    'required'  => false,           
    'user_defined' => false,
    'searchable' => true,
    'filterable' => true,
    'comparable' => true,
    'visible_on_front' => false,
    'unique' => false,
    'label' => $attributeName,
  
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