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
if ($version == '1.0.7') {
    return;
} elseif ($version != '1.0.6') {
    die('Please, run migration Rewards 1.0.6');
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/notification_rule')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/notification_rule_website')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rewards/notification_rule_customer_group')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/notification_rule')}` (
    `notification_rule_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `is_active` INT(11) ,
    `active_from` TIMESTAMP NULL,
    `active_to` TIMESTAMP NULL,
    `conditions_serialized` TEXT,
    `actions_serialized` TEXT,
    `sort_order` INT(11) ,
    `is_stop_processing` TINYINT(1) NOT NULL DEFAULT 0,
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `message` TEXT,
    PRIMARY KEY (`notification_rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/notification_rule_website')}` (
    `notification_rule_website_id` int(11) NOT NULL AUTO_INCREMENT,
    `website_id` SMALLINT(5) unsigned NOT NULL,
    `notification_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_notification_rule_website_website_id` (`website_id`),
    CONSTRAINT `mst_db6d4a7fb8972f4c254b7dd06ca60626` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_notification_rule_website_notification_rule_id` (`notification_rule_id`),
    CONSTRAINT `mst_255c148ec390133a55734a26d23b1e0f` FOREIGN KEY (`notification_rule_id`) REFERENCES `{$this->getTable('rewards/notification_rule')}` (`notification_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`notification_rule_website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/notification_rule_customer_group')}` (
    `notification_rule_customer_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    `notification_rule_id` INT(11) NOT NULL,
    KEY `fk_rewards_notification_rule_customer_group_customer_group_id` (`customer_group_id`),
    CONSTRAINT `mst_de9f1b36b6407092472fc563fdd539ec` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_notification_rule_customer_group_notification_rule_id` (`notification_rule_id`),
    CONSTRAINT `mst_e93bf2a010c9ea0e988df730faf40a7c` FOREIGN KEY (`notification_rule_id`) REFERENCES `{$this->getTable('rewards/notification_rule')}` (`notification_rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`notification_rule_customer_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

/*                                    **/

$installer->endSetup();
