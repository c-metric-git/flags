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
/* @var $installer Mage_Core_Model_Resource_Setup  */
$installer->startSetup();

$installer->setDefaultModuleConfig();
$installer->setMageStoreConfig();
$installer->run("
UPDATE {$this->getTable('eav_entity_type')} SET `increment_model` = 'adjordernum/increment' WHERE `entity_type_code` IN ('order', 'invoice', 'shipment', 'creditmemo' ) LIMIT 4;
");

$installer->endSetup();