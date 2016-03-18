<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_weight", "TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_boost", "VARCHAR( 255 ) NOT NULL DEFAULT ''");
$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), "solr_search_field_range", "TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0'");

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('solrbridge_solrsearch_logs')};

	CREATE TABLE {$this->getTable('solrbridge_solrsearch_logs')} (
      `logs_id` int(10) NOT NULL AUTO_INCREMENT,
      `store_id` int(5) NOT NULL DEFAULT '0',
      `solrcore` varchar(50) NOT NULL DEFAULT '',
      `percent` varchar(50) NOT NULL DEFAULT '',
      `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `message` TEXT,
      PRIMARY KEY (`logs_id`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_LOGS_ID` (`logs_id`),
      KEY `IDX_WEBMODS_SOLRSEARCH_LOGS_STORE_ID` (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'solrbridge/settings/solr_server_url', 'http://localhost:8080/solr/'),
	('default', 0, 'solrbridge/settings/solr_server_url_auth', '0'),
	('default', 0, 'solrbridge/settings/solr_server_url_auth_username', NULL),
	('default', 0, 'solrbridge/settings/solr_server_url_auth_password', NULL),
	('default', 0, 'solrbridge/settings/solr_quick_search_display_thumnail', '1'),
	('default', 0, 'solrbridge/settings/solr_quick_search_allow_filter', '1'),
	('default', 0, 'solrbridge/settings/solr_index_auto_when_product_save', '1'),
	('default', 0, 'solrbridge/settings/solr_search_in_category', '1'),
	('default', 0, 'solrbridge/settings/use_category_as_facet', '1'),
	('default', 0, 'solrbridge/settings/display_category_as_hierachy', '1'),
	('default', 0, 'solrbridge/settings/display_brand_suggestion', '0'),
	('default', 0, 'solrbridge/settings/brand_attribute_code', 'manufacturer'),
	('default', 0, 'solrbridge/settings/items_per_commit', '50'),
	('default', 0, 'solrbridge/settings/use_ajax_result_page', '0'),
	('default', 0, 'solrbridgeindices/english/label', 'English'),
	('default', 0, 'solrbridgeindices/french/label', 'French'),
	('default', 0, 'solrbridgeindices/polish/label', 'Polish'),
	('default', 0, 'solrbridgeindices/dutch/label', 'Dutch'),
	('default', 0, 'solrbridgeindices/german/label', 'German'),
	('default', 0, 'solrbridgeindices/italian/label', 'Italian'),
	('default', 0, 'solrbridge/settings/allow_multiple_filter', '1'),
	('default', 0, 'solrbridge/settings/currency_position', '1');
  ");

$installer->endSetup();


//Uninstallationi scripts
/*
delete from core_config_data where `path` like 'solrbridge_solrsearch%';
delete from core_resource where `code` = 'solrsearch_setup';
*/