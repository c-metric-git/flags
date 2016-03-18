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


$installer = $this;
$installer->startSetup();

$rules = Mage::getModel("rewards/earning_rule")->getCollection()
			->addFieldToFilter('type', Mirasvit_Rewards_Model_Config::TYPE_BEHAVIOR);

foreach($rules as $rule) {
	$rule->afterLoad();
	$comments = array(
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SIGNUP => "For registering in our store",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_VOTE => "Points for poll",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SEND_LINK => "For sending your friend product '%s'",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP => "For newsletter subscription",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TAG => "For tag '%s'",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REVIEW => "For submitting a product review '%s'",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP => "For sign up of referral customer '%s'",
//		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_FIRST_ORDER => "For first order of referral customer '%s'",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER => "For order of referral customer '%s'",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE => "For Facebook Like",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE => "For Google+",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_PINTEREST_PIN => "For Pinterest Pin",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_TWITTER_TWEET => "For Tweeting",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_BIRTHDAY => "For having a birthday",
		Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_INACTIVITY => "Gift",
	);
	if (isset($comments[$rule->getBehaviorTrigger()])) {
		if (!$rule->getHistoryMessage()) {
			$message = $comments[$rule->getBehaviorTrigger()];
			$rule->setHistoryMessage($message)->save();
		}
	}
	if ($rule->getBehaviorTrigger() == Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_BIRTHDAY) {
		if (!$rule->getEmailMessage()) {
$email = '
<p>Dear {{htmlescape var=$customer.name}}!</p>
<p>Happy Birthday!</p>
<p>We hope you have a wonderful day and that the year ahead is filled with much love, many wonderful surprises and gives you lasting memories that you will cherish in all the days ahead.</p>
<p>Please, use {{var transaction_amount}} for your next orders!</p>
<strong>{{var store.getFrontendName()}}</strong>';
			$rule->setEmailMessage($email)->save();
		}
	}
}

$installer->endSetup();
