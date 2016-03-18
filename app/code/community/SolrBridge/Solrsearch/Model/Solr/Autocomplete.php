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
class SolrBridge_Solrsearch_Model_Solr_Autocomplete extends SolrBridge_Solrsearch_Model_Solr_Query
{

	public function init($options = array())
	{
		parent::init($options);

		return $this;
	}

	public function buildQueryUrl($hasCore=true)
	{
		$queryUrl = Mage::helper('solrsearch')->getSetting('solr_server_url');

		$q = $this->getQueryText();

		if (!empty($this->synonym))
		{
			$q = $this->synonym;
		}

		if ($hasCore){
			$queryUrl = trim($queryUrl,'/').'/'.$this->solrcore.'/select/?q='.urlencode(strtolower(trim($q)));
		}else{
			$queryUrl = trim($queryUrl,'/').'/select/?q='.urlencode(strtolower(trim($q)));
		}

		$queryUrl .= '&spellcheck.q='.urlencode(Mage::helper('solrsearch')->getPreparedBoostText($q));

		$facetFieldsString = $this->convertFacetFieldsToString();
		$boostFieldsString = $this->convertBoostFieldsToString();
		$filterQueryString = $this->filterQuery;

		$this->facetLimit = 5;
		$this->rows = -1;

		//Facet fields
		$queryUrl .= '&facet.field=textSearchStandard&facet.prefix='.urlencode(strtolower(trim($q)));
		//Range fields
		if (!$this->isAutocomplete) {
			$rangeFieldsString = $this->convertRangeFieldsToString();
			$queryUrl .= '&'.$rangeFieldsString;
		}
		//filter query
		if (!empty($filterQueryString)) {
			$queryUrl .= '&fq='.$filterQueryString;
		}
		//boost query
		if (!empty($boostFieldsString)) {
			$queryUrl .= '&bq='.urlencode($boostFieldsString);
		}
		return $queryUrl;
	}
}