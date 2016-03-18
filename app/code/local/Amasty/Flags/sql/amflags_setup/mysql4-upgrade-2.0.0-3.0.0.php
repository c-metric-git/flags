<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
$installer = $this;
$installer->startSetup();
$installer->run("
                ALTER TABLE `{$this->getTable('amflags/flag')}` 
                 ADD `apply_shipping` TEXT NOT NULL ; 
                ");
$installer->run("
                ALTER TABLE `{$this->getTable('amflags/order_flag')}` 
                 DROP INDEX `order_id` , 
                 ADD `column_id` INT UNSIGNED NOT NULL , 
                 ADD UNIQUE INDEX `order_id` ( `order_id` , `column_id` ) ; 
                ");
$installer->run("
                UPDATE `{$this->getTable('amflags/order_flag')}` 
                 SET `column_id` = 1 ; 
                ");                
$installer->run("
                CREATE TABLE IF NOT EXISTS `{$this->getTable('amflags/column')}` ( 
                 `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , 
                 `alias` VARCHAR( 255 ) NOT NULL , 
                 `pos` SMALLINT NOT NULL , 
                 `comment` TEXT NOT NULL , 
                 `apply_flag` TEXT NOT NULL 
                ) ENGINE = InnoDB DEFAULT CHARSET=utf8 ; 
                ");

// Get existing flags for applying to column
$flagCollection = Mage::getModel('amflags/flag')->getCollection();
$flags = array();
foreach ($flagCollection as $flag)
{
    $flags[] = $flag->getEntityId();
}
$applyFlags = implode(',', $flags);

$installer->run("
                INSERT INTO `{$this->getTable('amflags/column')}` 
                 ( `entity_id` , `alias` , `pos` , `comment` , `apply_flag` ) 
                 VALUES 
                   ( 1 , 'Flags' , 1 , '' , '{$applyFlags}' ) ; 
                ");
$installer->endSetup();