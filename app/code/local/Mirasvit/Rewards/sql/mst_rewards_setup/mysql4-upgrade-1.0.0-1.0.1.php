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
if ($version == '1.0.1') {
    return;
} elseif ($version != '1.0.0') {
    die('Please, run migration Rewards 1.0.0');
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/rate_customer_group')}`;
    ";
    $installer->run($sql);
}
$sql = "
ALTER TABLE `{$this->getTable('rewards/rate')}` DROP FOREIGN KEY `mst_632982faf103efa4dd5d5e8dd83325ed`;
ALTER TABLE `{$this->getTable('rewards/rate')}` DROP `customer_group_id`;

CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/rate_customer_group')}` (
    `rate_customer_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `rate_id` INT(11) NOT NULL,
    `customer_group_id` smallint(5) unsigned NOT NULL,
    KEY `fk_rewards_rate_customer_group_rate_id` (`rate_id`),
    CONSTRAINT `mst_302fa2ad0cef8e9966a41ef689459e17` FOREIGN KEY (`rate_id`) REFERENCES `{$this->getTable('rewards/rate')}` (`rate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rewards_rate_customer_group_customer_group_id` (`customer_group_id`),
    CONSTRAINT `mst_8918d72f44e39adaea93b9c9c015ab80` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`rate_customer_group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

/*                                    **/

$installer->endSetup();
