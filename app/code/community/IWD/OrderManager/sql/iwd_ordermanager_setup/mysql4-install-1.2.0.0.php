<?php
$installer = $this;
$installer->startSetup();
$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('iwd_backup_flat_comments')};
        CREATE TABLE {$this->getTable('iwd_backup_flat_comments')}(
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `admin_user_id` INT(11) NOT NULL,
          `history_by` ENUM('order','invoice','shipment','creditmemo'),
          `deletion_at` TIMESTAMP NOT NULL,
          `deletion_row` TEXT,
          PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");
$installer->endSetup();
