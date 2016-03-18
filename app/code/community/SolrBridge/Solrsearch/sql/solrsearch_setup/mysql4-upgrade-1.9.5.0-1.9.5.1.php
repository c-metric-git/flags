<?php
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("
    REPLACE INTO {$this->getTable('core_config_data')} (`scope`, `scope_id`, `path`, `value`) VALUES
	('default', 0, 'solrbridge/settings/autocomplete_product_limit', '5'),
	('default', 0, 'solrbridge/settings/autocomplete_brand_limit', '3'),
	('default', 0, 'solrbridge/settings/autocomplete_category_limit', '3'),
	('default', 0, 'solrbridge/settings/use_ajax_result_page', '0'),
	('default', 0, 'solrbridge/settings/use_price_slider', '0'),
	('default', 0, 'solrbridge/settings/autocomplete_thumb_size', '32x32');
  ");

$installer->endSetup();