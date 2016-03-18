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
class SolrBridge_Solrsearch_Block_Category_View extends Mage_Catalog_Block_Category_View
{
	public function getProductListHtml()
	{
		return $this->getChildHtml('solrsearch_product_list');
	}
	
	/**
	 * Check if category display mode is "Static Block Only"
	 * For anchor category with applied filter Static Block Only mode not allowed
	 *
	 * @return bool
	 */
	public function isContentMode()
	{
		$category = $this->getCurrentCategory();
		$res = false;
		if ($category->getDisplayMode()==Mage_Catalog_Model_Category::DM_PAGE) {
			$res = true;
			if ($category->getIsAnchor()) {
				$state = Mage::getSingleton('catalog/layer')->getState();
				if ($state && $state->getFilters()) {
					$res = false;
				}
	
				$solrModel = Mage::getSingleton('solrsearch/solr');
				if ($solrModel) {
					$filterQuery = $solrModel->getStandardFilterQuery();
					if (is_array($filterQuery) && count($filterQuery)) {
						$res = false;
					}
				}
			}
		}
		return $res;
	}
}