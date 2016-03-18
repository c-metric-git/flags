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

class Itoris_ProductQa_Model_Notify {

	const TYPE_ANSWER = 1;
	const TYPE_QUESTION = 2;
	const ADMIN = 'Admin';
	const CUSTOMER = 'User';
	const GUEST = 'Guest';

	protected $questionOrAnswer = 'question_or_answer';
	protected $storeViewName = 'store_view_name';
	protected $userType = 'user_type';
	protected $nickname = 'nickname';
	protected $productName = 'product_name';
	protected $questionOrAnswerDetails = 'question_or_answer_details';
	protected $customerFirstName = 'customer_first_name';
	protected $question = 'question';
	protected $answer = 'answer';
	protected $questionDetailsBackendUrl = 'question_details_backend_url';
	protected $reply = 'reply';
	protected $username = 'username';
	protected $productPage = 'product_page';

	public function sendNotification($data, $whom, $settings = null) {
		/** @var $emailTemplate Mage_Core_Model_Email_Template */
		$emailTemplate = Mage::getModel('core/email_template');

		$settings = is_null($settings) ? Mage::registry('settings') : $settings;

		$templateText = $settings->__call('getTemplate'. $whom .'Notification', null);

		switch ($data['type']) {
			case Itoris_ProductQa_Model_Notify::TYPE_QUESTION:
				$class = "Itoris_ProductQa_Model_Questions";
				$type = Mage::helper('itoris_productqa')->__('question');
				break;
			case Itoris_ProductQa_Model_Notify::TYPE_ANSWER:
				$class = "Itoris_ProductQa_Model_Answers";
				$type = Mage::helper('itoris_productqa')->__('answer');
				break;
		}

		$emailTemplateVariables = array(
			$this->questionOrAnswer          => $type,
			$this->storeViewName             => $data['store_name'],
			$this->userType                  => Mage::helper('itoris_productqa')->getUserType($class, $data['user_type']),
			$this->nickname                  => $data['nickname'],
			$this->productName               => $data['product_name'],
			$this->questionOrAnswerDetails   => $data['qa_details'],
			$this->questionDetailsBackendUrl => Mage::helper('itoris_productqa')->getHtmlLink($data['q_url']),
			$this->answer                    => $data['qa_details'],
			$this->question                  => (isset($data['question_details'])) ? $data['question_details'] : '',
			$this->customerFirstName         => (isset($data['customer_name'])) ? $data['customer_name'] : '',
			$this->reply                     => $data['qa_details'],
			$this->username                  => isset($data['username']) ? $data['username'] : '',
			$this->productPage               => isset($data['product_page']) ? $data['product_page'] : '',
		);

		$templateText = $this->prepareTemplate($templateText, $emailTemplateVariables);

		$emailTemplate->setTemplateText($templateText);

		$emailTemplate->setSenderName($this->prepareTemplate($settings->__call('getTemplate'. $whom .'Name', null), $emailTemplateVariables));
    	$emailTemplate->setSenderEmail($settings->__call('getTemplate'. $whom .'Email', null));
        $emailTemplate->setTemplateSubject($this->prepareTemplate($settings->__call('getTemplate'. $whom .'Subject', null), $emailTemplateVariables));

		$emailTo = (isset($data['customer_email'])) ? $data['customer_email'] : $settings->getAdminEmail();

		return $emailTemplate->send($emailTo);
	}

	public function prepareAndSendNotification($questionId, $answerText, $submitterType, $nickname) {
		$question = Mage::getModel('itoris_productqa/questions')->load($questionId);
		$product = Mage::getModel('catalog/product')->load($question->getProductId());
		$productName = $product->getName();
		$data = array(
			'type'             => Itoris_ProductQa_Model_Notify::TYPE_ANSWER,
			'user_type'        => $submitterType,
			'nickname'         => $nickname,
			'product_name'     => $productName,
			'qa_details'       => $answerText,
			'q_url'            => Mage::getUrl('itoris_productqa/admin_questions/edit/id/'. $questionId),
			'question_details' => $question->getContent(),
			'username'         => $question->getNickname(),
			'product_page'     => $product->getProductUrl(),
			'customer_email'   => $question->getEmail(),
		);
		if ($question->getCustomerId()) {
			$customer = Mage::getModel('customer/customer')->load($question->getCustomerId());
			$customerName = $customer->getFirstname();
			$customerEmail = $customer->getEmail();
			$data['customer_name'] = $customerName;
			$data['customer_email'] = $customerEmail;
		}

		$storeIds = Mage::getModel('itoris_productqa/questions')->getQuestionVisibility($questionId);

		foreach ($storeIds as $value) {
			$store = Mage::getModel('core/store')->load($value['store_id']);
			$data['store_name'] = $store->getName();
			$websiteId = $store->getWebsiteId();
			$settings = Mage::getModel('itoris_productqa/settings')->load($value['store_id'], $websiteId);
			Mage::register('settings',$settings);
			$question->sendNotifications($data);
			Mage::unregister('settings');
			break;
		}
	}

	/**
	 * Insert variables into a template
	 *
	 * @param $template
	 * @param $variables
	 * @return string
	 */
	protected function prepareTemplate($template, $variables) {
		foreach ($variables as $key => $value) {
			$template = str_replace('{{' . $key . '}}', $value, $template);
		}
		return $template;
	}
}
?>