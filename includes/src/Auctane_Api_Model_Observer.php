<?php
/**
 * ShipStation
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@auctane.com so we can send you a copy immediately.
 *
 * @category   Shipping
 * @package    Auctane_Api
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Auctane_Api_Model_Observer
{
    protected static $_counter = 0;
    
    protected static $_cartRules = array();
    
    protected static $_shippingAmountProcessed = array();
    
    /**
     * Calculate discounts by sales rules
     * @param Varien_Event_Observer $observer
     */    
    public function salesruleProcess($observer)
    {                
        $quote = $observer->getQuote();
        $address = $observer->getAddress();
        $rule = $observer->getRule();
                
        $discounts = unserialize($quote->getAuctaneapiDiscounts());

        if (!self::$_counter) {
            $discounts = array();
            $address->setBaseShippingDiscountAmount(0);
            self::$_counter++;
        } 

        if (!isset(self::$_shippingAmountProcessed[$rule->getId()]) && $address->getShippingAmount()) {
            $shippingAmount = $address->getShippingAmountForDiscount();
            if ($shippingAmount !== null) {
                $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
            } else {
                $baseShippingAmount = $address->getBaseShippingAmount();
            }

            //check for discount applied on shipping amount or not
            if (!$rule['apply_to_shipping'])
                $baseShippingAmount = 0;

            $baseDiscountAmount = 0;
            $rulePercent = min(100, $rule->getDiscountAmount());
            switch ($rule->getSimpleAction()) {
                case Mage_SalesRule_Model_Rule::TO_PERCENT_ACTION:
                    $rulePercent = max(0, 100 - $rule->getDiscountAmount());
                case Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION:
                    $shipDiscount = $baseShippingAmount - $address->getBaseShippingDiscountAmount();
                    $baseDiscountAmount = ($shipDiscount) * $rulePercent / 100;
                    break;
                case Mage_SalesRule_Model_Rule::TO_FIXED_ACTION:
                    $baseDiscountAmount = $baseShippingAmount - $rule->getDiscountAmount();
                    break;
                case Mage_SalesRule_Model_Rule::BY_FIXED_ACTION:
                    $baseDiscountAmount = $rule->getDiscountAmount();
                    break;
                case Mage_SalesRule_Model_Rule::CART_FIXED_ACTION:
                    self::$_cartRules = $address->getCartFixedRules();
                    if (!isset(self::$_cartRules[$rule->getId()])) {
                        self::$_cartRules[$rule->getId()] = $rule->getDiscountAmount();
                    }
                    if (self::$_cartRules[$rule->getId()] > 0) {
                        $shipAmount = $baseShippingAmount - $address->getBaseShippingDiscountAmount();
                        $baseDiscountAmount = min($shipAmount, self::$_cartRules[$rule->getId()]);
                        self::$_cartRules[$rule->getId()] -= $baseDiscountAmount;
                    }
                    break;
            }

            $ruleDiscount = 0;
            $left = $baseShippingAmount - ($address->getBaseShippingDiscountAmount() + $baseDiscountAmount);
            if ($left >= 0)
                $ruleDiscount = $baseDiscountAmount;
            $discountId = $rule->getId() . '-' . $observer->getItem()->getId() . '-' . uniqid();
            $discounts[$discountId] = $observer->getResult()->getBaseDiscountAmount() + $ruleDiscount;
            $shipDiscount = min($address->getBaseShippingDiscountAmount() + $baseDiscountAmount, $baseShippingAmount);
            $address->setBaseShippingDiscountAmount($shipDiscount);

            self::$_shippingAmountProcessed[$rule->getId()] = true;
        } else {
            $discountId = $rule->getId() . '-' . $observer->getItem()->getId() . '-' . uniqid();
            $discounts[$discountId] = $observer->getResult()->getBaseDiscountAmount();
        }
                
        $quote->setAuctaneapiDiscounts(serialize($discounts));
    }
      
}