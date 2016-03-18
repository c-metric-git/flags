<?php
class SolrBridge_Searchrequest_Block_Title extends SolrBridge_Solrsearch_Block_Result_Title
{
	protected function _construct()
	{
		$this->setTemplate('solrsearch/searchrequest/title.phtml');
	}
}