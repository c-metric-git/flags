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


class Mirasvit_Rewards_Block_Checkout_Spend extends Mirasvit_Rewards_Block_Checkout_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mst_rewards/checkout/spend.phtml' );
    }

    public function getSpendPoints()
    {
        return $this->getPurchase()->getSpendPoints();
    }

    public function getSpendAmount()
    {
        return $this->getPurchase()->getSpendAmount();
    }

    /**
     * @deprecated renamed
     */
    public function getPointsSpent()
    {
        return $this->getPurchase()->getSpendPoints();
    }

    /**
     * @deprecated renamed
     */
    public function getDiscount()
    {
        return $this->getPurchase()->getSpendAmount();
    }

    public function _toHtml()
    {
        if (!Mage::getModel('customer/session')->isLoggedIn()) {
            return '';
        }
        return parent::_toHtml();
    }
}
