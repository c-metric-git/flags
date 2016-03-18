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
 * @copyright  Copyright (c) 2013 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

/**
 * @method setSortMode()
 * @method getSortMode()
 */

class Itoris_ProductQa_Block_Form_Question extends Mage_Core_Block_Template {

	protected $questions = array();
	protected $activeAnswer = null;
	protected $isActiveQuestionInfo = false;
	protected $activeQuestions = array();
	protected $currentProductUrl = '';

	protected function _construct() {
		if (!is_null($this->getRequest()->getParam('answer', null))) {
			$this->activeAnswer = (int)$this->getRequest()->getParam('answer');
		}
		$activeQuestons = $this->getRequest()->getParam('question_id', array());
		if (!is_array($activeQuestons)) {
			$activeQuestons = array($activeQuestons);
		}

		$this->activeQuestions = $activeQuestons;
		$this->isActiveQuestionInfo = (bool)$this->getRequest()->getParam('page');
	}

	public function getActiveAnswer() {
		return $this->activeAnswer;
	}

	public function getIsActiveQuestionInfo() {
		return $this->isActiveQuestionInfo;
	}

	public function canShowQuestionInfo($answerNum, $questionId) {
		return $this->isQuestionActive($questionId) || ($this->getIsActiveQuestionInfo() && $this->getActiveAnswer() === $answerNum);
	}

	public function isQuestionActive($num) {
		return in_array($num, $this->activeQuestions);
	}

	public function setQuestions($questions) {
		if (is_array($questions)) {
			$this->questions = $questions;
		}

		return $this;
	}

	public function getQuestions() {
		return $this->questions;
	}

	public function getAnswersHtml($answers, $type) {
		$answersBlock = $this->getLayout()->createBlock('itoris_productqa/form_answer');
		$answersBlock->setTemplate('itoris/productqa/form/answer/' . $type . '.phtml');
		$answersBlock->setAnswers($answers);
		return $answersBlock->toHtml();
	}

	public function canSubscribeOnQuestion() {
		return Mage::helper('itoris_productqa')->getSettingsFrontend()->getAllowSubscribingQuestion();
	}

	public function isSubscribedToQuestion($questionId) {
		if (!$this->isGuest()) {
			$question = Mage::getModel('itoris_productqa/questions')->load($questionId);
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			if ($question->getId() && $question->getNotify() && $question->getCustomerId() == $customerId) {
				return true;
			}
			return Mage::getModel('itoris_productqa/question_subscriber')->isSubscribed($questionId, $customerId);
		}
		return false;
	}

	public function isGuest() {
		return !Mage::getSingleton('customer/session')->isLoggedIn();
	}

	public function preparePageUrl($page) {
		$currentUrl = $this->currentProductUrl;
		$currentUrl .= strpos($currentUrl, '?') === false ? '?' : '&';
		$currentUrl .= 'sort=' . $this->getSortMode();
		$currentUrl .= '&page=' . $page;
		return $currentUrl;
	}

	public function setProductId($productId) {
		$this->setData('product_id', $productId);
		if (Mage::registry('current_product') && Mage::registry('current_product')->getId() == $productId) {
			$this->currentProductUrl = Mage::registry('current_product')->getProductUrl();
		} else {
			$this->currentProductUrl = Mage::getModel('catalog/product')->load($productId)->getProductUrl();
		}
		return $this;
	}

	public function isSearchRequest() {
		return $this->getRequest()->getParam(Itoris_ProductQa_Block_ProductQa::SEARCH_QUERY_VAR_NAME);
	}

	/**
	 * @return Itoris_ProductQa_Helper_Data
	 */
	public function getDataHelper() {
		return Mage::helper('itoris_productqa');
	}
}

?>