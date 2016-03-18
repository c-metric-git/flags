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
class SolrBridge_Solrsearch_Model_Ultility
{
	public $logfile = null;

	public $logPath = '/solrbridge';

	public $allowCategoryIds = array();

	protected $logQuery = true;

	public $solrServerUrl = 'http://localhost:8080/solr/';

	public $itemsPerCommit = 100;

	public $writeLog = false;

	public $checkInStock = FALSE;

	public $productAttributes = array();

	public $threadEnable = false;
	
	public $documents = '';
	
	public $fetchedProducts = 0;
	
	public $indexer = null;

	public function __construct()
	{
		$solr_server_url = Mage::helper('solrsearch')->getSetting('solr_server_url');
		$this->solrServerUrl = $solr_server_url;

		$itemsPerCommitConfig = Mage::helper('solrsearch')->getSetting('items_per_commit');
		if( intval($itemsPerCommitConfig) > 0 )
		{
			$this->itemsPerCommit = $itemsPerCommitConfig;
		}

		$checkInstockConfig =  Mage::helper('solrsearch')->getSetting('check_instock');
		if( intval($checkInstockConfig) > 0 )
		{
			$this->checkInStock = $checkInstockConfig;
		}

		$writeLog =  Mage::helper('solrsearch')->getSetting('write_log');
		if( intval($writeLog) > 0 )
		{
			$this->writeLog = true;
		}
	}
    /**
     * Return the thread manager
     * @return SolrBridge_Solrsearch_Model_Resource_Thread
     */
	public function getThreadManager() {
	    $shellDir = '/' . trim ( Mage::getBaseDir ( 'base' ), '/' );
	    $config = array (
	            'timeout' => 10 * 60, // seconds
	            'maxProcess' => 5,
	            'scriptPath' => $shellDir . '/shell/solrbridge.php'  // path to worker script
	    );
	    return Mage::getResourceModel ( 'solrsearch/thread' )->init ( $config );
	}
    /**
     * Check to see if store view has solr core assigned
     */
	public function checkStoresSolrIndexAssign()
	{
	    $availableCores = $this->getAvailableCores();

	    $messages = array();

	    foreach ($availableCores as $solrcore => $infoArray)
	    {
	        $storeIds = $this->getMappedStoreIdsFromSolrCore($solrcore);

	        if ( !empty($storeIds) )
	        {
	            foreach ($storeIds as $storeid)
	            {
	                $storeObject = Mage::getModel('core/store')->load($storeid);
	                $solrCore = Mage::helper('solrsearch')->getSetting('solr_index', $storeid);

	                if (empty($solrCore) || !Mage::getResourceModel('solrsearch/solr')->pingSolrCore($solrCore)) {
	                    $warningMessage = 'It seems the store ['.$storeObject->getName().'] has no Solr Index assigned yet, please go to SolrBridge > General settings and select the configuration scrope on the top left and set the option Solr index';
	                    $messages[] = $warningMessage;
	                }
	            }
	        }
	    }

	    return $messages;
	}

    /**
     * Load the product attributes collection
     * @return multitype:
     */
	public function getProductAttributesOLD()
	{
		if (empty($this->productAttributes))
		{
			$this->productAttributes = Mage::getResourceModel('catalog/product_attribute_collection')
							->setOrder('position', 'ASC');
			/*
			foreach ($productAttrs as $attribute){
				$this->productAttributes[$attribute->getAttributeCode()] = $attribute;
			}
			*/
		}
		return $this->productAttributes;
	}
    /**
     * Load the product attributes collection
     * @return multitype:
     */
	public function getProductAttributes() {
		if (empty($this->productAttributes)) {
			$this->productAttributes = Mage::getResourceModel('catalog/product_attribute_collection');
			$this->productAttributes->addIsFilterableFilter();
			$this->productAttributes->setOrder('position', Varien_Data_Collection::SORT_ORDER_ASC);
			$this->productAttributes->addIsFilterableInSearchFilter();
		}
		return $this->productAttributes;
	}

	public function getWriteConnection()
	{
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		return $writeConnection;
	}

	public function getReadConnection()
	{
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		return $readConnection;
	}

	public function getLogTable()
	{
		$resource = Mage::getSingleton('core/resource');
		$logtable = $resource->getTableName('solrsearch/logs');
		return $logtable;
	}

	/**
	 * Write log message to file
	 * @param string $message
	 * @param number $logType
	 */
	public function writeLog($message = '', $store_id = 0, $solrcore="", $percent=0, $db = false) {
		if ($db) {
			$logTable = $this->getLogTable();
			/*
			$logSqlQuery = "REPLACE INTO {$logTable} (`logs_id`, `store_id`, `solrcore`, `percent`, `message`) VALUES
			(NULL, '{$store_id}', '{$solrcore}', '{$percent}', '{$message}');";
			*/
			$data = array(
				'store_id' => $store_id,
				'solrcore' => $solrcore,
				'percent' => $percent,
				'message' => $message
			);

			try{
				$this->getWriteConnection()->insertOnDuplicate($logTable, $data);
			}catch (Exception $e){
				echo $e->getMessage();
			}
		}

		return $message."\n";
	}

	public function getThreadPath()
	{
		$baseDir = '/'.trim(Mage::getBaseDir('var'), '/');
		$threadPath = $baseDir.$this->logPath.'/threads';
		if (!file_exists($threadPath)) {
			mkdir($threadPath);
		}
		return $threadPath;
	}

	public function saveFileJsonData($jsonData, $threadId)
	{
		$threadPath = $this->getThreadPath();
		$jsonDataFile = $threadPath.'/'.$threadId;
		$fileHandler = fopen($jsonDataFile, 'w');
		fwrite($fileHandler, $jsonData);
		fclose($fileHandler);
	}

	/**
	 * Log successed indexed product id into table
	 * @param array $infoArray
	 * @param string $solrcore
	 */
	public function logProductId($infoArray, $solrcore)
	{
		//Log index fields
		$writeConnection = $this->getWriteConnection();
		$logtable = $this->getLogTable();

		$indexTableName =  Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logtable, $solrcore, $writeConnection);

		if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexTableName)) {
			$indexSql = "";
			if ( is_array($infoArray) ) {
				$indexSql = "REPLACE INTO {$indexTableName} (`store_id`, `value`, `changed`) VALUES ";
				$indexSqlValues = '';
				foreach ($infoArray as $logInfo){
					if ( isset($logInfo['productid']) && is_numeric($logInfo['productid']) && isset($logInfo['storeid']) && is_numeric($logInfo['storeid'])) {
						$storeid = $logInfo['storeid'];
						$productid = $logInfo['productid'];
						$indexSqlValues .= "({$storeid}, {$productid}, 0),";
					}
				}

				if (!empty($indexSqlValues)) {
				    $indexSql .= $indexSqlValues;
				    $indexSql = trim($indexSql, ',');
				    $indexSql .= ";";
				}else{
				    $indexSql = '';
                }
			}

			if(!empty($indexSql)){
			     try{
			         $writeConnection->query($indexSql);
                 }catch(Exception $e){
                     echo $e->getMessage();
                     die($indexSql);
                 }
			}
		}else{
			return false;
		}
	}
	public function updateProductLogId($productId, $changed = 1)
	{
	    $writeConnection = $this->getWriteConnection();
	    $logTableName = $this->getLogTable();

	    $availableCores = $this->getAvailableCores();

	    if ( isset($availableCores) && !empty($availableCores) )
	    {
	        foreach ($availableCores as $solrcore => $info)
	        {
	            if (isset($info['stores']) && strlen(trim($info['stores'], ',')) > 0)
	            {
	                try
	                {
	                    $indexedTableName = Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logTableName, $solrcore);

	                    if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexedTableName))
	                    {
	                        $indexSql = "REPLACE INTO {$indexedTableName} (`store_id`, `value`, `changed`) VALUES ";
	                        $indexSqlValues = '';
	                        $storeIds = explode(',', trim($info['stores'], ','));
	                        foreach ($storeIds as $storeid)
	                        {
	                            if ( isset($storeid) && !empty($storeid) && is_numeric($storeid) )
	                            {
	                                $indexSqlValues .= "({$storeid}, {$productId}, {$changed}),";
	                            }
	                        }
	                        if (!empty($indexSqlValues)) {
	                            $indexSql .= $indexSqlValues;
	                            $indexSql = trim($indexSql, ',');
	                            $indexSql .= ";";
	                        }else{
	                            $indexSql = '';
	                        }
	                        if(!empty($indexSql)){
	                            try{
	                                $writeConnection->query($indexSql);
	                            }catch(Exception $e){
	                                 $this->writeLog($e->getMessage(), 0, '', '', true);
	                            }
	                        }
	                    }
	                }
	                catch (Exception $e)
	                {
	                    $this->writeLog($e->getMessage(), 0, '', '', true);
	                }
	            }
	        }
	    }
	}
	public function deleteProductLogId($productId)
	{
		$writeConnection = $this->getWriteConnection();
		$logTableName = $this->getLogTable();
	
		$availableCores = $this->getAvailableCores();
	
		if ( isset($availableCores) && !empty($availableCores) )
		{
			foreach ($availableCores as $solrcore => $info)
			{
				if (isset($info['stores']) && strlen(trim($info['stores'], ',')) > 0)
				{
					try
					{
						$indexedTableName = Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logTableName, $solrcore);
	
						if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexedTableName))
						{
							$indexSql = "DELETE FROM {$indexedTableName} WHERE  `value` = ".$productId;

							if(!empty($indexSql)){
								try{
									$writeConnection->query($indexSql);
								}catch(Exception $e){
									$this->writeLog($e->getMessage(), 0, '', '', true);
								}
							}
						}
					}
					catch (Exception $e)
					{
						$this->writeLog($e->getMessage(), 0, '', '', true);
					}
				}
			}
		}
	}
	/**
	 * Log search terms for statistic
	 * @param string $searchText
	 * @param int $numberResults
	 * @param int $storeId
	 */
	public function logSearchTerm($searchText, $numberResults, $storeId)
	{
		if (empty($searchText) || empty($storeId) || $searchText == '**' || $searchText == '*' || $searchText == '*:*') {
			return false;
		}

		if ($this->threadEnable)
		{
		    $this->getThreadManager()
		    ->addThread(array('savesearchterm' => $searchText, 'resultnum' => $numberResults, 'storeid' => $storeId))
		    ->run();
		    return true;
		}else{
		    $this->saveSearchTerm($searchText, $numberResults, $storeId);
		    return true;
		}
	}
	/**
	 * Save search term
	 * @param string $searchText
	 * @param number $numberResults
	 * @param number $storeId
	 */
	protected function saveSearchTerm($searchText, $numberResults, $storeId)
	{
	    $query = Mage::getModel('catalogsearch/query')->loadByQuery($searchText);
	    //Search term not exist
	    if (!$query->getId()) {
	        $query->setQueryText($searchText);
	    }
	    $query->setStoreId($storeId);

	    if ($query->getQueryText() != '')
	    {
	        if ($query->getId())
	        {
	            $query->setPopularity($query->getPopularity()+1);
	        }
	        else
	        {
	            $query->setPopularity(1)->setDisplayInTerms(1);
	            $query->setIsActive(1)->setIsProcessed(1);
	        }
	        $query->setNumResults($numberResults);
	        $query->save();
	    }
	}

	public function removeLogProductId($id, $solrcore) {

		$writeConnection = $this->getWriteConnection();
		$logtable = $this->getLogTable();

		$indexTableName =  Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logtable, $solrcore, $writeConnection);

		if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexTableName)) {
			if (is_array($id) && !empty($id)) {
				$productIdsString = implode(',', $id);
				$writeConnection->query("DELETE FROM {$indexTableName} WHERE `value` IN (".$productIdsString.");");
			}else if( is_numeric($id) ){
				$writeConnection->query("DELETE FROM {$indexTableName} WHERE `value`=".$id.";");
			}
		}
	}

	public function getMinimalProductCollection()
	{
		$collection = Mage::getResourceModel('catalog/product_collection');

		return $collection;
	}

	/**
	 * Get product collection by store id
	 * @param int $store_id
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	function getProductCollectionByStoreId($store_id)
	{
		$oldStore = Mage::app ()->getStore ();

		Mage::app ()->setCurrentStore ( $store_id );

		$collection = Mage::getResourceModel ( 'solrsearch/product_collection' );

		$collection->addAttributeToSelect ( '*' )
		->addMinimalPrice ()
		->addFinalPrice ()
		->addTaxPercents ()
		->addUrlRewrite ();

		Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $collection );
		Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $collection );

		Mage::helper ( 'solrsearch' )->applyInstockCheck ( $collection );

		Mage::app ()->setCurrentStore ( $oldStore );

		return $collection;
	}
	
	/**
	 * Get product collection by store id
	 * @param int $store_id
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	function getProductCollectionByStoreIdNew($store_id)
	{
		$store = Mage::getModel('core/store')->load($store_id);
	
		$productAttributes = Mage::getResourceModel('catalog/product_attribute_collection')
		->addIsSearchableFilter()
		//->addIsFilterableInSearchFilter()
		//->addIsFilterableFilter()
		->setOrder('position', 'ASC');
	
	
		$collection = Mage::getResourceModel ( 'solrsearch/product_collection' );
	
		$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
	
		$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
		$collection->addStoreFilter($store);
		$collection->addMinimalPrice()
		->addFinalPrice()
		->addTaxPercents();
	
		//Join attributes
		foreach ($productAttributes as $attribute)
		{
			if (!$attribute->getBackend()->isStatic())
			{
	
				$collection->joinAttribute(
						$attribute->getAttributeCode().'_'.$attribute->getBackendType(),
						'catalog_product/'.$attribute->getAttributeCode(),
						'entity_id',
						null,
						'left',
						$adminStore
				);
	
				$collection->joinAttribute(
						'store_'.$attribute->getAttributeCode().'_'.$attribute->getBackendType(),
						'catalog_product/'.$attribute->getAttributeCode(),
						'entity_id',
						null,
						'left',
						$store->getId()
				);
			}
		}
	
		$collection->joinAttribute(
				'visibility',
				'catalog_product/visibility',
				'entity_id',
				null,
				'inner',
				$store->getId()
		);
	
		$collection->addFieldToFilter('visibility', array('in' => array(3,4)));
		$collection->addFieldToFilter('store_status_int', array('gt' => 0));
	
		$collection->addWebsiteNamesToResult();
	
		return $collection;
	}

	/**
	 * Update collection
	 * @param int $store_id
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	public function getProductCollectionForUpdate($collection, $solrcore)
	{
		$collection->clear();

		$logTable = $this->getLogTable();
		$indexTableName = Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logTable, $solrcore);

		$collection->getSelect()
				   ->joinLeft(
				   		array('wbmsslogs' => $indexTableName),
				   		'wbmsslogs.value = e.entity_id AND wbmsslogs.store_id = cat_index.store_id',
						array('wbmsslogs_store_id' => 'wbmsslogs.store_id', 'wbmsslogs_entity_id' => 'wbmsslogs.value')
					)
				   ->where('wbmsslogs.value IS NULL AND wbmsslogs.store_id IS NULL');

		if ($this->writeLog) {
			$this->writeLog($collection->getSelect());
		}

		return $collection;
	}

	/**
	 * Get product collection for only one product
	 * @param unknown $product
	 * @param int $store_id
	 */
	public function getProductCollectionByProduct($product, $store_id){

		$collection = $this->getProductCollectionByStoreId($store_id);

		$collection->addAttributeToFilter('entity_id', array('in' => array( $product->getId() )));

		return $collection;
	}

	/**
	 * Get product collection metadata
	 * @param int $store_id
	 * @return array
	 */
	function getProductCollectionMetaData($solrcore)
	{
		$storeIds = $this->getMappedStoreIdsFromSolrCore($solrcore);
		$metaDataArray = array();
		if (is_array($storeIds)) {
			$itemsPerCommit = $this->itemsPerCommit;
			$totalProductCount = 0;

			$loadedStores = array();
			$loadedStoresName = array();

			foreach ($storeIds as $storeId) {
				$storeProductCollection = $this->getProductCollectionByStoreId($storeId);
				$storeProductCount = $storeProductCollection->getSize();
				$totalProductCount += $storeProductCount;
				$metaDataArray['stores'][$storeId]['productCount'] = $storeProductCount;

				$totalPages = ceil($storeProductCount/$itemsPerCommit);
				$metaDataArray['stores'][$storeId]['totalPages'] = $totalPages;
				$metaDataArray['stores'][$storeId]['collection'] = $storeProductCollection;

				$store = Mage::getModel('core/store')->load($storeId);
				$loadedStores[$storeId] = $store;
				$loadedStoresName[$storeId] = $store->getName();
			}
			$metaDataArray['totalProductCount'] = $totalProductCount;
			$metaDataArray['loadedStores'] = $loadedStores;
			$metaDataArray['loadedStoresName'] = $loadedStoresName;
		}
		return $metaDataArray;
	}

	/**
	 * Get product collection metadata
	 * @param int $store_id
	 * @return array
	 */
	function getProductCollectionMetaDataForUpdate($collection, $solrcore)
	{
		$itemsPerCommit = $this->itemsPerCommit;
		$collection = $this->getProductCollectionForUpdate($collection, $solrcore);

		$sql = $collection->getSelectCountSql();

		$productCount = $collection->getConnection()->fetchOne($sql);

		$metaDataArray = array();
		//$productCount = $collection->getSize();
		$metaDataArray['productCount'] = $productCount;
		$totalPages = ceil($productCount/$itemsPerCommit);
		$metaDataArray['totalPages'] = $totalPages;
		$metaDataArray['collection'] = $collection;

		if ($this->writeLog) {
			$this->writeLog(print_r($metaDataArray, true));
		}

		return $metaDataArray;
	}
	
	public function getAllIds($idsSelect)
	{
		$idsSelect = clone $this->getSelect();
		$idsSelect->reset(Zend_Db_Select::ORDER);
		$idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
		$idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
		$idsSelect->reset(Zend_Db_Select::COLUMNS);
	
		$idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
		return $this->getConnection()->fetchCol($idsSelect);
	}

	/**
	 * Parse product collection into json
	 * @param Mage_Catalog_Model_Product_Collection $collection
	 * @param Mage_Core_Model_Store $store
	 * @return array
	 */
	public function parseJsonData($collection, $store, $onlyprice = false)
	{
		$limit = $collection->getSelect()->getPart('limitcount');
		$offset = $collection->getSelect()->getPart('limitoffset');
		$collection->load();
		$productIds = $collection->getLoadedIds();
		
		//$attributeIndexer = Mage::getResourceModel('solrsearch/indexer_attribute');
		//$attributeData = $attributeIndexer->getProductAttributeData($productIds, $store->getId());
		$attributeData = Mage::getResourceModel('solrsearch/product_data')->getProductAttributes($productIds, $store->getId());
		
		$this->fetchedProducts = 0;
		
		/* THIS CODE USING TO FIX MAGENTO 1.6
		$from = $collection->getSelect()->getPart('from');
		$joinCondition = $from['price_index']['joinCondition'];
		$joinCondition = str_replace('OR (price_index.store_id = \'0\')','OR (price_index.store_id = \''.$store->getId().'\')',$joinCondition);
		$from['price_index']['joinCondition'] = $joinCondition;
		$collection->getSelect()->setPart('from', $from);
		*/
		
		$this->documents = "{";
		//Walk collection
		Mage::getSingleton('core/resource_iterator')->walk(
			$collection->getSelect(),
			array(array($this, 'parseProductJsonData')),
			array('store' => $store, 'onlyprice' => $onlyprice, 'attributes' => $attributeData)
		);
		
		$jsonData = trim($this->documents,",").'}';
		
		return array('jsondata'=> $jsonData, 'fetchedProducts' => (int) $this->fetchedProducts);
	}
	/**
	 * Callback function for collection walk
	 * @param unknown $args
	 */
	public function parseProductJsonData( $args )
	{
		$onlyprice 	= $args['onlyprice'];
		$store 		= $args['store'];
		
		$ignoreFields = array('sku', 'price', 'status');
		
		$fetchedProducts = 0;
		
		//included sub products for search
		$included_subproduct = (int)Mage::helper('solrsearch')->getSetting('included_subproduct');
		
		//display brand suggestion
		$display_brand_suggestion = Mage::helper('solrsearch')->getSetting('display_brand_suggestion');
		//display brand suggestion attribute code
		$brand_attribute_code = Mage::helper('solrsearch')->getSetting('brand_attribute_code');
		$brand_attribute_code = trim($brand_attribute_code);
		$includedBrandAttributeCodes = array();
		if ($display_brand_suggestion > 0 && !empty($brand_attribute_code)) {
			$includedBrandAttributeCodes[] = $brand_attribute_code;
		}
		
		//Product search weight attribute code
		$includedSearchWeightAttributeCodes = array();
		$search_weight_attribute_code = Mage::helper('solrsearch')->getSetting('search_weight_attribute_code');
		$search_weight_attribute_code = trim($search_weight_attribute_code);
		if (!empty($search_weight_attribute_code)) 
		{
			$includedSearchWeightAttributeCodes[] = $search_weight_attribute_code;
		}
		
		$index = 1;
		
		$_product = Mage::getModel('catalog/product');
		$_product->setData($args['row']);
		
		$textSearch = array();
		$textSearchText = array();
		$docData = array();
		//Price index only
		if( $onlyprice )
		{
			$docData ['products_id'] = $_product->getId ();
			$docData ['unique_id'] = $store->getId () . 'P' . $_product->getId ();
			$docData ['store_id'] = $store->getId ();
			$docData ['website_id'] = $store->getWebsiteId ();
		
			$stock = Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $_product );
			if ($stock->getIsInStock ()) {
				$docData ['instock_int'] = 1;
			} else {
				$docData ['instock_int'] = 0;
			}
			$docData ['product_status'] = $_product->getStatus ();
		
			$this->updatePriceData ( $docData, $_product, $store );
			$this->documents .= '"set": ' . json_encode ( array ('doc' => $docData) ) . ",";
		}
		else
		{
			$solrBridgeData = Mage::getModel('solrsearch/data');
			$solrBridgeData->setStore($store);
			$solrBridgeData->setBrandAttributes($includedBrandAttributeCodes);
			$solrBridgeData->setSearchWeightAttributes($includedSearchWeightAttributeCodes);
			$solrBridgeData->setIgnoreFields($ignoreFields);
			
			$docData = $solrBridgeData->getProductAttributesData($_product, $args['attributes'][$_product->getId()]);
		
			//Categories
			$solrBridgeData->prepareCategoriesData($_product, $docData);
			//product tags
			$solrBridgeData->prepareTagsData($_product, $docData);
		
			$solrBridgeData->prepareFinalProductData($_product, $docData);
		
			$textSearch = $solrBridgeData->getTextSearch();
			$textSearchText = $solrBridgeData->getTextSearchText();
		
			if (!empty($included_subproduct) && $included_subproduct > 0) {
				if ($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED)
				{
					$associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
					foreach ($associatedProducts as $subproduct) {
						$_subproduct = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($subproduct->getId());
		
						$solrBridgeSubData = Mage::getModel('solrsearch/data');
						$solrBridgeSubData->setStore($store);
						$solrBridgeSubData->setBrandAttributes($includedBrandAttributeCodes);
						$solrBridgeSubData->setSearchWeightAttributes($includedSearchWeightAttributeCodes);
						$solrBridgeSubData->setIgnoreFields($ignoreFields);
						$docSubData = $solrBridgeData->getProductAttributesData($_subproduct, $args['attributes'][$_product->getId()]);
		
						//Categories
						$solrBridgeSubData->prepareCategoriesData($_subproduct, $docSubData);
						//product tags
						$solrBridgeSubData->prepareTagsData($_subproduct, $docSubData);
		
						$solrBridgeSubData->prepareFinalProductData($_subproduct, $docSubData);
		
						$subTextSearch = $solrBridgeSubData->getTextSearch();
						array_push($subTextSearch, $_subproduct->getName());
						$textSearch = array_merge($textSearch, $subTextSearch);
		
						$subTextSearchText = $solrBridgeSubData->getTextSearchText();
						$textSearchText = array_merge($textSearchText, $subTextSearchText);
					}
				}
				else if ($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
				{
					$productIds = $_product->getTypeInstance()->getChildrenIds($_product->getId());
					if (isset($productIds[0]) && !empty($productIds[0])) {
						$collection = $_product->getTypeInstance()->getUsedProductCollection($_product);
						$attributeData = Mage::getResourceModel('solrsearch/product_data')->getProductAttributes($productIds[0], $store->getId());
						foreach ($collection as $subproduct) {
							$_subproduct = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($subproduct->getId());
							$solrBridgeSubData = Mage::getModel('solrsearch/data');
							$solrBridgeSubData->setStore($store);
							$solrBridgeSubData->setBrandAttributes($includedBrandAttributeCodes);
							$solrBridgeSubData->setSearchWeightAttributes($includedSearchWeightAttributeCodes);
							$solrBridgeSubData->setIgnoreFields($ignoreFields);
							$docSubData = $solrBridgeData->getProductAttributesData($_subproduct, $attributeData[$_subproduct->getId()]);
					
							//Collect facets
							foreach ($docSubData as $key => $value) {
								if ( substr($key, -5) == 'facet' ) {
									if (isset($docData[$key])) {
										$docData[$key] = array_merge($docData[$key], $value);
									} else {
										$docData[$key] = $value;
									}
								}
							}
					
							//Categories
							$solrBridgeSubData->prepareCategoriesData($_subproduct, $docSubData);
							//product tags
							$solrBridgeSubData->prepareTagsData($_subproduct, $docSubData);
					
							$solrBridgeSubData->prepareFinalProductData($_subproduct, $docSubData);
					
							$subTextSearch = $solrBridgeSubData->getTextSearch();
							array_push($subTextSearch, $_subproduct->getName());
							$textSearch = array_merge($textSearch, $subTextSearch);
					
							$subTextSearchText = $solrBridgeSubData->getTextSearchText();
							$textSearchText = array_merge($textSearchText, $subTextSearchText);
						}
					}
				}
			}
			
			$uniqueTextSearch = array();
			foreach ( $textSearch as $keyword ) {
			    $lowerText = strtolower($keyword);
			    if ( !in_array($lowerText, $uniqueTextSearch) ) {
			        array_push($uniqueTextSearch, $lowerText);
			    }
			}
			$textSearch = $uniqueTextSearch;
		
			//Prepare text search data
			$docData['textSearch'] = (array) $textSearch;
			$docData['textSearchText'] = (array) $textSearchText;
			$docData['textSearchStandard'] = (array) $textSearch;
			$docData['textSearchGeneral'] = (array) $textSearch;
		
			$this->updatePriceData($docData, $_product, $store);
		
			if (Mage::helper('core')->isModuleEnabled('SolrBridge_Mongo'))
			{
				Mage::dispatchEvent('solrbridge_solrsearch_product_load_for_index', array('product'=>$_product));
			}
			
			try{
				$this->generateThumb($_product);
			}catch (Exception $e){
				$message = Mage::helper('solrsearch')->__('#%s %s at product %s[%s] in store [%s]', $index, $e->getMessage() , $_product->getId(), $_product->getName(), $store->getName());
				$this->writeLog($message, $store->getId(), 'english', 0, true);
			}
			$this->documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
		}
		/*
		$this->documents = '{';
		//Post json data one by one (each product 1 time)
		if (!is_null($this->indexer))
		{
			$partialJsonData = '{';
			$partialJsonData .= '"add": '.json_encode(array('doc'=>$docData));
			$partialJsonData .= '}';
			$this->indexer->postJsonData($partialJsonData);
		}
		*/
		
		$this->fetchedProducts++;
		unset($_product);
	}
	
	public function updatePriceDataNew($data, $product, $store)
	{
		$adapter = $this->getWriteConnection();
		$resource = Mage::getSingleton('core/resource');
		$tableName = $resource->getTableName('catalog/product_index_price');
		
		$select = $adapter->select()
		->from(
				array('t_price_index' => $tableName))
				->where('t_price_index.website_id=?', $store->getWebsiteId())
				->where('t_price_index.entity_id = ?', $product->getId());
		
		$query = $adapter->query($select);
		$prices = array();
		while ($row = $query->fetch())
		{
			$prices[$row['entity_id']][] = $row;
		}
		
		///////////////
		if (isset( $prices[$product->getId()] ))
		{
			$oldCurrency = $store->getData('current_currency');
			$prices = $prices[$product->getId()];
			
			$currencyCodes = $store->getAvailableCurrencyCodes(true);
			foreach ($currencyCodes as $currencycode)
			{
				foreach ($prices as $priceData)
				{
					$code = SolrBridge_Base::getPriceFieldPrefix($currencycode, $priceData['customer_group_id']);
					
					$price = $store->convertPrice($priceData['final_price'], false, false);
					$specialPrice = $store->convertPrice($priceData['final_price'], false, false);
					
					$data[$code.'_price_decimal'] = $price;
					$data[$code.'_special_price_decimal'] = $specialPrice;
					
					$data['sort_'.$code.'_special_price_decimal'] = ($specialPrice > 0)?$specialPrice:$price;
					
					$data[$code.'_special_price_fromdate_int'] = 0;
					$data[$code.'_special_price_todate_int'] = 0;
				}
			}
			$store->setData('current_currency', $oldCurrency);
		}
	}

	public function updatePriceData(&$data, $product, $store)
	{
		$customerGroupCollection = Mage::getResourceModel('customer/group_collection')
		->addTaxClass();

		$storeObject = $store;
		$currenciesCode = $storeObject->getAvailableCurrencyCodes(true);
		foreach ($currenciesCode as $currencycode)
		{
			$currency  = Mage::getModel('directory/currency')->load($currencycode);
			$storeObject->setData('current_currency', $currency);

			foreach ($customerGroupCollection as $group){

				$price = 0;//including tax
				$specialPrice = 0;//including tax
				$sortSpecialPrice = 0;

				$returnData = Mage::getModel('solrsearch/price')->getProductPrice($product, $storeObject, $group->getId());

				if (isset($returnData['price']) && $returnData['price'] > 0) {
				    $price = $returnData['price'];
				}
				if (isset($returnData['special_price']) && $returnData['special_price'] > 0) {
					$specialPrice = $returnData['special_price'];
				}

				$code = SolrBridge_Base::getPriceFieldPrefix($currencycode, $group->getId());

				$data[$code.'_price_decimal'] = $price;
				$data[$code.'_special_price_decimal'] = $specialPrice;

				$data['sort_'.$code.'_special_price_decimal'] = ($specialPrice > 0)?$specialPrice:$price;

				$specialPriceFromDate = 0;

				$specialPriceToDate = 0;

				if ($specialPrice > 0 && isset($returnData['product']) && is_object($returnData['product'])) {
					$specialPriceFromDate = $returnData['product']->getSpecialFromDate();
					$specialPriceToDate = $returnData['product']->getSpecialToDate();
				}
				if ($specialPriceFromDate > 0 && $specialPriceToDate > 0) {
					$data[$code.'_special_price_fromdate_int'] = strtotime($specialPriceFromDate);
					$data[$code.'_special_price_todate_int'] = strtotime($specialPriceToDate);
				}else{
					if (isset($returnData['special_price_from_time']) && $returnData['special_price_from_time'] > 0) {
						$data[$code.'_special_price_fromdate_int'] = $returnData['special_price_from_time'];
					}
					if (isset($returnData['special_price_to_time']) && $returnData['special_price_to_time'] > 0) {
						$data[$code.'_special_price_todate_int'] = $returnData['special_price_to_time'];
					}
				}

				if(isset($data[$code.'_special_price_fromdate_int']) && !is_numeric($data[$code.'_special_price_fromdate_int'])){
					//$data[$code.'_special_price_fromdate_int'] = strtotime($data[$code.'_special_price_fromdate_int']);
					$data[$code.'_special_price_fromdate_int'] = 0;
				}
				if(isset($data[$code.'_special_price_todate_int']) && !is_numeric($data[$code.'_special_price_todate_int'])){
					//$data[$code.'_special_price_todate_int'] = strtotime($data[$code.'_special_price_todate_int']);
					$data[$code.'_special_price_fromdate_int'] = 0;
				}

				if (!isset($data[$code.'_special_price_fromdate_int'])) {
					$data[$code.'_special_price_fromdate_int'] = 0;
				}
				if (!isset($data[$code.'_special_price_todate_int'])) {
					$data[$code.'_special_price_todate_int'] = 0;
				}
			}
		}
	}

	/**
	 * Generate product thumbnails
	 * @param unknown_type $product
	 */
	public function generateThumb($product, $debug = false){

		$thumsize = Mage::helper('solrsearch')->getSetting('autocomplete_thumb_size');
		$width = 32;
		$height = 32;
		if (!empty($thumsize)) {
			$thumbSizeArray = explode('x', $thumsize);
			if (isset($thumbSizeArray[0]) && is_numeric($thumbSizeArray[0])) {
				if (isset($thumbSizeArray[1]) && is_numeric($thumbSizeArray[1])) {
					$width = trim($thumbSizeArray[0]);
					$height = trim($thumbSizeArray[1]);
				}
			}
		}

		$productId = $product->getId();

		$image = trim($product->getSmallImage());
		if (empty($image) || $image == 'no_selection') {
			$image = trim($product->getImage());
		}

		if (empty($image) || $image == 'no_selection'){
			$productImagePath = Mage::helper('solrsearch/image')->getImagePlaceHolder();
		}else{
			$productImagePath = Mage::getBaseDir("media").DS.'catalog'.DS.'product'.DS.$image;
		}

		if (!file_exists($productImagePath)){
			if ($product->getImage() != 'no_selection' && $product->getImage()){
				$productImagePath = Mage::helper('solrsearch/image')->init($product, 'image')->resize($width, $height)->getImagePath();

				if (!file_exists($productImagePath)){
				    $productImagePath = Mage::helper('solrsearch/image')->getImagePlaceHolder();
				}
			}
		}

		$productImageThumbPath = Mage::getBaseDir('media').DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
		if (file_exists($productImageThumbPath)) {
			unlink($productImageThumbPath);
		}
		$imageResizedUrl = Mage::getBaseUrl("media").DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
		try{
			$imageObj = new Varien_Image($productImagePath);
			$imageObj->constrainOnly(FALSE);
			$imageObj->keepAspectRatio(TRUE);
			$imageObj->keepFrame(FALSE);
			$imageObj->backgroundColor(array(255,255,255));
			//$imageObj->keepTransparency(TRUE);
			$imageObj->resize($width, $height);
			$imageObj->save($productImageThumbPath);
		}catch (Exception $e){
			$exceptionMessage = 'Exception at product['.$productId.']['.$productImageThumbPath.']:'.$e->getMessage();
			$this->writeLog($exceptionMessage, 0, '', '', true);
		}
		if (file_exists($productImageThumbPath)) {
			return true;
		}
		return false;
	}

	/**
	 * Get product attribute collection
	 * this function deprecated since version 1.9.6.10
	 */
	public function getProductAttributeCollection()
	{
		$cachedKey = 'solrbridge_solrsearch_product_attribute_'.Mage::app()->getStore()->getId().'_'.Mage::app()->getStore()->getWebsiteId();

		if (false !== ($attributesInfo = Mage::app()->getCache()->load($cachedKey)))
		{
			return unserialize($attributesInfo);
		}
		else
		{
			$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
			$catalogProductEntityTypeId = $entityType->getEntityTypeId();
			$attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($catalogProductEntityTypeId)
			->addSetInfo()
			->setOrder('position', 'ASC')
			->getData();

			Mage::app()->getCache()->save(serialize($attributesInfo), $cachedKey, array('solrbridge_solrsearch'));
		}

		return $attributesInfo;
	}

	/**
	 * Retrive available stores
	 * @return array
	 */
	public function getAvailableCores() {
		//return (array) Mage::getStoreConfig('solrbridgeindices', 0);
	    return Mage::getModel('solrsearch/adminhtml_system_source_config_cores')->toArray();
	}
	/**
	 * Get store ids which mapped to Solr core
	 * @param string $solrcore
	 * @return array
	 */
	public function getMappedStoreIdsFromSolrCoreOLD($solrcore)
	{
		$storeIdArray = array();

		if (isset($solrcore) && !empty($solrcore)) {
			$availableCores = $this->getAvailableCores();
			if ( isset($availableCores[$solrcore]) && isset($availableCores[$solrcore]['stores']) )
			{
				if ( strlen( trim($availableCores[$solrcore]['stores'], ',') ) > 0 )
				{
					$storeIdArray = trim($availableCores[$solrcore]['stores'], ',');
					$storeIdArray = array_map('intval', explode(',', $storeIdArray));
				}
			}
		}
		return $storeIdArray;
	}
	
	/**
	 * Get store ids which mapped to Solr core
	 * @param string $solrcore
	 * @return array
	 */
	public function getMappedStoreIdsFromSolrCore($solrcore)
	{
	    $storeIdArray = array();
	
	    $indexCollection = Mage::getModel('solrsearch/index')->getCollection();
	    $indexCollection->addFieldToFilter('solr_core', array('eq' => $solrcore));
	
	    foreach ($indexCollection as $index) {
	        $storeIdArray[] = $index->getStoreId();
	    }
	
	    return $storeIdArray;
	}
	
	/**
	 * Get solr cores by store id
	 * @param int $solrcore
	 * @return array
	 */
	public function getSolrcoresByStoreId($storeId)
	{
		$coreArray = array();
	
		if (isset($storeId) && $storeId > 0) 
		{
			//Get all available solr cores
			$availableCores = $this->getAvailableCores();
			foreach ($availableCores as $solrcore => $coredata)
			{
				if ( isset( $coredata['stores'] ) )
				{
					if ( strlen( trim($coredata['stores'], ',') ) > 0 )
					{
						$storeIdArray = trim($coredata['stores'], ',');
						$storeIdArray = array_map('intval', explode(',', $storeIdArray));
						if ( in_array($storeId, $storeIdArray) )
						{
							$coreArray[] = $solrcore;
						}
					}
				}
			}
		}
		return array_unique($coreArray);
	}

	public function getRootCategoryIds($storeIds = array())
	{
		$rootCatIds = array();
		if (isset($storeIds) && !empty($storeIds))
		{
			foreach ($storeIds as $storeId){
				$catId = Mage::getModel('core/store')->load($storeId)->getRootCategoryId();
				if (!empty($catId)) {
					$rootCatIds[] = $catId;
				}
			}
		}
		return array_map('intval', array_unique($rootCatIds));
	}

	/**
	 * This function is deprecated since version 1.9.6.8
	 * @param unknown $docData
	 * @param unknown $parentProduct
	 * @param unknown $store
	 */
	public function updateDocDataForConfigurableProducts(&$docData, $parentProduct, $store)
	{
		if($parentProduct->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
			$collection = $parentProduct->getTypeInstance()->getUsedProductCollection($parentProduct);

			foreach ($collection as $product) {
				$textSearch = array();

				$_product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
				$atributes = $_product->getAttributes();
				foreach ($atributes as $key=>$atributeObj) {
					$backendType = $atributeObj->getBackendType();
					$frontEndInput = $atributeObj->getFrontendInput();
					$attributeCode = $atributeObj->getAttributeCode();
					$attributeData = $atributeObj->getData();

					if (!$atributeObj->getIsSearchable()) continue; // ignore fields which are not searchable

					if ($backendType == 'int') {
						$backendType = 'varchar';
					}

					$attributeKey = $key.'_'.$backendType;

					$attributeKeyFacets = $key.'_facet';

					if (!is_array($atributeObj->getFrontEnd()->getValue($_product))){
						$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
					}else {
						$attributeVal = $atributeObj->getFrontEnd()->getValue($_product);
						$attributeVal = implode(' ', $attributeVal);
					}

					if ($_product->getData($key) == null)
					{
						$attributeVal = null;
					}
					$attributeValFacets = array();
					if (!empty($attributeVal) && $attributeVal != 'No') {
						if($frontEndInput == 'multiselect') {
							$attributeValFacetsArray = @explode(',', $attributeVal);
							$attributeValFacets = array();
							foreach ($attributeValFacetsArray as $val) {
								$attributeValFacets[] = trim($val);
							}
						}else {
							$attributeValFacets[] = trim($attributeVal);
						}

						if ($backendType == 'datetime') {
							$attributeVal = date("Y-m-d\TG:i:s\Z", $attributeVal);
						}

						if (!in_array($attributeVal, $textSearch) && $attributeVal != 'None' && $attributeCode != 'status' && $attributeCode != 'sku'){
							$textSearch[] = $attributeVal;
						}

						if (
						(isset($attributeData['is_filterable_in_search']) && !empty($attributeData['is_filterable_in_search']) && $attributeValFacets != 'No' && $attributeKey != 'price_decimal' && $attributeKey != 'special_price_decimal')
						) {
							if (isset($docData[$attributeKeyFacets])) {
								$docData[$attributeKeyFacets] = array_merge($docData[$attributeKeyFacets], $attributeValFacets);
							}else{
								$docData[$attributeKeyFacets] = $attributeValFacets;
							}
							if (is_array($docData[$attributeKeyFacets]) && count($docData[$attributeKeyFacets]) > 0) {
								$docData[$attributeKeyFacets] = array_unique($docData[$attributeKeyFacets]);
							}

						}
					}

				}
				$docData['textSearch'] = array_merge($docData['textSearch'], $textSearch);
			}
		}
	}
}
?>