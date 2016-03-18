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


class Mirasvit_Rewards_Helper_Validator extends Mirasvit_MstCore_Helper_Validator_Abstract
{
    public function testMagentoCrc()
    {
        $filter = array(
            'app/code/core/Mage/Core',
            'app/code/core/Mage/Review',
            'js'
        );
        return Mage::helper('mstcore/validator_crc')->testMagentoCrc($filter);
    }

    public function testMirasvitCrc()
    {
        $modules = array('Rewards');
        return Mage::helper('mstcore/validator_crc')->testMirasvitCrc($modules);
    }

    public function testISpeedCache()
    {
        $result = self::SUCCESS;
        $title = 'My_Ispeed';
        $description = array();
        if (Mage::helper('mstcore')->isModuleInstalled('My_Ispeed')) {
            $result = self::INFO;
            $description[] = 'Extension My_Ispeed is installed. Please, go to the Configuration > Settings > I-Speed > General Configuration and add \'rewards\' to the list of Ignored URLs. Then clear ALL cache.';
        }

        return array($result, $title, $description);
    }

    public function testMgtVarnishCache()
    {
        $result = self::SUCCESS;
        $title = 'Mgt_Varnish';
        $description = array();
        if (Mage::helper('mstcore')->isModuleInstalled('Mgt_Varnish')) {
            $result = self::INFO;
            $description[] = 'Extension Mgt_Varnish is installed. Please, go to the Configuration > Settings > MGT-COMMERCE.COM > Varnish and add \'rewards\' to the list of Excluded Routes. Then clear ALL cache.';
        }

        return array($result, $title, $description);
    }


    public function testTables()
    {
        $tables = array(
            'customer/entity',
            'core/store',
            'rewards/earning_rule',
            'rewards/earning_rule_customer_group',
            'rewards/earning_rule_product',
            'rewards/earning_rule_website',
            'rewards/spending_rule',
            'rewards/spending_rule_customer_group',
            'rewards/spending_rule_website',
            'rewards/referral',
            'rewards/transaction',
        );
        return $this->dbCheckTables($tables);
    }
}