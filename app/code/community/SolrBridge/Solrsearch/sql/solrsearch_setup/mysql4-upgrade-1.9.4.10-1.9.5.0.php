<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$query = "SELECT * FROM {$installer->getTable('core_config_data')} WHERE `path` like 'webmods_solrsearch%';";

$results = $installer->getConnection()->fetchAll($query);

foreach ($results as $config) {
	if (isset($config['path'])){
		$path = str_replace('webmods_', 'solrbridge_', $config['path']);
		$installer->setConfigData($path, $config['value']);
	}
}

//Delete old config data
$deleteQuery = "delete from {$installer->getTable('core_config_data')} where `path` like 'webmods_solrsearch%';";
$installer->getConnection()->query($deleteQuery);

$installer->run("
		DROP TABLE IF EXISTS {$this->getTable('webmods_solrsearch_logs')};
		DROP TABLE IF EXISTS {$this->getTable('solrbridge_solrsearch_logs')};
		
		CREATE TABLE {$this->getTable('solrbridge_solrsearch_logs')} (
		`logs_id` int(10) NOT NULL AUTO_INCREMENT,
		`store_id` int(5) NOT NULL DEFAULT '0',
		`solrcore` varchar(50) NOT NULL DEFAULT '',
		`percent` varchar(50) NOT NULL DEFAULT '',
		`update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`message` TEXT NULL,
		PRIMARY KEY (`logs_id`),
		KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_LOGS_ID` (`logs_id`),
		KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_STORE_ID` (`store_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		");

$installer->endSetup();