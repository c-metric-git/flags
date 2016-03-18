<?php
class SolrBridge_Solrsearch_Block_Result_Options extends Mage_Core_Block_Template
{
	protected function _construct()
    {
    	$this->setTemplate('solrsearch/result/options.phtml');
    }
    
	public function _prepareLayout()
    {
    	return parent::_prepareLayout();
    }
    
	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    	return parent::_beforeToHtml();
    }
}