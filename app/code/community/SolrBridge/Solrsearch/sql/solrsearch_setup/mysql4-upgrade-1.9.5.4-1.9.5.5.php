<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();


$query = "SELECT * FROM {$installer->getTable('core_config_data')} WHERE `path` like 'solrbridge_solrsearch_indexes/%';";
$results = $installer->getConnection()->fetchAll($query);
foreach ($results as $config) {
	if (isset($config['path'])){
		$path = str_replace('solrbridge_solrsearch_indexes', 'solrbridgeindices', $config['path']);
		$installer->setConfigData($path, $config['value']);
	}
}

$query = "SELECT * FROM {$installer->getTable('core_config_data')} WHERE `path` like 'solrbridge_solrsearch/%';";
$results = $installer->getConnection()->fetchAll($query);
foreach ($results as $config) {
	if (isset($config['path'])){
		$path = str_replace('solrbridge_solrsearch', 'solrbridge', $config['path']);
		$installer->setConfigData($path, $config['value']);
	}
}

//Delete old config data
//$deleteQuery = "delete from {$installer->getTable('core_config_data')} where `path` like 'solrbridge_solrsearch%';";
//$installer->getConnection()->query($deleteQuery);

//Delete old index table names
$allStores = Mage::getModel('core/store')->getCollection();

foreach ($allStores as $store) {
	$logTableName = $installer->getTable('solrsearch/logs');
	$indexedTableName = str_replace('_logs', '_index_'.$store->getId(), $logTableName);
	$dropSql = "DROP TABLE IF EXISTS ".$indexedTableName;
	$installer->getConnection()->query($dropSql);
}

$installer->run("
		REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
		('default', 0, 'solrbridge/settings/thread_enable', 0);
  ");

$installer->endSetup();