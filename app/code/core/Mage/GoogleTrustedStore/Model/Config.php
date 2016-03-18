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

/**
 * Module's config
 *
 */
class Mage_GoogleTrustedStore_Model_Config
{
    const XML_PATH_LAST_TIME_GENERATED_DATE = 'google/trustedstore/last_generated_date';
    const XML_PATH_CANCELLATION_REASONS = 'global/googletrustedstore/order_cancellation_reasons';
    const XML_PATH_DEFAULT_CANCELLATION_REASON = 'google/trustedstore/default_order_cancellation_reason';
    const XML_PATH_ENABLED = 'google/trustedstore/enabled';
    const XML_PATH_ACCOUNT_ID = 'google/trustedstore/account_id';
    const XML_PATH_ESTIMATED_SHIP_DATE = 'google/trustedstore/estimated_ship_date';
    const XML_PATH_FTP_HOSTNAME = 'global/googletrustedstore/ftp_host';
    const XML_PATH_FTP_USERNAME = 'google/trustedstore/ftp_username';
    const XML_PATH_FTP_PASSWORD = 'google/trustedstore/ftp_password';
    const XML_PATH_FTP_MODE = 'google/trustedstore/ftp_mode';
    const XML_PATH_SHIPMENT_FEED_FILENAME = 'google/trustedstore/shipment_feed_filename';
    const XML_PATH_CANCELLATION_FEED_FILENAME ='google/trustedstore/cancellation_feed_filename';
    const XML_PATH_CARRIERS = 'global/googletrustedstore/carriers';
    const XML_PATH_GOOGLE_SHOPPING_ACCOUNT_ID = 'google/trustedstore/google_shopping_account_id';
    const XML_GOOGLE_GROUP_EMAIL = 'global/googletrustedstore/google_group_email';

    const XML_PATH_NOTIFICATION_ENABLED = 'google/trustedstore/notification_enabled';
    const XML_PATH_NOTIFICATION_EMAIL = 'google/trustedstore/notification_recipient_email';

    const XML_PATH_DEFAULT_SENDER_NAME = 'trans_email/ident_general/name';
    const XML_PATH_DEFAULT_SENDER_EMAIL= 'trans_email/ident_general/email';

    const XML_PATH_NOTIFICATION_SUBJECT = 'Google Trusted Stores Magento Extension Notification:';
    const XML_PATH_NOTIFICATION_SUCCESS_MESSAGE_GENERATE = '{entity_name} export file has been successfully generated';
    const XML_PATH_NOTIFICATION_ERROR_MESSAGE_GENERATE = '{entity_name} export file generation failure: {error_message}';
    const XML_PATH_NOTIFICATION_SUCCESS_MESSAGE_UPLOAD = '{entity_name} export file has been successfully uploaded';
    const XML_PATH_NOTIFICATION_ERROR_MESSAGE_UPLOAD = '{entity_name} export file upload failure: {error_message}';

    const CARRIER_CODE_OTHER = 'Other';
    const CARRIER_NAME_OTHER = 'OTHER';
    /**
     * Check if extension is enabled in admin panel or not
     * @param mixed $store
     *
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Returns ID of Google trusted stores account
     * @param mixed $store
     *
     * @return string
     */
    public function getAccountId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ACCOUNT_ID, $store);
    }

    /**
     * Load reasons from config
     *
     * @throws RuntimeException if cannot load
     * @return array array(code => description, ...)
     */
    public function getCancellationReasons()
    {
        $reasons = Mage::getConfig()->getNode(self::XML_PATH_CANCELLATION_REASONS);
        if (!$reasons) {
            throw new RuntimeException("Order cancellation reasons were not found at "
                . self::XML_PATH_CANCELLATION_REASONS . "; seems like config is broken."
            );
        }

        return $reasons->children();
    }

    /**
     * Returns code of default cancellation reason
     *
     * @throws RuntimeException If cannot load code or invalid value of code
     * @return string
     */
    public function getDefaultCancellationReasonCode()
    {
        $default = Mage::getStoreConfig(self::XML_PATH_DEFAULT_CANCELLATION_REASON);
        if (!$default) {
            throw new RuntimeException("Default order cancellation reasons was not found at "
                . self::XML_PATH_DEFAULT_CANCELLATION_REASON . "; seems like config is broken."
            );
        }
        // Check for consistence
        if (!Mage::getConfig()->getNode(self::XML_PATH_CANCELLATION_REASONS . '/' . $default)) {
            throw new RuntimeException(
                "$default order cancellation reason was defined as default but there is no such reason by "
                . self::XML_PATH_CANCELLATION_REASONS
            );
        }

        return (string)$default;
    }

    /**
     * Returns description by specified reason code
     *
     * @param string $code
     * @return string
     */
    public function getDescriptionOfCancellationReasonByCode($code)
    {
        return (string)Mage::getConfig()->getNode(self::XML_PATH_CANCELLATION_REASONS . '/' . $code);
    }

    /**
     * Returns estimated shipping period in days
     * @param mixed $store
     *
     * @return integer
     */
    public function getEstimatedShippingPeriod($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ESTIMATED_SHIP_DATE, $store);
    }

    /**
     * Returns FTP host's name of Google trusted stores account
     *
     * @return string
     */
    public function getFtpHostName()
    {
        return Mage::getConfig()->getNode(self::XML_PATH_FTP_HOSTNAME);
    }

    /**
     * Returns FTP user name of Google trusted stores account
     * @param mixed $store
     *
     * @return string
     */
    public function getFtpUserName($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FTP_USERNAME, $store);
    }

    /**
     * Returns FTP user name of Google trusted stores account
     * @param mixed $store
     *
     * @return string
     */
    public function getFtpPassword($store = null)
    {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_FTP_PASSWORD, $store));
    }

    /**
     * Returns FTP mode
     * @param mixed $store
     *
     * @return integer
     */
    public function getFtpMode($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FTP_MODE, $store);
    }

     /**
     * Returns dir for feed file storage
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     */
    protected function _getFeedDir($store)
    {
        if (Mage::getBaseDir('tmp')) {
            $dir = Mage::getBaseDir('tmp') . DIRECTORY_SEPARATOR . $store->getCode();
            if (!file_exists($dir)) {
                mkdir($dir,0777);
            }
            if (!is_dir($dir) || !is_writable($dir)) {
                throw new Varien_Exception('Unable to find writable feed catalog');
            }
        }
        return $dir;
    }

    /**
     * Returns full local path to feed
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFtpShipmentFileName(Mage_Core_Model_Store $store)
    {
        return $this->_getFeedDir($store) . DIRECTORY_SEPARATOR . $this->getFtpShipmentTargetFileName($store);
    }

    /**
     * Get FTP shipment target file name
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFtpShipmentTargetFileName(Mage_Core_Model_Store $store)
    {
        return Mage::getStoreConfig(self::XML_PATH_SHIPMENT_FEED_FILENAME, $store);
    }

    /**
     * Get FTP cancellation file name
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFtpCancellationFileName(Mage_Core_Model_Store $store)
    {
        return $this->_getFeedDir($store) . DIRECTORY_SEPARATOR . $this->getFtpCancellationTargetFileName($store);
    }

    /**
     * Get FTP cancellation target file name
     * @param Mage_Core_Model_Store $store
     *
     * @return string
     */
    public function getFtpCancellationTargetFileName(Mage_Core_Model_Store $store)
    {
        return Mage::getStoreConfig(self::XML_PATH_CANCELLATION_FEED_FILENAME, $store);
    }

    /**
     * Get last time when files were generated
     * @param mixed $store
     *
     * @return Zend_Date
     */
    public function getLastTimeGenerated($store = null)
    {
        $dateString = Mage::getStoreConfig(self::XML_PATH_LAST_TIME_GENERATED_DATE, $store);

        return $dateString
            ? new Zend_Date($dateString, Zend_Date::ISO_8601)
            : Zend_Date::now()->subDay(1);
    }

    /**
     * Set last time when files were generated
     *
     * @param Zend_Date $lastTime
     * @param Mage_Core_Model_Store $store
     */
    public function setLastTimeGenerated(Zend_Date $lastTime = null, $store)
    {
        if (!$lastTime) {
            $lastTime = Zend_Date::now();
        }
        $scope = $store ? 'stores' : 'default';
        $scopeId = $store ? $store->getId() : 0;
        $path = self::XML_PATH_LAST_TIME_GENERATED_DATE;

        $configDataCollection = Mage::getModel('core/config_data')
            ->getCollection()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('scope_id', $scopeId)
            ->addFieldToFilter('path', array('eq' => $path))
            ->load();
        if (count($configDataCollection)) {
            $configDataCollection->getFirstItem()
                ->setValue($lastTime->toString(Zend_Date::ISO_8601))
                ->save();
        } else {
            Mage::getModel('core/config_data')
                ->setPath(self::XML_PATH_LAST_TIME_GENERATED_DATE) // in case new record
                ->setValue($lastTime->toString(Zend_Date::ISO_8601))
                ->setScope($scope)
                ->setScopeId($scopeId)
                ->save();
        }
        $store->resetConfig();
    }

    /**
     * Returns values for "carrier code" field of shipment feed
     *
     * @param string $shipmentCarrierCode Carrier code from shipment
     * @return string
     */
    public function getCarrierCode($shipmentCarrierCode)
    {
        $code = Mage::getConfig()->getNode(self::XML_PATH_CARRIERS . '/main/' . $shipmentCarrierCode);

        return $code ? (string)$code : self::CARRIER_CODE_OTHER;
    }

    /**
     * Returns value for "other carrier name" field of shipment feed
     *
     * @param string $shipmentCarrierCode Carrier code from shipment
     * @return string
     */
    public function getOtherCarrierName($shipmentCarrierCode)
    {
        $code = Mage::getConfig()->getNode(self::XML_PATH_CARRIERS . '/other/' . $shipmentCarrierCode);

        return $code ? (string)$code : self::CARRIER_NAME_OTHER;
    }

    /**
     * Returns Google Shopping Account Id
     *
     * @return string
     */
    public function getGoogleShoppingAccountId()
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_SHOPPING_ACCOUNT_ID);
    }

    /**
     * Returns email for subscription to group
     *
     * @return string
     */
    public function getSubscriptionEmail()
    {
        $email = Mage::getConfig()->getNode(self::XML_GOOGLE_GROUP_EMAIL);
        if (!$email) {
            throw new RuntimeException(
                'Structure of config is incorrect; cannot get item by path ' . self::XML_GOOGLE_GROUP_EMAIL
            );
        }
        list ($name, $domain) = explode('@', $email, 2);

        return $name . '+subscribe@' . $domain;
    }

    /**
     * Check if Returns/Shipments email notification is enabled in admin panel or not
     *
     * @param $store
     * @return boolean
     */
    public function isNotificationEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_NOTIFICATION_ENABLED, $store);
    }

    /**
     * Get Returns/Shipments email for notification
     *
     * @param $store
     * @return string
     */
    public function getNotificationRecipientEmail($store = null)
    {
        $email = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_EMAIL, $store);
        if (!$email) {
            throw new RuntimeException(
                'Structure of config is incorrect; cannot get item by path ' . self::XML_PATH_NOTIFICATION_EMAIL
            );
        }
        return $email;
    }

    /**
     * Get Notification subject for generate operation
     *
     * @return string
     */
    public function getNotificationSubject()
    {
        return self::XML_PATH_NOTIFICATION_SUBJECT;
    }

    /**
     * Get Notification success message for generate operation
     *
     * @return string
     */
    public function getNotificationSuccessMessageGenerate()
    {
        return self::XML_PATH_NOTIFICATION_SUCCESS_MESSAGE_GENERATE;
    }

    /**
     * Get Notification error message for generate operation
     *
     * @return string
     */
    public function getNotificationErrorMessageGenerate()
    {
        return self::XML_PATH_NOTIFICATION_ERROR_MESSAGE_GENERATE;
    }

    /**
     * Get Notification success message for upload operation
     *
     * @return string
     */
    public function getNotificationSuccessMessageUpload()
    {
        return self::XML_PATH_NOTIFICATION_SUCCESS_MESSAGE_UPLOAD;
    }

    /**
     * Get Notification error message for upload operation
     *
     * @return string
     */
    public function getNotificationErrorMessageUpload()
    {
        return self::XML_PATH_NOTIFICATION_ERROR_MESSAGE_UPLOAD;
    }

    /**
     * Get System Default Sender Email address
     *
     * @param $store
     * @return string
     */
    public function getDefaultSenderEmail($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_SENDER_EMAIL, $store);
    }

    /**
     * Get System Default Sender name
     *
     * @param $store
     * @return string
     */
    public function getDefaultSenderName($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_SENDER_NAME, $store);
    }
}
