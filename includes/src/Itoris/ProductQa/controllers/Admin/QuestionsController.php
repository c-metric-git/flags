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

class Itoris_ProductQa_Admin_QuestionsController extends Itoris_ProductQa_Controller_Admin_Controller {

	private function _init() {
		$this->_setActiveMenu('catalog');
	}

	/**
	 * Questions grid
	 */
	public function indexAction() {
		Mage::register('questionsPage', (int)$this->getRequest()->getParam('questionsPage'));
		try {
			$collection = Mage::getModel('itoris_productqa/questions')->getCollection();
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		Mage::register('questions', $collection);
		$this->loadLayout();
		$questions = $this->getLayout()->createBlock( 'itoris_productqa/admin_questions' );
		$this->getLayout()->getBlock( 'content' )->append( $questions );
		$this->renderLayout();
	}

	/**
	 * Pending questions grid
	 */
	public function pendingAction(){
		$this->_redirect('itoris_productqa/admin_questions', array('questionsPage' => Itoris_ProductQa_Block_Admin_Questions::PAGE_PENDING));
	}

	/**
	 * Inappropriate questions grid
	 */
	public function inapprAction(){
		$this->_redirect('itoris_productqa/admin_questions', array('questionsPage' => Itoris_ProductQa_Block_Admin_Questions::PAGE_INAPPR));
	}

	/**
	 * Grid with questions without answers
	 */
	public function notAnsweredAction(){
		$this->_redirect('itoris_productqa/admin_questions', array('questionsPage' => Itoris_ProductQa_Block_Admin_Questions::PAGE_NOT_ANSWERED));
	}

	/**
	 * Edit a question page
	 */
	public function editAction(){
		$questionId = (int)$this->getRequest()->getParam('id');
		$model = Mage::getModel('itoris_productqa/questions');
		$question = $model->getQuestionInfo($questionId);

		switch ($question['submitter_type']) {
			case Itoris_ProductQa_Model_Questions::SUBMITTER_CUSTOMER:
				$customer = Mage::getModel('customer/customer')->load($question['customer_id']);
				$question['user_name'] = $customer->getName();
				$question['user_email'] = $customer->getEmail();
				$question['user_type'] = Mage::helper('itoris_productqa')->__('Customer');
				break;
			case Itoris_ProductQa_Model_Questions::SUBMITTER_ADMIN:
				$question['user_type'] = Mage::helper('itoris_productqa')->__('Administrator');
				break;
			case Itoris_ProductQa_Model_Questions::SUBMITTER_VISITOR:
				$question['user_type'] = Mage::helper('itoris_productqa')->__('Guest');
				break;
		}

		Mage::register('question', $question);

		$answerCollection = Mage::getModel('itoris_productqa/answers')->getCollection()->questionAnswers($questionId);

		Mage::register('answerCollection', $answerCollection);

		$this->loadLayout();
		$editBlock = $this->getLayout()->createBlock( 'itoris_productqa/admin_edit' );
		$this->getLayout()->getBlock( 'content' )->append( $editBlock );
		$this->renderLayout();
	}

	/**
	 * Create a new question action
	 */
	public function newAction() {
		$this->loadLayout();
		$newBlock = $this->getLayout()->createBlock( 'itoris_productqa/admin_new' );
		$this->getLayout()->getBlock( 'content' )->append( $newBlock );
		$this->renderLayout();
	}

	/**
	 * Mass delete questions action
	 */
	public function massDeleteAction(){
		$questionsIds = $this->getRequest()->getParam('question');
		if ($this->getRequest()->getParam('id')) {
			$questionsIds[] = $this->getRequest()->getParam('id');
		}
		
		if (empty($questionsIds)) {
			$this->_getSession()->addError($this->__('Please select question(s).'));
		} else {
			try {
				$model = Mage::getSingleton('itoris_productqa/questions');
				foreach ($questionsIds as $id) {
					$model->load($id)->delete();
				}
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('itoris_productqa/admin_questions');
	}

	/**
	 * Mass change question status action
	 */
	public function massStatusAction(){
		$questionsIds = $this->getRequest()->getParam('question');
		if (empty($questionsIds)) {
			$this->_getSession()->addError($this->__('Please select question(s).'));
		} else {
			$model = Mage::getSingleton('itoris_productqa/questions');
			try {
				foreach ($questionsIds as $id) {
					$model->load($id)->setStatus((int)$this->getRequest()->getParam('status'))
							->save();
				}
			} catch(Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('itoris_productqa/admin_questions');
	}

	/**
	 * Add question action
	 */
	public function addAction() {
		$productsIds = $this->getRequest()->getParam('product');
		$data = array(
			'status'         => (int)$this->getRequest()->getParam('status'),
		    'submitter_type' => Itoris_ProductQa_Model_Questions::SUBMITTER_ADMIN,
		    'nickname'       => $this->getRequest()->getParam('nickname'),
		    'content'        => $this->getRequest()->getParam('question'),
		    'customer_id'    => Mage::getSingleton('admin/session')->getUser()->getId(),
		    'notify'         => 0,
		    'store_id'       => $this->getRequest()->getParam('visible'),
		);
		try {
			$model = Mage::getSingleton('itoris_productqa/questions');
			for ($i = 0; $i < count($productsIds); $i++) {
				$data['product_id'] = (int)$productsIds[$i];
				$model->addQuestion($data);
			}
		} catch(Exception $e) {
			$this->_getSession()->addError($this->__('Question has not been added!'));
		}
		$this->_getSession()->addSuccess('Question has been added');

		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/edit', array('id' => Mage::registry('q_id')));
		} else {
			$this->_redirect('*/*');
		}
	}

	/**
	 * Save question action
	 */
	public function saveAction() {
		$data = array(
			'q_id'     => (int)$this->getRequest()->getParam('id'),
			'status'   => (int)$this->getRequest()->getParam('status'),
		    'nickname' => $this->getRequest()->getParam('nickname'),
		    'content'  => $this->getRequest()->getParam('question'),
			'inappr'   => ($this->getRequest()->getParam('inappr')) ? 1 : 0,
		);
		$answers = $this->getRequest()->getParam('answer');
		$questionModel = Mage::getSingleton('itoris_productqa/questions');

		try {
			$questionModel->load($data['q_id'])->setStatus($data['status'])
									->setNickname($data['nickname'])
									->setContent($data['content'])
									->setData('inappr', $data['inappr'])
									->save();
			$questionModel->updateVisibility($data['q_id'], $this->getRequest()->getParam('visible'));

			if (!empty($answers)) {
				$answerModel = Mage::getSingleton('itoris_productqa/answers');
				foreach ($answers as $key => $value) {
					if (isset($value['delete'])) {
						$answerModel->load($key)->delete();
					} else {
						$inappr = (isset($value['inappr'])) ? (int)$value['inappr'] : 0;
						$answerModel->load($key)->setStatus((int)$value['status'])
											->setNickname($value['nickname'])
											->setContent($value['answer'])
											->setData('inappr', $inappr)
											->save();
						if ($value['status'] == Itoris_ProductQa_Model_Answers::STATUS_APPROVED && $value['status'] != $value['status_before']) {
							$submitter = $answerModel->load($key)->getSubmitterType();
							Mage::getModel('itoris_productqa/notify')->prepareAndSendNotification($data['q_id'], $value['answer'], $submitter, $value['nickname']);
						}
					}
				}
			}
			$this->_getSession()->addSuccess($this->__('Question has been saved'));
		} catch(Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/edit', array('id' => $questionModel->getId(), '_current'=>true));
		} else {
			$this->_redirect('*/*');
		}
	}

	protected function _isAllowed() {
		switch ($this->getRequest()->getActionName()) {
			case 'pending':
				$type = 'pending';
				break;
			case 'inappr':
				$type = 'inappropriate';
				break;
			case 'notAnswered':
				$type = 'not_answered';
				break;
			default:
				$type = 'all';
		}

		return Mage::getSingleton('admin/session')->isAllowed('admin/catalog/itoris_productqa/questions/' . $type);
	}
}

?>