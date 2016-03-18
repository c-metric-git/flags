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
class SolrBridge_Solrsearch_Block_Product_List extends Mage_Catalog_Block_Product_List
{
	protected $facetFieldsLabels;
	protected $_productCollection;
	protected $_solrModel = null;
	protected $_solrData = null;
	protected $_sortDirection = 'asc';
	protected function _construct()
    {
    	$this->setTemplate('catalog/product/list.phtml');
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
        $head = $this->getLayout()->getBlock('head');
        $is_ajax = Mage::helper('solrsearch')->getSetting('use_ajax_result_page');
        if (intval($is_ajax) > 0) {
            $head->addJs('solrsearch/sbsajax.js');
        }
    	// add Home breadcrumb
    	if (Mage::app()->getFrontController()->getRequest()->getRouteName() === 'solrsearch') {
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
    		$head->setTitle($title);
    	}
    	else
    	{
    		$this->getLayout()->createBlock('catalog/breadcrumbs');
    	}
        return parent::_prepareLayout();
    }


    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
    	if (is_null($this->_productCollection)) {
    		$toolbar = $this->getToolbarBlock();

    		$orderby = $toolbar->getCurrentOrder();

    		$direction = $toolbar->getCurrentDirection();

    		$documents = array();
    		if( isset($this->_solrData['response']['docs']) ){
    			$documents = $this->_solrData['response']['docs'];
    		}

    		$productIds = array();
    		if(is_array($documents) && count($documents) > 0) {
    			foreach ($documents as $_doc) {
    				if ( isset($_doc['products_id']) ) {
    					$productIds[] = $_doc['products_id'];
    				}
    			}
    		}

    		$store = Mage::app()->getStore();
    		$collection = Mage::getModel('catalog/product')->getCollection();

    		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

    		$collection->addAttributeToFilter('entity_id', array('in' => $productIds));

    		if (method_exists($collection,'addPriceData'))
    		{
    			$collection->addPriceData();
    		}

    		$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());

    		if (Mage::app()->getRequest()->getRouteName() == 'catalog') {
    			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
    		}else{
    			Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection($collection);
    		}

    		Mage::helper('solrsearch')->applyInstockCheck($collection);

    		$collection->getSelect()->order("find_in_set(e.entity_id,'".implode(',',$productIds)."')");

    		if (Mage::app()->getRequest()->getRouteName() == 'catalog')
    		{
    			$layer = Mage::getSingleton('catalog/layer');
    			$_category = $layer->getCurrentCategory();
    			if (isset($_category) && $currentCategoryId = $_category->getId())
    			{
    				$collection->addUrlRewrite($currentCategoryId);
    			}
    		};
    		$this->_productCollection = $collection;
    	}

    	return $this->_productCollection;
    }
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getOptionsBlock()
    {
        $block = $this->getLayout()->createBlock('solrsearch/result_options', microtime());
        return $block;
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

    	$toolbar = $this->getToolbarBlock();

    	// called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $solrCollection = Mage::getModel('solrsearch/solr_collection');
        $solrCollection->setSolrData($this->_solrData);
        $toolbar->setCollection($solrCollection);
        //$toolbar->setSolrData($this->_solrData);

    	$this->setChild('toolbar', $toolbar);

    	//$facetsBlock = $this->getFacesBlock();

    	//$facetsBlock->setData('solrdata', $this->_solrData);

    	$this->setData('solrdata', $this->_solrData);

    	Mage::dispatchEvent('catalog_block_product_list_collection', array(
    	'collection' => $this->_getProductCollection()
    	));

    	$this->_getProductCollection()->load();

    }

	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getOptionsHtml()
    {
        return $this->getChildHtml('options');
    }
    public function setFacetFieldsLabels($facetFieldsLabels){
    	$this->facetFieldsLabels = $facetFieldsLabels;
    }
	public function getFacetFieldsLabels(){
    	return $this->facetFieldsLabels;
    }
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
    	return $this->_getProductCollection()->load();
    }
}