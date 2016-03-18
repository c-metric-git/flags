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


class Mirasvit_Rewards_Block_Checkout_Cart_Usepoints extends Mage_Checkout_Block_Cart_Abstract
{

    protected function getPurchase()
    {
       $purchase = Mage::helper('rewards/purchase')->getByQuote($this->getQuote());
       return $purchase;
    }

    /**
     * @deprecated method renamed
     */
	public function getPointsAmount()
	{
        if (!$this->getPurchase()) {
            return 0;
        }
		return $this->getPurchase()->getSpendPoints();
	}

    public function getBalancePoints()
    {
        return Mage::helper('rewards/balance')->getBalancePoints($this->getCustomer());
    }

    public function getMaxPointsNumberToSpent()
    {
        if (!$this->getPurchase()) {
            return 0;
        }
    	return $this->getPurchase()->getMaxPointsNumberToSpent();
    }

    public function _toHtml()
    {
        if (!Mage::getModel('customer/session')->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}