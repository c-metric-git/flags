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
                 ADD `apply_status` TEXT NOT NULL ;
                ");
$installer->endSetup(); 