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



class Mirasvit_Rewards_Model_Config_Source_Referral_Status
{
    public function toArray()
    {
        return array(
            Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_SENT => Mage::helper('rewards')->__('Message Sent'),
            Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_SIGNUP => Mage::helper('rewards')->__('Signed Up'),
            Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_MADE_ORDER => Mage::helper('rewards')->__('Made Order'),
            Mirasvit_Rewards_Model_Config::REFERRAL_STATUS_VISITED => Mage::helper('rewards')->__('Visited by Referral Link'),
        );
    }
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->toArray() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
        }

        return $result;
    }

    /************************/
}
