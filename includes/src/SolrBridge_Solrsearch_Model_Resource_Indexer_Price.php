<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Model_Resource_Indexer_Price extends SolrBridge_Solrsearch_Model_Resource_Indexer
{
	public function execute()
	{
		//Prepare collection metadata
		$this->prepareCollectionMetaData($this->solrcore);

		$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
		$this->totalMagentoProducts = (int) $this->collectionMetaData['totalProductCount'];

		$this->loadedStores = $this->collectionMetaData['loadedStores'];
		$this->loadedStoresName = $this->collectionMetaData['loadedStoresName'];
		if (!$this->totalFetchedProducts) {
			$this->messages[] = Mage::helper('solrsearch')->__('Start indexing process for core (%s)', $this->solrcore);
		}

		$this->messages[] = Mage::helper('solrsearch')->__('Magento product count : %s', $this->totalMagentoProducts);

		$this->messages[] = Mage::helper('solrsearch')->__('Existing solr documents : %s', $this->totalSolrDocuments);

		if ($this->totalFetchedProducts >= $this->totalMagentoProducts)
		{
			$this->messages[] = Mage::helper('solrsearch')->__('There is no new products to update');
			$this->response['status'] = 'FINISH';
			$this->response['message'] =$this->messages;
			$this->percent = 100;
			return $this;
		}

		if ( $this->action == 'REINDEXPRICE' ) { // There is no any solr document exists

			$this->reindexSolrData(true);
			return $this;
		}
	}
}