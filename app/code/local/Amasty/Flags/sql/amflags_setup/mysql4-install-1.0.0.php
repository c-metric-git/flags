<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
$installer = $this;
$installer->startSetup();
$installer->run("
                CREATE TABLE IF NOT EXISTS `{$this->getTable('amflags/flag')}` (
                 `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                 `alias` VARCHAR( 255 ) NOT NULL ,
                 `priority` SMALLINT NOT NULL ,
                 `comment` TEXT NOT NULL
                ) ENGINE = InnoDB DEFAULT CHARSET=utf8 ;
                ");
$installer->run("
                CREATE TABLE `{$this->getTable('amflags/order_flag')}` (
                 `entity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY , 
                 `order_id` INT UNSIGNED NOT NULL , 
                 `flag_id` INT UNSIGNED NOT NULL , 
                 `comment` TEXT NOT NULL 
                ) ENGINE = InnoDB DEFAULT CHARSET=utf8 ; 
                ALTER TABLE `{$this->getTable('amflags/order_flag')}` 
                 ADD UNIQUE ( `order_id` , `flag_id` ) ; 
                ");
$installer->run("
                INSERT INTO `{$this->getTable('amflags/flag')}` 
                 ( `entity_id` , `alias` , `priority` , `comment` ) 
                 VALUES 
                   ( 1 , 'Red'     , 100 , '' ) , 
                   ( 2 , 'Yellow'  , 90  , '' ) , 
                   ( 3 , 'Magenta' , 80  , '' ) , 
                   ( 4 , 'Blue'    , 70  , '' ) , 
                   ( 5 , 'Green'   , 60  , '' ) ; 
                ");
$installer->endSetup(); 