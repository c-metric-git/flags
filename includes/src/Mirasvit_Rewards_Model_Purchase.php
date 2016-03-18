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



/**
 * @method Mirasvit_Rewards_Model_Resource_Purchase_Collection|Mirasvit_Rewards_Model_Purchase[] getCollection()
 * @method Mirasvit_Rewards_Model_Purchase load(int $id)
 * @method bool getIsMassDelete()
 * @method Mirasvit_Rewards_Model_Purchase setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method Mirasvit_Rewards_Model_Purchase setIsMassStatus(bool $flag)
 * @method Mirasvit_Rewards_Model_Resource_Purchase getResource()
 */
class Mirasvit_Rewards_Model_Purchase extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rewards/purchase');
    }

    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/

    protected $quote = null;
    public function getQuote()
    {
        if (!$this->getQuoteId()) {
            return false;
        }
        if (!$this->quote) {
            $this->quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
        }

        return $this->quote;
    }

    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    public function getMaxPointsNumberToSpent()
    {
        if (!Mage::getModel('customer/session')->isLoggedIn()) {
            return 0;
        }

        return $this->getSpendMaxPoints();
    }

    protected function getSpendLimit($subtotal)
    {
        $limit = Mage::getSingleton('rewards/config')->getGeneralSpendLimit();
        if (!$limit) {
            return false;
        }
        if (strpos($limit, '%')) {
            $limit = str_replace('%', '', $limit);
            $limit = (int) $limit;
            $spendLimit = $subtotal * $limit / 100;
            if ($spendLimit > 0) {
                return $spendLimit;
            }
        } else {
            return $limit;
        }
    }

    /**
     * Quote can be changed time to time. So we have to refresh avaliable points number.
     *
     * @param bool $forceRefresh - must be used only if we 100% sure that it will be called once.
     *
     * @return object
     */
    public function refreshPointsNumber($forceRefresh = false)
    {
        $quote = $this->getQuote();
        if (!$forceRefresh && !$this->hasChanges($quote)) {
            return $this;
        }
        Varien_Profiler::start('rewards:refreshPointsNumber');

        $this->refreshQuote(false); //reset points discount

        //recalculate spending points
        $cartRange = Mage::helper('rewards/balance_spend')->getCartRange($quote);

        $pointsNumber = (int) $this->getSpendPoints();
        if ($pointsNumber != 0 && $pointsNumber < $cartRange->getMinPoints()) {
            $pointsNumber = $cartRange->getMinPoints();
        }
        if ($pointsNumber > $cartRange->getMaxPoints()) {
            $pointsNumber = $cartRange->getMaxPoints();
        }

        $balancePoints = Mage::helper('rewards/balance')->getBalancePoints($quote->getCustomerId());
        $cartMax = min($cartRange->getMaxPoints(), $balancePoints);
        if ($pointsNumber > $balancePoints) {
            $pointsNumber = $balancePoints;
            if ($pointsNumber < $cartRange->getMinPoints()) {
                $this->setSpendPoints(0);
                $this->setSpendAmount(0);
                $this->setSpendMinPoints($cartRange->getMinPoints());
                $this->setSpendMaxPoints($cartMax);
            }
        }

        $cartPoints = Mage::helper('rewards/balance_spend')->getCartPoints($quote, $pointsNumber);


        $this->setSpendPoints($cartPoints->getPoints());
        $this->setSpendAmount($cartPoints->getAmount());
        $this->setSpendMinPoints($cartRange->getMinPoints());
        $this->setSpendMaxPoints($cartMax);
        $this->save(); //we need this. otherwise points are not updated in the magegiant checkout ajax.

        $this->refreshQuote(true); //apply points discount with rewards discount

        //recalculate earning points
        $earn = Mage::helper('rewards/balance_earn')->getPointsEarned($quote);
        $this->setEarnPoints($earn);
        Varien_Profiler::stop('rewards:refreshPointsNumber');

        return $this;
    }

    protected function refreshQuote($withRewardsDiscount)
    {
        Varien_Profiler::start('rewards:refreshQuote');
        Mage::getSingleton('rewards/config')->setCalculateTotalFlag($withRewardsDiscount);
        $quote = $this->getQuote();
        $isPurchaseSave = $quote->getIsPurchaseSave(); // we don't need to recalculate points now
        $quote->setIsPurchaseSave(true);

        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->save()
        ;
//        if ($withRewardsDiscount) {
//            echo 'total';
//            echo $quote->getGrandTotal();
//            die;
//        }
        $quote->setIsPurchaseSave($isPurchaseSave);
        Varien_Profiler::stop('rewards:refreshQuote');
    }

    /**
     * check quote for changes.
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return bool
     */
    protected function hasChanges($quote)
    {
        $cachedQuote = Mage::getSingleton('customer/session')->getRWCachedQuote();
        $data = array();
        $data[] = (double) $quote->getData('grandtotal');
        $data[] = (double) $quote->getData('subtotal');
        $data[] = (double) $quote->getData('subtotal_with_discount');
        if ($shipping = $quote->getShippingAddress()) {
            $data[] = $shipping->getData('shipping_method');
            $data[] = (double) $shipping->getData('shipping_amount');
        }
        if ($payment = $quote->getPayment()) {
            if ($payment->hasMethodInstance()) {
                $data[] = $payment->getMethodInstance()->getCode();
            }
        }
        $newCachedQuote = implode('|', $data);

        if ($cachedQuote != $newCachedQuote) {
            Mage::getSingleton('customer/session')->setRWCachedQuote($newCachedQuote);

            return true;
        }

        return false;
    }
}
