<?php
$installer = $this;

$installer->startSetup();

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('solrbridge_solrsearch_request')};

	CREATE TABLE IF NOT EXISTS {$this->getTable('solrbridge_solrsearch_request')} (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
	`mobile_number` text NOT NULL COMMENT 'Mobile Number',
	`message` text COMMENT 'Message',
	`category` varchar(255) DEFAULT NULL COMMENT 'Category',
	`email_id` text COMMENT 'Email Id',
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Search Request Management!' AUTO_INCREMENT=1 ;
");

$installer->endSetup();