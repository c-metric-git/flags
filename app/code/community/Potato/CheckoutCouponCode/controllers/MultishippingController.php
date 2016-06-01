<?php

class Potato_CheckoutCouponCode_MultishippingController
    extends Mage_Core_Controller_Front_Action
{
    public function applyCouponAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array(
            'success'          => true,
            'coupon_applied'   => false,
            'messages'         => array(),
            'coupon_code_list' => '',
            'payment_html'     => '',
        );

        $quote = $this->_getQuote();

        if (!$quote->getItemsCount()) {
            $result['success'] = false;
            return $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($result)
            );
        }

        $couponCode = (string)$this->getRequest()->getParam('coupon_code');

        try {
            // Apply coupon code
            $this->_getApi()->applyCouponCode($couponCode, $quote);

            // Collect totals
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote
                ->collectTotals()
                ->save()
            ;

            if (!$this->_getApi()->isCouponCodeApplied($couponCode, $quote)) {
                $result['success'] = true;
                $result['messages'][] = $this->__('Coupon code is not valid.');
                return $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode($result)
                );
            }

            $result['coupon_code_list'] = $this->_getAppliedCouponCodeList();
            $result['payment_html'] = $this->_getPaymentMethodsHtml();
            $result['coupon_applied'] = true;
            $result['messages'][] = $this->_getDiscountMessage($couponCode);
        } catch (Mage_Core_Exception $e) {
            $result['success'] = false;
            $result['messages'][] = $e->getMessage();
        } catch (Exception $e) {
            $result['success'] = false;
            $result['messages'][] = $this->__('Cannot apply the coupon code.');
            Mage::logException($e);
        }
        return $this->getResponse()->setBody(
            Mage::helper('po_ccc')->jsonEncode($result)
        );
    }

    public function cancelCouponAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $result = array(
            'success'          => true,
            'coupon_applied'   => false,
            'messages'         => array(),
            'coupon_code_list' => '',
            'payment_html'     => '',
        );

        $quote = $this->_getQuote();

        if (!$quote->getItemsCount()) {
            $result['success'] = false;
            return $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($result)
            );
        }

        $couponCode = $this->getRequest()->getParam('coupon_code', null);

        try {
            // Cancel coupon code
            $this->_getApi()->cancelCouponCode($couponCode, $quote);

            // Collect totals
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote
                ->collectTotals()
                ->save()
            ;

            if ($this->_getApi()->isCouponCodeApplied($couponCode, $quote)) {
                $result['success'] = false;
                return $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode($result)
                );
            }

            $result['coupon_code_list'] = $this->_getAppliedCouponCodeList();
            $result['payment_html'] = $this->_getPaymentMethodsHtml();
            $result['coupon_applied'] = false;
            $result['messages'][] = $this->__('Coupon code was canceled.');
        } catch (Mage_Core_Exception $e) {
            $result['success'] = false;
            $result['messages'][] = $e->getMessage();
        } catch (Exception $e) {
            $result['success'] = false;
            $result['messages'][] = $this->__('Cannot cancel the coupon code.');
            Mage::logException($e);
        }
        return $this->getResponse()->setBody(
            Mage::helper('po_ccc')->jsonEncode($result)
        );
    }

    /**
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _getMultishipping()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getMultishipping()->getQuote();
    }

    /**
     * @return bool
     */
    protected function _expireAjax()
    {
        if (
            !$this->_getQuote()->hasItems()
            || $this->_getQuote()->getHasError()
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        return false;
    }

    /**
     * @return AW_Onestepcheckout_AjaxController
     */
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse()
        ;
        return $this;
    }

    /**
     * @return string
     */
    protected function _getDiscountMessage($couponCode)
    {
        $message = $this->__('Coupon code was applied.');
        if ($this->_getConfig()->canShowDiscountInMessage()) {
            $discountAmount = $this->_getApi()->getCouponCodeDiscountAmount(
                $couponCode, $this->_getQuote()
            );
            $discountAmount = Mage::helper('core')->formatPrice(
                abs($discountAmount), false
            );
            $message .= Mage::helper('po_ccc')->__(
                ' (Your discount amount is %s. It has been applied to the cart.)',
                $discountAmount
            );
        }
        return $message;
    }

    protected function _getAppliedCouponCodeList()
    {
        $couponList = $this->getLayout()->createBlock('po_ccc/list', 'po_ccc_list');
        $couponList->setTemplate('po_ccc/list.phtml');
        return $couponList->toHtml();
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $this->loadLayout('po_ccc_multishipping_billing_methods');
        $couponCodeListBlock = $this->getLayout()->getBlock('po_ccc.multishipping_billing_methods');
        return $couponCodeListBlock->toHtml();
    }

    /**
     * @return Potato_CheckoutCouponCode_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('po_ccc/config');
    }

    /**
     * @return Potato_CheckoutCouponCode_Helper_Api
     */
    protected function _getApi()
    {
        return Mage::helper('po_ccc/api');
    }
}