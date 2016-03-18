<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'solrbridge/settings/excluded_categories_recusive', '0'),
	('default', 0, 'solrbridgeindices/spanish/label', 'Spanish'),
	('default', 0, 'solrbridgeindices/hungarian/label', 'Hungarian'),
	('default', 0, 'solrbridgeindices/portuguese/label', 'Portuguese'),
	('default', 0, 'solrbridgeindices/swedish/label', 'Swedish'),
	('default', 0, 'solrbridgeindices/finnish/label', 'Finnish');
  ");

$installer->endSetup();