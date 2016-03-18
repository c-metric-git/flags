<?php 
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_PRODUCTQA
 * @copyright  Copyright (c) 2012 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

class Itoris_ProductQa_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $alias = 'productqa';

	/**
	 * Convert $days to string: 'today' or 'X days ago' or 'X months ago' or 'X years and Y months ago'
	 *
	 * @param $days
	 * @return string
	 */
	public function getDateStr($days) {
		$days = (int)$days;
		if ($days < 0) {
			return $this->__('today');
		}
		if (!$days) {
			return $this->__('today');
		}
		if ($days < 31) {
			return $days . $this->__(' days ago');
		} elseif ($days <= 365) {
			return (int)($days/30) . $this->__(' months ago');
		} else {
			$months = (int)(($days%365)/30);
			$months = ($months) ? $this->__(' and ') . $months . $this->__(' months ago') : '';
			return (int)($days/365) . $this->__(' years') . $months . $this->__(' ago');
		}
	}

	/**
	 * Get html link element
	 *
	 * @param $url
	 * @return string
	 */
	public function getHtmlLink($url){
		return '<a href="'. $url .'">'. $url .'</a>';
	}

	/**
	 * Get user type label
	 *
	 * @param $class
	 * @param $id
	 * @return string
	 */
	public function getUserType($class, $id){
		if ($class == 'Itoris_ProductQa_Model_Answers') {
			switch ($id) {
				case Itoris_ProductQa_Model_Answers::SUBMITTER_ADMIN:
					return $this->__('Administrator');
				case Itoris_ProductQa_Model_Answers::SUBMITTER_CUSTOMER:
					return $this->__('Customer');
				case Itoris_ProductQa_Model_Answers::SUBMITTER_VISITOR:
					return $this->__('Guest');
			}
		}
		if ($class == 'Itoris_ProductQa_Model_Questions') {
			switch ($id) {
				case Itoris_ProductQa_Model_Questions::SUBMITTER_ADMIN:
					return $this->__('Administrator');
				case Itoris_ProductQa_Model_Questions::SUBMITTER_CUSTOMER:
					return $this->__('Customer');
				case Itoris_ProductQa_Model_Questions::SUBMITTER_VISITOR:
					return $this->__('Guest');
			}
		}
	}

	public function getProductUrl($id, $storeId = null) {
		$product = Mage::getModel('catalog/product')->load((int)$id);
		$productUrl = $product->getUrlModel();
		if ($this->isOldVersion()) {
			$url = $product->getProductUrl($id);
		} else {
			$url = $productUrl->getUrl($product);
		}
		if ($storeId) {
			$product->setStoreId($storeId);
			if ($this->isOldVersion()) {
				$urlInStore = $product->getProductUrl($id);
			} else {
				$urlInStore = $productUrl->getUrlInStore($product);
			}
			$urls = array(
				'url' => $url,
				'url_in_store' => $urlInStore,
			);
			return $urls;
		}

		return $url;
	}

	/**
	 * Is magento version lower than 1.4.0.0
	 *
	 * @return bool
	 */
	public function isOldVersion() {
		if (version_compare(Mage::getVersion(),'1.4.0', '<')) {
			return true;
		} else {
			return false;
		}
	}

	public function isAdminRegistered() {
		try {
			return Itoris_Installer_Client::isAdminRegistered($this->getAlias());
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			return false;
		}
	}

	public function isRegisteredAutonomous($website = null) {
		return Itoris_Installer_Client::isRegisteredAutonomous($this->getAlias(), $website);
	}

	public function registerCurrentStoreHost($sn) {
		return Itoris_Installer_Client::registerCurrentStoreHost($this->getAlias(), $sn);
	}

	public function isRegistered($website) {
		return Itoris_Installer_Client::isRegistered($this->getAlias(), $website);
	}

	public function getAlias() {
		return $this->alias;
	}

	public function prepareHtmlText($text) {
		return nl2br($text);
	}

	/**
	 * @return Itoris_ProductQa_Model_Settings
	 */
	public function getSettingsFrontend() {
		$storeId = Mage::app()->getStore()->getId();
		$websiteId = Mage::app()->getWebsite()->getId();
		return Mage::getSingleton('itoris_productqa/settings')->load($websiteId, $storeId);
	}
}
?>