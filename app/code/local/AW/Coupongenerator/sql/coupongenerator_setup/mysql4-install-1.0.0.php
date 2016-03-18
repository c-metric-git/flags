<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Coupongenerator
 * @version    1.0.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

$this->startSetup();
try {
    $this->run("
        CREATE TABLE IF NOT EXISTS `{$this->getTable('coupongenerator/salesrule')}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `rule_id` int(10) unsigned NOT NULL,
            `expiration_days` smallint(5) unsigned NOT NULL DEFAULT '0',
            `extra_uses_per_coupon` int(11) NOT NULL DEFAULT '0',
            `coupon_prefix` varchar(16) DEFAULT NULL,
            `email_template` int(10) unsigned DEFAULT NULL,
            `email_template_config` tinyint(1) unsigned NOT NULL DEFAULT '1',
            PRIMARY KEY (`id`),
            UNIQUE KEY (`rule_id`),
            FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('salesrule/rule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

        CREATE TABLE IF NOT EXISTS `{$this->getTable('coupongenerator/coupon')}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `coupon_id` int(10) unsigned NOT NULL,
            `admin_user_id` int(10) unsigned DEFAULT NULL,
            `recipient_email` varchar(100) DEFAULT NULL,
            `customer_id` int(10) unsigned DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`coupon_id`),
            -- FOREIGN KEY (`admin_user_id`) REFERENCES {$this->getTable('admin/user')} (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
            FOREIGN KEY (`coupon_id`) REFERENCES {$this->getTable('salesrule/coupon')} (`coupon_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
    ");
} catch (Exception $ex) {
    Mage::logException($ex);
}

$this->endSetup();
