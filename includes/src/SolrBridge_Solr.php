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
class SolrBridge_Solr extends SolrBridge_Base
{
	public $queryText = '';

	public $fieldList = array('name_varchar', 'products_id', 'url_path_varchar', 'product_type_static');

	protected $solrcore = null;

	protected $start = 0;

	protected $rows = 9;

	protected $facetLimit = 200;

	protected $queryFields = 'textSearchStandard^80 textSearch^40 textSearchText^10 textSearchGeneral^1';

	protected $mm = '0%';

	protected $facetFields = array();

	protected $boostFields = array();

	protected $rangeFields = array();

	protected $filterQuery = null;

	protected $sort = null;

	protected $priceFieldName = null;

	public function getFacetLimit()
	{
		//Facet limit
		$facetLimit = 3;
		$facetLimitConf = array($facetLimit);
		$categoryLimitConf = $this->getConfigValue('autocomplete_category_limit');
		if (is_numeric($categoryLimitConf)) {
			$facetLimitConf[] = $categoryLimitConf;
		}
		$brandLimitConf = $this->getConfigValue('autocomplete_brand_limit');
		if (is_numeric($brandLimitConf)) {
			$facetLimitConf[] = $brandLimitConf;
		}
		$facetLimit = max($facetLimitConf);

		return $facetLimit;
	}

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->solrcore = $this->getConfigValue('solr_index');

		if ($this->getQueryText()) {
			$this->queryText = $this->getQueryText();
		}
		$rows = $this->getConfigValue('autocomplete_product_limit');
		if ($rows) {
			$this->rows = $rows;
		}


		//Facet limit
		$facetLimit = $this->getFacetLimit();


		if (isset($facetLimit) && is_numeric($facetLimit)) {
			$this->facetLimit = $facetLimit;
		}

		$relevancy = (int) $this->getConfigValue('relevancy');
		if ($relevancy > 0) {
			$this->mm = '100%';
		}else{
			$this->mm = '0%';
		}

		$allow_part_of_word = (int) $this->getConfigValue('allow_part_of_word');
		if ($allow_part_of_word < 1) {
			$this->queryFields = 'textSearchStandard^80 textSearch^40 textSearchText^10 textSearchGeneral^1';
		}

		if (isset($this->config['boostfields']) && is_array($this->config['boostfields'])) {
		    $this->boostFields = $this->config['boostfields'];
		    foreach ($this->boostFields as $key=>$item){
		    	if (is_array($item)) {
		    	    foreach ($item as $index=>$subitem){
		    	    	if (isset($subitem['value']) && $subitem['value'] == 'PLACEHOLDER') {
		    	    		$this->boostFields[$key][$index]['value'] = $this->getQueryText();
		    	    	}
		    	    }
		    	}
		    }
		}

		//Prepare facet fields
		$use_category_as_facet = (int) $this->getConfigValue('use_category_as_facet');

		if ($use_category_as_facet > 0)
		{
			$display_category_as_hierachy = $this->getConfigValue('display_category_as_hierachy');

			if ($display_category_as_hierachy > 0)
			{
				$this->facetFields[] = 'category_path';
			}
			else
			{
				$this->facetFields[] = 'category_facet';
			}
		}

		$display_brand_suggestion = (int) $this->getConfigValue('display_brand_suggestion');
		if ($display_brand_suggestion > 0)
		{
			$brand_attribute_code = $this->getConfigValue('brand_attribute_code');
			if (isset($brand_attribute_code) && !empty($brand_attribute_code))
			{
				$this->facetFields[] = $this->getConfigValue('brand_attribute_code').'_facet';
			}
		}


		$pricefields = $this->getPriceFields();
		if (is_array($pricefields) && !empty($pricefields)) {
		    $this->fieldList = array_merge($this->fieldList, $pricefields);
		}
	}

	/**
	 * Prepare solr filter query paprams
	 */
	protected function prepareFilterQuery()
	{
		$filterQuery = array();

		$storeid = $this->getParam('storeid');

		$config = $this->getConfig();

		$websiteid = isset($config['stores'][$storeid]['website_id'])?$config['stores'][$storeid]['website_id']:0;

		$checkInstock = (int) $this->getConfigValue('check_instock');

		$filterQuery = array_merge($filterQuery, array(
				'store_id' => array($storeid),
				'website_id' => array($websiteid),
				'product_status' => array(1)
		));

		if ($checkInstock > 0) {
			$filterQuery['instock_int'] = array(1);
		}

		$filterQueryArray = array();

		foreach($filterQuery as $key=>$filterItem)
		{

			if(count($filterItem) > 0){
				$query = '';
				foreach($filterItem as $value){
						$query .= $key.':%22'.urlencode(trim(addslashes($value))).'%22+OR+';
				}

				$query = trim($query, '+OR+');

				$filterQueryArray[] = $query;
			}
		}

		$filterQueryString = '';

		if(count($filterQueryArray) > 0) {
			if(count($filterQueryArray) < 2) {
				$filterQueryString .= $filterQueryArray[0];
			}else{
				$filterQueryString .= '%28'.@implode('%29+AND+%28', $filterQueryArray).'%29';
			}
		}

		$this->filterQuery = $filterQueryString;
	}

	public function setFieldList($fieldList = array())
	{
		if (!empty($fieldList)) {
			$this->fieldList = $fieldList;
		}
	}

	/**
	 * Send search request to Solr server and get response
	 * @return array
	 */
	public function execute()
	{
		$queryUrl = $this->buildQueryUrl();
		$storeId = $this->getParam('storeid');
		$arguments = array(
				'json.nl' => 'map',
				'rows' => $this->rows,
				'start' => $this->start,
				'fl' => @implode(',', $this->fieldList),
				'qf' => $this->queryFields,
				'spellcheck' => 'true',
				'spellcheck.collate' => 'true',
				'facet' => 'true',
				'facet.mincount' => 1,
				'facet.limit' => $this->facetLimit,
				'timestamp' => time(),
				'mm' => $this->mm,
				'defType'=> 'edismax',
				'wt'=> 'json',
		);

		$resultSet = $this->doRequest($queryUrl, $arguments, 'array');

		if (isset($resultSet['response']['numFound']) && intval($resultSet['response']['numFound']) > 0)
		{
			return $resultSet;
		}
		else
		{
			$queryText = $this->getQueryText();
			if (isset($resultSet['spellcheck']['suggestions']['collation']))
			{
				$queryText = strtolower($resultSet['spellcheck']['suggestions']['collation']);
			}

			if (!empty($queryText))
			{
				$this->queryText = $queryText;
				$queryUrl = $this->buildQueryUrl();
				$arguments['mm'] = '0%';

				$resultSet = $this->doRequest($queryUrl, $arguments, 'array');
			}
		}
		return $resultSet;
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
		if (!empty($facetFieldsString)) {
			$queryUrl .= '&'.$facetFieldsString;
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

	/**
	 * Get query text - q parameter
	 */
	public function getQueryText()
	{
		if (empty($this->queryText)) {
			$this->queryText = $this->getParam('q');
		}
		$queryText = str_replace(':', '', $this->queryText);
		return $queryText;
	}

	public function getCategoryFacets($facetfield = 'category_path', $queryText)
	{
		$queryUrl = $this->getConfigValue('solr_server_url');

		$arguments = array(
				'json.nl' => 'map',
				'wt'=> 'json',
		);

		$this->prepareFilterQuery();
		$filterQueryString = $this->filterQuery;


		$queryUrl = trim($queryUrl,'/').'/'.$this->solrcore;
		$url = trim($queryUrl,'/').'/select/?q=category_text:('.urlencode($queryText).')&rows=-1&facet=true&facet.field='.$facetfield.'&facet.mincount=1';

		//filter query
		if (!empty($filterQueryString)) {
		    $url .= '&fq='.$filterQueryString;
		}

		$resultSet = $this->doRequest($url, $arguments, 'array');

		$returnData = array();
		if(isset($resultSet['facet_counts']['facet_fields'][$facetfield]) && is_array($resultSet['facet_counts']['facet_fields'][$facetfield]))
		{
			$returnData = $resultSet['facet_counts']['facet_fields'][$facetfield];
		}

		return $returnData;
	}

	public function query()
	{
		$params = $this->getParams();
		print_r($params);
		print_r($this->getConfig());
		die();
	}
}