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

class Itoris_ProductQa_Block_Customer_ProductQa extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareLayout() {
		if (!Mage::helper('itoris_productqa')->isRegisteredAutonomous(Mage::app()->getWebsite())) {
			return;
		}
		$this->setTemplate('itoris/productqa/customer/productqa.phtml');

		$customerId = Mage::getSingleton('customer/session')->getId();

		$questions = Mage::getModel('itoris_productqa/questions')->getCollection()
						->getCustomerQuestions($customerId);

		$this->setQuestions($questions);

		$answers = Mage::getModel('itoris_productqa/answers')->getCollection()
						->getCustomerAnswers($customerId);

		$this->setAnswers($answers);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle($this->__('My Questions/Answers'));

		parent::_prepareLayout();

		$questionsPager = $this->getLayout()->createBlock('page/html_pager', 'questions.pager')
            ->setCollection($this->getQuestions());
        $this->setChild('questions_pager', $questionsPager);
        $this->getQuestions()->load();

		$answersPager = $this->getLayout()->createBlock('page/html_pager', 'answers.pager')
				->setCollection($this->getAnswers());
		$this->setChild('answers_pager', $answersPager);
		$this->getAnswers()->load();

        return $this;
    }

	/**
	 * Get question status html
	 *
	 * @param $item
	 * @return string
	 */
	protected function getHtmlStatusQ($item) {
		switch($item->getStatus()){
			case Itoris_ProductQa_Model_Questions::STATUS_PENDING:
				return $this->__('Pending');
				break;
			case Itoris_ProductQa_Model_Questions::STATUS_APPROVED:
				$html = '<span style="color: green">('. $item->getAnswers() .' '. $this->__('answers') .')</span><br/>
						<a href="'. $this->getProductUrlInStore() .'">
						'. $this->__('View Details') .'</a>';
				return $html;
				break;
		}
	}

	/**
	 * Get answer status html
	 *
	 * @param $item
	 * @return string
	 */
	protected function getHtmlStatusA($item) {
		switch($item->getStatus()){
			case Itoris_ProductQa_Model_Answers::STATUS_PENDING:
				return $this->__('Pending');
				break;
			case Itoris_ProductQa_Model_Answers::STATUS_APPROVED:
				$html = '<a href="'. $this->getProductUrlInStore() .'">
						'. $this->__('View Details') .'</a>';
				return $html;
				break;
		}
	}

	/**
	 * @param $id
	 * @param $storeId
	 */
	protected function prepareProductUrl($id, $storeId) {
		$urls = Mage::helper('itoris_productqa')->getProductUrl($id, $storeId);
		$this->setProductUrl($urls['url']);
		$this->setProductUrlInStore($urls['url_in_store']);
	}
}
?>