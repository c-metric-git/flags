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



class Mirasvit_Rewards_Helper_Balance_Earn
{
    /**
     * @return Mirasvit_Rewards_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('rewards/config');
    }

    /**
     * @return bool
     */
    public function isIncludeTax()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $priceIncludesTax = Mage::helper('tax')->priceIncludesTax($quote->getStore());

        return $priceIncludesTax;
    }

    /**
     * @param Mage_Sales_Model_Quote               $quote
     * @param Mirasvit_Rewards_Model_Spending_Rule $rule
     *
     * @return float
     */
    protected function getLimitedSubtotal($quote, $rule)
    {
        $priceIncludesTax = $this->isIncludeTax();
        $subtotal = 0;
        foreach ($quote->getItemsCollection() as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */
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
        if ($this->getConfig()->getGeneralIsEarnShipping()) {
            if ($priceIncludesTax) {
                $shipping = $quote->getShippingAddress()->getBaseShippingInclTax();
            } else {
                $shipping = $quote->getShippingAddress()->getBaseShippingInclTax() - $quote->getShippingAddress()->getBaseShippingTaxAmount();
            }

            $subtotal += $shipping;
        }

        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_Credit')) {
            if ($credit = $quote->getShippingAddress()->getBaseCreditAmount()) {
                $subtotal -= $credit;
            }
        }

        if ($subtotal < 0) {
            $subtotal = 0;
        }

        return $subtotal;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return int number of points
     */
    public function getPointsEarned($quote)
    {
        $totalPoints = 0;
        foreach ($quote->getAllItems() as $item) {
            $productId = $item->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);

            if ($item->getParentItemId() && $product->getTypeID() == 'simple') {
                continue;
            }

            $productPoints = $this->getProductPoints($product, $quote->getCustomerGroupId(), $quote->getStore()->getWebsiteId()) * $item->getQty();

            $totalPoints += $productPoints;
        }

        $cartPoints = $this->getCartPoints($quote);
        $totalPoints += $cartPoints;

        return $totalPoints;
    }

    /**
     * calculates the number of points for some product.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int|bool                   $customerGroupId
     * @param int|bool                   $websiteId
     *
     * @return int number of points
     */
    public function getProductPoints($product, $customerGroupId = false, $websiteId = false)
    {
        $product = Mage::getModel('catalog/product')->load($product->getId());
        if ($customerGroupId === false) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }

        if ($websiteId === false) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }

        $finalPrice = $product->getFinalPrice();//final price in base currency

        $priceInclTax = Mage::helper('tax')->getPrice($product, $finalPrice, true);
        $priceExclTax = Mage::helper('tax')->getPrice($product, $finalPrice);

        $rules = Mage::getModel('rewards/earning_rule')->getCollection()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addCurrentFilter()
            ->addFieldToFilter('type', Mirasvit_Rewards_Model_Earning_Rule::TYPE_PRODUCT)
            ->setOrder('sort_order')
        ;
        $select = (string) $rules->getSelect();
        $total = 0;
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($rule->validate($product)) {
                switch ($rule->getEarningStyle()) {
                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_GIVE:
                        $total += $rule->getEarnPoints();
                        break;

                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_PRICE:
                        if ($this->isIncludeTax()) {
                            $steps = (int) ($priceInclTax / $rule->getMonetaryStep());
                        } else {
                            $steps = (int) ($priceExclTax / $rule->getMonetaryStep());
                        }
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                }

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }

        return $total;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return int number of points
     */
    protected function getCartPoints($quote)
    {
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = $quote->getStore()->getWebsiteId();
        $rules = Mage::getModel('rewards/earning_rule')->getCollection()
                    ->addWebsiteFilter($websiteId)
                    ->addCustomerGroupFilter($customerGroupId)
                    ->addCurrentFilter()
                    ->addFieldToFilter('type', Mirasvit_Rewards_Model_Earning_Rule::TYPE_CART)
                    ->setOrder('sort_order')
                    ;
        $select = (string) $rules->getSelect();
        $total = 0;
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($rule->validate($address)) {
                switch ($rule->getEarningStyle()) {
                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_GIVE:
                        $total += $rule->getEarnPoints();
                        break;

                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_AMOUNT_SPENT:
                        $subtotal = $this->getLimitedSubtotal($quote, $rule);
                        $steps = (int) ($subtotal / $rule->getMonetaryStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                    case Mirasvit_Rewards_Model_Config::EARNING_STYLE_QTY_SPENT:
                        $steps = (int) ($quote->getItemsQty() / $rule->getQtyStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                }
                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }

        return $total;
    }
}
