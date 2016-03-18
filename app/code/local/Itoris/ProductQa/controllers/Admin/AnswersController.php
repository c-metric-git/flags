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

class Itoris_ProductQa_Admin_AnswersController extends Itoris_ProductQa_Controller_Admin_Controller {

	private function _init(){
		$this->_setActiveMenu('catalog');
	}

	/**
	 * Answers grid
	 */
	public function indexAction() {
		Mage::register('answersPage', (int)$this->getRequest()->getParam('answersPage'));
		try {
			$collection = Mage::getModel('itoris_productqa/answers')->getCollection();
		} catch(Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		Mage::register('answers', $collection);
		$this->loadLayout();
		$answersBlock = $this->getLayout()->createBlock( 'itoris_productqa/admin_answers' );
		$this->getLayout()->getBlock( 'content' )->append( $answersBlock );
		$this->renderLayout();
	}

	/**
	 * Add answer action
	 */
	public function addAction() {
		$qId = (int)$this->getRequest()->getPost('q_id');
		$answer = $this->getRequest()->getPost('content');
		$status = (int)$this->getRequest()->getPost('status');
		$data = array(
			'status'         => $status,
			'submitter_type' => (int)Itoris_ProductQa_Model_Answers::SUBMITTER_ADMIN,
			'nickname'       => $this->getRequest()->getPost('nickname'),
			'content'        => $answer,
			'customer_id'    => Mage::getSingleton('admin/session')->getUser()->getUserId(),
			'q_id'           => $qId,
		);

		try {
			Mage::getModel('itoris_productqa/answers')->addAnswer($data);
			$this->_getSession()->addSuccess($this->__('Answer has been saved'));
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Answer has not been saved'));
		}
		if ($status == Itoris_ProductQa_Model_Answers::STATUS_APPROVED) {
			try {
				Mage::getModel('itoris_productqa/notify')->prepareAndSendNotification($qId, $answer, Itoris_ProductQa_Model_Answers::SUBMITTER_ADMIN, $data['nickname']);
			} catch(Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
	}

	/**
	 * Pending answers grid
	 */
	public function pendingAction() {
		$this->_redirect('itoris_productqa/admin_answers', array('answersPage' => Itoris_ProductQa_Block_Admin_Answers::PAGE_PENDING));
	}

	/**
	 * Inappropriate answers grid
	 */
	public function inapprAction() {
		$this->_redirect('itoris_productqa/admin_answers', array('answersPage' => Itoris_ProductQa_Block_Admin_Answers::PAGE_INAPPR));
	}

	/**
	 * Mass delete answers action
	 */
	public function massDeleteAction() {
		$answersIds = $this->getRequest()->getParam('answers');
		if (empty($answersIds)) {
			$this->_getSession()->addError($this->__('Please select answer(s).'));
		} else {
			try {
				$model = Mage::getSingleton('itoris_productqa/answers');
				foreach ($answersIds as $id) {
					$model->load($id)->delete();
				}
			} catch(Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('itoris_productqa/admin_answers');
	}

	/**
	 * Mass change answers status action
	 */
	public function massStatusAction(){
		$answersIds = $this->getRequest()->getParam('answers');
		if (empty($answersIds)) {
			$this->_getSession()->addError($this->__('Please select answer(s).'));
		} else {
			$model = Mage::getSingleton('itoris_productqa/answers');
			$status = (int)$this->getRequest()->getParam('status');
			try {
				foreach ($answersIds as $id) {
					$model->load($id)->setStatus($status)
							->save();
					if ($status == Itoris_ProductQa_Model_Answers::STATUS_APPROVED) {
						$answerModel = $model->load($id);
						$qId = $answerModel->getQId();
						$answer = $answerModel->getContent();
						$answerSubmitter = $answerModel->getSubmitterType();
						$nickname = $answerModel->getNickname();
						try {
							Mage::getModel('itoris_productqa/notify')->prepareAndSendNotification($qId, $answer, $answerSubmitter, $nickname);
						} catch(Exception $e) {
							$this->_getSession()->addError($e->getMessage());
						}
					}
				}
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('itoris_productqa/admin_answers');
	}

	protected function _isAllowed() {
		switch ($this->getRequest()->getActionName()) {
			case 'pending':
				$type = 'pending';
				break;
			case 'inappr':
				$type = 'inappropriate';
				break;
			default:
				$type = 'all';
		}

		return Mage::getSingleton('admin/session')->isAllowed('admin/catalog/itoris_productqa/answers/' . $type);
	}
}

?>