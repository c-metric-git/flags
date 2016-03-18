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


class Mirasvit_RewardsSocial_PinterestController extends Mage_Core_Controller_Front_Action
{
	public function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

    public function pinAction()
    {
    	$url = $this->getRequest()->getParam('url');
		$transaction = Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_PINTEREST_PIN, $this->_getCustomer(), false, $url);
		if ($transaction) {
			echo Mage::helper('rewardssocial')->__("You've earned %s for Pin!", Mage::helper('rewards')->formatPoints($transaction->getAmount()));
			die;
		}
    }
}
