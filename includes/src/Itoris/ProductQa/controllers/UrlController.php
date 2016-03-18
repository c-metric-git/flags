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
 
class Itoris_ProductQa_UrlController extends Mage_Core_Controller_Front_Action {

	/**
	 * Set url to product page with the opened form for add a new question
	 */
	public function questionFormAction() {
		$url =  Mage::getSingleton('customer/session')->getBeforeAuthUrl() . '?question=1';
		Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);
	}

	/**
	 * Set url to product page with the opened form for add a new answer for the question
	 */
	public function answerFormAction() {
		$url =  Mage::getSingleton('customer/session')->getBeforeAuthUrl()
			 	. '?answer='
				. (int)$this->getRequest()->getParam('answer')
				. '&page='
				. (int)$this->getRequest()->getParam('page');
		$form = $this->getRequest()->getParam('form');
		if (!empty($form)) {
			$url .= '&form=1';
		}
		
		Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);
	}
}
?>