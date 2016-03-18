<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
$installer = $this;

$installer->startSetup();


$installer->run("
CREATE TABLE {$this->getTable('adjordernum')} (
  `ordernum_id` smallint(5) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL,
  `order_prefix` varchar(32) NOT NULL,
  `order_start` int(11) NOT NULL,
  `order_incr` int(10) unsigned NOT NULL,
  `invoice_prefix` varchar(32) NOT NULL,
  `invoice_start` int(11) NOT NULL,
  `invoice_incr` int(11) NOT NULL,
  `shipment_prefix` varchar(32) default NULL,
  `shipment_start` int(11) NOT NULL,
  `shipment_incr` int(11) NOT NULL,
  `creditmemo_prefix` varchar(32) NOT NULL,
  `creditmemo_start` int(11) NOT NULL,
  `creditmemo_incr` int(11) NOT NULL,
  PRIMARY KEY  (`ordernum_id`),
  UNIQUE KEY `store_id` (`store_id`)
) ENGINE=InnoDB CHARSET=utf8;


");


$installer->endSetup();