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

class Itoris_ProductQa_Block_ProductQa extends Mage_Core_Block_Template {

	const THEME_SIMPLE = 'simple';
	const THEME_SHARP = 'sharp';
	const THEME_SMOOTH = 'smooth';
	const SEARCH_QUERY_VAR_NAME = 's';

	protected $themes = array(
		self::THEME_SHARP => array(
			Itoris_ProductQa_Model_Settings::THEME_SHARP_BLUE    => 'blue',
			Itoris_ProductQa_Model_Settings::THEME_SHARP_BLACK   => 'black',
			Itoris_ProductQa_Model_Settings::THEME_SHARP_MAGENTO => 'magento',
			Itoris_ProductQa_Model_Settings::THEME_SHARP_GRAY    => 'gray',
			Itoris_ProductQa_Model_Settings::THEME_SHARP_WHITE   => 'white',
		),
		self::THEME_SMOOTH => array(
			Itoris_ProductQa_Model_Settings::THEME_SMOOTH_BLUE    => 'blue',
			Itoris_ProductQa_Model_Settings::THEME_SMOOTH_BLACK   => 'black',
			Itoris_ProductQa_Model_Settings::THEME_SMOOTH_MAGENTO => 'magento',
			Itoris_ProductQa_Model_Settings::THEME_SMOOTH_GRAY    => 'gray',
			Itoris_ProductQa_Model_Settings::THEME_SMOOTH_WHITE   => 'white',
		),
	);
	protected $styleTheme;
	protected $styleThemeColor = null;
	protected $isSimpleThemeFlag = null;
	protected $settings = null;
	protected $questionSortMode = null;
	protected $currentProductId = null;
	protected $searchQuery = null;

	protected function _construct() {
		parent::_construct();
		if ($this->getRequest()->getParam(self::SEARCH_QUERY_VAR_NAME)) {
			$this->setSearchQuery($this->getRequest()->getParam(self::SEARCH_QUERY_VAR_NAME));
		}
	}

	protected function _prepareLayout() {
		if(!Mage::helper('itoris_productqa')->isRegisteredAutonomous(Mage::app()->getWebsite())){
			return;
		}
		try {
			$this->prepareSettings();
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
			return;
		}

		Mage::getSingleton('customer/session')->setBeforeAuthUrl($this->helper('core/url')->getCurrentUrl());
		$subscribed = false;
		if ($this->getCustomerId()) {
			if ($this->getRequest()->getParam('question')) {
				$this->setShowQuestionForm(true);
			} else {
				$this->setShowQuestionForm(false);
			}
			$customerEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			$subscribed = (bool)Mage::getModel('newsletter/subscriber')->loadByEmail($customerEmail)->getId();
			if ($this->getRequest()->getParam('page')) {
				Mage::unregister('page');
				Mage::register('page', (int)$this->getRequest()->getParam('page'));
				$this->setShowAnswer((int)$this->getRequest()->getParam('answer'));
				$this->setShowQuestionInfo(true);
			}
			if ($this->getRequest()->getParam('form')) {
				$this->setShowAnswerForm((int)$this->getRequest()->getParam('form'));
			}
		}
		$this->setIsSubscribed($subscribed);
		$settings = $this->getSettings();

		if ($settings->getEnable() == Itoris_ProductQa_Model_Settings::ENABLE_YES) {
			if ($this->visibility($settings->getVisible())) {
				/** @var $headBlock Mage_Page_Block_Html_Head */
				$headBlock = $this->getLayout()->getBlock('head');
				$headBlock->addJs('itoris/productqa/productqa.js');

				switch ($this->getThemeType($settings->getColorScheme())) {
					case self::THEME_SIMPLE:
						$this->isSimpleThemeFlag = true;
						$this->setTemplate('itoris/productqa/simple.phtml');
						$headBlock->addItem('js_css', 'itoris/productqa/simple/main.css');
						$this->styleTheme = $headBlock->getJsUrl('itoris/productqa/simple/main.css');
						break;
					case self::THEME_SHARP:
						$this->setTemplate('itoris/productqa/sharp.phtml');
						$headBlock->addItem('js_css', 'itoris/productqa/sharp/main.css');
						$this->styleTheme = $headBlock->getJsUrl('itoris/productqa/sharp/main.css');
						$style = 'itoris/productqa/sharp/' . $this->themes['sharp'][$settings->getColorScheme()] .'/css/main.css';
						$headBlock->addItem('js_css', $style);
						$this->styleThemeColor = $headBlock->getJsUrl($style);
						break;
					case self::THEME_SMOOTH:
						$this->setTemplate('itoris/productqa/smooth.phtml');
						$headBlock->addItem('js_css', 'itoris/productqa/smooth/main.css');
						$this->styleTheme = $headBlock->getJsUrl('itoris/productqa/smooth/main.css');
						$style = 'itoris/productqa/smooth/' . $this->themes['smooth'][$settings->getColorScheme()] .'/css/main.css';
						$headBlock->addItem('js_css', $style);
						$this->styleThemeColor = $headBlock->getJsUrl($style);
						break;
				}
			}
		}
	}

	/**
	 * Prepare productQ&A settings
	 * Don't allow create productQ&A block if it already exists on the page
	 *
	 * @throws Exception
	 */
	protected function prepareSettings() {
		$this->setCustomerId(Mage::getSingleton('customer/session')->getId());

		$product = Mage::registry('product');
		if (empty($product)) {
			throw new Exception('Product Questions/Answers is not allowed on this page!');
		}
		$this->setProductId($product->getId());
		$this->setStoreId(Mage::app()->getStore()->getId());
		$this->setWebsiteId(Mage::app()->getWebsite()->getId());

		Mage::register('storeId', $this->getStoreId());

		$this->getSettings();

		Mage::register('perPage', $this->getSettings()->getQuestionsPerPage());
		$page = $this->getRequest()->getParam('page');
		Mage::register('page', $page ? $page : 1);
		Mage::register('settings', $this->getSettings());
		$sort = $this->getRequest()->getParam('sort');
		if (!$sort) {
			$sort = Itoris_ProductQa_Model_Questions::SORT_RECENT;
		}
		$this->setQA($this->getProductId(), $sort);
	}

	/**
	 * @return Itoris_ProductQa_Model_Settings
	 */
	public function getSettings() {
		if (is_null($this->settings)) {
			$this->settings = Mage::getSingleton('itoris_productqa/settings')->load(Mage::app()->getWebsite()->getId(), Mage::app()->getStore()->getId());
		}

		return $this->settings;
	}

	/**
	 * Set sorted by sort parameter questions and answers for product
	 *
	 * @param $productId
	 * @param int $mode
	 */
	protected function setQA($productId, $mode = Itoris_ProductQa_Model_Questions::SORT_RECENT, $includeQuestionId = null) {
		$this->questionSortMode = $mode;
		$this->currentProductId = $productId;
		$questions = Mage::getModel('itoris_productqa/questions')->getQuestions($productId, $mode, $includeQuestionId, $this->getSearchQuery());
		$questionsIds = array();
		foreach ($questions as $question) {
			$questionsIds[] = $question['id'];
		}
		if (!empty($questionsIds)) {
			$this->setAnswers(Mage::getModel('itoris_productqa/answers')->getAnswers($questionsIds));
			$this->setQuestions($this->attachAnswersToQuestions($questions));
		}
	}

	protected function attachAnswersToQuestions($questions) {
		foreach ($this->getAnswers() as $answer) {
			foreach ($questions as $key => $question) {
				if($question['id'] == $answer['q_id'])
					$questions[$key]['answer'][] = $answer;
			}
		}
		return $questions;
	}

	/**
	 * Is visible productQ&A for the current user
	 *
	 * @param $visibleCode
	 * @return bool
	 */
	private function visibility($visibleCode) {
		if (($visibleCode == Itoris_ProductQa_Model_Settings::VISIBLE_ALL)
			|| ($visibleCode == Itoris_ProductQa_Model_Settings::VISIBLE_CUSTOMER && $this->getCustomerId())
		) {
			return true;
		}
		return false;
	}

	protected function getThemeType($themeCode){
		foreach ($this->themes as $code => $themes) {
			if (isset($themes[$themeCode])) {
				return $code;
			}
		}
		return self::THEME_SIMPLE;
	}

	public function getQuestionsHtml($questions) {
		$questionBlock = $this->getLayout()->createBlock('itoris_productqa/form_question');
		$questionBlock->setSortMode($this->questionSortMode)
			->setProductId($this->currentProductId)
			->setQuestions($questions)
			->setTemplate('itoris/productqa/form/question/'. ($this->isSimpleTheme() ? 'simple' : 'theme') .'.phtml');
		return $questionBlock->toHtml();
	}

	protected function getAnswersHtml($answers) {
		$answersBlock = $this->getLayout()->createBlock('itoris_productqa/form_answer');
		$answersBlock->setTemplate('itoris/productqa/form/answer/'. ($this->isSimpleTheme() ? 'simple' : 'theme') .'.phtml');
		$answersBlock->setAnswers($answers);
		return $answersBlock->toHtml();
	}

	public function getQuestionStatus() {
		$status = $this->getSettings()->getQuestionsApproval();
		if (($status == Itoris_ProductQa_Model_Settings::Q_APPROVAL_AUTO)
			|| ($status == Itoris_ProductQa_Model_Settings::Q_APPROVAL_AUTO_CUSTOMER && $this->getCustomerId())
		) {
			$status = Itoris_ProductQa_Model_Questions::STATUS_APPROVED;
		} else {
			$status = Itoris_ProductQa_Model_Questions::STATUS_PENDING;
		}

		return $status;
	}

	public function getAnswerStatus() {
		$status = $this->getSettings()->getAnswersApproval();
		if (($status == Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO)
			|| ($status == Itoris_ProductQa_Model_Settings::A_APPROVAL_AUTO_CUSTOMER && $this->getCustomerId())
		) {
			$status = Itoris_ProductQa_Model_Answers::STATUS_APPROVED;
		} else {
			$status = Itoris_ProductQa_Model_Answers::STATUS_PENDING;
		}
		return $status;
	}

	/**
	 * Get a captcha html by the captcha type
	 *
	 * @param $imgId
	 * @return string
	 */
	public function getCaptchaHtml($imgId) {
		switch ($this->getSettings()->getCaptcha()) {
			case Itoris_ProductQa_Model_Settings::SHOW_SECURIMAGE:
				$type = 'securimage';
				break;
			case Itoris_ProductQa_Model_Settings::SHOW_CAPTCHA:
				$type = 'captchaForm';
				break;
			case Itoris_ProductQa_Model_Settings::SHOW_ALIKON:
			default:
				$type = 'alikon';
				break;
		}
		$captcha = $this->getLayout()->createBlock('itoris_productqa/form_captcha');
		$captcha->setCaptchaId($imgId)->setType($type);
		return $captcha->toHtml();
	}

	public function isSimpleTheme() {
		if (is_null($this->isSimpleThemeFlag)) {
			$this->isSimpleThemeFlag = self::THEME_SIMPLE == $this->getThemeType($this->getSettings()->getColorScheme());
		}
		return $this->isSimpleThemeFlag;
	}

	public function getModeValues() {
		return array(
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_HELPFUL_ANSWERS,
				'label' => $this->__('Questions With The Most Helpful Answers'),
			),
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_RECENT,
				'label' => $this->__('Most Recent Questions'),
			),
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_OLDEST,
				'label' => $this->__('Oldest Questions'),
			),
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_RECENT_ANSWERS,
				'label' => $this->__('Questions With Most Recent Answers'),
			),
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_OLDEST_ANSWERS,
				'label' => $this->__('Questions With Most Oldest Answers'),
			),
			array(
				'value' => Itoris_ProductQa_Model_Questions::SORT_MOST_ANSWERS,
				'label' => $this->__('Questions With Most Answers'),
			),
		);
	}

	/**
	 * Get input html element with a value equal to question status
	 *
	 * @deprecated
	 * @return string
	 */
	protected function getQuestionStatusHtml() {
		return '<input type="hidden" id="itoris_question_status" name="status" value="' . $this->getQuestionStatus() . '"/>';
	}

	/**
	 * Get input html element with a value equal to answer status
	 *
	 * @deprecated
	 * @return string
	 */
	public function getAnswerStatusHtml() {
		return '<input type="hidden" id="itoris_answer_status" name="status" value="' . $this->getAnswerStatus() . '"/>';
	}

	/**
	 * Get input html element with a value equal to a product id
	 *
	 * @deprecated
	 * @return string
	 */
	public function getProductIdHtml() {
		return '<input type="hidden" name="product_id" value="' . $this->getProductId() . '"/>';
	}

	/**
	 * Get input html element with a value equal to a store id
	 *
	 * @deprecated
	 * @return string
	 */
	protected function getStoreIdHtml() {
		return '<input type="hidden" name="store_id" value="' . $this->getStoreId() . '"/>';
	}

	/**
	 * Config for ProductQa js object
	 *
	 * @return string
	 */
	public function getConfigJson() {
		$config = array(
			'allowRateGuestAll'      => $this->getSettings()->canVisitorRate(),
			'allowRateGuestInappr'   => $this->getSettings()->canVisitorRateInappr(),
			'search_query_var'       => self::SEARCH_QUERY_VAR_NAME,
			'default_search_message' => $this->getThemeType($this->getSettings()->getColorScheme()) == self::THEME_SMOOTH ? $this->__('Search Q/A') : $this->__('Search phrase'),
		);
		return Zend_Json::encode($config);
	}

	public function getCurrentSortModeLabel() {
		foreach ($this->getModeValues() as $mode) {
			if ($mode['value'] == $this->questionSortMode) {
				return $mode['label'];
			}
		}
		return '';
	}

	public function setSearchQuery($query) {
		$this->searchQuery = (string)$query;
		return $this;
	}

	public function getSearchQuery() {
		return $this->searchQuery;
	}
}
?>