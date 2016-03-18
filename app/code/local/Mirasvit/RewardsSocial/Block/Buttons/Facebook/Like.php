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



class Mirasvit_RewardsSocial_Block_Buttons_Facebook_Like extends Mirasvit_RewardsSocial_Block_Buttons_Abstract
{
	public function getAppId()
	{
		return $this->getConfig()->getFacebookAppId();
	}

	public function getLikeUrl()
	{
		return Mage::getUrl('rewardssocial/facebook/like');
	}

	public function getUnlikeUrl()
	{
		return Mage::getUrl('rewardssocial/facebook/unlike');
	}

	public function isLiked()
	{
		if (!$customer = $this->_getCustomer()) {
			return false;
		}
		$url = $this->getCurrentUrl();
		if ($earnedTransaction = Mage::helper('rewardssocial/balance')->getEarnedPointsTransaction($customer, Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE.'-'.$url)) {
			return true;
		}
	}

	public function getEstimatedEarnPoints()
	{
		$url = $this->getCurrentUrl();
		return Mage::helper('rewards/behavior')->getEstimatedEarnPoints(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE, $this->_getCustomer(), false, $url);
	}

	public function isActive()
	{
		return $this->getConfig()->getFacebookIsActive();
	}

	public function _toHtml()
	{
		if ($this->isActive()) {
			return parent::_toHtml();
		}
	}

	/**
	 * @deprecated
	 */
	public function getEstimatedPointsAmount()
	{
		return $this->getEstimatedEarnPoints();
	}
}