<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class Webtex_Giftcards_CartController extends Mage_Checkout_CartController
{
    public function activateGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $amount = (float)$this->getRequest()->getParam('giftcard_amount');
        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');
        if($amount > $card->getCardBalance()){
            $this->_getSession()->addError(
                $this->__('Invalid Card Amount')
            );
            $this->_goBack();
        } else {

            $storeId = Mage::app()->getStore()->getWebsiteId();
            $currDate = date('Y-m-d');

            if($card->getId() && ($card->getWebsiteId() == $storeId || $card->getWebsiteId() == 0) && (!$card->getDateEnd() || $card->getDateEnd() >= $currDate)){
                if ($card->getId() && ($card->getCardStatus() == 1)) {
                    $card->activateCard();

                    $this->_getSession()->addSuccess(
                        $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftCardCode))
                    );
                    Mage::getSingleton('giftcards/session')->setActive('1');
                    $this->_setSessionVars($card, $amount);
                } else {
                    if($card->getId() && ($card->getCardStatus() == 2)) {
                        $this->_getSession()->addError(
                            $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode))
                        );
                    } else {
                        $this->_getSession()->addError(
                            $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode))
                        );
                    }
                }
            } else {
                $this->_getSession()->addError(
                    $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode))
                );
            }

            $this->_goBack();
        }
    }

    public function addGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');
        $response = array();
        $storeId = Mage::app()->getStore()->getWebsiteId();
        $currDate = date('Y-m-d');

        if($card->getId() && ($card->getWebsiteId() == $storeId || $card->getWebsiteId() == 0) && (!$card->getDateEnd() || $card->getDateEnd() >= $currDate)){
            if ($card->getId() && ($card->getCardStatus() == 1)) {
                $card->activateCard();

                $response['succes'] = true;
                $response['message'] = $this->__('Gift Card "%s" was added.', Mage::helper('core')->escapeHtml($giftCardCode));
                Mage::getSingleton('giftcards/session')->setActive('1');
                $this->_setSessionVars($card);

            } else {
                if($card->getId() && ($card->getCardStatus() == 2)) {
                    $response['error'] = true;
                    $response['message'] = $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode));
                } else {
                    $response['error'] = true;
                    $response['message'] = $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode));
                }
            }
        } else {
            $response['error'] = true;
            $response['message'] = $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode));
        }
        $response['update'] = $this->getLayout()->createBlock('giftcards/items')->toHtml();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function removegiftcardAction()
    {
        $cardId = $this->getRequest()->getParam('id');
        $oSession = Mage::getSingleton('giftcards/session');
        $cardIds = $oSession->getGiftCardsIds();
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['balance'];
        $cardCode = $cardIds[$cardId]['code'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);

        $result = array('success' => true,
            'message' => $this->__('Gift Card "%s" was succefuly removed.', Mage::helper('core')->escapeHtml($cardCode)),
            'update' => $this->getLayout()->createBlock('giftcards/checkout_coupon')->toHtml(),
            'table' => $this->getLayout()->createBlock('giftcards/checkout_items')->toHtml());
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function applyamountAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardAmount = $this->getRequest()->getParam('amount', false);

        $card = Mage::getModel('giftcards/giftcards')->load($cardId);

        if(!$cardAmount){
            $cardAmount = min($card->getCardBalance(), Mage::getSingleton('checkout/session')->getGrandTotal());
        }

        if($cardAmount > $card->getCardBalance()){
            $result = array('error' => true,
                'message' => $this->__('Invalid Card Amount'),
                'update' => $this->getLayout()->createBlock('giftcards/checkout_coupon')->toHtml(),
                'table' => $this->getLayout()->createBlock('giftcards/checkout_items')->toHtml());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {

            $cardIds = $oSession->getGiftCardsIds();
            $sessionBalance = $oSession->getGiftCardBalance();
            $newSessionBalance = $sessionBalance + $cardAmount;

            if (!empty($cardIds)) {
                $giftCardsIds = $oSession->getGiftCardsIds();

                $giftCardsIds[$cardId] =  array('balance' => $giftCardsIds[$cardId]['balance']-$cardAmount, 'code' => $card->getCardCode(), 'card_amount' => $giftCardsIds[$cardId]['card_amount']+$cardAmount);
                $oSession->setGiftCardsIds($giftCardsIds);

                $newBalance = $oSession->getGiftCardBalance() + $cardAmount;
                $oSession->setGiftCardBalance($newBalance);

            } else {
                $cardIds[$cardId] = array('balance' => $card->getCardBalance()-$cardAmount, 'code' => $card->getCardCode(), 'card_amount' => $cardAmount);
                $oSession->setGiftCardsIds($cardIds);

                $oSession->setGiftCardBalance($cardAmount);
            }

            $result = array('success' => true,
                'message' => $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($card->getCardCode())),
                'update' => $this->getLayout()->createBlock('giftcards/checkout_coupon')->toHtml(),
                'table' => $this->getLayout()->createBlock('giftcards/checkout_items')->toHtml());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }


    public function deActivateGiftCardAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardIds = $oSession->getGiftCardsIds();
Mage::log($cardIds);
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['balance'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);
Mage::log($oSession->getData());
        $this->_goBack();
    }

    private function _setSessionVars($card, $amount)
    {
        $oSession = Mage::getSingleton('giftcards/session');

        if($amount == 0){
            $amount = min($card->getCardBalance(), Mage::helper('giftcards')->getCleanGrandTotal());
        }

        $giftCardsIds = $oSession->getGiftCardsIds();
        //append applied gift card id to gift card session
        //append applied gift card balance to gift card session
        if (!empty($giftCardsIds)) {
            $giftCardsIds[$card->getId()] =  array('balance' => $card->getCardBalance()-$amount, 'code' => $card->getCardCode(), 'card_amount' => $amount);
            $oSession->setGiftCardsIds($giftCardsIds);

            $newBalance = $oSession->getGiftCardBalance() + $amount;
            $oSession->setGiftCardBalance($newBalance);

        } else {
            $giftCardsIds[$card->getId()] = array('balance' => $card->getCardBalance()-$amount, 'code' => $card->getCardCode(), 'card_amount' => $amount);
            $oSession->setGiftCardsIds($giftCardsIds);

            $oSession->setGiftCardBalance($amount);
        }
    }

    public function agreeToUseAction()
    {

        $q = Mage::getSingleton('giftcards/session')->getActive() ? 0 : 1;
        Mage::getSingleton('giftcards/session')->setActive($q);

        if($q == 0){
            Mage::getSingleton('giftcards/session')->clear();
        }

        $result['goto_section'] = 'payment';
        $this->_getQuote()->collectTotals()->save();
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );


        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function ajaxActivateGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $amount = (float)$this->getRequest()->getParam('giftcard_amount');

        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');

        if($amount == 0){
            $amount = min($card->getCardBalance(), Mage::getSingleton('checkout/session')->getGrandTotal());
            //$amount = $card->getCardBalance();
        }

        $storeId = Mage::app()->getStore()->getWebsiteId();
        $currDate = date('Y-m-d');

        if($card->getId() && ($card->getWebsiteId() == $storeId || $card->getWebsiteId() == 0) && (!$card->getDateEnd() || $card->getDateEnd() >= $currDate)){
            if ($card->getId() && ($card->getCardStatus() == 1)) {

                Mage::getSingleton('giftcards/session')->setActive('1');
                $this->_setSessionVars($card, $amount);
                $this->_getQuote()->collectTotals();

            } else {
                if($card->getId() && ($card->getCardStatus() == 2)) {
                    $result['error'] = $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode));
                } else {
                    $result['error'] = $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode));
                }
            }
        } else {
            $result['error'] = $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode));
        }

        $result['goto_section'] = 'payment';
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function ajaxDeActivateGiftCardAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardIds = $oSession->getGiftCardsIds();
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['card_amount'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);

        $this->_getQuote()->collectTotals()->save();

        $result['goto_section'] = 'payment';
        $result['update_section'] = array(
            'name' => 'payment-method',
            'html' => $this->_getPaymentMethodsHtml()
        );
        $result['giftcard_section'] = array(
            'html' => $this->_getUpdatedCoupon()
        );

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load(array('checkout_onepage_paymentmethod', 'giftcard_onepage_coupon'));
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->removeOutputBlock('gc');
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getUpdatedCoupon()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load(array('checkout_onepage_paymentmethod', 'giftcard_onepage_coupon'));
        $layout->generateXml();
        $layout->generateBlocks();
        $layout->removeOutputBlock('root');
        $output = $layout->getOutput();
        return $output;
    }

    public function activateCheckoutGiftCardAction()
    {
        $giftCardCode = trim((string)$this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftCardCode, 'card_code');

        if ($card->getId() && ($card->getCardStatus() == 1)) {
            $card->activateCard();

            Mage::getSingleton('core/session')->addSuccess(
                $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftCardCode))
            );
            Mage::getSingleton('giftcards/session')->setActive('1');
            $this->_setSessionVars($card);
        } else {
            if($card->getId() && ($card->getCardStatus() == 2)) {
                Mage::getSingleton('core/session')->addError(
                    $this->__('Gift Card "%s" was used.', Mage::helper('core')->escapeHtml($giftCardCode))
                );
            } else {
                Mage::getSingleton('core/session')->addError(
                    $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftCardCode))
                );
            }
        }
    }

    public function deActivateCheckoutGiftCardAction()
    {
        $oSession = Mage::getSingleton('giftcards/session');
        $cardId = $this->getRequest()->getParam('id');
        $cardIds = $oSession->getGiftCardsIds();
        $sessionBalance = $oSession->getGiftCardBalance();
        $newSessionBalance = $sessionBalance - $cardIds[$cardId]['balance'];
        unset($cardIds[$cardId]);
        if(empty($cardIds))
        {
            Mage::getSingleton('giftcards/session')->clear();
        }
        $oSession->setGiftCardBalance($newSessionBalance);
        $oSession->setGiftCardsIds($cardIds);
        $this->_redirect('onestepcheckout/index/index');
        return;
    }


}
