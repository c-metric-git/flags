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
if ($version == '1.0.4') {
    return;
} elseif ($version != '1.0.3') {
    die('Please, run migration Rewards 1.0.3');
}
$installer->startSetup();
$sql = "
ALTER TABLE `{$this->getTable('rewards/earning_rule')}` ADD COLUMN `param1` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `{$this->getTable('rewards/earning_rule')}` ADD COLUMN `history_message` TEXT;
ALTER TABLE `{$this->getTable('rewards/earning_rule')}` ADD COLUMN `email_message` TEXT;
ALTER TABLE `{$this->getTable('rewards/rate')}` MODIFY rate_from DECIMAL(6,2);
ALTER TABLE `{$this->getTable('rewards/rate')}` MODIFY rate_to DECIMAL(6,2);
";
$installer->run($sql);

/*                                    **/

$installer->endSetup();
