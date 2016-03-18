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
/* @var $installer AdjustWare_Ordernum_Model_Mysql4_Setup  */
$installer->startSetup();
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql  */
$installer->setDefaultModuleConfig();
$installer->updateDefaultConfig();
$installer->updateWebsiteConfig();
$installer->updateStoreConfigs();


$installer->endSetup();