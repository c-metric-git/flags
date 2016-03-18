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
class SolrBridge_Solrsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	const QUERY_VAR_NAME = 'q';
	const FILTER_QUERY_VAR_NAME = 'fq';
	
	protected $_solrCore = null;
	protected $_storeId = null;
	
	public function getQueryParamName()
    {
        return self::QUERY_VAR_NAME;
    }
	public function getFilterQueryParamName()
    {
        return self::FILTER_QUERY_VAR_NAME;
    }
	public function getResultUrl($query = null,$filterQuery = null)
    {
        $url = $this->_getUrl('solrsearch', array(
            '_query' => array(self::QUERY_VAR_NAME => $query, self::FILTER_QUERY_VAR_NAME=>$filterQuery),
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),
        	'_nosid' => true,
        ));
        $url = str_replace('___SID=U', '', $url);
        $url = str_replace('?', '', $url);
        return $url;
    }

    public function getTemplate($defaultTemplate)
    {
        $isContentMode = Mage::app()->getLayout()->getBlockSingleton('solrsearch/category_view')->isContentMode();
        if($isContentMode){
            $defaultTemplate = 'page/1column.phtml';
        }
        return $defaultTemplate;
    }

    public function getDidYouMeanUrl($query = null)
    {
    	$url = $this->_getUrl('solrsearch', array(
    			'_query' => array(self::QUERY_VAR_NAME => $query),
    			'_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
    	));
    	return $url;
    }

    public function getAjaxQueryUrl($query = null,$filterQuery = null)
    {
    	$uri = '';
    	$advanced_autocomplete = (int)$this->getSetting('advanced_autocomplete');
    	if ($advanced_autocomplete > 0)
    	{
    		$uri = trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/').'/sb.php';
    	}
    	else
    	{
	    	$uri = $this->_getUrl('solrsearch/ajax/query', array(
	    			'_query' => array(self::QUERY_VAR_NAME => $query, self::FILTER_QUERY_VAR_NAME=>$filterQuery),
	    			'_secure' => Mage::app()->getFrontController()->getRequest()->isSecure(),
	    			'_nosid' => true,
	    	));
    	}
    	return trim($uri, '/');
    }

    public function getIgnoreQuery()
    {
    	return array('**', '*', '*:*', ':');
    }

    public function getFullProductUrl( $product )
    {
    	if(is_object($product) && $product->getSku())
    	{
    		// first try SQL approach
    		try
    		{
    			$query      = "
                            SELECT `request_path`
                            FROM `core_url_rewrite`
                            WHERE `product_id`='" . $product->getEntityId() . "'
                            AND `category_id`='" . end($product->getCategoryIds()) . "'
                            AND `store_id`='" . Mage::app()->getStore()->getId() . "';
                          ";
    			$read       = Mage::getSingleton('core/resource')->getConnection('core_read');
    			$result     = $read->fetchRow($query);
    			return Mage::getUrl('') . $result['request_path'];
    		}
    		// if it fails, than use failsafe way with category object loading
    		catch(Exception $e)
    		{
    			$allCategoryIds     = $product->getCategoryIds();
    			$lastCategoryId     = end($allCategoryIds);
    			$lastCategory       = Mage::getModel('catalog/category')->load($lastCategoryId);
    			$lastCategoryUrl    = $lastCategory->getUrl();
    			$fullProductUrl     = str_replace(Mage::getStoreConfig('catalog/seo/category_url_suffix'), '/', $lastCategoryUrl) . basename( $product->getUrlKey() ) . Mage::getStoreConfig('catalog/seo/product_url_suffix');
    			return $fullProductUrl;
    		}
    	}
    	return '';
    }
    
    public function getSolrCore() {
    
        if($this->_solrCore === null)
        {
            $index = Mage::getModel('solrsearch/index')->load($this->getStoreId(), 'store_id');
    
            $this->_solrCore =$index->getSolrCore();
        }
        return $this->_solrCore;
    }
    
    public function getStoreId() {
        if($this->_storeId === null)
        {
            $this->_storeId = Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

	/**
	 * Get configuration setting value by key
	 * @param string $key
	 * @param number $storeId
	 * @return string
	 */
    public function getSetting( $key, $storeId = 0 )
    {
    	$storeId = (isset($storeId) && is_numeric($storeId) && $storeId > 0) ? $storeId : Mage::app()->getStore()->getId();
    	$value = '';
    	$settings = Mage::getStoreConfig('solrbridge/settings', $storeId);

    	if (isset($settings[$key])) {
    		$value = $settings[$key];
    	}
    	return $value;
    }
    /**
     * Wrapper function for Mage::getStoreConfig('solrsearch/setting/xxx')
     * @param string $key
     * @return string
     */
    public function getSearchSetting($key) {
        $value = '';
        $settings = Mage::getStoreConfig('solrbridge/search', $this->_storeId);
    
        if (isset($settings[$key])) {
            $value = $settings[$key];
        }
        return $value;
    }
    /**
     * Get predefined search weights
     * @return array
     */
    public function getWeights()
    {
        $weights = array();
        $index = 1;
        foreach (range(10, 200, 10) as $number) {
            $weights[$index] = array(
                    'value' => $number,
                    'label' => $index
            );
            $index++;
        }
        return $weights;
    }

    public function getKeywordCachedKey($queryText)
    {
    	$key = sha1('solrbridge_solrsearch_'.Mage::app()->getStore()->getId().'_'.Mage::app()->getStore()->getWebsiteId().'_'.$queryText);

    	return $key;
    }
    /**
     * Parse and remove some special characters
     * @param string $text
     * @return string
     */
    public function getPreparedBoostTextOld($text){
    	$boostText =  (strrpos(trim($text,':'), ':') > -1)?'"'.trim($text,':').'"':trim($text,':');
    	return $boostText;
    }
    /**
     * Parse and remove some special characters
     * @param string $text
     * @return string
     */
    public function getPreparedBoostText($text)
    {
    	return SolrBridge_Base::getPreparedBoostText($text);
    }

    /**
     * Retrieve HTML escaped search query
     *
     * @return string
     */
    public function getEscapedQueryText($queryText = '')
    {
    	if (!empty($queryText)){
    		return $this->htmlEscape($queryText);
    	}else{
    		$solrModel = Mage::getModel('solrsearch/solr');
    		$queryText = $this->getParam('q');
    		if( isset($solrData['responseHeader']['params']['q']) && !empty($solrData['responseHeader']['params']['q']) ) {
    			if ($queryText != $solrData['responseHeader']['params']['q']) {
    				$queryText = $solrData['responseHeader']['params']['q'];
    			}
    		}
    	}
    	return $this->htmlEscape($queryText);
    }
    /**
     * Get parameters value
     * @return array
     */
	public function getParams() {
		return SolrBridge_Base::getParams();
    }
    /**
     * Get parameter value
     * @param $key
     * @return mixed
     */
    public function getParam($key) {
    	return SolrBridge_Base::getParam($key);
    }

    public function getPriceFieldName()
    {
    	$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

    	$code = Mage::app()->getStore()->getCurrentCurrencyCode().'_'.$customerGroupId;

    	$priceFieldName = $code.'_price_decimal';

    	return $priceFieldName;
    }

    /**
     * Get total existing solr documents
     * @param string $coreName
     * @return integer
     */
    public function getTotalDocumentsByCore( $solrCore = 'english' ) {
    	return Mage::getResourceModel('solrsearch/solr')->getDocumentCount($solrCore);
    }
    
    /**
     * Get total existing solr documents
     * @param string $coreName
     * @return integer
     */
    public function getDocumentCount( $solrCore = 'english', $storeId = null ) {
        return Mage::getResourceModel('solrsearch/solr')->getDocumentCount($solrCore, $storeId);
    }

    public function applyInstockCheck(&$collection)
    {
    	$checkInstockConfig =  $this->getSetting('check_instock');
    	$checkInstockMethod =  $this->getSetting('check_instock_method');
    	if( intval($checkInstockConfig) > 0 )
    	{
    		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
    			if (intval($checkInstockMethod) > 0)
    			{
    			    $collection->joinField('qty',
    			        'cataloginventory/stock_item',
    			        'qty',
    			        'product_id=entity_id',
    			        '{{table}}.is_in_stock=1 AND {{table}}.qty > 0',
    			        'inner');
    			}
    			else
    			{
    			    $collection->joinField('qty',
    			        'cataloginventory/stock_item',
    			        'qty',
    			        'product_id=entity_id',
    			        '{{table}}.is_in_stock=1',
    			        'inner');
    			}
    		}
    	}
    }

    public function hightlight($words,$text)
    {
    	return SolrBridge_Base::hightlight($words,$text);
    }
    /**
     * Check Solr Server Status
     * @return boolean
     */
    public function pingSolrServer(){
        $solrCore = $this->getSolrCore();
        if (!empty($solrCore)) {
            return Mage::getResourceModel('solrsearch/solr')->pingSolrCore($solrCore);
        }
        return false;
    }
    /**
     * Get product search weights facets
     * @return array
     */
    public function getProductSearchFieldWeights()
    {
    	$cachedKey = 'solrbridge_solrsearch_product_searchweights_'.Mage::app()->getStore()->getId().'_'.Mage::app()->getStore()->getWebsiteId();
    	if (false !== ($returnData = Mage::app()->getCache()->load($cachedKey))) {
    		$returnData = unserialize($returnData);
    	}else{
    		$returnData = array();
    		$solr_server_url = Mage::helper('solrsearch')->getSetting('solr_server_url');
    		$solr_index = $this->getSolrCore();

    		$solrUrl = trim($solr_server_url, '/').'/'.$solr_index;

    		$statsUrl = trim($solrUrl, '/').'/select/?q=*:*&rows=0&facet.field=product_search_weight_int&facet=true&json.nl=map&wt=json';
    		$statsData = Mage::getResourceModel('solrsearch/solr')->doRequest($statsUrl);

    		if (is_array($statsData) && isset($statsData['facet_counts']['facet_fields']['product_search_weight_int'])) {
    			$weightsFacets = $statsData['facet_counts']['facet_fields']['product_search_weight_int'];
    			if (is_array($weightsFacets)){
    				foreach ($weightsFacets as $key=>$val){
    					$returnData[] = $key;
    				}
    			}
    		}

    		if (count($returnData) > 0) {
    			Mage::app()->getCache()->save(serialize($returnData), $cachedKey, array('solrbridge_solrsearch'));
    		}
    	}
    	return $returnData;
    }
    
    /**
     * Get product search weights facets
     * @return array
     */
    public function getProductReviewsStats()
    {
    	$cachedKey = 'solrbridge_solrsearch_product_reviews_'.Mage::app()->getStore()->getId().'_'.Mage::app()->getStore()->getWebsiteId();
    	if (false !== ($returnData = Mage::app()->getCache()->load($cachedKey))) {
    		$returnData = unserialize($returnData);
    	}else{
    		$returnData = array();
    		$solr_server_url = Mage::helper('solrsearch')->getSetting('solr_server_url');
    		$solr_index = $this->getSolrCore();
    
    		$solrUrl = trim($solr_server_url, '/').'/'.$solr_index;
    
    		$statsUrl = trim($solrUrl, '/').'/select/?q=*:*&rows=0&facet.field=product_review_int&facet=true&json.nl=map&wt=json';
    		$statsData = Mage::getResourceModel('solrsearch/solr')->doRequest($statsUrl);
    
    		if (is_array($statsData) && isset($statsData['facet_counts']['facet_fields']['product_review_int'])) {
    			$weightsFacets = $statsData['facet_counts']['facet_fields']['product_review_int'];
    			if (is_array($weightsFacets)){
    				foreach ($weightsFacets as $key=>$val){
    					$returnData[] = $key;
    				}
    			}
    		}
    
    		if (count($returnData) > 0) {
    			Mage::app()->getCache()->save(serialize($returnData), $cachedKey, array('solrbridge_solrsearch'));
    		}
    	}
    	return $returnData;
    }

    public function getPriceFields($store = false)
    {
    	if (!$store)
    	{
    		$store = Mage::app()->getStore();
    	}

    	$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $priceFields = SolrBridge_Base::getPriceFieldsName($store->getCurrentCurrencyCode(), $customerGroupId);

        return $priceFields;
    }

    /**
     * Generate Javascript configuration for autocomplete
     * @param string $elementId
     * @param string $containerid
     * @return string
     */
    public function getAutocompleteConfig($elementId, $containerid="search_mini_form"){
    	$base_url = trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),'/');

    	$display_thumb = $this->getSetting('solr_quick_search_display_thumnail');

    	$allow_filter = $this->getSetting('solr_quick_search_allow_filter');

    	$currencyPositionSetting = $this->getSetting('currency_position');
    	$currencyPosition = 'before';
    	if ($currencyPositionSetting < 1) {
    		$currencyPosition = 'after';
    	}
    	//Category redirect to category page/ search result page
    	$autocompleteCategoryRedirect = $this->getSetting('autocomplete_category_redirect');

    	//display brand suggestion
    	$display_brand_suggestion = $this->getSetting('display_brand_suggestion');
    	//display brand suggestion attribute code
    	$brand_attribute_code = $this->getSetting('brand_attribute_code');

    	//Categories limit
    	$categoryLimit = 3;
		$categoryLimitConf = $this->getSetting('autocomplete_category_limit');
		if (is_numeric($categoryLimitConf)) {
			$categoryLimit = $categoryLimitConf;
		}
		$brandLimit = 3;
    	$brandLimitConf = $this->getSetting('autocomplete_brand_limit');
		if (is_numeric($brandLimitConf)) {
			$brandLimit = $brandLimitConf;
		}

    	$currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    	$ajaxBaseUrl = trim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),'/');
    	$searchResultUrl = trim($this->getResultUrl(), '/');
    	
    	$ajaxprice = $this->getSetting('ajaxprice');

    	$displaykeywordsuggestion = ($this->getSetting('display_keyword_suggestion'))?'true':'false';
    	$conf = array(
    		"id:'autocomplete'",
    		"target:'#{$containerid}'",
    		"inputid:'{$elementId}'",
    		"containerid:'{$containerid}'",
    		"boxWidth:469",
    		"searchTextPlaceHolder:'".$this->__('Search entire store here...')."'",
    		"currencySign:'&nbsp;{$currencySign}&nbsp;'",
    		"currencycode:'".Mage::app()->getStore()->getCurrentCurrencyCode()."'",
    		"ajaxBaseUrl:'{$ajaxBaseUrl}'",
    		"searchResultUrl:'{$searchResultUrl}'",
    		"viewAllResultText:'".$this->__('View all search results for %s')."'",
    		"categoryText:'".$this->__('Categories')."'",
    		"viewAllCategoryText:'".$this->__('View all categories >>')."'",
    		"viewAllBrandsText:'".$this->__('View all brands >>')."'",
    		"keywordsText:'".$this->__('Keywords')."'",
    		"productText:'".$this->__('product')."'",
    		"productsText:'".$this->__('products')."'",
    		"brandText:'".$this->__('Brands')."'",
    		"storetimestamp:'".Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore()->getId())."'",
    		"storeid:'".Mage::app()->getStore()->getId()."'",
    		"customergroupid:'".Mage::getSingleton('customer/session')->getCustomerGroupId()."'",
    		"categoryRedirect:'{$autocompleteCategoryRedirect}'",
    		"showBrand:'{$display_brand_suggestion}'",
    		"showBrandAttributeCode:'{$brand_attribute_code}'",
    		"displaykeywordsuggestion:'{$displaykeywordsuggestion}'",
    		"displayResultOfText:'".$this->__('Search results for %s')."'",
    		"displayResultOfInsteadText:'".$this->__('Search results for %s instead')."'",
    		"currencyPos:'{$currencyPosition}'",
    		"displayThumb:'{$display_thumb}'",
    		"allowFilter:'{$allow_filter}'",
    		"categoryLimit:'{$categoryLimit}'",
    		"brandLimit:'{$brandLimit}'",
    		"fromPriceText:'".$this->__('from')."'",
    		"ajaxprice:{$ajaxprice}",
    	);

    	return '{'.@implode(',', $conf).'}';
    }
}