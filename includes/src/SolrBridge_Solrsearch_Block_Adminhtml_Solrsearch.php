<?php
/**
 * @category SolrBridge
 * @package SolrBridge_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Adminhtml_Solrsearch extends Mage_Core_Block_Template
{
	protected $ultility = null;

	public function __construct()
	{
		$this->ultility = Mage::getModel('solrsearch/ultility');
		$this->setTemplate('solrsearch/solrsearch.phtml');
	}

	/**
	 * Return active solr cores
	 * @return array
	 */
	public function getActiveSolrCores()
	{
		$availableCores = $this->ultility->getAvailableCores();

		$activeSolrCores = array();

		foreach ($availableCores as $solrcore => $infoArray)
		{
			$storeIds = $this->ultility->getMappedStoreIdsFromSolrCore($solrcore);

			if ( !empty($storeIds) )
			{
				$collectionMetaData = $this->ultility->getProductCollectionMetaData($solrcore);

				$storeLabels = array();
				$productCount = (int) $collectionMetaData['totalProductCount'];
				$websiteids = array();

				$loadedStores = $collectionMetaData['loadedStores'];

				foreach ($loadedStores as $storeid => $storeObject)
				{
					$storeLabels[] = $storeObject->getWebsite()->getName().'-'.$storeObject->getName().'(<b>'.$collectionMetaData['stores'][$storeid]['productCount'].'</b> products)';
					$websiteids[] = $storeObject->getWebsiteId();
				}

				$infoArray['productCount'] = $productCount;

				$infoArray['websiteids'] = $websiteids;

				$infoArray['labels'] = $storeLabels;

				$infoArray['solrluke'] = Mage::getResourceModel('solrsearch/solr')->getSolrLuke($solrcore);

				$activeSolrCores[$solrcore] = $infoArray;
			}
		}

		return $activeSolrCores;
	}
}