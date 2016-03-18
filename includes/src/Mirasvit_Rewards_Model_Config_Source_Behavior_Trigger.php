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



class Mirasvit_Rewards_Model_Config_Source_Behavior_Trigger
{
    public function toArray()
    {
        return array(
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SIGNUP => Mage::helper('rewards')->__('Customer signs up in store'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_VOTE => Mage::helper('rewards')->__('Customer votes'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SEND_LINK => Mage::helper('rewards')->__('Customer emails product\'s link to a friend'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP => Mage::helper('rewards')->__('Newsletter sign up'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TAG => Mage::helper('rewards')->__('Customer adds tag to a product'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REVIEW => Mage::helper('rewards')->__('Customer writes a product\'s review'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_BIRTHDAY => Mage::helper('rewards')->__('Customer has a birthday'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_INACTIVITY => Mage::helper('rewards')->__('Customer is not active for long time'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE => Mage::helper('rewards')->__('Facebook Like'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TWITTER_TWEET => Mage::helper('rewards')->__('Twitter Tweet'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE => Mage::helper('rewards')->__('Google+ Like'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_PINTEREST_PIN => Mage::helper('rewards')->__('Pinterest Pin'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP => Mage::helper('rewards')->__('Referred customer signs up in store'),
//            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_FIRST_ORDER => Mage::helper('rewards')->__('First order from referred customer'),
            Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER => Mage::helper('rewards')->__('Order from referred customer'),
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
