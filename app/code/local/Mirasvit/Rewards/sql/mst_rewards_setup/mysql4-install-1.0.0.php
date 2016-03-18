<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Reward Points + Referral program
 * @version   1.1.2
 * @build     928
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */



/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$version = Mage::helper('mstcore/version')->getModuleVersionFromDb('mst_rewards');
if ($version == '1.0.0') {
    return;
}

$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/transaction')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/earning_rule')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/earning_rule_website')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/earning_rule_customer_group')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/earning_rule_product')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/rate')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/transaction')}` (
    `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(10) unsigned NOT NULL,
    `amount` INT(11) ,
    `comment` TEXT,
    `code` VARCHAR(255) NOT NULL DEFAULT '',
    `is_expired` TINYINT(1) NOT NULL DEFAULT 0,
    `is_expiration_email_sent` INT(11) ,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    KEY `fk_rewards_transaction_customer_id` (`customer_id`),
    CONSTRAINT `mst_483d47d6d9ca6e6ce9c5c98afb324511` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/earning_rule')}` (
    `earning_rule_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT,
    `is_active` INT(11) ,
    `active_from` TIMESTAMP NULL,
    `active_to` TIMESTAMP NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `conditions_serialized` TEXT,
    `actions_serialized` TEXT,
    `earning_style` VARCHAR(255) NOT NULL DEFAULT '',
    `points_amount` INT(11) ,
    `monetary_step` FLOAT,
    `qty_step` INT(11) ,
    `points_limit` INT(11) ,
    `behavior_trigger` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` INT(11) ,
    `is_stop_processing` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`earning_rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/earning_rule_website')}` (
    `earning_rule_website_id` int(11) NOT NULL AUTO_INCREMENT,
    `website_id` SMALLINT(5) unsigned NOT NULL,
    `earning_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_earning_rule_website_website_id` (`website_id`),
    CONSTRAINT `mst_22e9ea3fd7becfad6700fdbbc5a0e877` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_earning_rule_website_earning_rule_id` (`earning_rule_id`),
    CONSTRAINT `mst_cf771d9232aff1edb207f2d044aa60e2` FOREIGN KEY (`earning_rule_id`) REFERENCES `{$this->getTable('rewards/earning_rule')}` (`earning_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`earning_rule_website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/earning_rule_customer_group')}` (
    `earning_rule_customer_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `earning_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_earning_rule_customer_group_customer_group_id` (`customer_group_id`),
    CONSTRAINT `mst_5eca1729c31b360f69e120b50da0d862` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_earning_rule_customer_group_earning_rule_id` (`earning_rule_id`),
    CONSTRAINT `mst_60a3a681dbe1d9af2ea78afbdd7412a9` FOREIGN KEY (`earning_rule_id`) REFERENCES `{$this->getTable('rewards/earning_rule')}` (`earning_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`earning_rule_customer_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/earning_rule_product')}` (
    `earning_rule_product_id` int(11) NOT NULL AUTO_INCREMENT,
    `earning_rule_id` INT(11) NOT NULL,
    `er_product_id` int(10) unsigned NOT NULL,
    `er_website_id` SMALLINT(5) unsigned NOT NULL,
    `er_customer_group_id` smallint(5) unsigned NOT NULL,
    KEY `fk_rewards_earning_rule_product_earning_rule_id` (`earning_rule_id`),
    CONSTRAINT `mst_de7c3c4d87be41a9c4914614ccd1e54d` FOREIGN KEY (`earning_rule_id`) REFERENCES `{$this->getTable('rewards/earning_rule')}` (`earning_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_earning_rule_product_product_id` (`er_product_id`),
    CONSTRAINT `mst_c5d158838da3c1e937ceca03b8eb0c78` FOREIGN KEY (`er_product_id`) REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_earning_rule_product_website_id` (`er_website_id`),
    CONSTRAINT `mst_b01ee6099d2355e926e0ac84ebf62b67` FOREIGN KEY (`er_website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_earning_rule_product_customer_group_id` (`er_customer_group_id`),
    CONSTRAINT `mst_0fb1cf3119719a0bf242d0b672c26b5d` FOREIGN KEY (`er_customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`earning_rule_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/rate')}` (
    `rate_id` int(11) NOT NULL AUTO_INCREMENT,
    `rate_from` INT(11) ,
    `rate_to` INT(11) ,
    `direction` VARCHAR(255) NOT NULL DEFAULT '',
    `website_id` SMALLINT(5) unsigned NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    KEY `fk_rewards_rate_website_id` (`website_id`),
    CONSTRAINT `mst_beee6cdfa89192a5c99058a6a58d6c79` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_rate_customer_group_id` (`customer_group_id`),
    CONSTRAINT `mst_632982faf103efa4dd5d5e8dd83325ed` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`rate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

// try {
// $sql = "
// ALTER TABLE `{$this->getTable('sales/order')}` ADD `rewards_points_number` INT(11);
// ALTER TABLE `{$this->getTable('sales/order')}` ADD `rewards_base_amount` DECIMAL(11);
// ALTER TABLE `{$this->getTable('sales/order')}` ADD `rewards_amount` DECIMAL(11);

// ALTER TABLE `{$this->getTable('sales/quote')}` ADD `rewards_points_number` INT(11);
// ALTER TABLE `{$this->getTable('sales/quote')}` ADD `rewards_base_amount` DECIMAL(11);
// ALTER TABLE `{$this->getTable('sales/quote')}` ADD `rewards_amount` DECIMAL(11);
// ";
// $installer->run($sql);
// } catch(Exception $e) {}

$installer->endSetup();
