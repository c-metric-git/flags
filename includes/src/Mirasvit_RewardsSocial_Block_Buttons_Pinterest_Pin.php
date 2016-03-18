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



class Mirasvit_RewardsSocial_Block_Buttons_Pinterest_Pin extends Mirasvit_RewardsSocial_Block_Buttons_Abstract
{
	public function getPinUrl()
	{
		return Mage::getUrl('rewardssocial/pinterest/pin');
	}

	public function isLiked()
	{
		if (!$customer = $this->_getCustomer()) {
			return false;
		}
		$url = $this->getCurrentUrl();
		if ($earnedTransaction = Mage::helper('rewardssocial/balance')->getEarnedPointsTransaction($customer, Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_PINTEREST_PIN.'-'.$url)) {
			return true;
		}
	}

	public function getEstimatedEarnPoints()
	{
		$url = $this->getCurrentUrl();
		return Mage::helper('rewards/behavior')->getEstimatedEarnPoints(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_PINTEREST_PIN, $this->_getCustomer(), false, $url);
	}

	public function getMediaUrl()
	{
		if (!$product = Mage::registry('current_product')) {
			return false;
		}
		return Mage::helper('catalog/image')->init($product, 'image');
	}

	public function getProduct()
	{
		if (!$product = Mage::registry('current_product')) {
			return false;
		}
		return $product;
	}
	/**
	 * @deprecated
	 */
	public function getEstimatedPointsAmount()
	{
		return $this->getEstimatedEarnPoints();
	}

}