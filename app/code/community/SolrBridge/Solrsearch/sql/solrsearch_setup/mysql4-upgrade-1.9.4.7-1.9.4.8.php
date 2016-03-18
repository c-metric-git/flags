<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'solrbridgeindices/norwegian/label', 'Norwegian');
  ");

$installer->endSetup();