<?php

class Webtex_Giftcards_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_logger;
    
    protected function _construct()
    {
        parent::_construct();
        $this->_logger = Mage::getModel('giftcards/logger');
    }
    
    
    /**
     * Process saving gift card product
     *
     * @param $observer
     */
    public function catalogProductSaveBefore($observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'giftcards') {
            $product->setRequiredOptions('1');
        }
    }

    /**
     * Process saving order after user place order
     * Creates gift cards and charge off discount amount (only cards part) from user's balance
     *
     * @param $observer
     */
    public function checkoutTypeOnepageSaveOrderAfter($observer)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer()->getFirstname() . ' ' . Mage::getSingleton('customer/session')->getCustomer()->getLastname();

        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            $orders = $observer->getEvent()->getOrders();
            $order = array_shift($orders);
        }


        if(!strlen(trim($customer))){
            $customer = 'Guest. Remote IP '.$order->getRemoteIp();
        }

        $quote = $observer->getEvent()->getQuote();
        $giftcardDiscount = Mage::getSingleton('giftcards/session')->getGiftcardDiscount();
        if ($quote) {
            try {
                /* Create cards if its present in order */
                foreach ($quote->getAllVisibleItems() as $item) {
                    if ($item->getProduct()->getTypeId() == 'giftcards') {
                        $options = $item->getProduct()->getCustomOptions();
                        $optionsDataMap = array(
                            'card_type',
                            'mail_to',
                            'mail_to_email',
                            'mail_from',
                            'mail_message',
                            'offline_country',
                            'offline_state',
                            'offline_city',
                            'offline_street',
                            'offline_zip',
                            'offline_phone',
                            'mail_delivery_date',
                            'card_currency'
                        );
                        $data = array();
                        foreach ($optionsDataMap as $field) {
                            if (isset($options[$field])) {
                                $data[$field] = $options[$field]->getValue();
                            }
                        }
                        $data['card_amount'] = $item->getCalculationPrice()+$item->getTaxAmount();
                        $data['product_id'] = $item->getProductId();
                        $data['card_status'] = 0;
                        $data['order_id'] = $order->getId();
                        
                        if(Mage::getStoreConfig('giftcards/default/website_id')){
                            $data['website_id'] = Mage::app()->getStore()->getWebsiteId();
                        } else {
                            $data['website_id'] = 0;
                        }

                        if(!isset($data['mail_to_email']) || empty($data['mail_to_email'])){
                            $data['mail_to_email'] = $order->getCustomerEmail();
                        }

                        $curDate = date('m/d/Y');
                        for ($i = 0; $i < $item->getQty(); $i++) {
                          $prod = Mage::getModel('catalog/product')->load($item->getProductId());
                          if($prod->getAttributeText('wts_gc_pregenerate') == 'Yes'){
                            $preModel = Mage::getModel('giftcards/pregenerated')->getCollection()->addFieldToFilter('product_id', $item->getProductId())->addFieldToFilter('card_status',1);
                            $preCard = $preModel->getData();
                            $data['card_code'] = $preCard[0]['card_code'];
                            $preModel = Mage::getModel('giftcards/pregenerated')->load($preCard[0]['card_id']);
                            $preModel->setCardStatus(0);
                            $preModel->save();
                          }
                            $model = Mage::getModel('giftcards/giftcards');
                            $model->setData($data);
                            if (in_array($order->getState(), array('complete'))) {
                                if($item->getProduct()->getData('wts_gc_expired')){
                                    $model->setDateEnd(date('m/d/Y', strtotime("+".$item->getProduct()->getData('wts_gc_exired')." days")));
                                }
                                $model->setCardStatus(1);
                                $model->save();
                                if ((($curDate == $data['mail_delivery_date']) || empty($data['mail_delivery_date'])) && $data['card_type'] != 'offline') {
                                    $model->send();
                                }
                                
                            } else {
                                $model->setCardStatus(0);
                                $model->save();
                            }
                            $logData = array(
                                'card_action' => 'Created',
                                'card_id'     => $model->getId(),
                                'card_code'   => $model->getCardCode(),
                                'card_amount' => $model->getCardAmount(),
                                'card_balance'=> $model->getCardBalance(),
                                'card_status' => $model->getCardStatus(),
                                'order_id'    => $order->getIncrementId(),
                                'user_name'   => $customer,
                                'user'        => 'Customer',
                                
                            );
                            $this->_logger->writelog($logData);
                        }
                    }
                }

                if ($quote->getUseGiftcards()) {
                    $oSession = Mage::getSingleton('giftcards/session');
                    $giftCardsIds = $oSession->getGiftCardsIds();

                    $ids = array_keys($giftCardsIds);

                    $cards = Mage::getModel('giftcards/giftcards')->getCollection()
                        ->addFieldToFilter('card_status', 1)
                        ->addFieldToFilter('card_id', array(
                            'in' => $ids
                        ));

                    $baseCurrency = $quote->getBaseCurrencyCode();
                    
                    //$orderModel = Mage::getModel('sales/order')->load($order->getId());

                    foreach ($cards as $card) {
                        $oGiftCardOrder = Mage::getModel('giftcards/order');
                        
                        if (is_null($card->getCardCurrency()) || $card->getCardCurrency() == $baseCurrency) {
                            $useAmount = $giftCardsIds[$card->getId()]['card_amount'];
                            if ($useAmount > 0) {
                                $card->setCardBalance($giftCardsIds[$card->getId()]['balance']);
                                if ( $card->getCardBalance() == 0) {
                                    $card->setCardStatus(2); //set status to 'used' when gift card balance is 0;
                                }
                                $card->save();

                                $oGiftCardOrder->setIdGiftcard($card->getId());
                                $oGiftCardOrder->setIdOrder($order->getId());
                                $oGiftCardOrder->setDiscounted((float)$useAmount);
                                $oGiftCardOrder->save();
                            }
                        } else {
                            $convertedUseAmount = Mage::helper('giftcards')->currencyConvert($giftCardsIds[$card->getId()]['card_amount'], /*from*/
                                                                                             $card->getCardCurrency(), /*to*/
                                                                                             $baseCurrency);
                            $useAmount = $convertedUseAmount;
                            if ($useAmount > 0) {
                                $newCardBalance = Mage::helper('giftcards')->currencyConvert($giftCardsIds[$card->getId()]['balance'], $baseCurrency, $card->getCardCurrency());
                                $card->setCardBalance($newCardBalance);
                                if ($newCardBalance == 0) {
                                    $card->setCardStatus(2); //set status to 'used' when gift card balance is 0;
                                }
                                $card->save();

                                $oGiftCardOrder->setIdGiftcard($card->getId());
                                $oGiftCardOrder->setIdOrder($order->getId());

                                $oGiftCardOrder->setDiscounted((float)$useAmount);
                                $oGiftCardOrder->save();
                            }
                        }
                        $logData = array(
                            'card_action' => 'Used',
                            'card_id'     => $card->getId(),
                            'card_code'   => $card->getCardCode(),
                            'card_amount' => $card->getCardAmount(),
                            'card_balance'=> $card->getCardBalance(),
                            'card_status' => $card->getCardStatus(),
                            'order_id'    => $order->getIncrementId(),
                            'user_name'   => $customer,
                            'user'        => 'Customer',
                                
                        );
                        $this->_logger->writelog($logData);
                    }
                }
                Mage::getSingleton('giftcards/session')->clear();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('checkout')->sendPaymentFailedEmail($order, $e->getMessage());
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
            }
        }
    }

    /**
     * Process order cancel
     * Adds discounted amount back to user's balance (whole part?)
     *
     * @param $observer
     */
    public function salesOrderCancelAfter($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $giftCardsOrderCollection = Mage::getModel('giftcards/order')->getCollection()
            ->addFieldToFilter('id_order', $order->getId());

        if ($giftCardsOrderCollection->getSize() > 0) {
            $giftCardsIds = array();
            $discounted = array();
            foreach ($giftCardsOrderCollection as $giftCardOrderItem) {
                $giftCardsIds[] = $giftCardOrderItem->getIdGiftcard();
                $discounted[$giftCardOrderItem->getIdGiftcard()] = $giftCardOrderItem->getDiscounted();
            }
            $cards = Mage::getModel('giftcards/giftcards')->getCollection()
                ->addFieldToFilter('card_id', $giftCardsIds);
            foreach ($cards as $card) {
                if (is_null($card->getCardCurrency()) || $card->getCardCurrency() == $order->getBaseCurrencyCode()) {
                    $card->setCardBalance($card->getCardBalance() + $discounted[$card->getId()]);
                } else {
                    $reddemedValue = Mage::helper('giftcards')->currencyConvert($discounted[$card->getId()], $order->getBaseCurrencyCode(), $card->getCardCurrency());
                    $card->setCardBalance($card->getCardBalance() + $reddemedValue);
                }

                $card->setCardStatus(1);
                $card->save();

                $oGiftCardOrder = Mage::getModel('giftcards/order');
                $oGiftCardOrder->setIdGiftcard($card->getId());
                $oGiftCardOrder->setIdOrder($order->getId());
                $oGiftCardOrder->setDiscounted(-(float)$discounted[$card->getId()]);
                $oGiftCardOrder->save();
            }
        }
        
        $createdGiftCardsCollection = Mage::getModel('giftcards/giftcards')->getCollection()
            ->addFieldToFilter('order_id', $order->getId());
        
        $adminUser = Mage::getSingleton('admin/session');
        $staff = $adminUser->getUser()->getFirstname() . ' ' . $adminUser->getUser()->getLastname();
        
        foreach($createdGiftCardsCollection as $item){
            $item->setCardStatus(Webtex_Giftcards_Model_Giftcards::REFUNDED);
            $item->save();
            $logData = array(
                'card_action' => 'Refund',
                'card_id'     => $item->getId(),
                'card_code'   => $item->getCardCode(),
                'card_amount' => $item->getCardAmount(),
                'card_balance'=> $item->getCardBalance(),
                'card_status' => $item->getCardStatus(),
                'order_id'    => $order->getIncrementId(),
                'user_name'   => $staff,
                'user'        => 'Staff',
                                
                            );
            $this->_logger->writelog($logData);
        }
    }


    /**
     * Process order refund
     * Adds discounted amount back to user's balance
     *
     * @param $observer
     */

    public function saleOrderPaymentRefund($observer)
    {
        $oCreditmemo = $observer['creditmemo'];
        $oOrder = $oCreditmemo->getOrder();
        foreach($oCreditmemo->getAllItems() as $_item){
            $giftCardsOrderCollection = Mage::getModel('giftcards/order')->getCollection()->addFieldToFilter('id_order', $oOrder->getId());
            foreach($giftCardsOrderCollection as $giftCardOrder){
                $giftCard = Mage::getModel('giftcards/giftcards')->load($giftCardOrder->getIdGiftcard());
                $hash_data = unserialize($giftCard->getHashData());
                $cardItem = $hash_data[$giftCardOrder->getIdGiftcard()];
                $orderItem = $_item->getOrderItem();
                $refundAmountArray = $cardItem['items'][$orderItem->getQuoteItemId()];
                $refundAmount = $refundAmountArray['item_discount'] / (isset($refundAmountArray['qty']) ? $refundAmountArray['qty'] : 1);
                if (is_null($giftCard->getCardCurrency()) || $giftCard->getCardCurrency() == $oOrder->getBaseCurrencyCode()) {
                    $refundValue = $refundAmount * $_item->getQty();
                } else {
                    $refundValue = Mage::helper('giftcards')->currencyConvert($refundAmount, $oOrder->getBaseCurrencyCode(), $giftCard->getCardCurrency()) * $_item->getQty();
                }
                if($refundValue > 0 ){
                    $hash_data[$giftCardOrder->getIdGiftcard()]['items'][$orderItem->getQuoteItemId()]['item_discount'] = $hash_data[$giftCardOrder->getIdGiftcard()]['items'][$orderItem->getQuoteItemId()]['item_discount'] - $refundValue;
                    $giftCard->setCardBalance($giftCard->getCardBalance() + $refundValue);
                    $giftCard->setCardStatus(1);
                    $giftCard->setHashData(serialize($hash_data));
                    $giftCard->save();
                    $oGiftCardOrder = Mage::getModel('giftcards/order');
                    $oGiftCardOrder->setIdGiftcard($giftCard->getId());
                    $oGiftCardOrder->setIdOrder($oOrder->getId());
                    $oGiftCardOrder->setDiscounted(-(float)$refundValue);
                    $oGiftCardOrder->save();
                }
                if($oCreditmemo->getShippingAmount() > 0){
                    $refundValue = $hash_data[$giftCardOrder->getIdGiftcard()]['shipping_discount'];
                    //if($refundValue){
                    //    $oCreditmemo->setShippingAmount(0);
                    //}
                    $hash_data[$giftCardOrder->getIdGiftcard()]['shipping_discount'] = 0 ;
                    $giftCard->setCardBalance($giftCard->getCardBalance() + $refundValue);
                    $giftCard->setCardStatus(1);
                    $giftCard->setHashData(serialize($hash_data));
                    $giftCard->save();
                    $oGiftCardOrder = Mage::getModel('giftcards/order');
                    $oGiftCardOrder->setIdGiftcard($giftCard->getId());
                    $oGiftCardOrder->setIdOrder($oOrder->getId());
                    $oGiftCardOrder->setDiscounted(-(float)$refundValue);
                    $oGiftCardOrder->save();
                }
            }
            
        }
        
        $createdGiftCardsCollection = Mage::getModel('giftcards/giftcards')->getCollection()
            ->addFieldToFilter('order_id', $oOrder->getId());
        
        $adminUser = Mage::getSingleton('admin/session');
        $staff = $adminUser->getUser()->getFirstname() . ' ' . $adminUser->getUser()->getLastname();
        
        foreach($createdGiftCardsCollection as $item){
            $item->setCardStatus(Webtex_Giftcards_Model_Giftcards::REFUNDED);
            $item->save();
            $logData = array(
                'card_action' => 'Refund',
                'card_id'     => $item->getId(),
                'card_code'   => $item->getCardCode(),
                'card_amount' => $item->getCardAmount(),
                'card_balance'=> $item->getCardBalance(),
                'card_status' => $item->getCardStatus(),
                'order_id'    => $oOrder->getIncrementId(),
                'user_name'   => $staff,
                'user'        => 'Staff',
                                
                            );
            $this->_logger->writelog($logData);
        }
        
    }

/*
    public function saleOrderPaymentRefund($observer)
    {
        $oCreditmemo = $observer['creditmemo'];
        $oOrder = $oCreditmemo->getOrder();
        $giftCardsOrderCollection = Mage::getModel('giftcards/order')->getCollection()->addFieldToFilter('id_order', $oOrder->getId());
        if($giftCardsOrderCollection->getSize() > 0) {
            $gcAmountDiscount = 0;
            foreach ($giftCardsOrderCollection as $giftCardOrderItem) {
                $giftCardsIds[] = $giftCardOrderItem->getIdGiftcard();
                $gcAmountDiscount += $oCreditmemo->getDiscountAmount();
                $aDiscounted[$giftCardOrderItem->getIdGiftcard()] = $oCreditmemo->getDiscountAmount();
            }
            
            $cards = Mage::getModel('giftcards/giftcards')->getCollection()
                     ->addFieldToFilter('card_id', $giftCardsIds);

            foreach ($cards as $card) {
                if (is_null($card->getCardCurrency()) || $card->getCardCurrency() == $oOrder->getBaseCurrencyCode()) {
                    $card->setCardBalance($card->getCardBalance() + $aDiscounted[$card->getId()]);
                } else {
                    $reddemedValue = Mage::helper('giftcards')->currencyConvert($aDiscounted[$card->getId()], $oOrder->getBaseCurrencyCode(), $card->getCardCurrency());
                    $card->setCardBalance($card->getCardBalance() + $reddemedValue);
                }

                $card->setCardStatus(1);
                $card->save();

                $oGiftCardOrder = Mage::getModel('giftcards/order');
                $oGiftCardOrder->setIdGiftcard($card->getId());
                $oGiftCardOrder->setIdOrder($oOrder->getId());
                $oGiftCardOrder->setDiscounted(-(float)$aDiscounted[$card->getId()]);
                $oGiftCardOrder->save();
            }
        }
    }

*/

    /**
     * Process order saving
     * Send cards emails on order complete
     *
     * @param $observer
     */
    public function salesOrderSaveAfter($observer)
    {
        $curDate = date('Y-m-d');
        $order = $observer->getEvent()->getOrder();

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $user = 'Customer';
        
        if(!$customer->getId()){
            $customer = Mage::getSingleton('admin/session')->getUser();
            $user     = 'Staff';
        }
        
        if($customer && $customer->getId()){
            $userName = $customer->getFirstname() . ' ' . $customer->getLastname();
        } else {
            $userName = 'Guest. Remote IP '.$order->getRemoteIp();
        }
        

        if (in_array($order->getState(), array('complete'))) {
            $cards = Mage::getModel('giftcards/giftcards')->getCollection()
                ->addFieldToFilter('order_id', $order->getId());
            foreach ($cards as $card) {
                if($card->getCardStatus() == 0) {
                    $product = Mage::getModel('catalog/product')->load($card->getProductId());
                    if($product->getData('wts_gc_expired')){
                        $card->setDateEnd(date('Y-m-d', strtotime("+".$product->getData('wts_gc_exired')." days")));
                    }
                    $card->setCardStatus(1)->save();
                    if ((($card->getMailDeliveryDate() == null) || ($curDate >= $card->getMailDeliveryDate())) && $card->getCardType() != 'offline') {
                        $card->send();
                    }
                    $logData = array(
                        'card_action' => 'Updated',
                        'card_id'     => $card->getId(),
                        'card_code'   => $card->getCardCode(),
                        'card_amount' => $card->getCardAmount(),
                        'card_balance'=> $card->getCardBalance(),
                        'card_status' => $card->getCardStatus(),
                        'order_id'    => $order->getIncrementId(),
                        'user_name'   => $userName,
                        'user'        => 'Customer',
                                
                    );
                    $this->_logger->writelog($logData);
                }
            }
        }
    }

    /**
     * Hide price for giftcard in product list when price of giftcard product isn't defined(=0)
     * @param $observer
     */
    public function checkPriceIsZero($observer)
    {
        $block = $observer->getBlock();

        if (get_class($block) === 'Mage_Catalog_Block_Product_Price') {
            $product = $block->getProduct();
            if ($product->getTypeId() === 'giftcards') {
                if ($product->getPrice() == 0) {
                    $observer->getTransport()->setHtml('&nbsp');
                }
            }
        }
    }

    /**
     * Send email based on delivery date specified by customer
     * starts every day at 01.00 am (see config.xml)
     */
    public function sendEmailByDeliveryDate()
    {
        $currentDate = date('Y-m-d');
        $oGiftCards = Mage::getModel('giftcards/giftcards')->getCollection()
            ->addFieldToFilter('mail_delivery_date', array('eq' => $currentDate))
            ->addFieldToFilter('card_status', 1);
        foreach ($oGiftCards as $oGiftCard) {
            $oGiftCard->send();
        }
    }
}