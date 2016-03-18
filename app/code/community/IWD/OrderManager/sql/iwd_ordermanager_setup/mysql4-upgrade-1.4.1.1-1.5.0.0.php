<?php
$installer = $this;
$installer->startSetup();

/** add tables for archive **/
    $installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('iwd_sales_archive_order_grid')}`;
    CREATE TABLE `{$this->getTable('iwd_sales_archive_order_grid')}` (
        `entity_id` INT(10) UNSIGNED NOT NULL COMMENT 'Entity Id',
        `increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Increment Id',
        `status` VARCHAR(32) NULL DEFAULT NULL COMMENT 'Status',
        `store_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Store Id',
        `store_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Store Name',
        `customer_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Customer Id',
        `base_grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base Grand Total',
        `base_total_paid` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base Total Paid',
        `grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Grand Total',
        `total_paid` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Total Paid',
        `base_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Base Currency Code',
        `order_currency_code` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Order Currency Code',
        `shipping_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Shipping Name',
        `billing_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Billing Name',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Updated At',
        PRIMARY KEY (`entity_id`),
        UNIQUE INDEX `UNQ_IWD_SALES_ORDER_GRID_ARCHIVE_INCREMENT_ID` (`increment_id`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_STORE_ID` (`store_id`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_CUSTOMER_ID` (`customer_id`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_STATUS` (`status`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_BASE_GRAND_TOTAL` (`base_grand_total`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_BASE_TOTAL_PAID` (`base_total_paid`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_GRAND_TOTAL` (`grand_total`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_TOTAL_PAID` (`total_paid`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_SHIPPING_NAME` (`shipping_name`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_BILLING_NAME` (`billing_name`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_CREATED_AT` (`created_at`),
        INDEX `IDX_IWD_SALES_ORDER_GRID_ARCHIVE_UPDATED_AT` (`updated_at`),
        CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_CUSTOMER_ID_CUSTOMER_ENT_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON UPDATE CASCADE ON DELETE SET NULL,
        CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_ENT_ID_SALES_FLAT_ORDER_ENT_ID` FOREIGN KEY (`entity_id`) REFERENCES `sales_flat_order` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_IWD_ORDER_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COMMENT='IWD Sales Order Grid Archive'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('iwd_sales_archive_invoice_grid')}`;
    CREATE TABLE `{$this->getTable('iwd_sales_archive_invoice_grid')}` (
        `entity_id` INT(10) UNSIGNED NOT NULL COMMENT 'Entity Id',
        `increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Increment Id',
        `store_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Store Id',
        `base_grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base Grand Total',
        `grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Grand Total',
        `order_id` INT(10) UNSIGNED NOT NULL COMMENT 'Order Id',
        `state` INT(11) NULL DEFAULT NULL COMMENT 'State',
        `store_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Store Currency Code',
        `order_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Order Currency Code',
        `base_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Base Currency Code',
        `global_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Global Currency Code',
        `order_increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Order Increment Id',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `order_created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Order Created At',
        `billing_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Billing Name',
        PRIMARY KEY (`entity_id`),
        UNIQUE INDEX `UNQ_IWD_SALES_INVOICE_GRID_ARCHIVE_INCREMENT_ID` (`increment_id`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_STORE_ID` (`store_id`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_GRAND_TOTAL` (`grand_total`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_ORDER_ID` (`order_id`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_STATE` (`state`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_ORDER_INCREMENT_ID` (`order_increment_id`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_CREATED_AT` (`created_at`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_ORDER_CREATED_AT` (`order_created_at`),
        INDEX `IDX_IWD_SALES_INVOICE_GRID_ARCHIVE_BILLING_NAME` (`billing_name`),
        CONSTRAINT `FK_IWD_INVOICE_GRID_ARCHIVE_ENT_ID_SALES_FLAT_INVOICE_ID` FOREIGN KEY (`entity_id`) REFERENCES `sales_flat_invoice` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_IWD_INVOICE_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COMMENT='IWD Sales Invoice Grid Archive'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('iwd_sales_archive_shipment_grid')}`;
    CREATE TABLE `{$this->getTable('iwd_sales_archive_shipment_grid')}` (
        `entity_id` INT(10) UNSIGNED NOT NULL COMMENT 'Entity Id',
        `increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Increment Id',
        `store_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Store Id',
        `total_qty` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Total Qty',
        `order_id` INT(10) UNSIGNED NOT NULL COMMENT 'Order Id',
        `shipment_status` INT(11) NULL DEFAULT NULL COMMENT 'Shipment Status',
        `order_increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Order Increment Id',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `order_created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Order Created At',
        `shipping_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Shipping Name',
        PRIMARY KEY (`entity_id`),
        UNIQUE INDEX `UNQ_IWD_SALES_SHIPMENT_GRID_ARCHIVE_INCREMENT_ID` (`increment_id`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_STORE_ID` (`store_id`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_TOTAL_QTY` (`total_qty`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_ORDER_ID` (`order_id`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_SHIPMENT_STATUS` (`shipment_status`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_ORDER_INCREMENT_ID` (`order_increment_id`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_CREATED_AT` (`created_at`),
        INDEX `IDX_IWD_SALES_SHIPMENT_GRID_ARCHIVE_ORDER_CREATED_AT` (`order_created_at`),
        INDEX `IDX_SALES_FLAT_SHIPMENT_GRID_SHIPPING_NAME` (`shipping_name`),
	    CONSTRAINT `FK_IWD_SHIPMENT_GRID_ARCHIVE_ENT_ID_SALES_FLAT_SHIPMENT_ID` FOREIGN KEY (`entity_id`) REFERENCES `sales_flat_shipment` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_ENT_SHIPMENT_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON UPDATE CASCADE ON DELETE SET NULL
    )
    COMMENT='IWD Sales Shipment Grid Archive'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('iwd_sales_archive_creditmemo_grid')}`;
    CREATE TABLE `{$this->getTable('iwd_sales_archive_creditmemo_grid')}` (
        `entity_id` INT(10) UNSIGNED NOT NULL COMMENT 'Entity Id',
        `increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Increment Id',
        `store_id` SMALLINT(5) UNSIGNED NULL DEFAULT NULL COMMENT 'Store Id',
        `store_to_order_rate` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Store To Order Rate',
        `base_to_order_rate` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base To Order Rate',
        `grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Grand Total',
        `store_to_base_rate` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Store To Base Rate',
        `base_to_global_rate` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base To Global Rate',
        `base_grand_total` DECIMAL(12,4) NULL DEFAULT NULL COMMENT 'Base Grand Total',
        `order_id` INT(10) UNSIGNED NOT NULL COMMENT 'Order Id',
        `creditmemo_status` INT(11) NULL DEFAULT NULL COMMENT 'Creditmemo Status',
        `state` INT(11) NULL DEFAULT NULL COMMENT 'State',
        `invoice_id` INT(11) NULL DEFAULT NULL COMMENT 'Invoice Id',
        `store_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Store Currency Code',
        `order_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Order Currency Code',
        `base_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Base Currency Code',
        `global_currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Global Currency Code',
        `order_increment_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Order Increment Id',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `order_created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Order Created At',
        `billing_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Billing Name',
        PRIMARY KEY (`entity_id`),
        UNIQUE INDEX `UNQ_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_INCREMENT_ID` (`increment_id`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_STORE_ID` (`store_id`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_GRAND_TOTAL` (`grand_total`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_BASE_GRAND_TOTAL` (`base_grand_total`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_ORDER_ID` (`order_id`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_CREDITMEMO_STATUS` (`creditmemo_status`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_STATE` (`state`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_ORDER_INCREMENT_ID` (`order_increment_id`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_CREATED_AT` (`created_at`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_ORDER_CREATED_AT` (`order_created_at`),
        INDEX `IDX_IWD_SALES_CREDITMEMO_GRID_ARCHIVE_BILLING_NAME` (`billing_name`),
        CONSTRAINT `FK_IWD_CREDITMEMO_GRID_ARCHIVE_ID_SALES_FLAT_CREDITMEMO_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON UPDATE CASCADE ON DELETE CASCADE,
        CONSTRAINT `FK_ENT_CREDITMEMO_GRID_ARCHIVE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`entity_id`) REFERENCES `sales_flat_creditmemo` (`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
    )
    COMMENT='IWD Sales Creditmemo Grid Archive'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


    DROP TABLE IF EXISTS `{$this->getTable('iwd_edit_order_confirm')}`;
    CREATE TABLE `{$this->getTable('iwd_edit_order_confirm')}` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `order_id` INT(10) UNSIGNED NOT NULL COMMENT 'Order Id',
        `edit_type` INT(2) UNSIGNED NOT NULL COMMENT 'Edit type shipping-payment-items',
        `params` TEXT NULL COMMENT 'Query, params',
        `log_operations` TEXT NULL COMMENT 'Log Operations',
        `status` INT(2) NOT NULL DEFAULT '0' COMMENT 'Status new-open-confirm',
        `confirm_link` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Confirm Link',
        `confirm_ip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Confirm From IP',
        `request_ip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Update Request From IP',
        `customer_email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Customer Email',
        `admin_id` INT(10) UNSIGNED NOT NULL COMMENT 'Admin Id',
        `admin_name` VARCHAR(255) NULL COMMENT 'Admin Name',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Updated At',

        PRIMARY KEY (`id`),
        UNIQUE INDEX `UNQ_IWD_EDIT_ORDER_CONFIRM_ID` (`id`)
    )
    COMMENT='IWD EDIT ORDER CONFIRM AND LOG'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

/** add table for confirm **/
$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('iwd_edit_order_confirm')}`;
    CREATE TABLE `{$this->getTable('iwd_edit_order_confirm')}` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Id',
        `order_id` INT(10) UNSIGNED NOT NULL COMMENT 'Order Id',
        `edit_type` INT(2) UNSIGNED NOT NULL COMMENT 'Edit type shipping-payment-items',
        `params` TEXT NULL COMMENT 'Query, params',
        `log_operations` TEXT NULL COMMENT 'Log Operations',
        `status` INT(2) NOT NULL DEFAULT '0' COMMENT 'Status new-open-confirm',
        `confirm_link` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Confirm Link',
        `confirm_ip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Confirm From IP',
        `request_ip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Update Request From IP',
        `customer_email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Customer Email',
        `admin_id` INT(10) UNSIGNED NOT NULL COMMENT 'Admin Id',
        `admin_name` VARCHAR(255) NULL COMMENT 'Admin Name',
        `created_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Created At',
        `updated_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Updated At',

        PRIMARY KEY (`id`),
        UNIQUE INDEX `UNQ_IWD_EDIT_ORDER_CONFIRM_ID` (`id`)
    )
    COMMENT='IWD EDIT ORDER CONFIRM AND LOG'
    ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$block = Mage::getModel('cms/block');
$page = Mage::getModel('cms/page');
$stores = array(0);

/** add static blocks **/
/* 1. Error confirm/cancel operation */
$dataBlock = array(
    'title' => 'IWD OrderManager - Confirmation Error',
    'identifier' => 'iwd_ordermanager_confirm_error',
    'stores' => $stores,
    'is_active' => 1,
    'content'	=> <<<EOB
		<h1 class="title">Confirmation Error</h1>
		<p>Sorry, confirmation error</p>
EOB
);
$block->setData($dataBlock)->save();

/* 2. Error confirm operation success */
$dataBlock = array(
    'title' => 'IWD OrderManager - Confirmation Cancel',
    'identifier' => 'iwd_ordermanager_confirm_cancel',
    'stores' => $stores,
    'is_active' => 1,
    'content'	=> <<<EOB
		<h1 class="title">Confirmation Cancel</h1>
		<p>Confirmation for edit order was canceled!</p>
EOB
);
$block->setData($dataBlock)->save();

/* 3. Error cancel operation success */
$dataBlock = array(
    'title' => 'IWD OrderManager - Confirmation Success',
    'identifier' => 'iwd_ordermanager_confirm_success',
    'stores' => $stores,
    'is_active' => 1,
    'content'	=> <<<EOB
		<h1 class="title">Confirmation Success</h1>
		<p>Confirmation for edit order was successfully accepted.</p>
EOB
);
$block->setData($dataBlock)->save();

$installer->endSetup();
