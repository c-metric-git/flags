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


class Mirasvit_Rewards_Helper_Balance_Spend
{

    const MIN_START = 999999999;

    /**
     * @return  Mirasvit_Rewards_Model_Config
     */
    public function getConfig(){
        return Mage::getSingleton('rewards/config');
    }

    /**
     * @param   Mage_Sales_Model_Quote $quote
     * @param   Mirasvit_Rewards_Model_Spending_Rule $rule
     * @return  float
     */
    protected function getLimitedSubtotal($quote, $rule) {
        $subtotal = 0;
        $priceIncludesTax = Mage::helper('tax')->priceIncludesTax($quote->getStore());
        foreach ($quote->getItemsCollection() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($rule->getActions()->validate($item)) {
                if ($priceIncludesTax) {
                    $subtotal += $item->getBasePriceInclTax() * $item->getQty() - $item->getBaseDiscountAmount();
                } else {
                    $subtotal += $item->getBasePrice() * $item->getQty() - $item->getBaseDiscountAmount();
                }

            }
        }
        if ($this->getConfig()->getGeneralIsSpendShipping()) {
            $shipping = $quote->getShippingAddress()->getBaseShippingInclTax();
            $subtotal += $shipping;
        }
        return $subtotal;
    }

    protected function getRules($quote) {
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = $quote->getStore()->getWebsiteId();
        $rules = Mage::getModel('rewards/spending_rule')->getCollection()
                    ->addWebsiteFilter($websiteId)
                    ->addCustomerGroupFilter($customerGroupId)
                    ->addCurrentFilter()
                    ->setOrder('sort_order')
                    ;
        return $rules;
    }

    public function getCartRange($quote)
    {
        $rules = $this->getRules($quote);

        $minPoints = self::MIN_START;
        $maxPoints = 0;
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($rule->validate($address)) {
                $localMinPoints = self::MIN_START;
                if ($rule->getSpendMinPointsNumber()) {
                    $localMinPoints = min($localMinPoints, $rule->getSpendMinPointsNumber());
                }
                $subtotal = $this->getLimitedSubtotal($quote, $rule);

                $steps1 = (int)($subtotal/$rule->getMonetaryStep());
                if ($steps1 != $subtotal/$rule->getMonetaryStep()) {
                    $steps1++;
                }
                if ($max = $rule->getSpendMaxAmount($subtotal)) {
                    $stepsMax = (int)($max/$rule->getMonetaryStep());
                    $steps1 = min($steps1, $stepsMax);
                }
                if ($min = $rule->getSpendMinAmount($subtotal)) {
                    $stepsMin = (int)($min/$rule->getMonetaryStep());
                    $localMinPoints = min($stepsMin, $localMinPoints);
                }
                $maxPointsForThis = $steps1 * $rule->getSpendPoints();
                if ($rule->getSpendMaxPointsNumber()) {
                    $maxPointsForThis = min($maxPointsForThis, $rule->getSpendMaxPointsNumber());
                }
                $maxPoints = max($maxPoints, $maxPointsForThis);

                if ($localMinPoints == self::MIN_START) {
                    $localMinPoints = 0;
                }
                $minPoints = max($localMinPoints, $rule->getSpendPoints());

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }
        if ($minPoints == self::MIN_START) {
            $minPoints = 0;
        }
        if ($minPoints > $maxPoints) {
            $minPoints = $maxPoints = 0;
        }
        return new Varien_Object(array('min_points' => $minPoints, 'max_points' => $maxPoints));
    }

	public function getCartPoints($quote, $pointsNumber)
    {
        $rules = $this->getRules($quote);
        $spendPoints = 0;
        $totalAmount = 0;
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($rule->validate($address)) {
                if ($rule->getSpendMinPointsNumber() && $pointsNumber < $rule->getSpendMinPointsNumber()) {
                    continue;
                }
                if ($rule->getSpendMaxPointsNumber() && $pointsNumber > $rule->getSpendMaxPointsNumber()) {
                    continue;
                }

                $subtotal = $this->getLimitedSubtotal($quote, $rule);
                if ($max = $rule->getSpendMaxAmount($subtotal)) {
                    $stepsMax = (int)($max/$rule->getMonetaryStep());
                    $pointsMax = $rule->getSpendPoints() * $stepsMax;
                    if ($pointsNumber > $pointsMax) {
                        continue;
                    }
                }
                if ($min = $rule->getSpendMinAmount($subtotal)) {
                    $stepsMin = (int)($min/$rule->getMonetaryStep());
                    $pointsMin = $rule->getSpendPoints() * $stepsMin;
                    if ($pointsNumber < $pointsMin) {
                        continue;
                    }
                }

                $steps1 = (int)($subtotal/$rule->getMonetaryStep());
                if ($steps1 != $subtotal/$rule->getMonetaryStep()) {
                    $steps1++;
                }
                $steps2 = (int)($pointsNumber/$rule->getSpendPoints());
                $steps = min($steps1, $steps2);

                $spendPoints =  max($steps * $rule->getSpendPoints(), $spendPoints);
                $amount = $steps * $rule->getMonetaryStep();

                $amount = min($amount, $subtotal);
                $totalAmount += $amount;

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }
        return new Varien_Object(array('points' => $spendPoints, 'amount' => $totalAmount));
    }
}