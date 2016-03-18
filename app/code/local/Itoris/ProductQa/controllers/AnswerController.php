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
 
class Itoris_ProductQa_AnswerController extends Mage_Core_Controller_Front_Action {

	/**
	 * Add answer action
	 */
	public function addAction() {
		$output = array();
		$customerId = Mage::getSingleton('customer/session')->getId();
		$submitter_type = ($customerId) ? Itoris_ProductQa_Model_Answers::SUBMITTER_CUSTOMER
										: Itoris_ProductQa_Model_Answers::SUBMITTER_VISITOR;

		$storeId = Mage::app()->getStore()->getId();
		$websiteId = Mage::app()->getWebsite()->getId();
		$settings = Mage::getSingleton('itoris_productqa/settings')->load($websiteId, $storeId);

		if (!$customerId && $settings->getCaptcha() != Itoris_ProductQa_Model_Settings::NO_CAPTCHA) {
			$code = $this->getRequest()->getParam('captcha_code_answer');
			$captcha = $this->getRequest()->getParam('captcha');
			if(!Mage::helper('itoris_productqa/captcha')->captchaValidate($code, $captcha)){
				$output['error'] = true;
				$output['captcha'] = true;
				$this->getResponse()->setBody(Zend_Json::encode($output));
				return;
			}
		}

		$questionId = (int)$this->getRequest()->getParam('question_id');
		$question = Mage::getModel('itoris_productqa/questions')->load($questionId);
		$productId = $question->getProductId();
		$data = array(
			'status' => $this->getRequest()->getParam('status'),
			'submitter_type' => $submitter_type,
			'q_id' => $questionId,
			'nickname' => $this->getRequest()->getParam('nickname_answer'),
			'content' => htmlspecialchars($this->getRequest()->getParam('answer')),
			'customer_id' => $customerId,
			'newsletter' => ($this->getRequest()->getParam('newsletter')) ? 1 : 0,
			'newsletter_email' => $this->getRequest()->getParam('newsletter_email', null),
			'product_id' => $productId,
		);
		try {
			$output['subscribe'] = Mage::getSingleton('itoris_productqa/answers')->addAnswer($data);

			Mage::register('settings', $settings);
			/** @var $product Mage_Catalog_Model_Product */
			$product = Mage::getModel('catalog/product')->load($productId);
			$notification = array(
				'store_name'   => Mage::getModel('core/store')->load($storeId)->getName(),
				'user_type'    => $data['submitter_type'],
				'nickname'     => $data['nickname'],
				'product_name' => $product->getName(),
				'q_url'        => Mage::getModel('adminhtml/url')->getUrl('itoris_productqa/admin_questions/edit', array('id' => (int)$data['q_id'])),
				'qa_details'   => $data['content'],
				'type'         => Itoris_ProductQa_Model_Notify::TYPE_ANSWER,
				'username'     => $question->getNickname(),
				'product_page' => $product->getProductUrl(),
			);
			if ($settings->getNotifyAdministrator()) {
				Mage::getModel('itoris_productqa/notify')->sendNotification($notification, Itoris_ProductQa_Model_Notify::ADMIN);
			}

			if ($settings->getAnswersApproval() == Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO
				|| ($customerId && $settings->getAnswersApproval() == Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO_CUSTOMER)
			) {
				$question->sendNotifications($notification);
			}

			if ($this->getRequest()->getParam('status') == Itoris_ProductQa_Model_Answers::STATUS_APPROVED) {
				$ajax = Mage::getBlockSingleton('itoris_productqa/productQaAjax');
				$output['html'] = $ajax->getHtmlForAnswers($questionId);
			} else {
				$output['html'] = 'ok';
			}
			$this->getResponse()->setBody(Zend_Json::encode($output));
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$output['error'] = true;
			$this->getResponse()->setBody(Zend_Json::encode($output));
		}
	}

	public function ratingPlusAction() {
		$answerId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRate()) {
				exit;
			}
			$remoteIp = $this->getCustomerSession()->isLoggedIn() ? null : Mage::helper('core/http')->getRemoteAddr();
			Mage::getSingleton('itoris_productqa/answers')->addRating($answerId,$this->getCustomerSession()->getCustomerId(),'1', $remoteIp);
		} catch (Exception $e) {}
		exit;
	}

	public function ratingMinusAction() {
		$answerId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRate()) {
				exit;
			}
			$remoteIp = $this->getCustomerSession()->isLoggedIn() ? null : Mage::helper('core/http')->getRemoteAddr();
			Mage::getSingleton('itoris_productqa/answers')->addRating($answerId, $this->getCustomerSession()->getCustomerId(), '-1', $remoteIp);
		} catch (Exception $e) {}
		exit;
	}

	public function inapprAction() {
		$answerId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRateInappr()) {
				exit;
			}
			Mage::getSingleton('itoris_productqa/answers')->setInappr($answerId);
			$this->getResponse()->setBody(Zend_Json::encode(array('message' => $this->__('Thank you for your report! Our moderator will review it shortly.'))));
		} catch (Exception $e) {}
	}

	/**
	 * @return Mage_Customer_Model_Session
	 */
	protected function getCustomerSession() {
		return Mage::getSingleton('customer/session');
	}

	/**
	 * @return Itoris_ProductQa_Model_Settings
	 */
	protected function getSettings() {
		$storeId = Mage::app()->getStore()->getId();
		$websiteId = Mage::app()->getWebsite()->getId();
		return Mage::getSingleton('itoris_productqa/settings')->load($websiteId, $storeId);
	}
}
?>