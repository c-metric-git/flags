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
class SolrBridge_Solrsearch_Model_Solr_Query
{
    public $ultility = null;

    protected $isAutocomplete = false;

    protected $queryText = '*:*';

    protected $synonym = null;

    protected $solrcore = null;

    protected $start = 0;

    protected $rows = 9;

    protected $facetFields = array();

    protected $facetLimit = 200;

    protected $fieldList = array();

    protected $queryFields = 'textSearchStandard^80 textSearch^40 textSearchText^10 textSearchGeneral^1';

    protected $mm = '0%';

    protected $boostFields = array();

    protected $rangeFields = array();

    protected $filterQuery = null;

    protected $sort = null;

    protected $priceFieldName = null;

    public function __construct()
    {
       $this->ultility = Mage::getSingleton('solrsearch/ultility');
       $this->priceFieldName = Mage::helper('solrsearch')->getPriceFieldName();
    }

    public function getRangeFields()
    {
        return $this->rangeFields;
    }

    public function setFieldList($fieldList = array())
    {
        if (!empty($fieldList)) {
            $this->fieldList = $fieldList;
        }
    }

    public function init($options = array())
    {
        if (isset($options['queryText']) && !empty($options['queryText'])) {
            $this->queryText = $options['queryText'];
        }
        if (isset($options['rows']) && is_numeric($options['rows'])) {
            $this->rows = $options['rows'];
        }
        if (isset($options['facetlimit']) && is_numeric($options['facetlimit'])) {
            $this->facetLimit = $options['facetlimit'];
        }
        if (isset($options['solrcore']) && !empty($options['solrcore'])) {
            $this->solrcore = $options['solrcore'];
        }
        if (isset($options['autocomplete']) && $options['autocomplete'] === true) {
            $this->isAutocomplete = true;
        }
        if (isset($options['mm']) && !empty($options['mm'])) {
            $this->mm = $options['mm'];
        }else{
            $relevancy = (int) Mage::helper('solrsearch')->getSetting('relevancy');
            if ($relevancy > 0) {
                $this->mm = '100%';
            }else{
                $this->mm = '0%';
            }
        }

        $allow_part_of_word = (int) Mage::helper('solrsearch')->getSetting('allow_part_of_word');
        if ($allow_part_of_word < 1) {
            $this->queryFields = 'textSearchStandard^80 textSearchText^10 textSearchGeneral^1';
        }

        return $this;
    }

    public function prepareQueryData()
    {
        $this->prepareFieldList();
        if (!$this->isAutocomplete) {
            $this->preparePagingAndSorting();
        }
        $this->prepareFacetAndBoostFields();
        $this->prepareFilterQuery();

        //If catalog category view - dont need to prepare synonym
        if (!Mage::app()->getRequest()->getRouteName() == 'catalog') {
            $this->prepareSynonym();
        }
        return $this;
    }
    /**
     * Determine which fields will be selected
     */
    protected function prepareFieldList()
    {
        if (empty($this->fieldList))
        {
            $this->fieldList = array('products_id', 'product_type_static', 'name_varchar', 'store_id', 'website_id', $this->priceFieldName);
        }
    }
    /**
     * Prepare solr paging and sorting params
     */
    protected function preparePagingAndSorting()
    {
        $currentPage = 1;
        $itemsPerPage= 32;

        $toolbarBlock = Mage::app()->getLayout()->getBlockSingleton("solrsearch/product_list_toolbar");

        $orderby = $toolbarBlock->getCurrentOrder();
        $direction = $toolbarBlock->getCurrentDirection();
        $paramOrder = Mage::helper('solrsearch')->getParam('order');

        if (Mage::app()->getRequest()->getRouteName() != 'catalog') {
        	$orderby = $paramOrder;
        }
        if (!empty($orderby) && !empty($direction) ) {
            $orderbyField = $this->getSortFieldByCode($orderby, $direction);
            if (!empty($orderbyField)) {
                $this->sort = $orderbyField;
            }
        }

        $page = (int)$toolbarBlock->getCurrentPage();
        if(!empty($page) && is_numeric($page)){
            $currentPage = $page;
        }

        $limit = $toolbarBlock->getLimit();
        if ($limit == 'all') {
            $itemsPerPage = 10000000;
        }else{
            if(!empty($limit) && is_numeric($limit)){
                $itemsPerPage = $limit;
            }
        }

        $start = $itemsPerPage * ($currentPage - 1);
        $this->start = $start;
        $this->rows = $itemsPerPage;
    }
    /**
     * Prepare facets and boost fields
     */
    protected function prepareFacetAndBoostFields()
    {
        $facetFields = array();
        $boostFields = array();
        $rangeFields = array();
    
        $searchSettingFields = Mage::helper('solrsearch')->getSearchSetting('search_result_config_fields');
    
        //Load search result fields settings from store config for performance - fast
        if ( isset($searchSettingFields) && !empty($searchSettingFields) ) {
            $searchSettingFields = json_decode($searchSettingFields, true);
            if ( isset($searchSettingFields['facets']) && !empty($searchSettingFields['facets'])) {
                $facetFields = $searchSettingFields['facets'];
            }
            if ( isset($searchSettingFields['boosts']) && !empty($searchSettingFields['boosts'])) {
                $boostFields = $searchSettingFields['boosts'];
            }
            if ( isset($searchSettingFields['ranges']) && !empty($searchSettingFields['ranges'])) {
                $rangeFields = $searchSettingFields['ranges'];
            }
        }
    
        //Need reload attributes again for facets and boosts fields - slow
        if ( empty($facetFields) || !empty($boostFields) ) {
            $facetFields = array();
            $boostFields = array();
            $rangeFields = array();
    
            $use_category_as_facet = (int) Mage::helper('solrsearch')->getSetting('use_category_as_facet');
    
            if ($use_category_as_facet > 0)
            {
                $display_category_as_hierachy = Mage::helper('solrsearch')->getSetting('display_category_as_hierachy');
    
                if ($display_category_as_hierachy > 0)
                {
                    $facetFields[] = 'category_path';
                }
                else
                {
                    $facetFields[] = 'category_facet';
                }
            }
    
    
            $boostWeights = Mage::helper('solrsearch')->getWeights(); //get static field weight mapping
    
            //$attributesInfo = $this->ultility->getProductAttributeCollection();
            $attributesCollection = $this->ultility->getProductAttributes();
    
            foreach($attributesCollection as $attr)
            {
                $attribute = $attr->getData();
                //Collects which attributes will be displayed as facet (range facet)
                if( isset($attribute['solr_search_field_range']) &&
                    !empty($attribute['solr_search_field_range']) &&
                    $attribute['solr_search_field_range'] > 0 &&
                    $attribute['frontend_input'] == 'price')
                {
                    if (isset($attribute['is_filterable_in_search']) && $attribute['is_filterable_in_search'] > 0) {
                        $rangeFields[$attribute['attribute_code']] = $attribute['attribute_code'].'_'.$attribute['backend_type'];
                    }
                }
    
                /*
                 * Collects which attributes used for solr boosting feature - boosting base on fixed value settings in admin
                 * This will override the above - boosting base on queryText
                 */
                $boostFieldSetting = $this->getBoostSettingByAttribute($attribute, $boostWeights);
                if (!empty($boostFieldSetting)) {
                    $boostFields[$attribute['attribute_code']] = $boostFieldSetting;
                }
    
                //Collects which attributes will be used for facets
                if ( isset($attribute['is_filterable_in_search']) &&
                    $attribute['is_filterable_in_search'] > 0)
                {
                    if (!isset($attribute['solr_search_field_range']) || intval($attribute['solr_search_field_range']) < 1)
                    {
                        if ($attribute['attribute_code'] !== 'price')
                        {
                            $facetFields[] = $attribute['attribute_code'].'_facet';
                        }
                    }
                }
            }
            $searchResultSettings = array();
            $searchResultSettings['facets'] = $facetFields;
            $searchResultSettings['boosts'] = $boostFields;
            $searchResultSettings['ranges'] = $rangeFields;
            Mage::getModel('core/config')->saveConfig('solrbridge/search/search_result_config_fields', json_encode($searchResultSettings), 'stores', Mage::app()->getStore()->getId());
        }
    
    
        $queryText = $this->getQueryText();
        if (count($boostFields)) // If there some boost settings from Magento admin
        {
            $defaultBoostFields = $this->getDefaultBoostSetting($queryText);
    
            if (is_array($defaultBoostFields) && is_array($boostFields)) {
                $boostFields = array_merge($defaultBoostFields, $boostFields);
            }
        }
        else //Load default boost settings
        {
            if (!empty($queryText))
            {
                $boostFields = $this->getDefaultBoostSetting($queryText);
            }
        }
    
        if (!$this->isAutocomplete) {
            $usePriceSilder = (int) Mage::helper('solrsearch')->getSetting('use_price_slider');
            if ($usePriceSilder < 1) {
                $rangeFields[$this->priceFieldName] = $this->priceFieldName;
            }
            $facetFields[] = $this->priceFieldName;
        }
    
        $this->facetFields = $facetFields;
        $this->boostFields = $boostFields;
        $this->rangeFields = $rangeFields;
    }

    public function getFacetFields()
    {
    	return $this->facetFields;
    }
    public function getBoostFields()
    {
    	return $this->boostFields;
    }

    /**
     * Parse boost values which settings from Magento Admin (Magento attribute form)
     * @param array $attribute
     * @return array
     */
    protected function getBoostSettingByAttribute($attribute, $boostWeights)
    {
        $boostFieldsArr = array();

        if (isset($attribute['solr_search_field_boost']) && !empty($attribute['solr_search_field_boost']))
        {
            $boostValues = explode("\n", $attribute['solr_search_field_boost']);

            $attributeCode = $attribute['attribute_code'];

            foreach ($boostValues as $boostValue) {

                $pair = explode('|', trim($boostValue));
                /**
                 * $pair[0] is boost value, $pair[1] is weight
                 * example: Sony|1, $pair[0] = Sony, $pair[1] = 1
                */
                if (isset($pair[0]) && !empty($pair[0]) && isset($pair[1]) && !empty($pair[1])) {

                    $boostText = Mage::helper('solrsearch')->getPreparedBoostText($pair[0]);
                    $boostWeight = $pair[1];

                    $boostFieldsArr[] = array(
                            'field' => $attributeCode.'_boost_exact',
                            'weight' => ((int)$boostWeights[$boostWeight] + 209),
                            'value' => $boostText,
                            'type' => 'absolute',
                    );
                    $boostFieldsArr[] = array(
                            'field'=>$attributeCode.'_boost',
                            'weight'=>((int)$boostWeights[$boostWeight] + 206),
                            'value'=>$boostText,
                            'type' => 'relative',
                    );
                    $boostFieldsArr[] = array(
                            'field'=>$attributeCode.'_relative_boost',
                            'weight'=>((int)$boostWeights[$boostWeight] + 202),
                            'value'=>$boostText,
                            'type' => 'relative',
                    );

                }
            }
        }else if (isset($attribute['solr_search_field_weight']) && !empty($attribute['solr_search_field_weight'])) {
            $queryText = $this->queryText;
            $boostText = Mage::helper('solrsearch')->getPreparedBoostText($queryText);

            if (!in_array( $boostText ,Mage::helper('solrsearch')->getIgnoreQuery() )) {
            	$attributeCode = $attribute['attribute_code'];

            	$boostFieldsArr[] = array(
            			'field' => $attributeCode.'_boost_exact',
            			'weight' => ((int)$attribute['solr_search_field_weight'] + 209),
            			'value' => $boostText,
            			'type' => 'absolute',
            	);
            	$boostFieldsArr[] = array(
            			'field'=>$attributeCode.'_boost',
            			'weight'=> ((int)$attribute['solr_search_field_weight'] + 206),
            			'value'=>$boostText,
            			'type' => 'relative',
            	);
            	$boostFieldsArr[] = array(
            			'field'=>$attributeCode.'_relative_boost',
            			'weight'=> ((int)$attribute['solr_search_field_weight'] + 202),
            			'value'=>$boostText,
            			'type' => 'relative',
            	);
            }
        }
        return $boostFieldsArr;
    }
    /**
     * Prepare solr filter query paprams
     */
    protected function prepareFilterQuery()
    {
        $filterQuery = Mage::getSingleton('core/session')->getSolrFilterQuery();
        $standardFilterQuery = array();
        if ($standardFilterQuery = $this->getStandardFilterQuery()) {
            $filterQuery = $this->getStandardFilterQuery();
        }

        if (!is_array($filterQuery) || !isset($filterQuery)) {
            $filterQuery = array();
        }
    	$params = Mage::app()->getRequest()->getParams();
 		if($params['searchBarOrder']!='')
		{
			$defaultFilterQuery = array(
					'store_id' => array(Mage::app()->getStore()->getId()),
					'website_id' => array(Mage::app()->getStore()->getWebsiteId()),
					'product_status' => array(1),
					'fl_filter_section_facet' => array($params['searchBarOrder'])
			);
		}
		else
		{
			$defaultFilterQuery = array(
					'store_id' => array(Mage::app()->getStore()->getId()),
					'website_id' => array(Mage::app()->getStore()->getWebsiteId()),
					'product_status' => array(1),
			);
		}
        $checkInstock =  (int) Mage::helper('solrsearch')->getSetting('check_instock');
        if ($checkInstock > 0) {
        	$defaultFilterQuery['instock_int'] = array(1);
        }

        $filterQuery = array_merge($filterQuery, $defaultFilterQuery);

        /**
         * Ignore the following section if the request is for autocomplete
         * The purpose is the speed up autocomplete
         */
        if (!$this->isAutocomplete) {

            if (Mage::app()->getRequest()->getRouteName() == 'catalog') {

                $layer = Mage::getSingleton('catalog/layer');
                $_category = $layer->getCurrentCategory();
                $currentCategoryId= $_category->getId();

                if (empty($filterQuery['category_id'])) {
                    $filterQuery['category_id'] = array($currentCategoryId);
                }

                $filterQuery['filter_visibility_int'] = Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds();

                //Check category is anchor
                if ($_category->getIsAnchor()) {
                    $childrenIds = $_category->getAllChildren(true);

                    if (is_array($childrenIds) && isset($filterQuery['category_id']) && is_array($filterQuery['category_id'])) {
                        if (!isset($standardFilterQuery['category_id'])){
                            $filterQuery['category_id'] = array_merge($filterQuery['category_id'], $childrenIds);
                        }
                    }
                }
            };
        }

        $filterQueryArray = array();
        $rangeFields = $this->rangeFields;

        foreach($filterQuery as $key=>$filterItem){
            //Ignore cateory facet - using category instead
            if ($key == 'category_facet') {
                continue;
            }

            if(count($filterItem) > 0){
                $query = '';
                foreach($filterItem as $value){
                    if ($key == 'price_decimal')
                    {
                        $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                        //Filter for special price
                        $this->updateSpecialPriceFilter($query, $value);
                    }
                    else if($key == 'price')
                    {
                        $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                        //Filter for special price
                        $this->updateSpecialPriceFilter($query, $value);
                    }
                    else
                    {
                        $face_key = substr($key, 0, strrpos($key, '_'));
                        if ($key == 'price_facet') 
                        {
                            $query .= $this->priceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
                            //Filter for special price
                            $this->updateSpecialPriceFilter($query, $value);
                        }
                        else if(array_key_exists($face_key, $rangeFields))
                        {
                            $query .= $rangeFields[$face_key].':['.urlencode(trim(addslashes($value))).']+OR+';
                        }else{
                            $query .= $key.':%22'.urlencode(trim(addslashes($value))).'%22+OR+';
                        }
                    }
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
    /**
     * This function used to fix sale/special price filter
     * @param unknown $query
     * @param unknown $value
     */
    public function updateSpecialPriceFilter(&$query, $value)
    {
    	//Filter for special price
    	$priceFields = Mage::helper('solrsearch')->getPriceFields();
    	if ( isset($priceFields[1]) )
    	{
    		$specialPriceFieldName = $priceFields[1];
    		$query .= 'sort_'.$specialPriceFieldName.':['.urlencode(trim($value).'.99999').']+OR+';
    	}
    }

    /**
     * Get default boost settings
     * @param string $queryText
     * @return array
     */
    protected function getDefaultBoostSetting($queryText)
    {
        $boostText = Mage::helper('solrsearch')->getPreparedBoostText($queryText);

        $boostFieldsArr['name'] = array(
                array
                (
                    'field' => 'name_boost_exact',
                    'weight' => 120,
                    'value' => $boostText,
                    'type' => 'absolute',
                ),
                array
                (
                    'field' =>'name_boost',
                    'weight' =>100,
                    'value'=>$boostText,
                    'type' => 'absolute',
                ),
                array
                (
                    'field' =>'name_relative_boost',
                    'weight' =>80,
                    'value'=>$boostText,
                    'type' => 'relative',
                ),
        );

        $products_search_fields_weights = Mage::helper('solrsearch')->getProductSearchFieldWeights();

        if (is_array($products_search_fields_weights)) {
            foreach ($products_search_fields_weights as $weight){
                if ((int)$weight > 0) {
                    $searchWeightBoost = array(
                            'field' => 'product_search_weight_int',
                            'weight' => (200 + ((int)$weight * 10)),
                            'value' => $weight,
                            'type' => 'absolute',
                    );
                    $boostFieldsArr['product_search_weight'][] = $searchWeightBoost;
                }
            }
        }
        
        //Enable the following block if product review enabled
        /*
        $products_search_reviews = Mage::helper('solrsearch')->getProductReviewsStats();
        if (is_array($products_search_reviews)) {
        	foreach ($products_search_reviews as $count){
        		if ((int)$count > 0) {
        			$searchReviewsBoost = array(
        					'field' => 'product_review_int',
        					'weight' => (200 + ((int)$count * 10)),
        					'value' => $count,
        					'type' => 'absolute',
        			);
        			$boostFieldsArr['product_review_int'][] = $searchReviewsBoost;
        		}
        	}
        }
        */

        $boostFieldsArr['category'] = array(
                array(
                        'field' =>'category_boost',
                        'weight' =>60,
                        'value'=>$boostText,
                        'type' => 'relative',
                )
        );
        return $boostFieldsArr;
    }

    protected function prepareSynonym()
    {
        $queryText = $this->getQueryText();

        $query = Mage::getModel('catalogsearch/query')->loadByQuery($queryText);

        if ($query->getSynonymFor())
        {
            $this->synonym = $query->getSynonymFor();
        }
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

        if (!$this->isAutocomplete) {
            $arguments['stats'] = 'true';
            $queryUrl .= '&'.$this->getStatsFields();

            if (!empty($this->sort)) {
                $queryUrl .= '&sort='.$this->sort;
            }
        }

        if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
            $arguments['q.alt'] = '*:*';
        };

        $resultSet = Mage::getResourceSingleton('solrsearch/solr')->doRequest($queryUrl, $arguments, 'array');

        if (isset($resultSet['response']['numFound']) && intval($resultSet['response']['numFound']) > 0)
        {
            //Log search query for statistic
            $queryText = $this->getQueryText();
            $this->ultility->logSearchTerm($queryText, $resultSet['response']['numFound'], $store->getId());

            return $resultSet;

        }
        else
        {
        	$spellCheck = array();
        	if (isset($resultSet['spellcheck']) && is_array($resultSet['spellcheck'])) {
        		$spellCheck = $resultSet['spellcheck'];
        	}
            $queryText = $this->getQueryText();
            if (isset($resultSet['spellcheck']['suggestions']['collation']))
            {
                $queryText = strtolower($resultSet['spellcheck']['suggestions']['collation']);
            }

            if (!empty($queryText))
            {
                $this->queryText = $queryText;
                $this->prepareFacetAndBoostFields();
                $queryUrl = $this->buildQueryUrl();
                $arguments['mm'] = '0%';

                if (!$this->isAutocomplete) {
                    $queryUrl .= '&'.$this->getStatsFields();

                    if (!empty($this->sort)) {
                        $queryUrl .= '&sort='.$this->sort;
                    }
                }

                $resultSet = Mage::getResourceModel('solrsearch/solr')->doRequest($queryUrl, $arguments, 'array');
                if (!empty($spellCheck)) {
                	$resultSet['spellcheck'] = $spellCheck;
                }
            }
        }
        //Log search query for statistic
        $queryText = $this->getQueryText();
        if (isset($resultSet['response']['numFound']))
        {
            $this->ultility->logSearchTerm($queryText, $resultSet['response']['numFound'], $store->getId());
        }

        return $resultSet;
    }
    /**
     * Get stats fields
     * @return string
     */
    protected function getStatsFields()
    {
        $statsFields = '';
        $statsFields = 'stats.field='.$this->priceFieldName;
        
        //Added this for fixing price range incase there is special price
        $priceFields = Mage::helper('solrsearch')->getPriceFields();
        if ( isset($priceFields[1]) )
        {
        	$specialPriceFieldName = $priceFields[1];
        	$statsFields .= '&stats.field='.'sort_'.$specialPriceFieldName;
        }
        
        return $statsFields;
    }

    /**
     * Get query text - q parameter
     */
    public function getQueryText()
    {
        if (empty($this->queryText)) {
            $this->queryText = Mage::helper('solrsearch')->getParam('q');
        }
        $queryText = str_replace(':', '', $this->queryText);
        return $queryText;
    }


    /**
     * Prepare solr query url
     * @param boolean $hasCore
     * @return string
     */
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

		$spellcheckQuery = Mage::helper('solrsearch')->getPreparedBoostText($q);
		if ( !in_array( $spellcheckQuery, Mage::helper('solrsearch')->getIgnoreQuery() ) )
		{
			$queryUrl .= '&spellcheck.q='.urlencode($spellcheckQuery);
		}

		$facetFieldsString = $this->convertFacetFieldsToString();
		$boostFieldsString = $this->convertBoostFieldsToString();
		$filterQueryString = $this->filterQuery;

		//Facet fields
		if (!empty($facetFieldsString)) {
		    $queryUrl .= '&'.$facetFieldsString;
		}
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

    /**
     * Get filter query array from url
     * @return array
     */
    public function getStandardFilterQuery()
    {
    	$params = Mage::helper('solrsearch')->getParams();
    	if (isset($params['fq']) && is_array($params['fq']))
    	{
    		$filterQuery = array();
    		foreach ($params['fq'] as $key=>$values)
    		{
    			if (!empty($key) && !is_array($values) && !empty($values))
    			{
    				if ($key == 'category_id')
    				{
    					$filterQuery[$key] = array($values);
    				}else
    				{
    					$filterQuery[$key.'_facet'] = array($values);
    				}
    			}else if(!empty($key) && is_array($values))
    			{
    				if ($key == 'category_id')
    				{
    					$filterQuery[$key] = $values;
    				}
    				else
    				{
    					$filterQuery[$key.'_facet'] = $values;
    				}
    			}
    		}
    		return $filterQuery;
    	}
		return array();
    }

    /**
     * convert Boost Settings Array To String
     * @param array $boostFieldsArr
     * $boostFieldsArr = array(
     * 						'att1' => array(
     * 										array('field' => 'field_x', 'weight' => 'n', 'value' => 'value'),
     * 										array('field' => 'field_y', 'weight' => 'n', 'value' => 'value')
     * 									   )
     * @return string
     */
    protected function convertBoostFieldsToString()
    {
    	$boostQueryString = '';
    
    	if (is_array($this->boostFields) && !empty($this->boostFields))
    	{
    		foreach( $this->boostFields as $attributeCode => $configArray)//Foreach attributes
    		{
    			foreach ($configArray as $config) // Foreach attribute config
    			{
    				$boostField = $config['field'];
    				$boostWeight = $config['weight'];
    				$boostValue = $config['value'];
    
    				if (!empty($boostValue))
    				{
    					if (isset($config['type']) && $config['type'] == 'absolute')
    					{
    						$boostQueryString .= $boostField.':"'.$boostValue.'"^'.$boostWeight.' ';
    					}
    					else
    					{
    						$boostQueryString .= $boostField.':'.$boostValue.'^'.$boostWeight.' ';
    					}
    				}
    
    			}
    		}
    	}
    
    	return $boostQueryString;
    }
    
    /**
     * Convert facetfields from array to param string
     * @return string
     */
    protected function convertFacetFieldsToString()
    {
        $facetFieldString = '';

        if (is_array($this->facetFields) && !empty($this->facetFields))
        {
            foreach ($this->facetFields as $fieldKey) {
                $facetFieldString .= 'facet.field='.$fieldKey.'&';
            }
        }
        if (!empty($facetFieldString)) {
            $facetFieldString = trim($facetFieldString,'&');
        }
        return $facetFieldString;
    }
    /**
     * Convert rangeFields from array to param string
     * @return string
     */
    protected function convertRangeFieldsToString()
    {
        $rangeFieldString = '';

        if (is_array($this->rangeFields) && !empty($this->rangeFields))
        {
            foreach ($this->rangeFields as $fieldItem)
            {
                $rangeFieldString .= '&facet.range='.$fieldItem;
                $rangeFieldString .= '&f.'.$fieldItem.'.facet.range.start=0';
                $rangeFieldString .= '&f.'.$fieldItem.'.facet.range.end=1000000';
                $rangeFieldString .= '&f.'.$fieldItem.'.facet.range.gap=100';
                $rangeFieldString .= '&f.'.$fieldItem.'.facet.mincount=1';
            }
        }
        if (!empty($rangeFieldString)) {
            $rangeFieldString = trim($rangeFieldString,'&');
        }
        return $rangeFieldString;
    }
    public function getSortFieldByCode($attributeCode, $direction)
    {
        if ($attributeCode == 'price')
        {
        	$priceFields = Mage::helper('solrsearch')->getPriceFields();
        	$priceFieldName = $priceFields[0];
        	$specialPriceFieldName = $priceFields[1];
            return 'sort_'.$specialPriceFieldName.'+'.$direction.','.$priceFieldName.'+'.$direction;
        }
        else if( $attributeCode == 'position' )
        {
        	if (Mage::app()->getRequest()->getRouteName() == 'catalog')
        	{
        		$layer = Mage::getSingleton('catalog/layer');
        		$_category = $layer->getCurrentCategory();
        		$currentCategoryId= $_category->getId();
        		if ($currentCategoryId)
        		{
        			return 'sort_cat_'.$currentCategoryId.'_pos_int+'.$direction;
        		}
        	};
        	
        	return 'sort_position_decimal+'.$direction;
        }
        else
        {
            $entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
            $catalogProductEntityTypeId = $entityType->getEntityTypeId();

            $productAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($catalogProductEntityTypeId, $attributeCode);

            $sortAttributeCode = '';
            if ($productAttribute && $productAttribute->getBackendType() != '') {
                return 'sort_'.$attributeCode.'_'.$productAttribute->getBackendType().'+'.$direction;
            }
        }
        return false;
    }
}