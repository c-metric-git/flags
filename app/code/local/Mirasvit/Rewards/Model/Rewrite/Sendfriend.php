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



class Mirasvit_Rewards_Model_Rewrite_Sendfriend extends Mage_Sendfriend_Model_Sendfriend {

	public function send() {
		parent::send ();
		$product = $this->getProduct();
        Mage::helper('rewards/behavior')->processRule(Mirasvit_Rewards_Model_Config::BEHAVIOR_TRIGGER_SEND_LINK,
            false, false, $product->getId());
	}

}