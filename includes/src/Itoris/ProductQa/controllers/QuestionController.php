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
 
class Itoris_ProductQa_QuestionController extends Mage_Core_Controller_Front_Action {

	/**
	 * Add question action
	 */
	public function addAction() {
		$output = null;
		$customerId = Mage::getSingleton('customer/session')->getId();
		$submitter_type = ($customerId) ? Itoris_ProductQa_Model_Questions::SUBMITTER_CUSTOMER
										: Itoris_ProductQa_Model_Questions::SUBMITTER_VISITOR;
		$productId = (int)$this->getRequest()->getParam('product_id');
		$mode = (int)$this->getRequest()->getParam('mode');
		$storeId = Mage::app()->getStore()->getId();
		$websiteId = Mage::app()->getWebsite()->getId();
		$settings = Mage::getSingleton('itoris_productqa/settings')->load($websiteId, $storeId);

		if (!$customerId && $settings->getCaptcha() != Itoris_ProductQa_Model_Settings::NO_CAPTCHA) {
			$code = $this->getRequest()->getParam('captcha_code_question');
			$captcha = $this->getRequest()->getParam('captcha');
			if(!Mage::helper('itoris_productqa/captcha')->captchaValidate($code, $captcha)){
				$output['error'] = true;
				$output['captcha'] = true;
				$this->getResponse()->setBody(Zend_Json::encode($output));
				return;
			}
		}

		$data = array(
			'status'           => $this->getRequest()->getParam('status'),
			'submitter_type'   => $submitter_type,
			'product_id'       => $productId,
			'nickname'         => $this->getRequest()->getParam('nickname_question'),
			'content'          => $this->getRequest()->getParam('question'),
			'customer_id'      => $customerId,
			'notify'           => ($this->getRequest()->getParam('notify')) ? 1 : 0,
			'notify_email'     => $this->getRequest()->getParam('notify_email', null),
			'store_id'         => $storeId,
			'newsletter'       => ($this->getRequest()->getParam('newsletter')) ? 1 : 0,
			'newsletter_email' => $this->getRequest()->getParam('newsletter_email', null),
		);
		Mage::register('storeId', $storeId);
		try {
			$questionsModel = Mage::getSingleton('itoris_productqa/questions');
			$output['subscribe'] = $questionsModel->addQuestion($data);
		} catch (Exception $e) {
			Mage::logException($e);
		}

		if ($settings->getNotifyAdministrator()) {
			Mage::register('settings', $settings);
			$product = Mage::getModel('catalog/product')->load($data['product_id']);
			$notification = array(
				'store_name'   => Mage::getModel('core/store')->load($data['store_id'])->getName(),
				'user_type'    => $data['submitter_type'],
				'nickname'     => $data['nickname'],
				'product_name' => $product->getName(),
				'q_url'        => Mage::getModel('adminhtml/url')->getUrl('itoris_productqa/admin_questions/edit', array('id' => Mage::registry('q_id'))),
				'qa_details'   => $data['content'],
				'type'         => Itoris_ProductQa_Model_Notify::TYPE_QUESTION,
			);
			try {
				Mage::getModel('itoris_productqa/notify')->sendNotification($notification, Itoris_ProductQa_Model_Notify::ADMIN);
			} catch(Exception $e) {
				Mage::logException($e);
			}
		}

		if ($this->getRequest()->getParam('status') == Itoris_ProductQa_Model_Questions::STATUS_APPROVED) {
			Mage::register('page', 1);
			Mage::register('perPage', (int)$this->getRequest()->getParam('per_page'));
			$output['html'] = $this->getQuestions($productId, $mode);
		} else {
			$output['html'] = 'ok';
		}
		$this->getResponse()->setBody(Zend_Json::encode($output));
	}

	public function modeAction() {
		$storeId = (int)$this->getRequest()->getParam('store_id');
		Mage::register('storeId', $storeId);
		$productId = (int)$this->getRequest()->getParam('product_id');
		$mode = (int)$this->getRequest()->getParam('mode');
		Mage::register('page', (int)$this->getRequest()->getParam('page'));
		Mage::register('perPage', (int)$this->getRequest()->getParam('per_page'));
		if (Mage::registry('page') != 1) {
			Mage::register('pages', (int)$this->getRequest()->getParam('pages'));
		}
		$this->getResponse()->setBody($this->getQuestions($productId, $mode));
	}

	public function getQuestions($productId, $mode = Itoris_ProductQa_Model_Questions::SORT_RECENT) {
		try {
			$ajax = Mage::getBlockSingleton('itoris_productqa/productQaAjax');
			return $ajax->getHtmlForQuestions($productId, $mode);
		} catch (Exception $e) {
			Mage::logException($e);
		}
		return null;
	}

	public function ratingPlusAction() {
		$questionId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRate()) {
				exit;
			}
			$remoteIp = $this->getCustomerSession()->isLoggedIn() ? null : Mage::helper('core/http')->getRemoteAddr();
			Mage::getSingleton('itoris_productqa/questions')->addRating($questionId, $this->getCustomerSession()->getCustomerId(), '1', $remoteIp);
		} catch (Exception $e) {}
		exit;
	}

	public function ratingMinusAction() {
		$questionId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRate()) {
				exit;
			}
			$remoteIp = $this->getCustomerSession()->isLoggedIn() ? null : Mage::helper('core/http')->getRemoteAddr();
			Mage::getSingleton('itoris_productqa/questions')->addRating($questionId, Mage::getSingleton('customer/session')->getId(), '-1', $remoteIp);
		} catch(Exception $e) {}
		exit;
	}

	public function inapprAction() {
		$questionId = $this->getRequest()->getParam('id');
		try {
			if (!$this->getCustomerSession()->isLoggedIn() && !$this->getSettings()->canVisitorRateInappr()) {
				exit;
			}
			Mage::getSingleton('itoris_productqa/questions')->setInappr($questionId);
			$this->getResponse()->setBody(Zend_Json::encode(array('message' => $this->__('Thank you for your report! Our moderator will review it shortly.'))));
		} catch (Exception $e) {}
	}

	public function subscribeAction() {
		$result = array('ok' => false);
		$questionId = (int)$this->getRequest()->getParam('question_id');
		if ($questionId && $this->getSettings()->getAllowSubscribingQuestion()) {
			$question = Mage::getModel('itoris_productqa/questions')->load($questionId);
			if ($questionId) {
				$customerId = null;
				$email = null;
				if ($this->getCustomerSession()->isLoggedIn()) {
					$customerId = $this->getCustomerSession()->getCustomer()->getId();
				} else {
					$email = $this->getRequest()->getParam('email');
					if (!$email) {
						$result['error'] = $this->__('Please check email');
					}
				}
				if ($question->getNotify()) {
					if (($customerId && $question->getCustomerId() == $customerId) || ($email && $question->getEmail() == $email)) {
						$result['error'] = $this->__('You already subscribed for this question');
					}
				}
				if (!isset($result['error'])) {
					try {
						$subscriber = Mage::getModel('itoris_productqa/question_subscriber');
						if (!$subscriber->isSubscribed($questionId, $customerId, $email)) {
							$subscriber->setQuestionId($questionId)
								->setCustomerId($customerId)
								->setEmail($email)
								->setStoreId(Mage::app()->getStore()->getId())
								->save();
							$result['ok'] = true;
							$result['message'] = $this->__('Subscription successfull');
							$result['is_customer'] = (bool)$customerId;
						} else {
							$result['error'] = $this->__('You already subscribed for this question');
						}
					} catch (Exception $e) {
						$result['error'] = $this->__('There was a problem with the subscription.');
					}
				}
			}
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
	}

	public function unsubscribeAction() {
		$result = array('ok' => false);
		$questionId = (int)$this->getRequest()->getParam('question_id');
		if ($questionId) {
			$question = Mage::getModel('itoris_productqa/questions')->load($questionId);
			if ($questionId) {
				$customerId = null;
				$email = null;
				if ($this->getCustomerSession()->isLoggedIn()) {
					$customerId = $this->getCustomerSession()->getCustomer()->getId();
				} else {
					$email = $this->getRequest()->getParam('email');
					if (!$email) {
						$result['error'] = $this->__('Please check email');
					}
				}
				try {
					if ($question->getNotify() && (($customerId && $question->getCustomerId() == $customerId) || ($email && $question->getEmail() == $email))) {
						$question->setNotify(false)
							->save();
						$result['ok'] = true;
						$result['message'] = $this->__('You have been unsubscribed');
					} else if (!isset($result['error'])) {
						$subscriber = Mage::getModel('itoris_productqa/question_subscriber');
						$collection = $subscriber->getCollection()
							->addFieldToFilter('question_id', array('eq' => $questionId));
						if ($customerId) {
							$collection->addFieldToFilter('customer_id', array('eq' => $customerId));
						}
						if ($email) {
							$collection->addFieldToFilter('email', array('eq' => $email));
						}
						if (count($collection)) {
							foreach ($collection as $item) {
								$item->delete();
							}
							$result['ok'] = true;
							$result['message'] = $this->__('You have been unsubscribed');
						}
					}
					$result['is_customer'] = (bool)$customerId;
				} catch (Exception $e) {
					$result['error'] = $this->__('There was a problem with the subscription.');
				}
			}
		}
		$this->getResponse()->setBody(Zend_Json::encode($result));
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