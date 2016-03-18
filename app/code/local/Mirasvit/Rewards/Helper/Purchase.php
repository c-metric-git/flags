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


class Mirasvit_Rewards_Helper_Purchase extends Mirasvit_MstCore_Helper_Help
{
    /**
     * @param $quoteId
     * @return bool|Mirasvit_Rewards_Model_Purchase
     */
    public function getByQuote($quoteId)
    {
        $quote = false;
        if (is_object($quoteId)) {
            $quote = $quoteId;
            $quoteId = $quote->getId();
        }
        if (!$quoteId) {
            return false;
        }
        $collection = Mage::getModel('rewards/purchase')->getCollection()
                        ->addFieldToFilter('quote_id', $quoteId);
        if ($collection->count()) {
            $purchase = $collection->getFirstItem();
            if ($quote) {
                $purchase->setQuote($quote);
            }
        } else {
            $purchase = Mage::getModel('rewards/purchase')->setQuoteId($quoteId);
            if ($quote) {
                $purchase->setQuote($quote);
            }
            $purchase->save();
        }
        return $purchase;
    }


    /**
     * @param $order
     * @return bool|Mirasvit_Rewards_Model_Purchase
     */
    public function getByOrder($order)
    {
        if (!$purchase = $this->getByQuote($order->getQuoteId())) {
            return false;
        }
        if (!$purchase->getOrderId()) {
            $purchase->setOrderId($order->getId())->save();
        }
        return $purchase;
    }

    /**
     * @return bool|Mirasvit_Rewards_Model_Purchase
     */
    public function getPurchase()
    {
        $quote = Mage::getModel('checkout/cart')->getQuote();
        return $this->getByQuote($quote);
    }

}