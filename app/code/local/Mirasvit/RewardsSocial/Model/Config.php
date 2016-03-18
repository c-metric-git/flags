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



class Mirasvit_RewardsSocial_Model_Config
{
    public function getFacebookIsActive($store = null)
    {
        return Mage::getStoreConfig('rewardssocial/facebook/is_active', $store);
    }

    public function getFacebookAppId($store = null)
    {
        return Mage::getStoreConfig('rewardssocial/facebook/app_id', $store);
    }

    public function getTwitterIsActive($store = null)
    {
        return Mage::getStoreConfig('rewardssocial/twitter/is_active', $store);
    }

    public function getGoogleplusIsActive($store = null)
    {
        return Mage::getStoreConfig('rewardssocial/googleplus/is_active', $store);
    }

    public function getPinterestIsActive($store = null)
    {
        return Mage::getStoreConfig('rewardssocial/pinterest/is_active', $store);
    }

    /************************/
}
