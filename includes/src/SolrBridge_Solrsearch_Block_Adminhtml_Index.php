<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Search
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Block_Adminhtml_Index extends Mage_Core_Block_Template {
	protected $_indexer = null;
	protected $ultility = null;
	
	public function _construct() {
		$this->_indexer = Mage::getResourceModel('solrsearch/indexer');
		$this->ultility = Mage::getModel('solrsearch/ultility');
	}
	public function getIndeCollection() {
		$collection = Mage::getModel('solrsearch/index')->getCollection();
		return $collection;
	}
	
	public function getStores() {
		$options = array();
		$websites = Mage::app()->getWebsites();
		foreach (Mage::app()->getWebsites() as $website) {
			$option = array();
			$website_id = $website->getId();
			$label = $website->getName();
			$items = array();
			foreach ($website->getGroups() as $group) {
				$stores = $group->getStores();
		
				$label = $label .' &raquo; '.$group->getName();
		
				foreach ($stores as $store) {
					$items[$store->getId()] = $store->getName();
				}
			}
			$options[] = array('label' => $label, 'items' => $items);
		}
		return $options;
	}
	
	public function getSolrCores() {
		return Mage::getModel('solrsearch/adminhtml_system_source_config_cores')->toOptionArray();
	}
	
	public function getPostUrl() {
		return Mage::helper("adminhtml")->getUrl('solrbridge_admin/adminhtml_index/save');
	}
	
	public function getDeleteUrl($index) {
		return Mage::helper("adminhtml")->getUrl('solrbridge_admin/adminhtml_index/delete', array('_query' => array('id' => $index->getIndexId())));
	}
	public function getReindexUrl($index) {
		return Mage::helper("adminhtml")->getUrl('solrbridge_admin/adminhtml_index/reindex', array('_query' => array('id' => $index->getIndexId())));
	}
	
	public function getUpdateUrl($index) {
		return Mage::helper("adminhtml")->getUrl('solrbridge_admin/adminhtml_index/update', array('_query' => array('id' => $index->getIndexId())));
	}
	/**
	 * Return total magento product by storeid
	 * @param unknown $index
	 */
	public function getTotal($index) {
	    
	    return $this->ultility->getProductCollectionByStoreId($index->getStoreId())->getSize();

	}
	
	public function getDocuments($index) {
	    
	    return Mage::getResourceSingleton('solrsearch/solr')->getDocumentCount($index->getSolrCore(), $index->getStoreId());

	}
	
	public function getStoreName($index) {
	    return Mage::app()->getStore($index->getStoreId())->getName();
	}
}