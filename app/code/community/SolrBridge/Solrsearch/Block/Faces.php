<?php
/**
 * @category SolrBridge
 * @package Solrbridge_Search
 * @author	Hau Danh
 * @copyright	Copyright (c) 2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Faces extends Mage_Core_Block_Template
{
	protected $solrData = array();

	protected $filterQuery = array();

	protected $solrModel = null;

	protected $rootCatIds = array ();
	
	protected $_index = 1;

	protected function _construct()
    {
    	$this->solrModel = Mage::getModel('solrsearch/solr');
    	$this->setTemplate('solrsearch/standard/searchfaces.phtml');
    }
    
    public function getLimit() {
    	return Mage::getStoreConfig('solrbridgenav/settings/moreless_limit', Mage::app()->getStore());
    }
    public function displayCategoryAsHierachy() {
    	return Mage::helper('solrsearch')->getSetting('display_category_as_hierachy');
    }
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getSolrData(){

    	return $this->solrData;
    }

    protected function prepareSolrData()
    {
    	$solrModel = Mage::registry('solrbridge_loaded_solr');

    	if ($solrModel) {
    		$this->solrModel = $solrModel;
    		$this->solrData = $this->solrModel->getSolrData();
    	}
    	else
    	{
    		$this->solrModel = Mage::getModel('solrsearch/solr');
    		$queryText = Mage::helper('solrsearch')->getParam('q');
    		$this->solrData = $this->solrModel->query($queryText);
    	}
    }

	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
		$this->prepareSolrData();
    	return parent::_beforeToHtml();
    }

    public function getFacetLabel($facetCode){

    	$startPoint = strrpos($facetCode, '_')+1;
    	$endPoint = strlen($facetCode);
    	$attributeCode = substr($facetCode, 0, ($startPoint-1));

    	$facetLabelCache = Mage::app()->loadCache('solr_bridge_'.$facetCode.'_cache_store_'.Mage::app()->getStore()->getId());

    	if ( isset($facetLabelCache) && !empty($facetLabelCache) ) {
    		return $facetLabelCache;
    	}else {
    		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
			$catalogProductEntityTypeId = $entityType->getEntityTypeId();

			$facetFieldsInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($catalogProductEntityTypeId)
			->setCodeFilter(array($attributeCode))
			->addStoreLabel(Mage::app()->getStore()->getId());

			$facetLabel = '';
			foreach($facetFieldsInfo as $att){
				if ($att->getAttributeCode() == $attributeCode) {
					$facetLabel = $att->getStoreLabel();
					Mage::app()->saveCache($facetLabel, 'solr_bridge_'.$facetCode.'_cache_store_'.Mage::app()->getStore()->getId(), array(), 60*60*24*360);
					break;
				}
			}

			if ($attributeCode == 'category')
			{
				$facetLabel = $this->__('Category');
			}
			return $facetLabel;
    	}
    }

    public function getFacetFields()
    {
    	$solrData = $this->getSolrData();

    	$priceFieldName = Mage::helper('solrsearch')->getPriceFieldName();

    	$facets_fields = array();

    	if (isset($solrData['facet_counts']['facet_fields']) && is_array($solrData['facet_counts']['facet_fields'])) {
    		$facets_fields = $solrData['facet_counts']['facet_fields'];
    	}

    	//Ignore the price_decimal
    	if (isset($facets_fields[$priceFieldName])) {
    		unset($facets_fields[$priceFieldName]);
    	}

    	$this->manupulateFacetFields($facets_fields);

    	//$this->arrayMoveElementToTop($facets_fields, 'manufacturer_facet');

    	return $facets_fields;
    }

    public function isSelectedFacetActive()
    {
    	$filterQuery = $this->solrModel->getStandardFilterQuery();

    	$this->filterQuery = $filterQuery;

    	$isFacetActived = false;
    	foreach($filterQuery as $key=>$value) {
    		if(is_array($value) && count($value) > 0) {
    			$isFacetActived = true;
    		}
    	}

    	return $isFacetActived;
    }

    protected function getFilterQuery()
    {
    	if (!$this->filterQuery) {
    		$this->filterQuery = $this->solrModel->getStandardFilterQuery();
    	}
    	return $this->filterQuery;
    }

    public function arrayMoveElementToTop(&$array, $key) {
        $temp = array($key => $array[$key]);
        unset($array[$key]);
        $array = $temp + $array;
    }

    protected function manupulateFacetFields(&$facetData)
    {

    	if (Mage::helper('solrsearch')->getSetting('allow_multiple_filter') > 0)
    	{
    		$queryText = Mage::helper('solrsearch')->getParam('q');

    		$key = Mage::helper('solrsearch')->getKeywordCachedKey($queryText);

    		$originalSolrData = Mage::getSingleton('core/session')->getOriginSolrFacetData();

    		$display_category_as_hierachy = $this->displayCategoryAsHierachy();

    		if (isset($originalSolrData) && isset($originalSolrData[$key])) {

    			$filterQuery = $this->getFilterQuery();

    			$filterQueryKeys = array_keys($filterQuery);

    			foreach ($filterQueryKeys as $facetkey) {
    				if (isset($originalSolrData[$key]['facet_counts']['facet_fields'][$facetkey]) && !empty($originalSolrData[$key]['facet_counts']['facet_fields'][$facetkey]))
    				{
    				    $facetData[$facetkey] = $originalSolrData[$key]['facet_counts']['facet_fields'][$facetkey];
    				}

    				if ($display_category_as_hierachy > 0 && $facetkey == 'category_facet') {
    				    if (isset($originalSolrData[$key]['facet_counts']['facet_fields']['category_path']) && !empty($originalSolrData[$key]['facet_counts']['facet_fields']['category_path'])) {
    				        $facetData['category_path'] = $originalSolrData[$key]['facet_counts']['facet_fields']['category_path'];
    				    }
    				}

    			}

    		}
    		//Update original facet data
    		$originalSolrData[$key]['facet_counts']['facet_fields'] = $facetData;
    		Mage::getSingleton('core/session')->setOriginSolrFacetData($originalSolrData);
    	}
    }

	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getFacesUrl($params=array())
    {
    	$_solrDataArray = $this->getSolrData();

    	$paramss = $this->getRequest()->getParams();

    	if( isset($_solrDataArray['responseHeader']['params']['q']) && !empty($_solrDataArray['responseHeader']['params']['q']) ) {
        	if (isset($paramss['q']) && $paramss['q'] != $_solrDataArray['responseHeader']['params']['q']) {
        		$paramss['q'] = $_solrDataArray['responseHeader']['params']['q'];
        	}
        }

        foreach ($params as $key=>$item) {
        	$key = trim($key);

        	if( in_array($key, array('min', 'max')) ) {
        		if (isset($paramss[$key])) {
        			unset($paramss[$key]);
        			$finalParams = array_merge_recursive($params, $paramss);
        		}
        	}

        	if ($key == 'fq') {
        		foreach ($item as $k=>$v) {
        			if (isset($paramss[$key][$k]) && $v == $paramss[$key][$k]){

        			}else{
        				if( $k == 'price' && isset($paramss[$key][$k]) || $k == 'category' || $k == 'category_id'){
        					unset($paramss[$key][$k]);
        				}
        				$finalParams = array_merge_recursive($params, $paramss);
        			}
        		}
        	}
        }

        if (isset($finalParams['p'])) {
        	$finalParams['p'] = 1;
        }

    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        if (isset($finalParams)) {

        	if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
        		if (isset($finalParams['q'])) {
        			unset($finalParams['q']);
        		}
        		if (isset($finalParams['id'])) {
        			unset($finalParams['id']);
        		}
        	}

        	$urlParams['_query']    = $finalParams;
        }
        return $this->getUrl('*/*/*', $urlParams);
    }

    public function getRemoveAllUrl(){
    	$_solrDataArray = $this->getSolrData();

    	$paramss = $this->getRequest()->getParams();

    	if(!isset($paramss['q'])){
	    	if( isset($_solrDataArray['responseHeader']['params']['q']) && !empty($_solrDataArray['responseHeader']['params']['q']) ) {
	        	if (isset($paramss['q']) && $paramss['q'] != $_solrDataArray['responseHeader']['params']['q']) {
	        		$paramss['q'] = $_solrDataArray['responseHeader']['params']['q'];
	        	}
	        }
    	}

        $finalParams = array();
        if(isset($paramss['q'])) {
        	$finalParams['q'] = $paramss['q'];
        }

        $urlParams = array();
        //$urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;

        if (isset($finalParams)) {

        	if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
        		if (isset($finalParams['q'])) {
        			unset($finalParams['q']);
        		}
        		if (isset($finalParams['id'])) {
        			unset($finalParams['id']);
        		}
        	}

        	$urlParams['_query']    = $finalParams;
        }

        return Mage::getUrl('*/*', $urlParams);
    }

	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getRemoveFacesUrl($key,$value)
    {
        $paramss = $this->getRequest()->getParams();

        $finalParams = $paramss;

        if (is_array($key) && is_array($value) && count($key) == count($value)){
        	$index = 0;
        	foreach ($key as $item)
        	{
        		if (isset($finalParams['fq'][$item]) && !is_array($finalParams['fq'][$item]) && !empty($finalParams['fq'][$item])) {
        			unset($finalParams['fq'][$item]);
        			if ($item == 'category' && isset($finalParams['fq'][$item.'_id'])) {
        				unset($finalParams['fq'][$item.'_id']);
        			}
        		}else if (isset($finalParams['fq'][$item]) && is_array($finalParams['fq'][$item]) && count($finalParams['fq'][$item]) > 0) {
        			foreach ($finalParams['fq'][$item] as $k=>$v) {
        				if ($v == $value) {
        					unset($finalParams['fq'][$item][$k]);
        					if ($item == 'category' && isset($finalParams['fq'][$item.'_id']) && isset($finalParams['fq'][$item.'_id'][$k])) {
        						unset($finalParams['fq'][$item.'_id'][$k]);
        					}
        				}
        			}
        		}

        		$index++;
        	}
        }else{
        	if (isset($finalParams['fq'][$key]) && !is_array($finalParams['fq'][$key]) && !empty($finalParams['fq'][$key])) {
        		unset($finalParams['fq'][$key]);
        		if ($key == 'category' && isset($finalParams['fq'][$key.'_id'])) {
        			unset($finalParams['fq'][$key.'_id']);
        		}
        	}else if (isset($finalParams['fq'][$key]) && is_array($finalParams['fq'][$key]) && count($finalParams['fq'][$key]) > 0) {
        		foreach ($finalParams['fq'][$key] as $k=>$v) {
        			if ($v == $value) {
        				unset($finalParams['fq'][$key][$k]);
        				if ($key == 'category' && isset($finalParams['fq'][$key.'_id']) && isset($finalParams['fq'][$key.'_id'][$k])) {
        					unset($finalParams['fq'][$key.'_id'][$k]);
        				}
        			}
        		}
        	}
        }

    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;

    	if (isset($finalParams)) {

    		if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
    			if (isset($finalParams['q'])) {
    				unset($finalParams['q']);
    			}
    			if (isset($finalParams['id'])) {
    				unset($finalParams['id']);
    			}
    		}

        	$urlParams['_query']    = $finalParams;
        }

        return Mage::getUrl('*/*/*', $urlParams);
    }

    public function parseCategoryPathFacet($categoryPathFaces)
    {

    	$categoryArray = $this->parseCategoryPathToArray($categoryPathFaces);

    	return $this->renderCategoryHierachy($categoryArray);

    }

	public function parseCategoryPathToArray($categoryPathFaces){
		$returnData = array();

		if (is_array($categoryPathFaces)) {
			foreach ($categoryPathFaces as $categoryPath=>$count) {

				$categoryPathArray = $this->pathToArray($categoryPath);

				$rootCatId = (isset ( $categoryPathArray [0] ['id'] )) ? $categoryPathArray [0] ['id'] : 0;

				if ($rootCatId) {
				    $this->rootCatIds [] = $rootCatId;
				}

				$index = 0;

				$parents = array();

				foreach ($categoryPathArray as $key=>$item)
				{
					$categoryName = $item['name'];
					$categoryId = $item['id'];
					$position = $item['position'];

					$categoryItem = array(
					    'id' => $categoryId,
					    'name' => $categoryName,
					    'count' => 0, 'parent_id' => 0, 'position' => $position,
					    'parent_id' => 0,
					    'root_cat_id' => $rootCatId
					);

					if ($index == (count($categoryPathArray) - 1)) {
						$categoryItem['count'] = $count;
					}

					if ($key > 0) {
						$categoryItem['parent_id'] = $categoryPathArray[($key - 1)]['id'];
					}

					$parents[] = $categoryId;

					if (array_key_exists($categoryId, $returnData)) {
						$returnData[$categoryId]['count'] = ($returnData[$categoryId]['count'] + $categoryItem['count']);
					}
					else
					{
						$returnData[$categoryId] = $categoryItem;
					}

					$index++;
				}
			}
		}
		$this->rootCatIds = array_unique ( $this->rootCatIds );
	    return $returnData;
    }
    /**
     * Convert string path to array
     * @param string $path
     * @return array
     */
    public function pathToArray($path) {
    	$chunks = explode('/', $path);
    	$result = array();
    	for ($i = 0; $i < sizeof($chunks) - 1; $i+=2)
    	{
    		//$result[] = array('id' => $chunks[($i+1)], 'name' => $chunks[$i]);
    		//CatId format x:y, x is category id, y is position
    		$catIdArr = explode(':', $chunks[($i+1)]);
    		$result[] = array('id' => $catIdArr[0], 'position' => $catIdArr[1], 'name' => $chunks[$i]);
    	}

    	return $result;
    }

    //output a multi-dimensional array as a nested UL

	protected function renderCategoryHierachy($categoryArray)
	{
		$menuData = array(
				'items' => array(),
				'parents' => array()
		);

		$rootCategoryId = 0;

		$layer = Mage::getSingleton('catalog/layer');
		$_category = $layer->getCurrentCategory();
		$currentCategoryId = $_category->getId();

		if (isset ( $this->filterQuery ['category_facet'] ) && isset ( $this->filterQuery ['category_id'] ) && isset($this->filterQuery ['category_id'] [0]))
		{
		    $currentCategoryId = $this->filterQuery ['category_id'] [0];
		}

		if ($currentCategoryId)
		{
		    if ( isset ( $categoryArray [ $currentCategoryId ] ['root_cat_id'] ) )
		    {
		        $rootCategoryId = $categoryArray [ $currentCategoryId ] ['root_cat_id'];
		    }
		}

		if ($rootCategoryId > 0) 
		{
			usort($categoryArray, array(get_class($this), 'categoryPositionSort'));
		    foreach ( $categoryArray as $menuItem )
		    {
		        if ($menuItem ['root_cat_id'] == $rootCategoryId)
		        {
		            $menuData ['items'] [$menuItem ['id']] = $menuItem;
		            $menuData ['parents'] [$menuItem ['parent_id']] [] = $menuItem ['id'];
		        }
		    }
		} 
		else 
		{
			usort($categoryArray, array(get_class($this), 'categoryPositionSort'));
		    foreach ( $categoryArray as $menuItem ) 
		    {
		        $menuData ['items'] [$menuItem ['id']] = $menuItem;
		        $menuData ['parents'] [$menuItem ['parent_id']] [] = $menuItem ['id'];
		    }
		}

		return $this->buildMenu(0, $menuData);
	}
	/**
	 * Used as callback for usort function to sort array
	 * @param array $a
	 * @param array $b
	 * @return number
	 */
	public function categoryPositionSort( $a, $b )
	{
		return $a['position'] - $b['position'];
	}
	/**
	 * Build category hierachy html
	 * @param int $parentId
	 * @param array $menuData
	 * @return html
	 */
	protected function buildMenu($parentId, $menuData, $level = -1, $path = '')
	{
		$html = '';

		$level ++;
		$path .= $parentId . '-';

		if (isset($menuData['parents'][$parentId]))
		{
			if(!$parentId){
				$html = '<ol class="sf-menu sf-vertical">';
			}else{
				$html = '<ol>';
			}
			$index = 0;
			foreach ($menuData['parents'][$parentId] as $itemId)
			{
				$count = $menuData['items'][$itemId]['count'];

				$categoryName = '';
				if (isset($menuData['items'][$itemId]['name'])) {
					$categoryName = $menuData['items'][$itemId]['name'];
				}

				$facetUrl = $this->getFacesUrl(array('fq'=>array('category' => $categoryName, 'category_id' => $itemId)));

				$parentClassName = (isset ( $menuData ['parents'] [$itemId] )) ? ' parent' : '';

				$classNames = 'facet-item level-' . $level. ' '. $parentClassName;

				if (isset($this->filterQuery['category_facet']) && isset($this->filterQuery['category_id'])
					&& in_array($categoryName, $this->filterQuery['category_facet'])
					&& in_array($itemId, $this->filterQuery['category_id'])
				){
					$classNames .= ' active';
					//$facetUrl = $this->getRemoveFacesUrl(array('category', 'category_id'), array($categoryName, $itemId));
				}

				$formattedCategoryFacet = $this->facetFormat(trim($categoryName));

				if ($count < 1) {
					//$facetUrl = 'javascript:;';
					//$classNames .= ' empty';
				}else{
					$formattedCategoryFacet .= '&nbsp;<span>('.$count.')</span>';
				}

				$classNames .= ' ' . $path . $itemId;

				$classNames = trim($classNames);
				
				$toggleClass = '';
				//Show more less if categories not display as hierachy
				if( $this->displayCategoryAsHierachy() < 1 && $this->_index > $this->getLimit() ) {
					$toggleClass = 'toggle-hide';
				}

				if(!$index){
					$html .= '<li class="first '.$toggleClass.'">' . (($categoryName)?'<a href="'.$facetUrl.'" class="'.$classNames.'">'.$formattedCategoryFacet.'</a>':"");
				}else{
					$html .= '<li class="'.$toggleClass.'">' . (($categoryName)?'<a href="'.$facetUrl.'" class="'.$classNames.'">'.$formattedCategoryFacet.'</a>':"");
				}
				$this->_index++;
				// find childitems recursively
				$html .= $this->buildMenu($itemId, $menuData, $level, $path);

				$html .= '</li>';
				$index++;
			}
			
			if( $this->displayCategoryAsHierachy() < 1 && $this->_index > ($this->getLimit() + 1) ) {
				$html .= '<li class="more-less plus"><a class="sb-more-less-button" href="#more" onclick="sbtogglemoreless(this)" rel="'.$this->__('Show Less').'">'.$this->__('Show More').'</a></li>';
			}
			
			$html .= '</ol>';
		}

		return $html;
	}
	public function facetFormat($text) {
	    $returnText = $text;
	    if (strrpos($text, '_._._') > -1) {
	        $returnText = str_replace('_._._', '/', $text);
	    }
	    return $this->htmlEscape($returnText);
	}
	protected function getHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getFacesUrl(array('fq'=>array('category'=>str_replace('_._._', '/', $key))));
		}else{
			return 'javascript:;';
		}
	}

	protected function getRemoveHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getRemoveFacesUrl('category', str_replace('_._._', '/', $key));
		}else{
			return 'javascript:;';
		}
	}

	public function getPriceFacets()
	{
		return $this->getChildHtml('solr_price_facets');
	}

	public function isFieldRange($fieldName){
		if (!empty($fieldName)) {
			$rangeFields = $this->solrModel->getRangeFields();
			if (is_array($rangeFields)) {
				return in_array($fieldName, $rangeFields);
			}
		}
		return false;
	}

	public function getRangeFacets($fieldName)
	{
		if ($fieldName != 'price_decimal') {
			$rangeBlock = $this->getChild('solr_range_facets');
			$rangeBlock->setRangeField($fieldName);
			return $rangeBlock->toHtml();
		}
	}

	public function isLayerNavigationActive()
	{
		$returnData = $this->getSolrData();

		if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
			return true;
		}

		$filterQuery = $this->getFilterQuery();
		if(!empty($filterQuery)) {
			return true;
		}

		return false;
	}

	public function isShoppingOptionsActive()
	{
		$facetFields = $this->getFacetFields();
		$active = false;
		foreach ($facetFields as $key=>$facet){
			if (count($facet) > 0) {
				$active = true;
				break;
			}
		}

		return $active;
	}

	public function formatFacetPrice($facetPriceRange){
		$priceArray = explode('TO', $facetPriceRange);

		$formattedPriceRange = $facetPriceRange;

		if (isset($priceArray[0]) && isset($priceArray[0])) {
			$currencySymboy = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
			$currencyPositionSetting = Mage::helper('solrsearch')->getSetting('currency_position');
			if ($currencyPositionSetting > 0) {
				$formattedPriceRange = $currencySymboy.trim($priceArray[0]).'TO'.$currencySymboy.trim($priceArray[1]);
			}else{
				$formattedPriceRange = trim($priceArray[0]).$currencySymboy.'TO'.trim($priceArray[1]).$currencySymboy;
			}

		}
		return $formattedPriceRange;
	}
	
	/**
	 * Build category hierachy html
	 * @param int $parentId
	 * @param array $menuData
	 * @return html
	 */
	protected function buildMenuAdv($parentId, $menuData, $level = -1, $path='')
	{
		$ulDisplay = 'none';
		$bgPos = 'left';
		if ( isset($this->filterQuery['category_facet']) && isset($this->filterQuery['category_id']) )
		{
			$ulDisplay = 'block';
			$bgPos = 'right';
		}
	
		$html = '';
		$level++;
		$path .= $parentId.'-';
		if (isset($menuData['parents'][$parentId]))
		{
			if(!$parentId){
				$html = '<ul id="sidebar-nav-menu">';
			}else{
				$html = '<ul expanded="0" style="margin-left: 5px; padding-left: 10px; display: '.$ulDisplay.';" class="level'.$level.'">';
			}
			$index = 0;
			foreach ($menuData['parents'][$parentId] as $itemId)
			{
				$count = $menuData['items'][$itemId]['count'];
	
				$categoryName = '';
				if (isset($menuData['items'][$itemId]['name'])) {
					$categoryName = $menuData['items'][$itemId]['name'];
				}
	
				$facetUrl = $this->getFacesUrl(array('fq'=>array('category' => $categoryName, 'category_id' => $itemId)));
				if ($count < 1 && isset($menuData['parents'][$itemId]))
				{
					//$childrenIds = $menuData['parents'][$itemId];
					$childrenIds = array();//$menuData['parents'][$itemId];
					$this->getChildrenIds($itemId, $menuData, $childrenIds);
					if(is_array($childrenIds) && count($childrenIds) > 0)
					{
						$facetUrl = $this->getFacesUrl(array('fq'=>array('category' => $categoryName, 'category_id' => $childrenIds)));
					}
				}
	
				$parentClassName = '';
	
				$className = 'level-'.$level.$parentClassName;
	
	
				if (isset($this->filterQuery['category_facet']) && isset($this->filterQuery['category_id'])
						&& in_array($categoryName, $this->filterQuery['category_facet'])
						&& in_array($itemId, $this->filterQuery['category_id'])
				){
					$className .= ' active';
				}
	
				$formattedCategoryFacet = $this->facetFormat(trim($categoryName));
	
				$parentClassName = (isset($menuData['parents'][$itemId]))?' parent':'';
	
	
				if($level < 1){$className .= ' active-bk';}
	
				$className .= ' '.$path.$itemId;
	
				$classNames = '';
	
				if ($count < 1) {
					$classNames .= ' empty';
				}else{
					$formattedCategoryFacet .= '&nbsp;<span>('.$count.')</span>';
				}
	
				$height = 0;
				if($parentClassName)
				{
					$height = 10;
				}
	
				$itemContent = '<span style="width: 8px; height: '.$height.'px; background-position: '.$bgPos.' center;" onclick="sbsexpandmenu(this.parentNode)" class="arrow">&nbsp;</span>
								<div style="margin-left: 14px;" class="collapsible-wrapper">
									<a style="background:none" href="'.$facetUrl.'"><span class="category_name">'.$formattedCategoryFacet.'</span></a>
								</div>';
	
				if(!$index){
					$html .= '<li class="first '.$className.'" style="margin-left: 0px;padding:3px 0">' . $itemContent;
				}else{
					$html .= '<li class="'.$className.'" style="margin-left: 0px;padding:3px 0">' . $itemContent;
				}
	
				// find childitems recursively
				$html .= $this->buildMenu($itemId, $menuData, $level, $path);
	
				$html .= '</li>';
				$index++;
			}
			$html .= '</ul>';
		}
	
		return $html;
	}
	public function getChildrenIds($parentid, $menuData, &$childrenIds)
	{
		if ( isset($menuData['parents'][$parentid]) )
		{
			$ids = $menuData['parents'][$parentid];
				
			if(is_array($ids) && count($ids) > 0)
			{
				$childrenIds = array_merge($childrenIds, $ids);
				foreach ($ids as $id)
				{
					$this->getChildrenIds($id, $menuData, $childrenIds);
				}
			}
		}
	}
}