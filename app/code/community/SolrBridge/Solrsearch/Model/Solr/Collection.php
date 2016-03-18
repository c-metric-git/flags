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
//class SolrBridge_Solrsearch_Model_Solr_Collection extends Mage_Catalog_Model_Resource_Product_Collection
class SolrBridge_Solrsearch_Model_Solr_Collection extends Varien_Data_Collection
{
	public $solrData = array();

	public function setSolrData($solrdata)
	{
		$this->solrData = $solrdata;
	}

	public function getSolrData()
	{
		return $this->solrData;
	}

	public function getSize()
	{
		$size = 0;
		if ($this->getSolrData()) {

			$collection = $this->getSolrData();

			if (isset($collection['response']['numFound'])) {
				$collectionSize = (int) $collection['response']['numFound'];
				if ($collectionSize > 0) {
				    return $collectionSize;
				}
			}
		}
		return $size;
	}

	public function getRows()
	{
		$rows = 0;
		if ($this->getSolrData()) {

			$collection = $this->getSolrData();

			if (isset($collection['responseHeader']['params']['rows'])) {
				$rows = (int) $collection['responseHeader']['params']['rows'];
				if ($rows > 0) {
					return $rows;
				}
			}
		}
		return $rows;
	}

	public function getDocs()
	{
		$docs = array();
		if ($this->getSolrData()) {

			$collection = $this->getSolrData();

			if (isset($collection['response']['docs'])) {
				$docs = $collection['response']['docs'];
				if (is_array($docs)) {
					return $docs;
				}
			}
		}
		return $docs;
	}
}