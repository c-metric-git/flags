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
if ($version == '1.0.6') {
    return;
} elseif ($version != '1.0.5') {
    die('Please, run migration Rewards 1.0.5');
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/spending_rule')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/spending_rule_website')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/spending_rule_customer_group')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/spending_rule')}` (
    `spending_rule_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT,
    `is_active` INT(11) ,
    `active_from` TIMESTAMP NULL,
    `active_to` TIMESTAMP NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `conditions_serialized` TEXT,
    `actions_serialized` TEXT,
    `spending_style` VARCHAR(255) NOT NULL DEFAULT '',
    `spend_points` INT(11) ,
    `monetary_step` DOUBLE,
    `spend_min_points` VARCHAR(255) NOT NULL DEFAULT '',
    `spend_max_points` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` INT(11) ,
    `is_stop_processing` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`spending_rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/spending_rule_website')}` (
    `spending_rule_website_id` int(11) NOT NULL AUTO_INCREMENT,
    `website_id` SMALLINT(5) unsigned NOT NULL,
    `spending_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_spending_rule_website_website_id` (`website_id`),
    CONSTRAINT `mst_e1d20ef11f53c2797bdb6d5404fef576` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_spending_rule_website_spending_rule_id` (`spending_rule_id`),
    CONSTRAINT `mst_2ba237f460951d74ab9a4719a0e67fcc` FOREIGN KEY (`spending_rule_id`) REFERENCES `{$this->getTable('rewards/spending_rule')}` (`spending_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`spending_rule_website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/spending_rule_customer_group')}` (
    `spending_rule_customer_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `spending_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_spending_rule_customer_group_customer_group_id` (`customer_group_id`),
    CONSTRAINT `mst_846b14acba8c499c87ea5ac96dbd3503` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_spending_rule_customer_group_spending_rule_id` (`spending_rule_id`),
    CONSTRAINT `mst_21a7d45c306a3a68e34f647cec115d2b` FOREIGN KEY (`spending_rule_id`) REFERENCES `{$this->getTable('rewards/spending_rule')}` (`spending_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`spending_rule_customer_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

// try {
$sql = "
ALTER TABLE `{$this->getTable('rewards/purchase')}` CHANGE `redeem_points` `spend_points` INT(11);
ALTER TABLE `{$this->getTable('rewards/purchase')}` CHANGE `redeem_amount` `spend_amount` DOUBLE;
ALTER TABLE `{$this->getTable('rewards/purchase')}` ADD `spend_max_points` INT(11)  NULL  DEFAULT NULL AFTER spend_amount;
ALTER TABLE `{$this->getTable('rewards/purchase')}` ADD `spend_min_points` INT(11)  NULL  DEFAULT NULL AFTER spend_amount;
";
$installer->run($sql);
// } catch (Exception $e) {
// }
/*                                    **/

$installer->endSetup();
