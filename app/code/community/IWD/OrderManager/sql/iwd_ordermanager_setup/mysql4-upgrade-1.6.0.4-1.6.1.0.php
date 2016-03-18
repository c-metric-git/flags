<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` DROP FOREIGN KEY `FK_IWD_ORDER_GRID_ARCHIVE_CUSTOMER_ID_CUSTOMER_ENT_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` DROP FOREIGN KEY `FK_IWD_ORDER_GRID_ARCHIVE_ENT_ID_SALES_FLAT_ORDER_ENT_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` DROP FOREIGN KEY `FK_IWD_ORDER_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` ADD CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_CUSTOMER_ID_CUSTOMER_ENT_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer_entity')}` (`entity_id`) ON UPDATE CASCADE ON DELETE SET NULL;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` ADD CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_ENT_ID_SALES_FLAT_ORDER_ENT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_flat_order')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` ADD CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL;

    ALTER TABLE `{$this->getTable('iwd_sales_archive_invoice_grid')}` DROP FOREIGN KEY `FK_IWD_INVOICE_GRID_ARCHIVE_ENT_ID_SALES_FLAT_INVOICE_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_invoice_grid')}` DROP FOREIGN KEY `FK_IWD_INVOICE_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_invoice_grid')}` ADD CONSTRAINT `FK_IWD_INVOICE_GRID_ARCHIVE_ENT_ID_SALES_FLAT_INVOICE_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_flat_invoice')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_invoice_grid')}` ADD CONSTRAINT `FK_IWD_INVOICE_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL;

    ALTER TABLE `{$this->getTable('iwd_sales_archive_shipment_grid')}` DROP FOREIGN KEY `FK_IWD_SHIPMENT_GRID_ARCHIVE_ENT_ID_SALES_FLAT_SHIPMENT_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_shipment_grid')}` DROP FOREIGN KEY `FK_ENT_SHIPMENT_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_shipment_grid')}` ADD CONSTRAINT `FK_IWD_SHIPMENT_GRID_ARCHIVE_ENT_ID_SALES_FLAT_SHIPMENT_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_flat_shipment')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_shipment_grid')}` ADD CONSTRAINT `FK_ENT_SHIPMENT_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL;

    ALTER TABLE `{$this->getTable('iwd_sales_archive_creditmemo_grid')}` DROP FOREIGN KEY `FK_IWD_CREDITMEMO_GRID_ARCHIVE_ID_SALES_FLAT_CREDITMEMO_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_creditmemo_grid')}` DROP FOREIGN KEY `FK_ENT_CREDITMEMO_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID`;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_creditmemo_grid')}` ADD CONSTRAINT `FK_IWD_CREDITMEMO_GRID_ARCHIVE_ID_SALES_FLAT_CREDITMEMO_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('sales_flat_creditmemo')}` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE;
    ALTER TABLE `{$this->getTable('iwd_sales_archive_creditmemo_grid')}` ADD CONSTRAINT `FK_ENT_CREDITMEMO_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE;
");
$installer->endSetup();