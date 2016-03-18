<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'solrbridgeindices/danish/label', 'Danish'),
	('default', 0, 'solrbridgeindices/russian/label', 'Russian'),
	('default', 0, 'solrbridge/settings/replace_catalog_layer_nav', 0),
	('default', 0, 'solrbridge/settings/relevancy', 0);
  ");

$installer->endSetup();