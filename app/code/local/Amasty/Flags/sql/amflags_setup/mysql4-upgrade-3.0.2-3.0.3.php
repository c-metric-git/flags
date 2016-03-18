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
                ADD `apply_column` VARCHAR(45) NOT NULL DEFAULT 1;
                ");
$installer->endSetup(); 