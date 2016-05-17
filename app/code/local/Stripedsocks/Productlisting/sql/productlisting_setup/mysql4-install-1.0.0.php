<?php

$installer = $this;

$this->startSetup();
$this->addAttribute('catalog_category', 'has_sets', array(
    'group'                => 'General Information',
    'type'              => 'int',//can be int, varchar, decimal, text, datetime
    'backend'           => '',
    'frontend_input'    => 'select',
    'frontend'          => '',
    'label'             => 'Has Sets',
    'input'             => 'select', //text, textarea, select, file, image, multilselect
    'source'            => 'eav/entity_attribute_source_boolean',//this is necessary for select and multilelect, for the rest leave it blank
    'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,//scope can be SCOPE_STORE or SCOPE_GLOBAL or SCOPE_WEBSITE
    'visible'           => true,
    'required'          => false,//or true
    'user_defined'      => true,
    'default'           => '',
	'visible_on_front' => true,
));
$this->endSetup();
