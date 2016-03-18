<?php
$installer = $this;
$installer->startSetup();
$installer->run("
        DROP TABLE IF EXISTS {$this->getTable('iwd_backup_flat_sales')};
        CREATE TABLE {$this->getTable('iwd_backup_flat_sales')}(
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `admin_user_id` INT(11) NOT NULL,
          `object_type` ENUM('order','invoice','shipment','creditmemo'),
          `deletion_at` TIMESTAMP NOT NULL,
          `object` TEXT,
          `object_items` TEXT,
          PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
	");
$installer->endSetup();
