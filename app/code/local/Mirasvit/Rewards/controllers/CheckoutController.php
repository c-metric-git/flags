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



require_once Mage::getBaseDir('code').'/core/Mage/Checkout/controllers/CartController.php';

class Mirasvit_Rewards_CheckoutController extends Mage_Checkout_CartController
{
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * @return Magegiant_Onestepcheckout_Model_Updater
     */
    public function getUpdater()
    {
        return Mage::getSingleton('onestepcheckout/updater');
    }


    public function applyPointsMagegiantstepcheckoutAction()
    {
        Mage::app()->getRequest()->setActionName('applyCoupon');
        $response = $this->processRequest();
        $result = array(
            'success'        => true,
            'coupon_applied' => false,
            'messages'       => array(),
            'blocks'         => array(),
            'grand_total'    => ""
        );
        if ($response['message']) {
            $result['coupon_applied'] = true;
        }
        $result['messages'][]     = $response['message'];
        $result['blocks']      = $this->getUpdater()->getBlocks();
        $result['grand_total'] = Mage::helper('onestepcheckout')->getGrandTotal($this->getOnepage()->getQuote());

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function applyPointsApptanaOnestepcheckoutAction()
    {
        $response = $this->processRequest();

        $html = $this->getLayout()
        ->createBlock('onestepcheckout/onestep_review_info')
        ->setTemplate('onestepcheckout/onestep/review/info.phtml')
        ->toHtml();

          // Add updated totals HTML to the output

        $response['summary'] = $html;

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function applyPointsIdevOnestepcheckoutAction()
    {
        $response = $this->processRequest();

        //// code from Idev_OneStepCheckout_AjaxController#add_couponAction
        /// it may be changed in other versions of checkout and that may be a problem
        $html = $this->getLayout()
            ->createBlock('checkout/onepage_shipping_method_available')
            ->setTemplate('onestepcheckout/shipping_method.phtml')
            ->toHtml();

        $response['shipping_method'] = $html;

        $html = $this->getLayout()
        ->createBlock('checkout/onepage_payment_methods', 'choose-payment-method')
        ->setTemplate('onestepcheckout/payment_method.phtml');

        //IDEV Checkout v 4.0.7 does not have method hasEeCustomerbalanace
        if (false && Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()) {
            if (Mage::helper('onestepcheckout')->hasEeCustomerbalanace()) {
                $customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array(
                    'template' => 'onestepcheckout/customerbalance/payment/additional.phtml',
                ));
                $customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array(
                    'template' => 'onestepcheckout/customerbalance/payment/scripts.phtml',
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($customerBalanceBlock)
                    ->append($customerBalanceBlockScripts);
            }

            if (Mage::helper('onestepcheckout')->hasEeRewards()) {
                $rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
                    'template' => 'onestepcheckout/reward/payment/additional.phtml',
                    'before' => '-',
                ));
                $rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
                    'template' => 'onestepcheckout/reward/payment/scripts.phtml',
                    'after' => '-',
                ));
                $this->getLayout()
                    ->getBlock('choose-payment-method')
                    ->append($rewardPointsBlock)
                    ->append($rewardPointsBlockScripts);
            }
        }

        //IDEV Checkout v 4.0.7 does not have method hasEeGiftcards
        if (false && Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('onestepcheckout')->hasEeGiftcards()) {
            $giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array(
                'template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml',
            ));
            $html->append($giftcardScripts);
        }

        $response['payment_method'] = $html->toHtml();

          // Add updated totals HTML to the output
        $html = $this->getLayout()
        ->createBlock('onestepcheckout/summary')
        ->setTemplate('onestepcheckout/summary.phtml')
        ->toHtml();

        $response['summary'] = $html;

        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function applyPointsIwdOnestepcheckoutAction()
    {
        $iwdVersion = Mage::getConfig()->getModuleConfig('IWD_Opc')->version;

        if ($iwdVersion >= '4.0.0') {
            /// this does not work for IWD '3.1.3'
            /// get list of available methods before discount changes
            $methods_before = Mage::helper('opc')->getAvailablePaymentMethods();
            ///////
        }

        $response = $this->processRequest();

        //// code from IWD_Opc_CouponController#couponPostAction
        /// it may be changed in other versions of checkout and that may be a problem
        $responseData = array();
        $responseData['message'] = $response['message'];
        $layout = $this->getLayout();
        $block = $layout->createBlock('rewards/checkout_cart_usepoints')
                        //->setTemplate('mst_rewards/checkout/cart/iwd/form.phtml');
                        ->setTemplate('mst_rewards/checkout/cart/usepoints_iwd_onestepcheckout.phtml');
        $responseData['rewards'] = $block->toHtml();

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()
                ->collectTotals()
                ->save();
            if ($iwdVersion >= '4.0.0') {
                /// this does not work for IWD '3.1.3'
                /// get list of available methods after discount changes
                $methods_after = Mage::helper('opc')->getAvailablePaymentMethods();
                ///////

                // check if need to reload payment methods
                $use_method = Mage::helper('opc')->checkUpdatedPaymentMethods($methods_before, $methods_after);
                if ($use_method != -1) {
                    $responseData['payments'] = $this->_getPaymentMethodsHtml($use_method);
                }
                /////
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $responseData['message'] = $this->__('Cannot apply points.');
            Mage::logException($e);
        }

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseData));
    }

    const XML_PATH_DEFAULT_PAYMENT = 'opc/default/payment';
    /**
     * Used only for IWD.
     * Get payments method step html.
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml($use_method = false)
    {

        /* UPDATE PAYMENT METHOD **/
        // check what method to use
        $apply_method = Mage::getStoreConfig(self::XML_PATH_DEFAULT_PAYMENT);
        if ($use_method) {
            $apply_method = $use_method;
        }

        $_cart = $this->_getCart();
        $_quote = $_cart->getQuote();
        $_quote->getPayment()->setMethod($apply_method);
        $_quote->setTotalsCollectedFlag(false)->collectTotals();
        $_quote->save();

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();

        $output = $layout->getOutput();

        return $output;
    }

    public function applyPointsPostAction()
    {
        $response = $this->processRequest();
        if ($response['success']) {
            $this->_getSession()->addSuccess($response['message']);
        } elseif ($response['message']) {
            $this->_getSession()->addError($response['message']);
        }
        $this->_goBack();
    }

    protected function processRequest()
    {
        return Mage::helper('rewards/checkout')->processRequest();
    }
}
