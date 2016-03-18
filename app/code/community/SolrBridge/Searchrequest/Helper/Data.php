<?php
class SolrBridge_Searchrequest_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function RequestData()
	{
		return array (
			1 => 'Can\'t find what I want',
			2 => 'Technical issue with website',
			3 => 'Product description is incorrect',
			4 => 'Feature request',
			5 => 'Others',
		);
	}
}