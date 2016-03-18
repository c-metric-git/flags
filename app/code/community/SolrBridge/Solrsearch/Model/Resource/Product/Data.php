<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade SolrBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Model_Resource_Product_Data extends Mage_Core_Model_Resource_Db_Abstract
{
	protected $imageAttrIds = null;
	
	protected $_ignoreAttributes = array();
	
	protected $_productAttributes = array();
	
	protected $_documentIds = array();
	
	protected $_indexTableName = null;
	
	public function _construct()
	{
		$this->_init('catalog/product', 'entity_id');
	}
	/**
	 * Set table name for update index case
	 * @param string $tableName
	 */
	public function setIndexTableName($tableName = null)
	{
		$this->_indexTableName = $tableName;
	}
	
	
	public function getProductAttributes($productIds, $storeId)
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
	
		while ( $item = $result->fetch() ) {
			$this->prepareAttributeItem($returnData, $item, $storeId);
		}
		return $returnData;
	}
	
	protected function prepareAttributeItem(&$returnData, $item, $storeId) {
		
		if( isset($returnData[$item['entity_id']][$item['attribute_id']]) ) {
			$existingItem = $returnData[$item['entity_id']][$item['attribute_id']];
			if ($storeId == $existingItem['store_id'] && !empty($existingItem['value'])) {
				return $existingItem;
			}
		}
		
		if ($item['store_id'] > 0)
		{
			if ($storeId == $item['store_id'])
			{
				if (!empty($item['value']))
				{
					if ( !$this->isIgnoreAttribute($item) )
					{
						if (isset($item['used_for_sort_by']) && $item['used_for_sort_by'] > 0)
						{
							$item['sort_value'] = $item['value'];
						}
						$item['value'] = $this->_getAttributeValue($item['attribute_id'], $item['value'], $storeId);
					}
					//$item['value'] = utf8_encode($item['value']);
					$returnData[$item['entity_id']][$item['attribute_id']] = $item;
				}
			}
		}
		else
		{
			if ( !$this->isIgnoreAttribute($item) )
			{
				if (isset($item['used_for_sort_by']) && $item['used_for_sort_by'] > 0 )
				{
					$item['sort_value'] = $item['value'];
				}
				$item['value'] = $this->_getAttributeValue($item['attribute_id'], $item['value'], $storeId);
			}
			//$item['value'] = utf8_encode($item['value']);
			$returnData[$item['entity_id']][$item['attribute_id']] = $item;
		}
	}
	
	protected function _getAttributeValue($attributeId, $value, $storeId)
	{
		$attribute = $this->_getAttribute($attributeId);
		if ($attribute && $attribute->usesSource())
		{
			$attribute->setStoreId($storeId);
			$value = $attribute->getSource()->getOptionText($value);
		
			if (is_array($value)) {
				$value = implode(',', $value);
			} elseif (empty($value)) {
				$inputType = $attribute->getFrontend()->getInputType();
				if ($inputType == 'select' || $inputType == 'multiselect') {
					return null;
				}
			}
		}
		return $value;
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
	
	/**
	 * Retrieve EAV Config Singleton
	 *
	 * @return Mage_Eav_Model_Config
	 */
	public function getEavConfig()
	{
		return Mage::getSingleton('eav/config');
	}
	
	/**
	 * Retrieve searchable attributes
	 *
	 * @param string $backendType
	 * @return array
	 */
	protected function _getAttributes($backendType = null)
	{
		if (is_null($this->_productAttributes)) {
			$this->_productAttributes = array();
	
			$productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');

			$attributes = $productAttributeCollection->getItems();
	
			$entity = $this->getEavConfig()
			->getEntityType(Mage_Catalog_Model_Product::ENTITY)
			->getEntity();
	
			foreach ($attributes as $attribute) {
				$attribute->setEntity($entity);
			}
	
			$this->_productAttributes = $attributes;
		}
		return $this->_productAttributes;
	}
	/**
	 * Retrieve searchable attribute by Id or code
	 *
	 * @param int|string $attribute
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	protected function _getAttribute($attribute)
	{
		$attributes = $this->_getAttributes();
		if (is_numeric($attribute)) {
			if (isset($attributes[$attribute])) {
				return $attributes[$attribute];
			}
		} elseif (is_string($attribute)) {
			foreach ($attributes as $attributeModel) {
				if ($attributeModel->getAttributeCode() == $attribute) {
					return $attributeModel;
				}
			}
		}
		return $this->getEavConfig()->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attribute);
	}
}