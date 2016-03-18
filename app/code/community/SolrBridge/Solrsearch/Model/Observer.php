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
class SolrBridge_Solrsearch_Model_Observer {

	protected $ultility = null;

	public $threadEnable = false;

	public $effectedProductIds = array();

	public $currentStoreId = null;

	public $data = array();

	public $autoIndex = false;

	public function __construct()
	{
		$this->ultility = Mage::getModel('solrsearch/ultility');
		$this->threadEnable = Mage::getResourceModel('solrsearch/solr')->threadEnable;
		$this->autoIndex = Mage::getResourceModel('solrsearch/solr')->autoIndex;
	}

	public function addSearchWeightFieldToAttributeForm($observer)
	{

		$weights = array();
		$weights[] = array(
						'value' => "",
						'label' => Mage::helper('solrsearch')->__('Default')
				);

		$weights = array_merge($weights, Mage::helper('solrsearch')->getWeights());

		$fieldset = $observer->getForm()->getElement('front_fieldset');
		$attribute = $observer->getAttribute();
		$attributeCode = $attribute->getName();

		$fieldset->addField('solr_search_field_weight', 'select', array(
				'name'      => 'solr_search_field_weight',
				'label'     => Mage::helper('solrsearch')->__('Solr Search weight'),
				'title'     => Mage::helper('solrsearch')->__('Solr Search weight'),
				'values'    => $weights,
				'note'  => Mage::helper('solrsearch')->__('Boost search result by keyword. The heigher value will produce the heigher result.')
		));

		$fieldset->addField('solr_search_field_boost', 'textarea', array(
				'name'      => 'solr_search_field_boost',
				'label'     => Mage::helper('solrsearch')->__('Solr Search boost'),
				'title'     => Mage::helper('solrsearch')->__('Solr Search booost'),
				//'values'    => $weights,
				'note'  => Mage::helper('solrsearch')->__('Boost search result by fixed value. Example: Sony|1. Each pair of key|value separted by linebreak, value will be 1-20. The heigher value will produce the heigher result.')
		));

		$options = array(
				array(
					'value' => 0,
					'label' => Mage::helper('solrsearch')->__('No')
				),
				array(
						'value' => 1,
						'label' => Mage::helper('solrsearch')->__('Yes')
				)
		);

		$fieldset->addField('solr_search_field_range', 'select', array(
				'name'      => 'solr_search_field_range',
				'label'     => Mage::helper('solrsearch')->__('Solr Display as range'),
				'title'     => Mage::helper('solrsearch')->__('Solr Display as range'),
				'values'    => $options,
		));
	}

	/**
	 * Update solr index when product tags save
	 * @param unknown $observer
	 */
	public function productTagSaveAfter($observer)
	{
		if ($this->autoIndex)
		{
			//use tags for search and facets
			$use_tags_for_search = Mage::helper('solrsearch')->getSetting('use_tags_for_search');

			if ($use_tags_for_search) {
				$currentTag = $observer->getEvent()->getDataObject();
				$productIds = $currentTag->setStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
				->getRelatedProductIds();

				if (!empty($productIds)) {
					foreach ($productIds as $productId){
						$this->updateSolrIndex($productId);
					}
				}
			}
		}
		else
		{
		    //use tags for search and facets
		    $use_tags_for_search = Mage::helper('solrsearch')->getSetting('use_tags_for_search');

		    if ($use_tags_for_search)
		    {
		        $currentTag = $observer->getEvent()->getDataObject();
		        $productIds = $currentTag->setStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
		        ->getRelatedProductIds();

		        if (!empty($productIds))
		        {
		            foreach ($productIds as $productId)
		            {
		                $this->ultility->updateProductLogId($productId, 1);
		            }
		        }
		    }
		}
	}

	/**
	 * When a magento product deleted
	 * @param unknown $observer
	 */
	public function productDeleteAfter($observer)
	{
		if ($this->autoIndex)
		{
			$product = $observer->getEvent()->getProduct();
			$this->updateSolrIndex($product->getId());
		}
		else
		{
		    $product = $observer->getEvent()->getProduct();
		    $this->ultility->updateProductLogId($product->getId(), 1);
		}
	}
	/**
	 * When added/update a product
	 * @param Varien_Event_Observer $observer
	 */
	public function productAddUpdate($observer)
	{
		if ($this->autoIndex)
		{
			$product = $observer->getProduct();
			$this->effectedProductIds[] = $product->getId();
			$store_id = Mage::app()->getRequest()->getParam('store');
			$this->currentStoreId = $store_id;
			$this->updateSolrIndex($product->getId(), $store_id);
		}
		else
		{
		    $product = $observer->getProduct();
		    $this->ultility->updateProductLogId($product->getId(), 1);
		}
	}
	//Update solr document after reindex price
	public function catalogReindexPriceAfter($observer)
	{
		if ($this->autoIndex)
		{
			if (is_array($this->effectedProductIds) && count($this->effectedProductIds) > 0)
			{
			    foreach ($this->effectedProductIds as $productid)
			    {
			    	$this->updateSolrIndex($productid, $this->currentStoreId);
			    }
			}
		}
		else
		{
		    if (is_array($this->effectedProductIds) && count($this->effectedProductIds) > 0)
		    {
		        foreach ($this->effectedProductIds as $productid)
		        {
		            $this->ultility->updateProductLogId($productid, 1);
		        }
		    }
		}
	}

	/**
	 * Update Solr documents when catalog rules applied
	 * @param unknown $observer
	 */
	public function catalogRuleApplyAfter($observer)
	{
		$productCondition = $observer->getEvent()->getData('product_condition');
		$store_id = Mage::app()->getRequest()->getParam('store');
		if (!empty($store_id)) {
		    $this->currentStoreId = $store_id;
		}
		$adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
		$productCondition = $productCondition->getIdsSelect($adapter)->__toString();
		$effectedProducts = $adapter->fetchAll($productCondition);
		foreach ($effectedProducts as $item)
		{
			if (isset($item['product_id']) && $item['product_id'] > 0) {
				$this->effectedProductIds[] = $item['product_id'];
			}
		}
	}

	public function updateSolrIndex($productid, $store_id = 0)
	{
	    if ($this->autoIndex)
	    {
	        if (is_numeric($productid))
	        {
	            if ($this->threadEnable)
	            {
	                $this->ultility
	                ->getThreadManager()
	                ->addThread(array('updatesingle' => $productid.'_'.$store_id))
	                ->run();
	            }
	            else
	            {
	                Mage::getResourceModel('solrsearch/solr')->updateSingleProduct($productid, $store_id);
	            }
	        }
	        Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
	    }
	}
	/**
	 * Catch category data before category save
	 * @param unknown $observer
	 */
	public function categorySaveBefore($observer)
	{
		if ($this->autoIndex)
		{
			$categoryId = $observer->getCategory()->getId();
			$this->data['save_category_id'] = $categoryId;
		}
	}
	public function categorySaveAfter()
	{
		if ($this->autoIndex)
		{
			if (isset($this->data['save_category_id']) && !empty($this->data['save_category_id']))
			{
				$categoryId = $this->data['save_category_id'];
				if (!empty($categoryId) && is_numeric($categoryId))
				{
					if ($this->threadEnable)
					{
						$this->ultility
						->getThreadManager()
						->addThread(array('updatecategory' => $categoryId))
						->run();
					}
					else
					{
						Mage::getResourceModel('solrsearch/solr')->updateSingleCategory($categoryId);
					}
				}
			}
		}
	}

	public function entityAttributeSaveAfter()
	{
		Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
		//Clear search result fields settings
		$stores = Mage::app()->getStores();
		foreach ($stores as $store) {
		    Mage::getModel('core/config')->deleteConfig('solrbridge/search/search_result_config_fields', 'stores', $store->getId());
		}
	}

	public function generateIndexTables()
	{
		Mage::getResourceModel('solrsearch/solr')->generateIndexTables();
	}

	public function generateStaticConfigOld()
	{
		$advanced_autocomplete = (int)Mage::helper('solrsearch')->getSetting('advanced_autocomplete');
		if ($advanced_autocomplete > 0)
		{
			$config = Mage::getStoreConfig('solrbridge', 0);
			$config2 = Mage::getStoreConfig('solrbridgeindices', 0);
			$config['solrbridgeindices'] = $config2;

			$etcDir = '/'.trim(Mage::getBaseDir('etc'), '/');

			$stores = Mage::app()->getStores();
			foreach ($stores as $store)
			{
				$config['stores'][$store->getId()] = Mage::getStoreConfig('solrbridge', $store->getId());
				$config['stores'][$store->getId()]['currencycode'] = $store->getCurrentCurrencyCode();
				//$config['stores'][$store->getId()]['pricefields'] = Mage::helper('solrsearch')->getPriceFields($store);
				$config['stores'][$store->getId()]['website_id'] = $store->getWebsiteId();
			}

			$solrcore = 'english';

			$options = array('solrcore' => $solrcore, 'queryText' => 'PLACEHOLDER', 'rows' => 20, 'facetlimit' => 200, 'autocomplete' => true);

			$solrModel = Mage::getModel('solrsearch/solr')->init($options)->prepareQueryData();

			$facetFields = $solrModel->getFacetFields();
			$boostFields = $solrModel->getBoostFields();

			$config['facetfields'] = $facetFields;
			$config['boostfields'] = $boostFields;

			$configFile = fopen($etcDir.'/solrbridge.conf', 'w');
			fwrite($configFile, json_encode($config));
			fclose($configFile);
		}
	}
	
	public function generateStaticConfig()
	{
		$advanced_autocomplete = (int)Mage::helper('solrsearch')->getSetting('advanced_autocomplete');
		if ($advanced_autocomplete > 0)
		{
			$cachedDir = '/'.trim(Mage::getBaseDir('var'), '/');
			$configFile = $cachedDir.'/cache/solrbridge.conf';
			if (file_exists($configFile))
			{
				unlink($configFile);
			}
		}
	}

	public function generateIgnoreSearchTermsConfig()
	{
	    $allow_ignore_term = (int)Mage::helper('solrsearch')->getSetting('allow_ignore_term');
	    if ($allow_ignore_term > 0)
	    {
	        $collection = Mage::getModel('catalogsearch/query')->getResourceCollection();

	        $collection->addFieldToFilter('display_in_terms', array('eq' => 0));

	        $ignoreSearchTerms = array();
	        foreach ($collection as $term)
	        {
	            $ignoreSearchTerms[] = strtolower($term->getQueryText());
	        }

	        if (!empty($ignoreSearchTerms)) {
	            $configModel = Mage::getModel('core/config');
	            $configModel->saveConfig('solrbridge/settings/ignoresearchterms', @implode(',', $ignoreSearchTerms));
	            $this->generateStaticConfig();
	        }
	    }
	}

	public function productModelSaveAfter($observer)
	{
		if ($this->autoIndex)
		{
			$model = $observer->getEvent()->getObject();
			$type = $model->getType();
			$entity = $model->getEntity();

			if ($type == 'mass_action' && $entity == 'catalog_product')
			{
				$dataObject = $model->getDataObject();
				if (isset($dataObject) && $dataObject instanceof Mage_Catalog_Model_Product_Action)
				{
					$productIds = $dataObject->getProductIds();
					if (is_array($productIds))
					{
						if ($this->threadEnable)
						{
							if (!empty($productIds))
							{
							    $this->ultility
							    ->getThreadManager()
							    ->addThread(array('updatemass' => @implode('_', $productIds)))
							    ->run();
							}
						}
						else
						{
							if (!empty($productIds))
							{
								foreach ($productIds as $productid)
								{
									$this->updateSolrIndex($productid);
								}
							}
						}
					}
				}
			}
		}
		else
		{
		    $model = $observer->getEvent()->getObject();
		    $type = $model->getType();
		    $entity = $model->getEntity();

		    if ($type == 'mass_action' && $entity == 'catalog_product') {
		        $dataObject = $model->getDataObject();
		        if (isset($dataObject) && $dataObject instanceof Mage_Catalog_Model_Product_Action)
		        {
		            $productIds = $dataObject->getProductIds();
		            if (is_array($productIds))
		            {
		                if (!empty($productIds))
		                {
		                    foreach ($productIds as $productid)
		                    {
		                        $this->ultility->updateProductLogId($productid, 1);
		                    }
		                }
		            }
		        }
		    }
		}
	}
	public function handleLayoutRender()
	{
	    $layout = Mage::getSingleton('core/layout');
	    if (!$layout)
	        return;

	    $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
	    $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
	    if (!$isAJAX)
	        return;

	    $layout->removeOutputBlock('root');
	    Mage::app()->getFrontController()->getResponse()->setHeader('content-type', 'application/json');

	    $page = $layout->getBlock('searchresult');
	    if (!$page){
	        $page = $layout->getBlock('solrsearch_product_list');
	    }

	    if (!$page)
	        return;

	    $blocks = array();
	    foreach ($layout->getAllBlocks() as $b){
	        if (!in_array($b->getNameInLayout(), array('searchfaces'))){
	            continue;
	        }
	        $b->setIsAjax(true);
	        $blocks['solr_search_facets'] = $this->_removeAjaxParam($b->toHtml());
	    }

	    if (!$blocks)
	        return;

	    $container = $layout->createBlock('core/template', 'solrsearch_container');
	    $container->setData('blocks', $blocks);
	    $container->setData('page', $this->_removeAjaxParam($page->toHtml()));

	    $layout->addOutputBlock('solrsearch_container', 'toJson');
	}
    /**
     * Check to replace Default Magento Layer Navigation with SolrBridge Layer Navigation
     * @param unknown $observer
     */
	public function handleCatalogLayoutRender($observer)
	{
	    $replaceCatalogLayerNavigation = (int) Mage::Helper('solrsearch')->getSetting('replace_catalog_layer_nav');
	    if ($replaceCatalogLayerNavigation > 0)
	    {
	        $layoutUpdate = Mage::getSingleton('core/layout')->getUpdate();
	        if ($category = Mage::registry('current_category') && !Mage::registry('current_product'))
	        {
	            $layoutUpdate->addHandle('solrbridge_solrsearch_category_view');
	        }
	    }
	}

	protected function _removeAjaxParam($html)
	{
	    $html = str_replace('is_ajax=1&amp;', '', $html);
	    $html = str_replace('is_ajax=1&',     '', $html);
	    $html = str_replace('is_ajax=1',      '', $html);

	    $html = str_replace('___SID=U', '', $html);

	    return $html;
	}
}