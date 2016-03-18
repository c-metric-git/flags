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
 * @copyright  Copyright (c) 2014 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */


class Itoris_ProductQa_Model_Observer {

	public function replaceSecretKey($observer) {
		if (Mage::getSingleton('adminhtml/url')->useSecretKey()
			&& Mage::app()->getRequest()->getParam(Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME, null) != Mage::getSingleton('adminhtml/url')->getSecretKey()
			&& Mage::app()->getRequest()->getModuleName() == 'itoris_productqa'
			&& Mage::app()->getRequest()->getControllerName() == 'admin_questions'
			&& Mage::app()->getRequest()->getActionName() == 'edit'
		) {
			Mage::app()->getRequest()->setParam(Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME, Mage::getSingleton('adminhtml/url')->getSecretKey());
		}
	}
}
?>