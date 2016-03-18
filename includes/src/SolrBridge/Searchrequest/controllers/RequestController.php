<?php
class SolrBridge_Searchrequest_RequestController extends Mage_Core_Controller_Front_Action {
	public function saveAction(){
		$data = $this->getRequest()->getParams();
		$dataRequest = Mage::helper('searchrequest')->RequestData();
		if( isset( $data['selection'] ) )
			$selection = $dataRequest[ (int)$data[ 'selection']];
		unset($data['selection']);
		$data['category'] = $selection;
		$requestModel = Mage::getModel('searchrequest/request');
		$requestModel->setMobileNumber($data['mobile_number']);
		$requestModel->setEmailId($data['email_id']);
		$requestModel->setCategory($data['category']);
		$requestModel->setMessage($data['message']);
		$requestModel->save();
		echo json_encode("Done!");
		/* $this->loadLayout();
		$this->renderLayout(); */
		
	}
}