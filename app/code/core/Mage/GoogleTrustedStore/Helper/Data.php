<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleTrustedStore
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Mage_GoogleTrustedStore_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return Mage_GoogleTrustedStore_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('googletrustedstore/config');
    }

    /**
     * Sends subscribe requst email to Google group
     *
     * @param string $email Email for subscription
     */
    public function subscribeForUpdate($email)
    {
        $message = new Zend_Mail;
        $message->setFrom($email)
            ->addTo($this->_getConfig()->getSubscriptionEmail())
            ->setBodyText('')
            ->send();
    }

    /**
     * @param $store
     * @param $items
     * @param $tplSuccess
     * @param $tplError
     * @return $this
     */
    public function sendFeedNotification($store, $items, $tplSuccess, $tplError)
    {
        $body = '';
        $hasError = false;
        $allowedKeys = array('entity_name', 'store_name', 'error_message');
        foreach($items as $item) {
            if ($item['successfully']) {
                $itemMsg = $this->__($tplSuccess);
            } else {
                $itemMsg = $this->__($tplError);
                $hasError = true;
            }
            foreach($allowedKeys as $key) {
                $value = isset($item[$key]) ? $item[$key] : '';
                $itemMsg = str_replace("{{$key}}", $value, $itemMsg);
            }
            $body .= $itemMsg . PHP_EOL;
        }

        $email = $this->_getConfig()->getNotificationRecipientEmail($store);
        $subject = $this->_getConfig()->getNotificationSubject();
        $subject.= $hasError ? $this->__('Failure') : $this->__('Success');

        $mail = new Zend_Mail();
        $mail->setFrom($this->_getConfig()->getDefaultSenderEmail(), $this->_getConfig()->getDefaultSenderName());
        $mail->addTo($email);
        $mail->setSubject($subject);
        $mail->setBodyHtml(nl2br($body));

        try {
            $mail->send();
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
}
