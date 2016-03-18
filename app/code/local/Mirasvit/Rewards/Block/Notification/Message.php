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


class Mirasvit_Rewards_Block_Notification_Message extends Mage_Core_Block_Template
{
	public function getProductPoints()
	{
		return Mage::helper('rewards/balance_earn')->getProductPoints($this->getProduct());
	}


	protected $rules = false;
	public function getRules()
	{
		if ($this->rules) {
			return $this->rules;
		}
		$request = Mage::app()->getRequest();
		$action = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();

		$type = false;
		switch ($action) {
			case 'rewards_account_index':
				$type = Mirasvit_Rewards_Model_Config::NOTIFICATION_POSITION_ACCOUNT_REWARDS;
				break;
			case 'rewards_referral_index':
				$type = Mirasvit_Rewards_Model_Config::NOTIFICATION_POSITION_ACCOUNT_REFERRALS;
				break;
			case 'checkout_cart_index':
				$type = Mirasvit_Rewards_Model_Config::NOTIFICATION_POSITION_CART;
				break;
			case 'checkout_onepage_index':
			case 'onestepcheckout_index_index':
				$type = Mirasvit_Rewards_Model_Config::NOTIFICATION_POSITION_CHECKOUT;
				break;
		}
		$quote = Mage::getModel('checkout/cart')->getQuote();
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = $quote->getStore()->getWebsiteId();
        $rulesCollection = Mage::getModel('rewards/notification_rule')->getCollection()
                    ->addWebsiteFilter($websiteId)
                    ->addCustomerGroupFilter($customerGroupId)
                    ->addCurrentFilter()
                    ->addTypeFiler($type)
                    ->setOrder('sort_order')
                    ;
        $rules = array();
        foreach ($rulesCollection as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($rule->validate($address)) {
            	$rules[] = $rule;
            }
        }
        $this->rules = $rules;
        return $this->rules;
	}

	public function _toHtml()
	{
		if (count($this->getRules()) == 0) {
			return '';
		}
		return parent::_toHtml();
	}
}