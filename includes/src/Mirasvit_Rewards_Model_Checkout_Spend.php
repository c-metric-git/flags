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



class Mirasvit_Rewards_Model_Checkout_Spend extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_calculator;

    public function __construct()
    {
        $this->setCode('reward_spend');
    }

    protected function getPurchase()
    {
       $purchase = Mage::helper('rewards/purchase')->getByQuote($this->getQuote());
       return $purchase;
    }
    /**
     * Add discount total information to address
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_SalesRule_Model_Quote_Discount
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (Mage::helper('rewards')->isMultiship($address)) {
            return $this;
        }
        $quote = $address->getQuote();

        if (!$purchase = Mage::helper('rewards/purchase')->getByQuote($quote)) {
            return $this;
        }
        $amount       = $purchase->getSpendAmount();
        $rewardPoints = $purchase->getSpendPoints();

        $config = Mage::getSingleton('rewards/config');

        if ($amount != 0 && !$config->getSpendTotalAppliedFlag()) {
            //$title = Mage::helper('rewards')->__('Spend %s', Mage::helper('rewards')->formatPoints($rewardPoints));
            $title = Mage::helper('rewards')->__('You Spend'); //will be used in some modifications of iwd onestepcheckout
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $rewardPoints //will be used in some modifications of iwd onestepcheckout
            ));
        }
        $config->setSpendTotalAppliedFlag(true);

        return $this;
    }
}