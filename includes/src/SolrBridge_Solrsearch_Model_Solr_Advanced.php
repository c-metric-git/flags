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
class SolrBridge_Solrsearch_Model_Solr_Advanced extends SolrBridge_Solrsearch_Model_Solr_Query
{
    public $queryString = '';

	public function init($options = array())
	{
		parent::init($options);

		return $this;
	}

	public function buildQueryUrl($hasCore=true)
	{
		$queryUrl = Mage::helper('solrsearch')->getSetting('solr_server_url');

		if ($hasCore){
			$queryUrl = trim($queryUrl,'/').'/'.$this->solrcore.'/select/?q='.$this->queryString;
		}else{
			$queryUrl = trim($queryUrl,'/').'/select/?q='.$this->queryString;
		}

		//die($queryUrl);

		//$queryUrl .= '&spellcheck.q='.urlencode(Mage::helper('solrsearch')->getPreparedBoostText($q));

		//$facetFieldsString = $this->convertFacetFieldsToString();
		//$boostFieldsString = $this->convertBoostFieldsToString();
		$filterQueryString = $this->filterQuery;

		//$this->facetLimit = 5;
		//$this->rows = -1;

		//Facet fields
		//$queryUrl .= '&facet.field=textSearchStandard&facet.prefix='.urlencode(strtolower(trim($q)));
		//Range fields
		//filter query
		if (!empty($filterQueryString)) {
			$queryUrl .= '&fq='.$filterQueryString;
		}
		return $queryUrl;
	}

	/**
	 * Send search request to Solr server and get response
	 * @return array
	 */
	public function execute()
	{
	    $queryUrl = $this->buildQueryUrl();
	    $store = Mage::app()->getStore();
	    $arguments = array(
	        'json.nl' => 'map',
	        'rows' => $this->rows,
	        'start' => $this->start,
	        'fl' => @implode(',', $this->fieldList),
	        //'qf' => $this->queryFields,
	        'spellcheck' => 'true',
	        'spellcheck.collate' => 'true',
	        'facet' => 'true',
	        'facet.mincount' => 1,
	        'facet.limit' => $this->facetLimit,
	        'timestamp' => time(),
	        //'mm' => $this->mm,
	        //'defType'=> 'edismax',
	        'wt'=> 'json',
	    );

	    $resultSet = Mage::getResourceSingleton('solrsearch/solr')->doRequest($queryUrl, $arguments, 'array');

	    return $resultSet;
	}
}