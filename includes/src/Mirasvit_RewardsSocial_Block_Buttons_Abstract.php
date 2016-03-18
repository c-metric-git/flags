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



class Mirasvit_RewardsSocial_Block_Buttons_Abstract extends Mage_Core_Block_Template
{
	public function getConfig()
	{
		return Mage::getSingleton('rewardssocial/config');
	}

	public function getLocaleCode()
	{
		return Mage::app()->getLocale()->getLocaleCode();
	}

	public function getCurrentUrl()
	{
		if ($product = Mage::registry('current_product')) {
			$url = $product->getProductUrl();
		} elseif ($category = Mage::registry('current_category')) {
			$url = $category->getUrl();
		} else {

		}

		$pos = strpos($url, "?__SID");
		if($pos !== false) {
			$url = substr($url, 0, $pos);
		}
		return $url;
	}

	public function getCurrentEncodedUrl()
	{
		$url = $this->getCurrentUrl();
		return urlencode($url);
	}

	public function _getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	public function isAuthorized() {
		$customer = $this->_getCustomer();
		if ($customer && $customer->getId() > 0) {
			return true;
		}
	}
}