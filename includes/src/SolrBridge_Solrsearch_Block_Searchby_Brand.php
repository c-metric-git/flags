<?php
class SolrBridge_Solrsearch_Block_Searchby_Brand extends Mage_Core_Block_Template
{
	public function getBrands()
	{
		return Mage::getModel('solrsearch/solr')->getBrandsFacets();
	}
}