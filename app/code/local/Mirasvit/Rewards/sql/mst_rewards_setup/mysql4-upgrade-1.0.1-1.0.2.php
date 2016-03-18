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
if ($version == '1.0.2') {
    return;
} elseif ($version != '1.0.1') {
    die('Please, run migration Rewards 1.0.1');
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/referral')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/referral')}` (
    `referral_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(10) unsigned NOT NULL,
    `new_customer_id` int(10) unsigned,
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `status` VARCHAR(255) NOT NULL DEFAULT '',
    `store_id` SMALLINT(5) unsigned NOT NULL,
    `last_transaction_id` INT(11),
    `points_amount` INT(11) ,
    `created_at` TIMESTAMP NULL,
    KEY `fk_rewards_referral_customer_id` (`customer_id`),
    CONSTRAINT `mst_4d0bc45af26c35e88842e7eb11429867` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_referral_new_customer_id` (`new_customer_id`),
    CONSTRAINT `mst_4d0bc45af26c35e88842e7eb11429867_new` FOREIGN KEY (`new_customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rewards_referral_store_id` (`store_id`),
    CONSTRAINT `mst_819b1f817c3290285662f9560f3754a2` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_referral_last_transaction_id` (`last_transaction_id`),
    CONSTRAINT `mst_211cd4f7af012960ed7d0ceb384f2527` FOREIGN KEY (`last_transaction_id`) REFERENCES `{$this->getTable('rewards/transaction')}` (`transaction_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`referral_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

/*                                    **/

$installer->endSetup();
