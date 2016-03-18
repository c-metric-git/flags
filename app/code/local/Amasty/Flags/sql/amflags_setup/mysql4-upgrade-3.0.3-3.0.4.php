<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Flags
 */
$this->startSetup();
$this->run(" ALTER TABLE `{$this->getTable('amflags/flag')}` ADD `apply_payment` TEXT NOT NULL ; ");
$this->endSetup();