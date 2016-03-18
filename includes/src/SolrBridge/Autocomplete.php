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
class SolrBridge_Autocomplete extends SolrBridge_Solr
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Prepare solr query url
	 * @param boolean $hasCore
	 * @return string
	 */
	public function buildQueryUrl($hasCore=true)
	{
		$queryUrl = $this->getConfigValue('solr_server_url');

		$q = $this->getQueryText();

		if ($hasCore){
			$queryUrl = trim($queryUrl,'/').'/'.$this->solrcore.'/select/?q='.urlencode(strtolower(trim($q)));
		}else{
			$queryUrl = trim($queryUrl,'/').'/select/?q='.urlencode(strtolower(trim($q)));
		}

		$queryUrl .= '&spellcheck.q='.urlencode(self::getPreparedBoostText($q));

		$facetFieldsString = $this->convertFacetFieldsToString();
		$boostFieldsString = $this->convertBoostFieldsToString();


		$this->prepareFilterQuery();
		$filterQueryString = $this->filterQuery;

		//Facet fields
		$queryUrl .= '&facet.field=textSearchStandard';

		$queryArray = explode(' ', $q);
		$tempQueryArray = array();
		foreach ($queryArray as $word)
		{
			$queryUrl .= '&f.textSearchStandard.facet.prefix='.urlencode(strtolower(trim($word)));

			if (count($tempQueryArray) > 0) {
				$tempQueryArray[] = $word;
				$queryUrl .= '&f.textSearchStandard.facet.prefix='.urlencode(strtolower(trim(@implode('+',$tempQueryArray))));
			}else{
				$tempQueryArray[] = $word;
			}

		}

		$this->facetLimit = 5;
		$this->rows = -1;

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