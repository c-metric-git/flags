<?php
/**
 * @category SolrBridge
 * @package SolrBridge_Solrsearch
 * @author Hau Danh
 * @copyright Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Result extends Mage_Core_Block_Template
{

	protected $facetFieldsLabels;

	protected $_productCollection;

	protected $_solrModel = null;

	protected $_solrData = null;

	protected $_sortDirection = 'asc';

	protected function _construct()
    {
    	$this->setTemplate('solrsearch/result.phtml');

    }

    protected function prepareSolrData()
    {
    	$solrModel = Mage::registry('solrbridge_loaded_solr');

    	if ($solrModel) {
    		$this->_solrModel = $solrModel;
    		$this->_solrData = $this->_solrModel->getSolrData();
    	}
    	else
    	{
    		$this->_solrModel = Mage::getModel('solrsearch/solr');
    		$queryText = Mage::helper('solrsearch')->getParam('q');
    		$this->_solrData = $this->_solrModel->query($queryText);
    	}
    }

	public function _prepareLayout()
    {
    	// add Home breadcrumb
    	$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
    	if ($breadcrumbs) {
    		$title = $this->__("Search results for: '%s'", $this->helper('catalogsearch')->getQueryText());

    		$breadcrumbs->addCrumb('home', array(
    				'label' => $this->__('Home'),
    				'title' => $this->__('Go to Home Page'),
    				'link'  => Mage::getBaseUrl()
    		))->addCrumb('search', array(
    				'label' => $title,
    				'title' => $title
    		));
    	}

    	// modify page title
    	$title = $this->__("Search results for: '%s'", $this->helper('solrsearch')->getEscapedQueryText());
    	$head = $this->getLayout()->getBlock('head');
    	$head->setTitle($title);
        return parent::_prepareLayout();
    }

	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getFacesBlock()
    {
        $block = $this->getLayout()->getBlock('searchfaces');
        return $block;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    	$this->prepareSolrData();

    	$this->setData('solrdata', $this->_solrData);


    	parent::_beforeToHtml();
    }

    public function getTitleBlock()
    {
    	$titleBlockHtml = $this->getChildHtml('solrsearch_result_title');
    	return $titleBlockHtml;
    }

    public function getProductListHtml()
    {
    	return $this->getChildHtml('solrsearch_product_list');
    }
}