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
class SolrBridge_Solrsearch_Model_Data
{
	public $ignoreFields = array();

	public $includedBrandAttributeCodes = array();

	public $includedSearchWeightAttributeCodes = array();

	public $textSearch = array();

	public $textSearchText = array();

	public $store = null;

	public $allowCategoryIds = array();

	public $categories = array();

	public $filterableAttributes = array();

	protected $_productAttributeCollection = null;

	public function setBrandAttributes($attributes)
	{
		$this->includedBrandAttributeCodes = $attributes;
	}

	public function setSearchWeightAttributes($attributes)
	{
		$this->includedSearchWeightAttributeCodes = $attributes;
	}

	public function setIgnoreFields($ignoreFields)
	{
		$this->ignoreFields = $ignoreFields;
	}

	public function setStore($store)
	{
		$this->store = $store;
	}

	public function setAllowCategoryIds($ids)
	{
		$this->allowCategoryIds = $ids;
	}

	public function getTextSearch()
	{
		return $this->textSearch;
	}

	public function getTextSearchText()
	{
		return $this->textSearchText;
	}

	public function pushTextSearch($rawstring) {
		if (! empty ( $rawstring )) {
			$string = trim ( $rawstring );
			if (! in_array ( $string, $this->textSearch )) {
				$this->textSearch [] = $string;
			}
		}
	}

	public function pushTextSearchText($rawstring) {
		if (! empty ( $rawstring )) {
			$string = trim ( $rawstring );
			if (! in_array ( $string, $this->textSearchText )) {
				$this->textSearchText [] = $string;
			}
		}
	}
    /**
     * Check to see if the attribute value will be pushed to Solr or Not
     * @param unknown $atributeObj
     * @return boolean
     */
	public function isAttributeIgnore($attribute)
	{
		$attributeCode = $attribute['attribute_code'];
		if ($attributeCode == 'visibility')
		{
			return false;
		}
		if (!empty($this->includedSearchWeightAttributeCodes) && in_array($attributeCode, $this->includedSearchWeightAttributeCodes))
		{
		    return false;
		}

		if (!empty($this->includedBrandAttributeCodes)  && in_array($attributeCode, $this->includedBrandAttributeCodes))
		{
		    return false;
		}

		if ( $attribute['is_searchable'] > 0 ) {
		    return false;
		}

		if ( $attribute['is_filterable'] > 0 || $attribute['is_filterable_in_search'] > 0 ) {
		    return false;
		}

		if ( (isset($attribute['solr_search_field_weight']) && !empty($attribute['solr_search_field_weight'])) ||
		     (isset($attribute['solr_search_field_boost']) && !empty($attribute['solr_search_field_boost'])) )
		{
		    return false;
		}

		if ($attribute['used_for_sort_by'] > 0) {
		    return false;
		}

	    if ($attribute['is_filterable_in_search'] > 0) {
		    return false;
		}

		return true;//Meaning the attribute is ignored
	}

	public function dispatchAttribute($attribute)
	{
	    Mage::dispatchEvent('solrbridge_solrsearch_attribute_load_for_index', array('attribute'=>$attribute));
	}

	public function getProductAttributeCollection()
	{
	    if ($productAttributeCollection = Mage::registry('solrbridge_solrsearch_indexing_product_attribute_collection'))
	    {
	        $this->_productAttributeCollection = $productAttributeCollection;
	    }

	    if ( is_null($this->_productAttributeCollection) )
	    {
	        $collection = Mage::getResourceModel('catalog/product_attribute_collection');
	        $collection
	        ->setItemObjectClass('catalog/resource_eav_attribute')
	        ->addStoreLabel($this->store->getId())
	        ->setOrder('position', 'ASC');
	        $this->_productAttributeCollection = $collection;

	        Mage::register('solrbridge_solrsearch_indexing_product_attribute_collection', $this->_productAttributeCollection);
	    }

	    return $this->_productAttributeCollection;
	}
	
	public function getProductAttributesData(&$_product, $attributes)
	{
		$docData = array();
		
		//print_r($attributes);
	
		foreach ($attributes as $attribute)
		{
			$backendType = $attribute['backend_type'];
			$frontEndInput = $attribute['frontend_input'];
			$attributeCode = $attribute['attribute_code'];
			
			if ($attribute['frontend_input'] == 'media_image')
			{
				$_product->setData($attributeCode, $attribute['value']);
				continue;
			}
	
			if ($this->isAttributeIgnore($attribute)) {
				continue;
			}
	
			if ($backendType == 'int') {
				$backendType = 'varchar';
			}
	
			$attributeKey = $attributeCode.'_'.$backendType;
	
			$attributeKeyFacets = $attributeCode.'_facet';
			
			/*
			if (!is_array($atributeObj->getFrontEnd()->getValue($_product))){
				$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
			} else {
				$attributeVal = $atributeObj->getFrontEnd()->getValue($_product);
				$attributeVal = implode(' ', $attributeVal);
			}
			*/
			$attributeVal = $attribute['value'];
			
			$_product->setData($attributeCode, $attributeVal);
			
			if (!empty($attributeVal) && $attributeCode == 'name')
			{
				$docData[$attributeKey] = $attributeVal;
				$docData[$attributeCode.'_boost'] = $attributeVal;
				$docData[$attributeCode.'_boost_exact'] = $attributeVal;
				$docData[$attributeCode.'_relative_boost'] = $attributeVal;
			}
	
			//Generate sort attribute
			if ($attribute['used_for_sort_by'] && !empty($attributeVal)) 
			{
				$sortValue = $_product->getData($attributeCode);
				if (!empty($sortValue)) {
					if ($backendType == 'datetime')
					{
						$sortValue = date("Y-m-d\TG:i:s\Z", strtotime($sortValue));
						$docData['sort_'.$attributeCode.'_'.$backendType] = $sortValue;
						$docData[$attributeKey] = $sortValue;
					}
					else
					{
						$docData['sort_'.$attributeCode.'_'.$backendType] = $sortValue;
						$docData[$attributeKey] = $attributeVal;
					}
				}
			}
	
			//Generate product search weight value
			if (in_array($attributeCode, $this->includedSearchWeightAttributeCodes)) {
				if (!empty($attributeVal) && is_numeric($attributeVal)) {
					$docData['product_search_weight_int'] = $attributeVal;
				}
			}
	
			//Start collect values
			if (empty($attributeVal) || $attributeVal == 'No' || $attributeVal == 'None') {
				unset($docData[$attributeKey]);
				unset($docData[$attributeKeyFacets]);
				unset($docData[$attributeCode.'_boost']);
				unset($docData[$attributeCode.'_boost_exact']);
				unset($docData[$attributeCode.'_relative_boost']);
			}else{
				$attributeValFacets = array();
				if($frontEndInput == 'multiselect') {
					$attributeValFacetsArray = @explode(',', $attributeVal);
					//$attributeValFacetsArray = @explode('|', $attributeVal);
					$attributeValFacets = array();
					foreach ($attributeValFacetsArray as $val) {
						$attributeValFacets[] = trim($val);
					}
				} else {
					$attributeValFacets[] = trim($attributeVal);
				}
	
				if ($backendType == 'datetime') {
					$attributeVal = date("Y-m-d\TG:i:s\Z", strtotime($attributeVal));
				}
	
				if( $attribute['is_searchable'] > 0 )
				{
					if (!in_array($attributeVal, $this->textSearch) && $attributeVal != 'None' && $attributeCode != 'status' && $attributeCode != 'sku' && $attributeCode != 'price'){
						if (strlen($attributeVal) > 255) {
							$this->pushTextSearchText ( $attributeVal );
						}else{
							$this->pushTextSearch ( $attributeVal );
						}
					}
				}
	
				if ( $attribute['is_filterable'] > 0 || $attribute['is_filterable_in_search'] > 0 ) 
				{
					if ($backendType != 'text' && !in_array($attributeCode, $this->ignoreFields))
					{
						$boostAttributeVal = Mage::helper('solrsearch')->getPreparedBoostText($attributeVal);
						 
						$docData[$attributeCode.'_boost'] = $boostAttributeVal;
	
						$docData[$attributeCode.'_boost_exact'] = $boostAttributeVal;
	
						$docData[$attributeCode.'_relative_boost'] = $boostAttributeVal;
	
						$docData[$attributeCode.'_text'] = $attributeVal;
	
						$docData[$attributeKey] = $attributeVal;
	
						//$this->pushTextSearch ( $atributeObj->getStoreLabel().' '.$attributeVal );
					}
				}
	
				if (
						(isset($attribute['solr_search_field_weight']) && !empty($attribute['solr_search_field_weight']))
						||
						(isset($attribute['solr_search_field_boost']) && !empty($attribute['solr_search_field_boost']))
				) {
						
					$boostAttributeVal = Mage::helper('solrsearch')->getPreparedBoostText($attributeVal);
						
					$docData[$attributeCode.'_boost'] = $boostAttributeVal;
	
					$docData[$attributeCode.'_boost_exact'] = $boostAttributeVal;
	
					$docData[$attributeCode.'_relative_boost'] = $boostAttributeVal;
	
					$docData[$attributeKey] = $attributeVal;
				}
	
				if (
						(isset($attribute['is_filterable_in_search']) && !empty($attribute['is_filterable_in_search']) && $attributeValFacets != 'No' && $attributeKey != 'price_decimal' && $attributeKey != 'special_price_decimal')
				) {
					$docData[$attributeKeyFacets] = $attributeValFacets;
				}
			}
		}
	
		return $docData;
	}

	public function getProductAttributesDataOld($_product)
	{
	    $oldStore = Mage::app ()->getStore ();
	    Mage::app ()->setCurrentStore ( $this->store );
	    $currentLocaleCode = Mage::app()->getLocale()->getLocaleCode();
	    $storeLocaleCode = Mage::getStoreConfig('general/locale/code', $this->store->getId());
	    if ($storeLocaleCode) {
	        Mage::getSingleton('core/translate')->setLocale($storeLocaleCode)->init('frontend', true);
	    }

		$docData = array();

		//$productAttributes = $this->getProductAttributeCollection();
		$productAttributes = $_product->getAttributes();

		foreach ($productAttributes as $atributeObj)
		{
			$atributeObj->setStoreId($this->store->getId());

			//Dispatch attribute object other modules using
			//$this->dispatchAttribute($atributeObj);

			$backendType = $atributeObj->getBackendType();
			$frontEndInput = $atributeObj->getFrontendInput();
			$attributeCode = $atributeObj->getAttributeCode();
			$attributeData = $atributeObj->getData();

			if ($this->isAttributeIgnore($atributeObj)) {
			    continue;
			}

			if ($backendType == 'int') {
				$backendType = 'varchar';
			}

			$attributeKey = $attributeCode.'_'.$backendType;

			$attributeKeyFacets = $attributeCode.'_facet';

			if (!is_array($atributeObj->getFrontEnd()->getValue($_product))){
				$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
			} else {
				$attributeVal = $atributeObj->getFrontEnd()->getValue($_product);
				$attributeVal = implode(' ', $attributeVal);
			}

			//Generate sort attribute
			if ($atributeObj->getUsedForSortBy() && !empty($attributeVal)) {
				$sortValue = $_product->getData($attributeCode);
				if (!empty($sortValue)) {
				    if ($backendType == 'datetime')
				    {
				        $sortValue = date("Y-m-d\TG:i:s\Z", strtotime($sortValue));
				        $docData['sort_'.$attributeCode.'_'.$backendType] = $sortValue;
				        $docData[$attributeKey] = $sortValue;
				    }
				    else
				    {
				        $docData['sort_'.$attributeCode.'_'.$backendType] = $sortValue;
				        $docData[$attributeKey] = $attributeVal;
				    }
				}
			}

			//Generate product search weight value
			if (in_array($attributeCode, $this->includedSearchWeightAttributeCodes)) {
				if (!empty($attributeVal) && is_numeric($attributeVal)) {
					$docData['product_search_weight_int'] = $attributeVal;
				}
			}

			//Start collect values
			if (empty($attributeVal) || $attributeVal == 'No' || $attributeVal == 'None') {
				unset($docData[$attributeKey]);
				unset($docData[$attributeKeyFacets]);
				unset($docData[$attributeCode.'_boost']);
				unset($docData[$attributeCode.'_boost_exact']);
				unset($docData[$attributeCode.'_relative_boost']);
			}else{
				$attributeValFacets = array();
				if($frontEndInput == 'multiselect') {
					$attributeValFacetsArray = @explode(',', $attributeVal);
					$attributeValFacets = array();
					foreach ($attributeValFacetsArray as $val) {
						$attributeValFacets[] = trim($val);
					}
				} else {
					$attributeValFacets[] = trim($attributeVal);
				}

				if ($backendType == 'datetime') {
					$attributeVal = date("Y-m-d\TG:i:s\Z", strtotime($attributeVal));
				}

				if( $atributeObj->getIsSearchable() )
				{
				    if (!in_array($attributeVal, $this->textSearch) && $attributeVal != 'None' && $attributeCode != 'status' && $attributeCode != 'sku' && $attributeCode != 'price'){
				        if (strlen($attributeVal) > 255) {
				            $this->pushTextSearchText ( $attributeVal );
				        }else{
				            $this->pushTextSearch ( $attributeVal );
				        }
				    }
				}

                if (in_array($attributeCode, $this->getFilterableAtributes())) {
                    if ($backendType != 'text' && !in_array($attributeCode, $this->ignoreFields))
                    {
                    	$boostAttributeVal = Mage::helper('solrsearch')->getPreparedBoostText($attributeVal);
                    	
                        $docData[$attributeCode.'_boost'] = $boostAttributeVal;

                        $docData[$attributeCode.'_boost_exact'] = $boostAttributeVal;

                        $docData[$attributeCode.'_relative_boost'] = $boostAttributeVal;

                        $docData[$attributeCode.'_text'] = $attributeVal;

                        $docData[$attributeKey] = $attributeVal;

                        $this->pushTextSearch ( $atributeObj->getStoreLabel().' '.$attributeVal );
                    }
                }

				if (
				(isset($attributeData['solr_search_field_weight']) && !empty($attributeData['solr_search_field_weight']))
				||
				(isset($attributeData['solr_search_field_boost']) && !empty($attributeData['solr_search_field_boost']))
				) {
					
					$boostAttributeVal = Mage::helper('solrsearch')->getPreparedBoostText($attributeVal);
					
					$docData[$attributeCode.'_boost'] = $boostAttributeVal;

					$docData[$attributeCode.'_boost_exact'] = $boostAttributeVal;

					$docData[$attributeCode.'_relative_boost'] = $boostAttributeVal;

					$docData[$attributeKey] = $attributeVal;
				}

				if (
				(isset($attributeData['is_filterable_in_search']) && !empty($attributeData['is_filterable_in_search']) && $attributeValFacets != 'No' && $attributeKey != 'price_decimal' && $attributeKey != 'special_price_decimal')
				) {
					$docData[$attributeKeyFacets] = $attributeValFacets;
				}
			}
		}

		Mage::app ()->setCurrentStore ( $oldStore );
		Mage::getSingleton('core/translate')->setLocale($currentLocaleCode)->init('frontend', true);

		return $docData;
	}

	public function getFilterableAtributes()
	{
	    if (!empty($this->filterableAttributes))
	    {
	        return $this->filterableAttributes;
	    }
	    $oldStore = Mage::app ()->getStore ();
	    Mage::app ()->setCurrentStore ( $this->store );

	    $cachedKey = 'solrbridge_solrsearch_indexing_filter_attributes_' . $this->store->getId ();

	    if (false !== ($returnData = Mage::app ()->getCache ()->load ( $cachedKey ))) {
	        Mage::app ()->setCurrentStore ( $oldStore );
	        $this->filterableAttributes = unserialize ( $returnData );
	        return $this->filterableAttributes;
	    }

	    $filterableAttributes = array();
	    $atts = Mage::getModel('catalog/layer')->getFilterableAttributes();
	    foreach ($atts as $att)
	    {
	        $filterableAttributes[] = $att->getAttributeCode();
	    }

	    if (! empty ( $filterableAttributes )) {
	        Mage::app ()->getCache ()->save ( serialize ( $filterableAttributes ), $cachedKey, array ('solrbridge_solrsearch_indexing') );
	    }
	    Mage::app ()->setCurrentStore ( $oldStore );
	    $this->filterableAttributes = $filterableAttributes;
	    return $this->filterableAttributes;
	}

	public function prepareCategoriesData($_product, &$docData)
	{
		$store = $this->store;

		//Prepare loading categories into caches
		if ( !isset( $this->categories[ $store->getId() ] ) ) {
		    $this->loadAllCategoriesByStore($store);
		}

		//is category name searchable
		$solr_include_category_in_search = Mage::helper('solrsearch')->getSetting('solr_search_in_category');
		//use category for facets
		$use_category_as_facet = Mage::helper('solrsearch')->getSetting('use_category_as_facet');

		//Calculate allow categories
		if( !isset($this->allowCategoryIds[$store->getId()]) )
		{
			$this->allowCategoryIds[$store->getId()] = ($allowCatIds = $this->getAllowCategoriesByStore($store))?$allowCatIds:array();
		}

		$cats = $_product->getCategoryIds();
		$catNames = array();
		$catNamesSearch = array();
		$categoryPaths = array();
		$categoryIds = array();

		/*
		foreach ($cats as $category_id)
		{
		    $storeid = $this->store->getId();
		    if (in_array($category_id, $this->allowCategoryIds[$storeid]))
		    {
		        $_cat = $this->getLoadedCategory($category_id, $storeid);

		        if ( is_array($_cat) && intval($_cat['is_active']) > 0 && intval($_cat['include_in_menu']) > 0 )
		        {
		            $catNames[] = $_cat['name'].'/'.$_cat['entity_id'];
		            $catNamesSearch[] = $_cat['name'];
		            $categoryPaths[] = $this->getCategoryPath($_cat, $this->store);
		            $categoryIds[] = $_cat['entity_id'];
		        }
		    }
		}
        */
		//* The old way, slow but it was working well
		foreach ($cats as $category_id)
		{
			$storeid = $this->store->getId();
			if (in_array($category_id, $this->allowCategoryIds[$storeid]))
			{
				//$_cat = Mage::getModel('catalog/category')->setStoreId($storeid)->load($category_id) ;
			    $_cat = $this->getLoadedCategory($category_id, $storeid);

				if ( $_cat && $_cat->getIsActive() && $_cat->getIncludeInMenu() )
				{
					$catNames[] = $_cat->getName().'/'.$_cat->getId().':'.$_cat->getPosition();
					$catNamesSearch[] = $_cat->getName();
					$categoryPaths[] = $this->getCategoryPath($_cat, $this->store);
					$categoryIds[] = $_cat->getId();
				}
			}
		}


		if ($use_category_as_facet) {
			$docData['category_facet'] = $catNames;
			$docData['category_text'] = $catNamesSearch;
			$docData['category_boost'] = $catNamesSearch;
			$docData['category_boost_exact'] = $catNamesSearch;
			$docData['category_relative_boost'] = $catNamesSearch;
		}
		$docData['category_path'] = $categoryPaths;
		$docData['category_id'] = $categoryIds;

		//Extend text search
		if ($solr_include_category_in_search > 0)
		{
			$this->textSearch = array_merge($this->textSearch, $catNamesSearch);
		}
		return array(
				'catNames' => $catNames,
				'catPaths' => $categoryPaths,
				'catIds'   => $categoryIds,
		);
	}

	public function prepareTagsData($_product, &$docData)
	{
		//use tags for search and facets
		$use_tags_for_search = Mage::helper('solrsearch')->getSetting('use_tags_for_search');

		//Use product tags for search
		if ($use_tags_for_search) {
			$tagNames = $this->getProductTags($_product);
			$docData['product_tags_facet'] = $tagNames;
			$docData['product_tags_boost'] = $tagNames;
			$docData['product_tags_boost_exact'] = $tagNames;
			$docData['product_tags_relative_boost'] = $tagNames;
			$this->textSearch = array_merge($this->textSearch, $tagNames);
		}
	}
	/**
	 * Get product tags
	 * @param unknown $product
	 * @return multitype:NULL
	 */
	public function getProductTags($product)
	{
		$productId = $product->getId();
		$tags = array();
		if ($productId > 0) {
			$collection = Mage::getModel('tag/tag')
			->getResourceCollection()
			->addProductFilter($productId)
			->addPopularity()
			->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED);
			foreach ($collection as $tag) {
				$tags[] = $tag->getName();
			}
		}
		return $tags;
	}

	/**
	 * This is slow, but it was working well
	 * Get category path
	 * @param unknown_type $category
	 */
	public function getCategoryPath($category, $store)
	{
		$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId().':'.$category->getData('position');
		while ($category->getParentId() > 0)
		{
			$parentCategory = $category->getParentCategory();
			//$category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($parentCategory->getId());

			$category = $this->getLoadedCategory($parentCategory->getId(), $store->getId());

			if (in_array($category->getId(), $this->allowCategoryIds[$store->getId()]))
			{
				if ( $category && $category->getIsActive() && $category->getIncludeInMenu() )
				{
					$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId().':'.$category->getData('position').'/'.$categoryPath;
				}
			}
		}
		return trim($categoryPath, '/');
	}

	/**
	 * Get category path
	 * @param unknown_type $category
	 */
	public function getCategoryPathNew($category, $store)
	{
	    $categoryPath = str_replace('/', '_._._',$category['name']).'/'.$category['entity_id'];

	    $parentIds = (array)$category['parent_ids'];

	    foreach ($parentIds as $parentId)
	    {
	        if ($parentId > 0)
	        {
	            $category = $this->getLoadedCategory($parentId, $store->getId());

	            if (in_array($category['entity_id'], $this->allowCategoryIds[$store->getId()]))
	            {
	                if ( is_array($category) && intval($category['is_active']) > 0 && intval($category['include_in_menu']) > 0 )
	                {
	                    $categoryPath = str_replace('/', '_._._',$category['name']).'/'.$category['entity_id'].'/'.$categoryPath;
	                }
	            }
	        }
	    }

	    return trim($categoryPath, '/');
	}

	/**
	 * Get category path
	 * @param unknown_type $category
	 */
	public function getCategoryPathBk($category, $store)
	{

		$categoryPath = '';

		$parentCategories = $category->getParentCategories();

		foreach ($parentCategories as $parentCat)
		{
			if (in_array($parentCat->getId(), $this->allowCategoryIds[$store->getId()]))
			{
				if ( $parentCat && $parentCat->getIsActive())
				{
					$categoryPath .= '/'.str_replace('/', '_._._',$parentCat->getName()).'/'.$parentCat->getId();
				}
			}
		}

		return trim($categoryPath, '/');
	}

	/**
	 * Get allow categories by store
	 * @param Mage_Core_Model_Store $store
	 * @return array
	 */
	public function getAllowCategoriesByStore($store)
	{
		$rootCatId = $store->getRootCategoryId();
	
		$rootCat = Mage::getModel('catalog/category')->load($rootCatId);
	
		$allowCatIds = Mage::getModel('catalog/category')->getResource()->getChildren($rootCat, true);
	
		$excludedCategoriesIds = Mage::helper('solrsearch')->getSetting('excluded_categories');
		$excludedCategoriesIdsArray = array();
	
		if (!empty($excludedCategoriesIds)) {
	
			$excludedCategoriesIdsArray = explode(',', trim($excludedCategoriesIds, ','));
			//Loaded categories recusive for excluding
			$recusiveExcludedCategory = Mage::helper('solrsearch')->getSetting('excluded_categories_recusive');
	
			if (isset($recusiveExcludedCategory) && intval($recusiveExcludedCategory) > 0) {
	
				$excludedChildrenCategoriesIdsArray = array();
	
				foreach ( $excludedCategoriesIdsArray as $catId ) {
					$parentCat = Mage::getModel('catalog/category')->load($catId);
					$excludedChildrenCategoriesIds = Mage::getModel('catalog/category')->getResource()->getChildren($parentCat, true);
					if (count($excludedChildrenCategoriesIds)) {
						$excludedChildrenCategoriesIdsArray = array_merge($excludedChildrenCategoriesIdsArray, $excludedChildrenCategoriesIds);
					}
				}
				//Merge categories id from settings and its children,
				$excludedCategoriesIdsArray = array_merge($excludedCategoriesIdsArray, $excludedChildrenCategoriesIdsArray);
			}
	
			if (count($excludedCategoriesIdsArray)) {
				$allowCatIds = array_diff($allowCatIds, $excludedCategoriesIdsArray);
			}
		}
	
		return $allowCatIds;
	}
	/**
	 * Get allow categories by store
	 * @param Mage_Core_Model_Store $store
	 * @return array
	 */
	public function getAllowCategoriesByStoreOld($store)
	{
		$cachedKey = 'solrbridge_solrsearch_indexing_allowcategories_' . $store->getId ();

		if (false !== ($returnData = Mage::app ()->getCache ()->load ( $cachedKey ))) {
			return unserialize ( $returnData );
		}

		$rootCatId = $store->getRootCategoryId();

		$rootCat = Mage::getModel('catalog/category')->load($rootCatId);

		$allowCatIds = Mage::getModel('catalog/category')->getResource()->getChildren($rootCat, true);

		$excludedCategoriesIds = Mage::helper('solrsearch')->getSetting('excluded_categories');
		$excludedCategoriesIdsArray = array();

		if (!empty($excludedCategoriesIds)) {

			$excludedCategoriesIdsArray = explode(',', trim($excludedCategoriesIds, ','));
			//Loaded categories recusive for excluding
			$recusiveExcludedCategory = Mage::helper('solrsearch')->getSetting('excluded_categories_recusive');

			if (isset($recusiveExcludedCategory) && intval($recusiveExcludedCategory) > 0) {

				$excludedChildrenCategoriesIdsArray = array();

				foreach ( $excludedCategoriesIdsArray as $catId ) {
					$parentCat = Mage::getModel('catalog/category')->load($catId);
					$excludedChildrenCategoriesIds = Mage::getModel('catalog/category')->getResource()->getChildren($parentCat, true);
					if (count($excludedChildrenCategoriesIds)) {
						$excludedChildrenCategoriesIdsArray = array_merge($excludedChildrenCategoriesIdsArray, $excludedChildrenCategoriesIds);
					}
				}
				//Merge categories id from settings and its children,
				$excludedCategoriesIdsArray = array_merge($excludedCategoriesIdsArray, $excludedChildrenCategoriesIdsArray);
			}

			if (count($excludedCategoriesIdsArray)) {
				$allowCatIds = array_diff($allowCatIds, $excludedCategoriesIdsArray);
			}
		}

		if (! empty ( $allowCatIds )) {
			Mage::app ()->getCache ()->save ( serialize ( $allowCatIds ), $cachedKey, array ('solrbridge_solrsearch_indexing') );
		}

		return $allowCatIds;
	}

	/**
	 * Load all categories of the store to cache
	 * @param unknown $store
	 */
	public function loadAllCategoriesByStore($store)
	{
	    if ($loadedCategoriesCollection = Mage::registry('solrbridge_solrsearch_indexing_loaded_category_collection'))
	    {
	        $this->categories = $loadedCategoriesCollection;
	    }
	    else
	    {
	        $rootCatId = $store->getRootCategoryId();

	        $categories = Mage::getModel('catalog/category')->getCategories($rootCatId);

	        $this->loadCategories($categories);

	        Mage::unregister('solrbridge_solrsearch_indexing_loaded_category_collection');
	        Mage::register('solrbridge_solrsearch_indexing_loaded_category_collection', $this->categories);
	    }

	    return $this->categories;
	}
	public function loadCategories($categories)
	{
	    foreach($categories as $category) {
	        $catId = $category->getId();
	        $cat = Mage::getModel('catalog/category')->setStoreId($this->store->getId())->load($catId);
	        $this->categories[$this->store->getId()][$catId] = $cat;
	        if($category->hasChildren()) {
	            $children = Mage::getModel('catalog/category')->getCategories($catId);
	            $this->loadCategories($children);
	        }
	    }
	}
	/**
	 * get loaded category object from cache
	 * @param int $catid
	 * @param int $storeid
	 * @return array|boolean
	 */
	public function getLoadedCategory($catid, $storeid)
	{
	    if ( isset( $this->categories[$storeid][$catid] ) )
	    {
	        return $this->categories[$storeid][$catid];
	    }
	    return Mage::getModel('catalog/category')->setStoreId($storeid)->load($catid);
	}

	public function prepareFinalProductData($_product, &$docData)
	{
		$store = $this->store;
		//Remove store from Product Url
		$remove_store_from_url = Mage::helper('solrsearch')->getSetting('remove_store_from_url');

		if (intval($remove_store_from_url) > 0) {
			$params['_store_to_url'] = false;
			$productUrl = $_product->getUrlInStore($_product, $params);
			$baseurl = $store->getBaseUrl();
			$productUrl = str_replace($baseurl, '/', $productUrl);
		}else{
			$productUrl = $_product->getProductUrl();
		}

		if (strpos($productUrl, 'solrbridge.php')) {
			$productUrl = str_replace('solrbridge.php', 'index.php', $productUrl);
		}

		$sku = $_product->getSku();
		if (empty($sku))
		{
			$_product = $_product->setStoreId( $store->getId() )->load( $_product->getId() );
			$sku = $_product->getSku();
		}
		$this->pushTextSearch ( $sku );
		$this->pushTextSearch ( str_replace(array('-', '_'), '', $sku) );

		$docData['url_path_varchar'] = $productUrl;
		$productName = $_product->getName();
		
		if (empty($productName))
		{
			$_product = $_product->setStoreId( $store->getId() )->load( $_product->getId() );
			$productName = $_product->getName();
			$this->pushTextSearch ( $productName );
		}
		
		$docData['name_varchar'] = $productName;
		$docData['name_boost'] = $productName;
		$docData['name_boost_exact'] = $productName;
		$docData['name_relative_boost'] = $productName;

		$docData['attribute_set_varchar'] = Mage::getModel('eav/entity_attribute_set')->load($_product->getAttributeSetId())->getAttributeSetName();
		$this->pushTextSearch ( $docData['attribute_set_varchar'] );

		$catIndexPosition = $_product->getData('cat_index_position');

		if (!empty($catIndexPosition) && is_numeric($catIndexPosition)) {
			$docData['sort_position_decimal'] = floatval($catIndexPosition);
		}else{
			$docData['sort_position_decimal'] = 0;
		}
		//Prepare category product positions for sorting
		$this->prepareProductCategoriesPosition($_product, $docData);

		$docData['sort_bestselling_decimal'] = $this->getProductOrderedQty($_product, $this->store);


		$docData['products_id'] = $_product->getId();
		$docData['product_type_static'] = (string)$_product->getTypeId();
		$docData['unique_id'] = $store->getId().'P'.$_product->getId();
		if (!isset($docData['product_search_weight_int'])) {
			$docData['product_search_weight_int'] = 0;
		}

		$multipleStoreModeSetting = Mage::helper('solrsearch')->getSetting('multiplestore');
		if (intval($multipleStoreModeSetting) > 0) {//multiple store by different category root and different website
		    $docData['store_id'] = $store->getId();
		    $docData['website_id'] = $store->getWebsiteId();
		}else{
		    if(isset($docData['category_id']) && !empty($docData['category_id'])){
		        $docData['store_id'] = $store->getId();
		        $docData['website_id'] = $store->getWebsiteId();
		    }else{
		        $docData['store_id'] = 0;
		        $docData['website_id'] = 0;
		    }
		}
		
		$docData['store_id'] = $store->getId();
		$docData['website_id'] = $store->getWebsiteId();

		$docData['filter_visibility_int'] = $_product->getVisibility();

		$checkInstockMethod =  Mage::helper('solrsearch')->getSetting('check_instock_method');

		try
		{
    		$stock = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $_product );

    		if (intval($checkInstockMethod) > 0)
    		{
    		    if ($stock->getIsInStock() && $stock->getQty() > 0)
    		    {
    		        $docData['instock_int'] = 1;
    		    }
    		    else
    		    {
    		        $docData['instock_int'] = 0;
    		    }
    		}
    		else
    		{
    		    if ($stock->getIsInStock())
    		    {
    		        $docData['instock_int'] = 1;
    		    }
    		    else
    		    {
    		        $docData['instock_int'] = 0;
    		    }
    		}

		}
		catch (Exception $e)
		{
            $docData['instock_int'] = 0;
    	}
		$docData['product_status'] = $_product->getStatus();
		//If need product reviews count, uncomment the following line
		//$docData['product_review_int'] = $this->getProductReviewCount($_product->getId(), $store->getId());
	}
	
	public function prepareProductCategoriesPosition($product, &$docData)
	{
		if ( isset($docData['category_id']) && is_array($docData['category_id']) && count($docData['category_id']))
		{
			$resource = Mage::getSingleton('core/resource');
			$writeAdapter = $resource->getConnection('core_write');
			$categoryProductTable = $resource->getTableName('catalog/category_product');
			
			$select = $writeAdapter->select()
			->from(array('cp' => $categoryProductTable), array('category_id', 'position'))
			->where('cp.product_id = '.$product->getId().' AND cp.category_id IN (?)', $docData['category_id']);
			
			$positions = $writeAdapter->fetchPairs($select);

			foreach ($positions as $catId => $pos)
			{
				$docData['sort_cat_'.$catId.'_pos_int'] = $pos;
			}
		}
	}

	public function getProductOrderedQty($_product, $store)
	{
		$visibility = Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds();
		if ($_product->getId() && in_array($_product->getVisibility(), $visibility) && $_product->getStatus())
		{
		    try{
    			$oldStore = Mage::app ()->getStore ();
    			Mage::app ()->setCurrentStore ( $store );

    			$storeId    = $store->getId();
    			$products = Mage::getResourceModel('reports/product_collection')
    			->addOrderedQty()
    			//->addAttributeToSelect(array('name')) //edit to suit tastes
    			->setStoreId($storeId)
    			->addStoreFilter($storeId)
    			->addIdFilter($_product->getId())->setOrder('ordered_qty', 'desc'); //best sellers on top
    			$data = $products->getFirstItem()->getData();

    			Mage::app ()->setCurrentStore ( $oldStore );

    			if(isset($data['ordered_qty']) && (int) $data['ordered_qty'])
    			{
    				return (int)$data['ordered_qty'];
    			}
		    }
		    catch (Exception $e)
		    {
		        return 0;
		    }
		}else{
			return 0;
		}
		return 0;
	}
	
	public function generateCategoryThumb($category)
	{
		$thumsize = Mage::helper('solrsearch')->getSetting('autocomplete_thumb_size');
		$width = 32;
		$height = 32;
		if (!empty($thumsize)) {
			$thumbSizeArray = explode('x', $thumsize);
			if (isset($thumbSizeArray[0]) && is_numeric($thumbSizeArray[0])) {
				if (isset($thumbSizeArray[1]) && is_numeric($thumbSizeArray[1])) {
					$width = trim($thumbSizeArray[0]);
					$height = trim($thumbSizeArray[1]);
				}
			}
		}
	
		$image = $category->getData('image');
		if( !empty($image) )
		{
			$imagePath = Mage::getBaseDir("media").DS.'catalog'.DS.'category'.DS.$image;
		}
			
		if( $imagePath && file_exists($imagePath) )
		{
	
		}
		else
		{
			$imagePath = Mage::helper('solrsearch/image')->getImagePlaceHolder();
		}
			
		$categoryImageThumbPath = Mage::getBaseDir('media').DS."catalog".DS."category".DS."sb_thumb".DS.$category->getId().'.jpg';
		if (file_exists($categoryImageThumbPath)) {
			unlink($categoryImageThumbPath);
		}
		try{
			$imageObj = new Varien_Image($imagePath);
			$imageObj->constrainOnly(FALSE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->keepFrame(FALSE);
			$imageObj->backgroundColor(array(255,255,255));
			//$imageObj->keepTransparency(TRUE);
			$imageObj->resize($width, $height);
			$imageObj->save($categoryImageThumbPath);
		}catch (Exception $e){
			echo 'Exception at product['.$category->getId().']['.$categoryImageThumbPath.']: ',  $e->getMessage(), "\n";
		}
		if (file_exists($categoryImageThumbPath)) {
			return true;
		}
		return false;
	}
	
	public function getProductReviewCount($productId, $storeId) {
		$collection = Mage::getModel('review/review')->getCollection()
		->addStoreFilter($storeId)
		->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
		->addEntityFilter('product', $productId)
		->setDateOrder();
		
		$count = $collection->getSize();
		return ($count > 0)?$count:0;
	}
}