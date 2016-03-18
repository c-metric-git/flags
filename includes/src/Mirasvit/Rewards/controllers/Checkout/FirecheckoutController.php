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


require_once Mage::getBaseDir("code").'/local/TM/FireCheckout/controllers/IndexController.php';

class Mirasvit_Rewards_Checkout_FirecheckoutController extends TM_FireCheckout_IndexController
{
    //this code is from firecheckout
    public function applyPointsAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $session = Mage::getSingleton('core/session');
        $response = Mage::helper('rewards/checkout')->processRequest();
        if ($response['success']) {
            $session->addSuccess($response['message']);
        } elseif ($response['message']) {
            $session->addError($response['message']);
        }
        $result = array();

        $quote    = $this->getCheckout()->getQuote();
        $oldTotal = $quote->getBaseGrandTotal();
        $sections = array();

        $quote->collectTotals();
        $sections[] = 'review';
        if (Mage::getStoreConfig('firecheckout/ajax_update/shipping_method_on_total')) {
            $sections[] = 'shipping-method';
            $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
            $quote->setTotalsCollectedFlag(false)->collectTotals();
        }

        if (Mage::getStoreConfig('firecheckout/ajax_update/payment_method_on_total')
            || $quote->getBaseGrandTotal() <= 0 || $oldTotal <= 0) {

            $sections[] = 'payment-method';
            $this->getOnepage()->applyPaymentMethod();

            if (Mage::getStoreConfig('firecheckout/ajax_update/total_on_payment_method')) {
                $quote->getShippingAddress()->setDiscountDescriptionArray(array())->isObjectNew(true);
                $quote->setTotalsCollectedFlag(false)->collectTotals();
            }
        }

        $quote->save();
        $result['update_section'] = $this->_renderSections($sections);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}