<?php
class SolrBridge_Searchrequest_Adminhtml_RequestController extends Mage_Adminhtml_Controller_Action {
	public function indexAction() {
		$this->loadLayout ()
			->_setActiveMenu('solrbridge/searchrequest');
		$this->renderLayout ();
	}
	public function ajaxdeleteAction()
	{
		$id = $this->getRequest()->getParam('id');
		try{
		$requestModel = Mage::getModel('searchrequest/request')->load($id);
		$requestModel->delete();
		echo json_encode(array('status'=>'SUCCESS'), true);
		exit();
	}catch(Exception $e){
		echo json_encode(array('status'=>$e.message()));
		exit();
		}
	}
	public function newAction(){
		$this->_forward('edit');
	}
	public function editAction()
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('searchrequest/request')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
		
			Mage::register('request_data', $model);
		
		
			$this->loadLayout()->_setActiveMenu ( 'solrbridge/searchrequest' )
			->_title ( $this->__ ( 'Search Request Manager' ) )
			->_addContent ( $this->getLayout ()->createBlock ( 'searchrequest/adminhtml_request_edit' ) )
			->renderLayout();
		
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('searchrequest')->__('Search Request Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function saveAction()
	{
		$edit = false;
		$data = $this->getRequest()->getParams();
		if(isset($data['id'])){ $edit = true;}
		$dataRequest = Mage::helper('searchrequest')->RequestData();
		if( isset( $data['selection'] ) )
			$selection = $dataRequest[ (int)$data[ 'selection']];
		unset($data['selection']);
		$data['category'] = $selection;
		$requestModel = Mage::getModel('searchrequest/request');
		if($edit)
		{
			$requestModel = Mage::getModel('searchrequest/request')->load($data['id']);
		}
		$requestModel->setMobileNumber($data['mobile_number']);
		$requestModel->setEmailId($data['email_id']);
		$requestModel->setCategory($data['category']);
		$requestModel->setMessage($data['message']);
		$requestModel->save();
		Mage::getSingleton('core/session')->addSuccess('This Search Request have saved been succesfully!');
		$this->_redirect('*/*');
	}
}