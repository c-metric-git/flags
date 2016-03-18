<?php
/**
 * @category SolrBridge
 * @package SolrBridge_Solrsearch
 * @author Hau Danh
 * @copyright Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 *
 */
//class SolrBridge_Solrsearch_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
class SolrBridge_Solrsearch_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
	public function isEnabledFlat()
	{
		return false;
	}
}