<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade SolrBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2015 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {
	protected $_indexer = null;
	
	protected $_limit = 100;
	
	public function _construct() {
		$this->_indexer = Mage::getResourceModel('solrsearch/indexer');
		$this->_limit = 50;//Mage::helper('solrbridge')->getSetting('items_per_commit');
	}
	
	protected function _initResponse() {
		$indexId = $this->getRequest()->getParam('id');
		$params = $this->getRequest()->getParams();
		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );
		 
		if (!isset($params['index_id']))
		{
			$index = Mage::getModel('solrsearch/index')->load($indexId);
			$response = $index->getData();
			$this->prepareResponse($response);
		}else{
			$response = $params;
		}
		return $response;
	}
	
	public function prepareResponse(&$response) {
		$params = array('percent' => 0, 'documents' => 0, 'page' => 0);
		$response = array_merge($params, $response);
	}
	
	public function indexAction() {
    	$this->loadLayout();
    	$this->renderLayout();
    }
    
    public function saveAction() {
        //loading existing index
    	$indexData = $this->getRequest()->getPost('index');
    	
    	$index = Mage::getModel('solrsearch/index')->load($indexData['store_id'], 'store_id');
    	if ($index && $index->getStoreId() == $indexData['store_id']) {
    	    $store = Mage::app()->getStore($index->getStoreId());
    	    $message = 'The store <b>'.$store->getName().'</b> has been already mapped. Please delete the existing store mapping.';
    	    Mage::getSingleton('adminhtml/session')->addError($message);
    	    $this->getResponse()->setRedirect($this->getUrl('solrbridge_admin/adminhtml_index/index'));
    	    return $this;
    	}
    	
    	$index = Mage::getModel('solrsearch/index');
    	$index->setData($indexData);
    	try{
    		$index->save();
    	} catch (Exception $e) {
    		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    	}
    	$this->getResponse()->setRedirect($this->getUrl('solrbridge_admin/adminhtml_index/index'));
    }
    /**
     * Reindex data
     */
    public function reindexAction() {
    	$response = $this->_initResponse();
    	
    	$this->prepareReindexResponse( $response );
    	
    	//echo json_encode($response);
    	//exit();
    	return $this->sendAjaxResponse( $response );
    }
    
    protected function sendAjaxResponse( $response ) {
    	$this->getResponse()->setHeader('Content-type', 'application/json');
    	$this->getResponse()->setBody(json_encode($response));
    	return $this;
    }
    
    public function prepareReindexResponse(&$response) {
    	$params = array('limit' => $this->_limit, 'store_id' => $response['store_id'], 'page' => $response['page']);
    	$params = array_merge($response, $params);
    	
    	if (!isset($params['process_status']) || $params['process_status'] == 'START') {
    	    $params['action'] = 'TRUNCATE';
    	    $params['solrcore'] = $params['solr_core'];
    	    $params['currentstoreid'] = $params['store_id'];
    	    
    		$result = $this->_indexer
    		->start($params)
    		->deleteByStore();
    		$response['process_status'] = 'PROCESSING';
    	}

    	$params['action'] = 'UPDATEINDEX';
    	$result = $this->_indexer
    		->start($params)
    		->execute()
    		->end();
    	 
    	$response = array_merge($response, $result);
    }
    
    public function updateAction() {
    	$response = $this->_initResponse();
    	
    	$this->prepareUpdateResponse($response);
    	
    	//echo json_encode($response);
    	//exit();
    	return $this->sendAjaxResponse( $response );
    }
    
    public function prepareUpdateResponse(&$response) {
    	$params = array('limit' => $this->_limit, 'store_id' => $response['store_id'], 'page' => $response['page']);
    	$params = array_merge($response, $params);

    	$params['solrcore'] = $params['solr_core'];
    	$params['currentstoreid'] = $params['store_id'];
    	
    	$params['action'] = 'UPDATEINDEX';
    	
    	$result = $this->_indexer
    		->start($params)
    		->execute()
    		->end();
    	 
    	$response = array_merge($response, $result);
    }
    
    public function deleteAction() {
    	$response = $this->_initResponse();
    	 
    	$this->prepareDeleteResponse($response);
    	 
    	//echo json_encode($response);
    	//exit();
    	return $this->sendAjaxResponse( $response );
    }
    
    public function prepareDeleteResponse(&$response) {
    	$params = array('limit' => $this->_limit, 'store_id' => $response['store_id'], 'page' => $response['page']);
    	$params = array_merge($response, $params);
    	
    	$params['solrcore'] = $params['solr_core'];
    	$params['currentstoreid'] = $params['store_id'];
    	
    	$result = $this->_indexer
    	->start($params)
    	->deleteByStore()
    	->end();
    	
    	//@TODO: what if solr server is not connected? can not delete documents
    	
    	if (isset($result['documents']) && $result['documents'] < 1 && isset($response['index_id'])) {
    		//remove index
    		$index = Mage::getModel('solrsearch/index')->load($response['index_id']);
    		$index->delete();
    	}
    	 
    	$response = array_merge($response, $result);
    }
}