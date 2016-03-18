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
class SolrBridge_Solrsearch_Model_Resource_Indexer_Attribute extends Mage_CatalogSearch_Model_Resource_Fulltext
{
	protected $imageAttrIds = null;
	
	protected $_ignoreAttributes = array();
	
	public function _construct()
	{
		//Product search weight attribute code
		$search_weight_attribute_code = Mage::helper('solrsearch')->getSetting('search_weight_attribute_code');
		$search_weight_attribute_code = trim($search_weight_attribute_code);
		if (!empty($search_weight_attribute_code))
		{
			array_push($this->_ignoreAttributes, $search_weight_attribute_code);
		}
		parent::_construct();
	}
	
	public function getProductAttributeData($productIds, $storeId)
	{
		if (empty($productIds))
		{
			return array();
		}
		$sql = "
			SELECT DISTINCT `attr_table`.*,
			`attrinfo2_table`.`attribute_code`, `attrinfo2_table`.`backend_type`, `attrinfo2_table`.`frontend_input`,
			`attrinfo1_table`.`is_filterable`,
			`attrinfo1_table`.`is_filterable_in_search`, `attrinfo1_table`.`is_searchable`, `attrinfo1_table`.`used_for_sort_by`,
			`attrinfo1_table`.`solr_search_field_weight`, `attrinfo1_table`.`solr_search_field_boost`
			FROM `".$this->getTable(array('catalog/product', 'varchar'))."` AS `attr_table`
			INNER JOIN `".$this->getTable('eav/entity_attribute')."` AS `set_table` ON attr_table.attribute_id = set_table.attribute_id
			INNER JOIN `".$this->getTable('catalog/eav_attribute')."` AS `attrinfo1_table` ON attr_table.attribute_id = attrinfo1_table.attribute_id
			INNER JOIN `".$this->getTable('eav/attribute')."` AS `attrinfo2_table` ON attr_table.attribute_id = attrinfo2_table.attribute_id
			WHERE (attr_table.store_id IN (0, {$storeId})) AND (attr_table.entity_id IN (".@implode(',', $productIds)."))
			
			UNION ALL
			SELECT DISTINCT `attr_table`.*,
			`attrinfo2_table`.`attribute_code`, `attrinfo2_table`.`backend_type`, `attrinfo2_table`.`frontend_input`,
			`attrinfo1_table`.`is_filterable`,
			`attrinfo1_table`.`is_filterable_in_search`, `attrinfo1_table`.`is_searchable`, `attrinfo1_table`.`used_for_sort_by`,
			`attrinfo1_table`.`solr_search_field_weight`, `attrinfo1_table`.`solr_search_field_boost`
			FROM `".$this->getTable(array('catalog/product', 'int'))."` AS `attr_table`
			INNER JOIN `".$this->getTable('eav/entity_attribute')."` AS `set_table` ON attr_table.attribute_id = set_table.attribute_id
			INNER JOIN `".$this->getTable('catalog/eav_attribute')."` AS `attrinfo1_table` ON attr_table.attribute_id = attrinfo1_table.attribute_id
			INNER JOIN `".$this->getTable('eav/attribute')."` AS `attrinfo2_table` ON attr_table.attribute_id = attrinfo2_table.attribute_id
			WHERE (attr_table.store_id IN (0, {$storeId})) AND (attr_table.entity_id IN (".@implode(',', $productIds)."))
	
			UNION ALL
			SELECT DISTINCT `attr_table`.*,
			`attrinfo2_table`.`attribute_code`, `attrinfo2_table`.`backend_type`, `attrinfo2_table`.`frontend_input`,
			`attrinfo1_table`.`is_filterable`,
			`attrinfo1_table`.`is_filterable_in_search`, `attrinfo1_table`.`is_searchable`, `attrinfo1_table`.`used_for_sort_by`,
			`attrinfo1_table`.`solr_search_field_weight`, `attrinfo1_table`.`solr_search_field_boost`
			FROM `".$this->getTable(array('catalog/product', 'text'))."` AS `attr_table`
			INNER JOIN `".$this->getTable('eav/entity_attribute')."` AS `set_table` ON attr_table.attribute_id = set_table.attribute_id
			INNER JOIN `".$this->getTable('catalog/eav_attribute')."` AS `attrinfo1_table` ON attr_table.attribute_id = attrinfo1_table.attribute_id
			INNER JOIN `".$this->getTable('eav/attribute')."` AS `attrinfo2_table` ON attr_table.attribute_id = attrinfo2_table.attribute_id
			WHERE (attr_table.store_id IN (0, {$storeId})) AND (attr_table.entity_id IN (".@implode(',', $productIds)."))
	
			UNION ALL
			SELECT DISTINCT `attr_table`.* ,
			`attrinfo2_table`.`attribute_code`, `attrinfo2_table`.`backend_type`, `attrinfo2_table`.`frontend_input`,
			`attrinfo1_table`.`is_filterable`,
			`attrinfo1_table`.`is_filterable_in_search`, `attrinfo1_table`.`is_searchable`, `attrinfo1_table`.`used_for_sort_by`,
			`attrinfo1_table`.`solr_search_field_weight`, `attrinfo1_table`.`solr_search_field_boost`
			FROM `".$this->getTable(array('catalog/product', 'datetime'))."` AS `attr_table`
			INNER JOIN `".$this->getTable('eav/entity_attribute')."` AS `set_table` ON attr_table.attribute_id = set_table.attribute_id
			INNER JOIN `".$this->getTable('catalog/eav_attribute')."` AS `attrinfo1_table` ON attr_table.attribute_id = attrinfo1_table.attribute_id
			INNER JOIN `".$this->getTable('eav/attribute')."` AS `attrinfo2_table` ON attr_table.attribute_id = attrinfo2_table.attribute_id
			WHERE (attr_table.store_id IN (0, {$storeId})) AND (attr_table.entity_id IN (".@implode(',', $productIds)."))
	
			UNION ALL
			SELECT DISTINCT `attr_table`.*,
			`attrinfo2_table`.`attribute_code`, `attrinfo2_table`.`backend_type`, `attrinfo2_table`.`frontend_input`,
			`attrinfo1_table`.`is_filterable`,
			`attrinfo1_table`.`is_filterable_in_search`, `attrinfo1_table`.`is_searchable`, `attrinfo1_table`.`used_for_sort_by`,
			`attrinfo1_table`.`solr_search_field_weight`, `attrinfo1_table`.`solr_search_field_boost`
			FROM `".$this->getTable(array('catalog/product', 'decimal'))."` AS `attr_table`
			INNER JOIN `".$this->getTable('eav/entity_attribute')."` AS `set_table` ON attr_table.attribute_id = set_table.attribute_id
			INNER JOIN `".$this->getTable('catalog/eav_attribute')."` AS `attrinfo1_table` ON attr_table.attribute_id = attrinfo1_table.attribute_id
			INNER JOIN `".$this->getTable('eav/attribute')."` AS `attrinfo2_table` ON attr_table.attribute_id = attrinfo2_table.attribute_id
			WHERE (attr_table.store_id IN (0, {$storeId})) AND (attr_table.entity_id IN (".@implode(',', $productIds)."))
	
			ORDER BY store_id DESC
		";
		$adapter = $this->_getWriteAdapter();
			$result = $adapter->query($sql);
	
			//prepare data
		$returnData = array();
	
		while ($item = $result->fetch()) {
			if ($item['store_id'] > 0)
			{
				if ($storeId == $item['store_id'])
				{
					if (!empty($item['value']))
					{
						if ( !$this->isIgnoreAttribute($item) )
						{
							$item['value'] = $this->_getAttributeValue($item['attribute_id'], $item['value'], $storeId);
						}
						$returnData[$item['entity_id']][$item['attribute_id']] = $item;
					}
				}
			}
			else
			{
				if ( !$this->isIgnoreAttribute($item) )
				{
					$item['value'] = $this->_getAttributeValue($item['attribute_id'], $item['value'], $storeId);
				}
				$returnData[$item['entity_id']][$item['attribute_id']] = $item;
			}
		}
		return $returnData;
	}
	
	public function isIgnoreAttribute($item)
	{
		$ignoreAttributeCodes = array('visibility', 'status');
		$ignoreFrontendInputs = array('media_image');
		if (in_array($item['attribute_code'], $ignoreAttributeCodes))
		{
			return true;
		}
		
		if (in_array($item['attribute_code'], $ignoreAttributeCodes))
		{
			return true;
		}
		
		if (in_array($item['frontend_input'], $ignoreFrontendInputs))
		{
			return true;
		}
		return false;
	}
	
	public function getProductAttributeDataOld($productIds, $storeId)
	{
		// prepare searchable attributes
		$staticFields = array();
		foreach ($this->_getSearchableAttributes('static') as $attribute) {
			$staticFields[] = $attribute->getAttributeCode();
		}
		$dynamicFields = array(
				'int'       => array_keys($this->_getSearchableAttributes('int')),
				'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
				'text'      => array_keys($this->_getSearchableAttributes('text')),
				'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
				'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
		);
		
		// status and visibility filter
		$visibility     = $this->_getSearchableAttribute('visibility');
		$status         = $this->_getSearchableAttribute('status');
		$statusVals     = Mage::getSingleton('catalog/product_status')->getVisibleStatusIds();
		
		//$allowedVisibilityValues = $this->_engine->getAllowedVisibility();
		
		$lastProductId = 0;
		
		$products = $this->_getSearchableProducts($storeId, $staticFields, $productIds, $lastProductId);
		if (!$products) {
			return array();
		}
		
		$productAttributes = array();
		$productRelations  = array();
		foreach ($products as $productData) {
			$lastProductId = $productData['entity_id'];
			$productAttributes[$productData['entity_id']] = $productData['entity_id'];
			/*
			$productChildren = $this->_getProductChildIds($productData['entity_id'], $productData['type_id']);
			$productRelations[$productData['entity_id']] = $productChildren;
			if ($productChildren) {
				foreach ($productChildren as $productChildId) {
					$productAttributes[$productChildId] = $productChildId;
				}
			}
			*/
		}
		
		$productIndexes    = array();
		$productAttributes = $this->_getProductAttributes($storeId, $productAttributes, $dynamicFields);
		
		foreach ($productAttributes as $productId => $attributes)
		{
			foreach ($attributes as $attributeId => $attributeData)
			{
				if ( in_array($attributeData['attribute_code'], array('visibility', 'status') ) || $attributeData['frontend_input'] == 'media_image' )
				{
					continue;
				}
				$productAttributes[$productId][$attributeId]['value'] = $this->_getAttributeValue($attributeId, $attributeData['value'], $storeId);
			}
		}
		
		return $productAttributes;
	}
	
	/**
	 * Load product(s) attributes
	 *
	 * @param int $storeId
	 * @param array $productIds
	 * @param array $attributeTypes
	 * @return array
	 */
	protected function _getProductAttributes($storeId, array $productIds, array $attributeTypes)
	{
		if ($this->getImageAttributeIds())
		{
			$attributeTypes['varchar'] = array_merge($attributeTypes['varchar'], $this->getImageAttributeIds());
		}
		
		$result  = array();
		$selects = array();
		$adapter = $this->_getWriteAdapter();
		$ifStoreValue = $adapter->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
		foreach ($attributeTypes as $backendType => $attributeIds) {
			if ($attributeIds) {
				$tableName = $this->getTable(array('catalog/product', $backendType));
				$selects[] = $adapter->select()
				->from(
						array('t_default' => $tableName),
						array('entity_id', 'attribute_id'))
						->joinLeft(
								array('t_store' => $tableName),
								$adapter->quoteInto(
										't_default.entity_id=t_store.entity_id' .
										' AND t_default.attribute_id=t_store.attribute_id' .
										' AND t_store.store_id=?',
										$storeId),
								array('value' => $this->_unifyField($ifStoreValue, $backendType)))
								//ADD by Hau
								->joinLeft(
									array('catalog_attr_table' => $this->getTable('catalog/eav_attribute')),
									't_default.attribute_id = catalog_attr_table.attribute_id',
									array('is_filterable' => 'catalog_attr_table.is_filterable',
										'is_filterable_in_search' => 'catalog_attr_table.is_filterable_in_search',
										'is_searchable' => 'catalog_attr_table.is_searchable',
										'used_for_sort_by' => 'catalog_attr_table.used_for_sort_by',
										'solr_search_field_weight' => 'catalog_attr_table.solr_search_field_weight',
										'solr_search_field_boost' => 'catalog_attr_table.solr_search_field_boost'))
								->joinLeft(
								array('attr_table' => $this->getTable('eav/attribute')),
								't_default.attribute_id = attr_table.attribute_id',
								array('attribute_code' => 'attr_table.attribute_code', 'backend_type' => 'attr_table.backend_type', 'frontend_input' => 'attr_table.frontend_input'))
								//End
				->where('t_default.store_id=?', 0)
				->where('t_default.attribute_id IN (?)', $attributeIds)
				->where('t_default.entity_id IN (?)', $productIds);
			}
		}
	
		if ($selects) {
			$select = $adapter->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
			$query = $adapter->query($select);
			while ($row = $query->fetch()) {
				$result[$row['entity_id']][$row['attribute_id']] = $row;
			}
		}
	
		return $result;
	}
	/**
	 * get product images attributes ids - image, small_image, thumbnail
	 * @return multitype:
	 */
	public function getImageAttributeIds()
	{
		if ($this->imageAttrIds === null)
		{
			$productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');
			$productAttributeCollection->addFieldToFilter('attribute_code', array('in' => array('image', 'small_image', 'thumbnail')));
			$this->imageAttrIds = array_keys($productAttributeCollection->getItems());
		}
		return $this->imageAttrIds;
	}
}