<?php
class SolrBridge_Solrsearch_Block_Plugin extends SolrBridge_Solrsearch_Block_Product_List
{
	protected function prepareSolrData()
	{
		$queryText = $this->getQuery();
		$limit = $this->getLimit();
		
		$this->_solrModel = Mage::getModel('solrsearch/solr');
		$this->_solrData = $this->_solrModel->query($queryText);
		if (isset($limit) && $limit > 0)
		{
			$this->_solrData['response']['docs'] = array_slice($this->_solrData['response']['docs'], 0, $limit);
		}
	}
}