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

ini_set("pcre.recursion_limit", "524");
 
class Itoris_ProductQa_Controller_Admin_Controller extends Mage_Adminhtml_Controller_Action {

	public function preDispatch() {
		$result =  parent::preDispatch();
		if (!Itoris_Installer_Client::isAdminRegistered($this->getDataHelper()->getAlias())) {
			$this->setFlag('', self::FLAG_NO_DISPATCH, true);
			$register = $this->getLayout()->createBlock( 'core/template' );
			$register->setMessage($this->__('Your copy of the component is not registered! All functions are disabled. Please register. Enter your S/N to register:'));
			$register->setTemplate('itoris/productqa/register.phtml');
			Mage::getSingleton('adminhtml/session')->addError($register->toHtml());
			$this->loadLayout();
			$this->renderLayout();
		}
		return $result;
	}

	/**
	 * Get data helper
	 *
	 * @return Itoris_ProductQa_Helper_Data
	 */
	protected function getDataHelper() {
		return Mage::helper('itoris_productqa');
	}
	
}
?>