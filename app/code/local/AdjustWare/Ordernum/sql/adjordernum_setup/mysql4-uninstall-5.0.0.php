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
/* @var $installer Aitoc_Aitsys_Model_Mysql4_Setup */
$setupModel = Mage::getModel('AdjustWare_Ordernum_Model_Mysql4_Setup','adjordernum_setup');
/* @var $setupModel AdjustWare_Ordernum_Model_Mysql4_Setup */
$installer->startSetup();
$hlp = Mage::helper('adminhtml');

$connection = $installer->getConnection();
$collection = $setupModel->getEavEntityStoreCollection();
foreach($collection as $field)
{
    $prefix = $field['increment_prefix'];
    $number = $field['increment_last_id'];
    $storeId = $field['store_id'];
    $typeId = $field['entity_type_id'];
    if(!empty($prefix))
    {
        if (strpos($number, $prefix)===0)
        {
            $number = (int)substr($number, strlen($prefix));
        } 
        else
        {
            $message = $hlp->__( 'Increment Prefix or Increment Last Id is corrupted in
                    table "eav_entity_store" in a row with a "entity_store_id" = '.$field['entity_store_id']);
            Mage::throwException($message);
        }   
    }
    $mageNumber = $setupModel->formatNumber($number, $storeId);
    $EavEntityStoreBind = array('increment_prefix' => $storeId,'increment_last_id' => $mageNumber);
    $EavEntityStoreWhere = 'store_id='.$storeId.' AND entity_type_id='.$typeId;
    $connection->update($installer->getTable('eav_entity_store'), $EavEntityStoreBind, $EavEntityStoreWhere);
}

$installer->run("
UPDATE {$this->getTable('eav_entity_type')} SET `increment_model` = 'eav/entity_increment_numeric' WHERE `entity_type_code` IN ('order', 'invoice', 'shipment', 'creditmemo' ) LIMIT 4;
");

//$installer->run("
//DELETE FROM {$this->getTable('adjordernum')} WHERE 1;
//");

$installer->endSetup();