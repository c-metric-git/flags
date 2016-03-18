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


class Mirasvit_Rewards_Helper_Checkout extends Mage_Core_Helper_Abstract
{
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function getRequest() {
        return Mage::app()->getRequest();
    }

    public function processRequest()
    {
        $response = array(
            'success' => false,
            'message' => false,
        );
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            return $response;
        }

        $pointsNumber = abs((int) $this->getRequest()->getParam('points_amount'));
        if ($this->getRequest()->getParam('remove-points') == 1) {
            $pointsNumber = false;
        }

        $purchase = Mage::helper('rewards/purchase')->getPurchase();
        $oldPointsNumber = $purchase->getSpendPoints();

        if (!$pointsNumber && !$oldPointsNumber) {
            return $response;
        }

        try {
            $purchase->setSpendPoints($pointsNumber)
                ->refreshPointsNumber(true)
                ->save();
            if ($pointsNumber) {
                if ($pointsNumber == $purchase->getSpendPoints()) {
                    $response['success'] = true;
                    $response['message'] = $this->__('%s was applied.', Mage::helper('rewards')->formatPoints($pointsNumber));
                }
                else {
                    $response['success'] = false;
                    if ($pointsNumber < $purchase->getSpendMinPoints()) {
                        $response['message'] = $this->__('Minimum number is %s.', Mage::helper('rewards')->formatPoints($purchase->getSpendMinPoints()));
                    } elseif ($pointsNumber > $purchase->getSpendMaxPoints()) {
                        $response['message'] = $this->__('Maximum number is %s.', Mage::helper('rewards')->formatPoints($purchase->getSpendMaxPoints()));
                    } else {
                        $response['success'] = true;
                        $response['message'] = $this->__('%s was applied.', Mage::helper('rewards')->formatPoints($purchase->getSpendPoints()));
                    }
                }
            } else {
                $response['success'] = true;
                $response['message'] = $this->__('%s was canceled.', Mage::helper('rewards')->getPointsName());
            }

        } catch (Mage_Core_Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = $this->__('Cannot apply %s.', Mage::helper('rewards')->getPointsName());
            Mage::logException($e);
        }
        $response['spend_points'] = $purchase->getSpendPoints();
        return $response;
    }
}