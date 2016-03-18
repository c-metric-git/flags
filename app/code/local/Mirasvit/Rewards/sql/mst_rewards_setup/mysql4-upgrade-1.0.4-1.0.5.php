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
if ($version == '1.0.5') {
    return;
} elseif ($version != '1.0.4') {
    die('Please, run migration Rewards 1.0.4');
}
$installer->startSetup();
if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rewards/purchase')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE IF NOT EXISTS `{$this->getTable('rewards/purchase')}` (
    `purchase_id` int(11) NOT NULL AUTO_INCREMENT,
    `quote_id` INT(11) ,
    `order_id` INT(11) ,
    `redeem_points` INT(11) ,
    `redeem_amount` FLOAT,
    `earn_points` INT(11) ,
    PRIMARY KEY (`purchase_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
";
$installer->run($sql);

try {
    $sql = "
ALTER TABLE `{$this->getTable('rewards/purchase')}` ADD UNIQUE INDEX (`order_id`);
ALTER TABLE `{$this->getTable('rewards/purchase')}` ADD UNIQUE INDEX (`quote_id`);
";
    $installer->run($sql);
} catch (Exception $e) {
}

try {
    $sql = "
ALTER TABLE `{$this->getTable('rewards/earning_rule')}` CHANGE `points_amount` `earn_points` INT(11)  NULL  DEFAULT NULL;
ALTER TABLE `{$this->getTable('rewards/referral')}` ADD `quote_id` INT(11)  NULL  DEFAULT NULL;
";
    $installer->run($sql);
} catch (Exception $e) {
}
/*                                    **/

$installer->endSetup();
