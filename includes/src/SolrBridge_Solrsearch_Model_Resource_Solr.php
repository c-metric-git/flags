<?php
/**
 * @category SolrBridge
 * @package Solrbridge_Search
 * @author	Hau Danh
 * @copyright	Copyright (c) 2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Model_Resource_Solr extends SolrBridge_Solrsearch_Model_Resource_Base {
	/**
	 * Solr server url
	 * @var string
	 */
	public $_solrServerUrl = 'http://localhost:8080/solr/';
	/**
	 * Solr core, it is the language name like english, german, etc
	 * @var string
	 */
	public $core = 'english';
	/**
	 * Get Solr server url from Magento setting
	 * @return string
	 */
    public function getSolrServerUrl()
    {
    	return $this->getSetting('solr_server_url');
    }
    public function getSolrUrl( $path, $solrcore = null ) {
        $solrServerUrl = $this->getSolrServerUrl();
        if ( !empty($solrcore) ) {
            return trim($solrServerUrl,'/').'/'.$solrcore.'/'.trim($path, '/');
        }
        return trim($solrServerUrl,'/').'/'.trim($path, '/');
    }
    /**
     * Ping to see if solr server available or not
     * @param string $core
     * @return boolean
     */
    public function pingSolrCore($solrcore = 'english') {
    	$solrServerUrl = $this->getSolrServerUrl();
    	$pingUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/admin/ping?wt=json';
    	$result = $this->doRequest($pingUrl);
    	if (isset($result['status']) && $result['status'] == 'OK') {
    		return true;
    	}
    	return false;
    }
    /**
     * Get solr core statistic to find how many documents exist
     * @param string $solrcore
     * @return array
     */
    public function getSolrLuke($solrcore) {
    	$solrServerUrl =$this->getSolrServerUrl();
    	$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/admin/luke?reportDocCount=true&fl=products_id&wt=json';
    	$result = $this->doRequest($queryUrl);
    	return $result;
    }
    public function getDocumentCount($solrcore, $storeId = null) {
    	$documentCount = 0;
    	try{
    		$solrServerUrl =$this->getSolrServerUrl();
    		$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=products_id:*&rows=-1&wt=json';
    		if (!empty($storeId) && is_numeric($storeId)) {
    		    $queryUrl .= '&fq=store_id:'.$storeId;
    		}
    		$result = $this->doRequest($queryUrl);
    		if ( isset($result['response']['numFound']) ) {
    			$documentCount = $result['response']['numFound'];
    		}
    	} catch (Exception $e) {
    		$this->ultility->writeLog($e->getMessage(), 1, $solrcore, 0, true);
    	}
    	return $documentCount;
    }
	/**
	 * Get all documents
	 * @param string $solrcore
	 * @return array
	 */
    public function getAllDocuments($solrcore) {
    	$total = Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);
    	$solrServerUrl =$this->getSolrServerUrl();
    	$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows='.$total.'&wt=json';
    	return $this->doRequest($queryUrl);
    }

    /**
     * Request Solr Server by CURL
     * @param string $url
     * @param mixed $postFields
     * @param string $type
     * @return array
     */
    public function doRequest($url, $postFields = null, $type='array') {
    	$sh = curl_init($url);
    	curl_setopt($sh, CURLOPT_HEADER, 0);
    	if(is_array($postFields)) {
    		curl_setopt($sh, CURLOPT_POST, true);
    		curl_setopt($sh, CURLOPT_POSTFIELDS, $postFields);
    	}
    	curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);

    	curl_setopt( $sh, CURLOPT_FOLLOWLOCATION, true );
    	if ($type == 'json') {
    		curl_setopt( $sh, CURLOPT_HEADER, true );
    	}

    	if (isset($_GET['user_agent']) || isset($_SERVER['HTTP_USER_AGENT'])) {
    		curl_setopt( $sh, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
    	}

    	$this->setupSolrAuthenticate($sh);

    	if ($type == 'json') {
    		list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec($sh), 2 );
    		$output = preg_split( '/[\r\n]+/', $contents );
    	}else{
    		$output = curl_exec($sh);
    		$output = json_decode($output,true);
    	}

    	curl_close($sh);
    	return $output;
    }

    /**
     * Setup Solr authentication user/pass if neccessary
     * @param resource $sh
     */
    public function setupSolrAuthenticate(&$sh) {
    	$isAuthentication = 0;
    	$authUser = '';
    	$authPass = '';

    	$isAuthenticationCache = Mage::app()->loadCache('solr_bridge_is_authentication');
    	if ( isset($isAuthenticationCache) && !empty($isAuthenticationCache) ) {
    		$isAuthentication = $isAuthenticationCache;
    		$authUser = Mage::app()->loadCache('solr_bridge_authentication_user');
    		$authPass = Mage::app()->loadCache('solr_bridge_authentication_pass');
    	}else {
    		// Save data to cache
    		$isAuthentication = $this->getSetting('solr_server_url_auth');
    		$authUser = $this->getSetting('solr_server_url_auth_username');
    		$authPass = $this->getSetting('solr_server_url_auth_password');

    		Mage::app()->saveCache($isAuthentication, 'solr_bridge_is_authentication', array('solrbridge_solrsearch'), 60*60*24);
    		Mage::app()->saveCache($authUser, 'solr_bridge_authentication_user', array('solrbridge_solrsearch'), 60*60*24);
    		Mage::app()->saveCache($authPass, 'solr_bridge_authentication_pass', array('solrbridge_solrsearch'), 60*60*24);
    	}

    	if (isset($isAuthentication) && $isAuthentication > 0 ) {
    		curl_setopt($sh, CURLOPT_USERPWD, $authUser.':'.$authPass);
    		curl_setopt($sh, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    	}
    }
	/**
	 * Remove solrbridge indices tables when Solr index data empty
	 * @param array $storeids
	 */
    public function truncateIndexTables($solrcore) {
    	$resource = Mage::getSingleton('core/resource');
    	$connection = $resource->getConnection('core_write');
    	$logtable = $resource->getTableName('solrsearch/logs');

    	if (!empty($solrcore)) {
    		$indexTableName = $this->getIndexTableName($logtable, $solrcore);
    		if (!empty($indexTableName) && $this->isIndexTableNameExist($indexTableName)) {
    			$truncateSql = "TRUNCATE TABLE {$indexTableName}";
    			$connection->query($truncateSql);
    		}
    	}
    }
	/**
	 * Get solr bridge index table name
	 * @param string $logTableName
	 * @param number $storeId
	 * @return string
	 */
    public function getIndexTableName($logTableName, $solrCore) {
    	$indexedTableName = str_replace('_logs', '_index_'.$solrCore, $logTableName);

    	return $indexedTableName;
    }
    public function isIndexTableNameExist($indexTableName) {
    	return $this->isTableExists($indexTableName);
    }
	/**
	 * Create index table if not exist
	 * @param string $logTableName
	 * @param number $storeId
	 * @param string $writeConnection
	 * @return string|boolean
	 */
    public function prepareIndexTable($logTableName, $solrCore, $writeConnection = null) {
    	if (!empty($logTableName) && !empty($solrCore)) {
    		if (!$writeConnection) {
    			$resource = Mage::getSingleton('core/resource');
    			$writeConnection = $resource->getConnection('core_write');
    		}

    		$indexedTableName = $this->getIndexTableName($logTableName, $solrCore);
    		
    		//Drop index table if exists
    		if ( $this->isIndexTableNameExist($indexedTableName) ) {
    			$writeConnection->query('DROP TABLE '.$indexedTableName);
    		}
    		
    		if (!$this->isIndexTableNameExist($indexedTableName)) {
    			$table = $writeConnection
    			->newTable($indexedTableName)
    			->setOption('type', 'MEMORY')
    			->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    					'unsigned'  => true,
    					'nullable'  => false,
    					'primary'   => true,
    					'default'   => '0',
    			), 'Store ID')
    			->addColumn('update_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    					'default'   => 'CURRENT_TIMESTAMP',
    			), 'Update Time')
    			->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    					'unsigned'  => true,
    					'nullable'  => false,
    					'primary'   => true,
    					'default'   => '0',
    			), 'Product ID')
    			->addColumn('changed', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
    			    'unsigned'  => true,
    			    'nullable'  => false,
    			    'default'   => '0',
    			), 'Item Changed')
    			->addIndex("IDX_SOLRBRIDGE_SOLRSEARCH_INDEX_".strtoupper($solrCore)."_STORE_ID", array('store_id'))
    			->addIndex("IDX_SOLRBRIDGE_SOLRSEARCH_INDEX_".strtoupper($solrCore)."_VALUE", array('value'));

    			$writeConnection->createTable($table);
    			$writeConnection->modifyColumn($indexedTableName, 'update_at', 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    		}
    		return $indexedTableName;
    	}
    	return false;
    }

	/**
	 * Generate index tables
	 */
    public function generateIndexTables() {
    	//Get available solr cores
    	$availableCores = $this->ultility->getAvailableCores();

    	if ( isset($availableCores) && !empty($availableCores) )
    	{
    		foreach ($availableCores as $solrcore => $info)
    		{
    			if (isset($info['stores']) && strlen(trim($info['stores'], ',')) > 0) {

    				$logTableName = $this->ultility->getLogTable();

    				$this->prepareIndexTable($logTableName, $solrcore);
    			}
    		}
    	}
    }

    /**
     * Synchronize data between Magento database and Solr index
     * @param string $solrCore
     * @return boolean
     */
    public function synchronizeData($solrCore) {
    	//Check if index table exists
        $logtable = $this->ultility->getLogTable();
        $indexTableName =  $this->getIndexTableName($logtable, $solrCore, $this->writeConnection);

        if (!$this->isIndexTableNameExist($indexTableName))
        {
            $logTableName = $this->ultility->getLogTable();
            $this->prepareIndexTable($logTableName, $solrCore);
            //$messge = Mage::helper('solrsearch')->__('The index table %s does not exists, please go to SolrBridge > Indices settings and click the button Save config and try again.', $indexTableName);
            //throw new Exception($messge);
        }

        //Find records which has changed = 1
        $sqlQuery = "SELECT * FROM {$indexTableName} WHERE `changed` > 0";
        $changedItems = $this->writeConnection->fetchAll($sqlQuery);
        foreach ($changedItems as $itemInfo)
        {
            if (isset($itemInfo['value']) && isset($itemInfo['store_id'])) {
                $this->deleteSolrDocument($itemInfo['value'], $solrCore, $itemInfo['store_id']);
            }
        }

    	//Get available solr cores
    	$availableCores = $this->ultility->getAvailableCores();

    	if ( isset($availableCores[$solrCore]) ) {
    	    //Check if there is any store mapped to the $solrCore
    	    $indexCollection = Mage::getModel('solrsearch/index')->getCollection();
    	    $indexCollection->addFieldToFilter('solr_core', array('eq' => $solrCore));
    	    
    		if ( $indexCollection->getSize() > 0 ) {
    			$returnData = $this->getAllDocuments($solrCore);

    			if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0) {
    				if (is_array($returnData['response']['docs'])) {
    					$index = 0;
    					$logIds = array();
    					foreach ($returnData['response']['docs'] as $doc) {

    						if (isset($doc['products_id']) && $doc['store_id'] && is_numeric($doc['products_id']) && is_numeric($doc['store_id'])) {

    							$logIds[] = array('productid' => $doc['products_id'], 'storeid' => $doc['store_id']);
    						}
    						if ($index >= 20) {
    							$this->ultility->logProductId($logIds, $solrCore);
    							$logIds = array();
    							$index = 0;
    							continue;
    						}
    						$index++;
    					}

    					if (!empty($logIds)) {
    						$this->ultility->logProductId($logIds, $solrCore);
    					}
    				}
    			}
    		}
    	}else{
    		return false;
    	}
    }
    
    /**
     * Find all documents which has products_id does not exists in magento database
     * Then remove solr documents
     * @param unknown $solrcore
     */
    public function cleanSolrData( $solrcore )
    {
    	$storeIds = $this->ultility->getMappedStoreIdsFromSolrCore( $solrcore );
    	
    	if ( !empty($storeIds) )
    	{
    		$solrServerUrl =$this->getSolrServerUrl();
    		
    		//Post new products
    		//Import existing products into index table
    		$resource = Mage::getSingleton('core/resource');
    		$readConnection = $resource->getConnection('core_read');
    		$writeConnection = $resource->getConnection('core_write');
    		$producttable = $resource->getTableName('catalog/product');
    		
    		$logTableName = $this->ultility->getLogTable();
    		$indexTableName =  $this->getIndexTableName($logTableName, $solrcore);
    		
    		if (!$this->isIndexTableNameExist($indexTableName))
    		{
    			$this->prepareIndexTable($logTableName, $solrcore, $writeConnection);
    		}
    		
    		$writeConnection->query('TRUNCATE TABLE `'.$indexTableName.'`');
    		
    		//Loop store ids which mapped to solr core
    		foreach ($storeIds as $storeid)
    		{
    			$storeObject = Mage::getModel('core/store')->load($storeid);
    			
    			//Get total documents by store id
    			$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&wt=json&rows=-1&fq=store_id:'.$storeid;
    			$result = $this->doRequest($queryUrl);
    			$numfound = $result['response']['numFound'];
    			
    			//Get all products_id
    			$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=-1&wt=json&indent=true&json.nl=map&facet=true&facet.field=products_id&facet.limit='.$numfound;
    			$result = $this->doRequest($queryUrl);
    			
    			$existingProductIds = array();
    			if ( isset($result['facet_counts']['facet_fields']['products_id']) )
    			{
    				$existingProductIds = array_keys( $result['facet_counts']['facet_fields']['products_id'] );
    			}
    			
    			$insertSql = "REPLACE INTO `'.$indexTableName.'` (`store_id`, `value`, `changed`) VALUES ";
    			$insertSqlValues = '';
    			foreach ($existingProductIds as $productId)
    			{
    				$insertSqlValues .= '(1, '.$productId.', 0),';
    			}
    			if ( !empty($insertSqlValues) )
    			{
    				$insertSql .= trim($insertSqlValues, ',');
    				$writeConnection->query($insertSql);
    			}
    		}
    		
    		//Finding solr documents which does not exists in Magento
    		$select = $readConnection->select()->from(array('wbmsslogs' => $indexTableName), array('value'));
    		$select->joinLeft(
    				array('e' => $producttable),
    				'wbmsslogs.value = e.entity_id',
    				array('wbmsslogs_entity_id' => 'wbmsslogs.value')
    		)
    		->where('e.entity_id IS NULL');
    		
    		$notExistingProductIds = $readConnection->fetchCol($select);
    		
    		if (count($notExistingProductIds) > 0)
    		{
    			$index = 1;
    			$deletedProductIds = array();
    			foreach ($notExistingProductIds as $productId)
    			{
    				echo $index." - Delete solr document #".$productId."\n";
    				$deletedProductIds[] = $productId;
    				$index++;
    				if($index >= 100)
    				{
    					echo "Started solr committed.....\n";
    					Mage::getResourceModel('solrsearch/solr')->deleteDocuments($deletedProductIds, 1);
    					$deletedProductIds = array();
    					$index = 1;
    				}
    			}
    		}
    	}
    }
    
    
    /**
     * Update single product which related in Solr index
     * @param int $productid
     */
    public function updateSingleProduct($productid, $store_id = 0)
    {
    	$availableCores = $this->ultility->getAvailableCores();

    	foreach ($availableCores as $solrcore => $infoArray)
    	{
    		if ($store_id > 0){
    			$this->updateSingleProductBySolrCore($solrcore, $productid, $store_id);
    		}else{
    			$storeIds = $this->ultility->getMappedStoreIdsFromSolrCore($solrcore);
    			if (is_array($storeIds) && !empty($storeIds)) {
    				foreach ($storeIds as $storeid) {
    					$this->updateSingleProductBySolrCore($solrcore, $productid, $storeid);
    				}
    			}
    		}
    	}
    }
    /**
     * Update single product which related in Solr index
     * @param int $productid
     */
    public function deleteSingleDocument($productid, $store_id = 0)
    {
    	$availableCores = $this->ultility->getAvailableCores();
    
    	foreach ($availableCores as $solrcore => $infoArray)
    	{
    		if ($store_id > 0){
    			$this->deleteSolrDocument($productid, $solrcore, $store_id);
    		}else{
    			$storeIds = $this->ultility->getMappedStoreIdsFromSolrCore($solrcore);
    			if (is_array($storeIds) && !empty($storeIds)) {
    				foreach ($storeIds as $storeid) {
    					$this->deleteSolrDocument($productid, $solrcore, $store_id);
    				}
    			}
    		}
    	}
    }
    
    /**
     * Update single product which related in Solr index
     * @param int $productid
     */
    public function deleteDocuments($productIds = array(), $store_id = 0)
    {
    	$availableCores = $this->ultility->getAvailableCores();
    	$solrServerUrl =$this->getSolrServerUrl();
    
    	foreach ($availableCores as $solrcore => $infoArray)
    	{
    		if (!empty($productIds) )
    		{
    			$queryString = '';
    			foreach ($productIds as $id)
    			{
    				$queryString .= '<query>products_id:'.$id. '</query>';
    			}
    			
    			$deleteDocumentUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete>'.$queryString.'</delete>&commit=true';
    			
    			$this->doRequest($deleteDocumentUrl);
    		}
    	}
    }

    public function updateSingleProductBySolrCore($solrcore, $productid, $storeid)
    {
    	if (!empty($solrcore) && !empty($productid) && !empty($storeid)) {
    		$_product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($productid);

    		if ( !$_product || (isset($_product) && !$_product->getId()) ) {//product deleted
    			$this->deleteSolrDocument($productid, $solrcore, $storeid);
    		}
    		else if($_product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) //Product disabled
    		{
    			$this->deleteSolrDocument($_product->getId(), $solrcore, $storeid);
    		}
    		else
    		{
    			if ($_product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
    			{
    				$this->deleteSolrDocument($_product->getId(), $solrcore, $storeid);
    			}
    			else
    			{
    				$this->updateSolrDocument($solrcore, $_product, $storeid);
    				$this->cleanUpIndexData($solrcore, $_product);
    			}
    		}
    	}
    }
    /**
     * Find and remove all solr documents which mapped to Magento product, but the magento product has no website assigned
     * @param string $solrcore
     * @param Mage_Catalog_Model_Product $_product
     */
    public function cleanUpIndexData($solrcore, $_product)
    {
        $productStoreIds = $_product->getStoreIds();
        $mappedStoreIds = $this->ultility->getMappedStoreIdsFromSolrCore($solrcore);

        if (is_array($productStoreIds) && is_array($mappedStoreIds) && $_product->getId() > 0) {
            $storeIds = array_diff($mappedStoreIds, $productStoreIds);

            if (is_array($storeIds)) {
                $queryString = '';
                foreach ($storeIds as $storeid) {
                    if ($storeid > 0) {
                        $queryString .= '<query>store_id:'.$storeid.'+AND+products_id:'.$_product->getId().'</query>';
                    }
                }
                if (!empty($queryString)) {
                    $solrServerUrl =$this->getSolrServerUrl();
                    $deleteDocumentUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete>'.$queryString.'</delete>&commit=true&json.nl=map&wt=json';
                    $this->doRequest($deleteDocumentUrl);
                }
            }
        }
    }

	/**
	 * Update solr document by product and store id
	 * @param string $solrcore
	 * @param Mage_Catalog_Model_Product $product
	 * @param int $storeid
	 */
    public function updateSolrDocument($solrcore, $product, $storeid)
    {
    	$solrServerUrl =$this->getSolrServerUrl();
    	$updateUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update/json?commit=true&wt=json';
    	$storeObject = Mage::getModel('core/store')->load($storeid);
    	$collection = $this->ultility->getProductCollectionByProduct($product, $storeid);
    	$dataArray = $this->ultility->parseJsonData($collection, $storeObject);
    	$jsonData = $dataArray['jsondata'];
    	$returnNoOfDocuments = $this->postJsonData($jsonData, $updateUrl, $solrcore);
    	$msg = 'Updated solr document for the product ['.$product->getName.'] in store ['.$storeid.'] successfully';
    	$this->ultility->writeLog($msg, $storeid, $solrcore, 0, true);
    	$this->ultility->updateProductLogId($product->getId(), 0);
    	Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
    }
	/**
	 * Delete solr document by product id
	 * @param int $productid
	 * @param string $solrcore
	 * @param int $storeid
	 */
    public function deleteSolrDocument($productid, $solrcore, $storeid)
    {

    	$solrServerUrl =$this->getSolrServerUrl();
    	$existingSolrDocumentCount = Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);
    	if ($existingSolrDocumentCount > 0)
    	{
    		$deleteDocumentUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete><query>products_id:'.$productid.'</query></delete>&commit=true&optimize=false&waitFlush=false';
    		if ($storeid > 0) {
    			$deleteDocumentUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete><query>unique_id:'.$storeid.'P'.$productid.'</query></delete>&commit=true&optimize=false&waitFlush=false';
    		}
    		$this->doRequest($deleteDocumentUrl);
    		//Remove product id from log table
    		$this->ultility->removeLogProductId($productid, $solrcore);
    		$msg = 'Removed solr document for the product ['.$productid.'] in store ['.$storeid.'] successfully';
    		$this->ultility->writeLog($msg, $storeid, $solrcore, 0, true);
    	}

    	Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
    }
    /**
     * Delete solr documents by category id
     * @param int $categoryid
     * @param string $solrcore
     * @param int $storeid
     */
    public function deleteSolrDocumentByCategory($categoryid, $solrcore, $storeid)
    {
    	$solrServerUrl =$this->getSolrServerUrl();
    	$existingSolrDocumentCount = Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);

    	if ( (int)$existingSolrDocumentCount > 0 ) {
    		try{
    			$currentCat = Mage::getModel('catalog/category')->load($categoryid);
    			$catIds = array($categoryid);
    			$childrenCatIds = Mage::getModel('catalog/category')->getResource()->getChildren($currentCat, true);
    			if (is_array($childrenCatIds) && !empty($childrenCatIds)){
    				$catIds = array_merge($catIds, $childrenCatIds);
    			}
    			$queryString = '';
    			$queryString2 = '';
    			if (is_array($catIds) && !empty($catIds)) {
    				$count = count($catIds);
    				$index = 1;
    				foreach ($catIds as $id) {
    					$queryString .= '<query>category_id:'.$id.'</query>';
    					if ($index == $count) {
    						$queryString2 .= 'category_id:'.$id;
    					}else{
    						$queryString2 .= 'category_id:'.$id.'+OR+';
    					}
    					$index++;
    				}
    			}

    			if (!empty($queryString) && !empty($queryString2)) {

    				//Get all documents which belong to categories to be removed
    				$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&fq='.trim($queryString2,'+OR+').'&json.nl=map&wt=json';

    				$solrData = $this->doRequest($queryUrl);
    				$productIds = array();
    				if (is_array($solrData) && isset($solrData['response']['numFound'])) {
    					$numCount = $solrData['response']['numFound'];
    					if ( (int)$numCount > 0 ){
    						if ( isset($solrData['response']['docs']) && is_array($solrData['response']['docs']) ) {
    							foreach ($solrData['response']['docs'] as $doc){
    								if ( isset($doc['products_id']) && is_numeric(trim($doc['products_id'])) ) {
    									$productIds[] = $doc['products_id'];
    								}
    							}
    						}
    					}
    				}

    				$Url = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete>'.$queryString.'</delete>&commit=true&json.nl=map&wt=json';
    				$data = $this->doRequest($Url);

    				if ( is_array($productIds) && !empty($productIds) ) {
    					$this->ultility->removeLogProductId($productIds, $solrcore);

    					$msg = 'Removed ['.count($productIds).'] solr documents which belong to category id ['.$categoryid.'] and it\'s children from solr core ['.$solrcore.']';
    					$msg = mysql_real_escape_string($msg);
    					$this->ultility->writeLog($msg, $storeid, $solrcore, 0, true);
    				}
    			}
    		}catch (Exception $e){
    			$msg = '[ERROR] - '.$e->getMessage();
    			$this->ultility->writeLog($msg, 0, '', 0, true);
    		}
    	}
    	Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
    }
    /**
     * When a category save, this function do:
     * 1. remove/add category to documents according to which products assigned to the category
     * 2. update category product position for sorting
     * @param object $category
     * @param string $solrcore
     * @param int $storeid
     */
    public function updateDocumentsByCategory($category, $solrcore, $storeid)
    {
    	//get all documents has category id
    	$solrServerUrl =$this->getSolrServerUrl();
    	$queryUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,category_id,category_facet,category_text,category_boost,category_boost_exact,category_relative_boost,category_path&fq=category_id:'.$category->getId().'&json.nl=map&wt=json';
    	$solrData = $this->doRequest($queryUrl);
    	//Products id which still exist in solr
    	//$existingData = array();
    	$existingCategoryData = array();
    	$productIds = array();
    	if (is_array($solrData) && isset($solrData['response']['numFound'])) {
    		$numCount = $solrData['response']['numFound'];
    		if ( (int)$numCount > 0 ){
    			if ( isset($solrData['response']['docs']) && is_array($solrData['response']['docs']) ) {
    				foreach ($solrData['response']['docs'] as $doc){
    					if ( isset($doc['products_id']) && is_numeric(trim($doc['products_id'])) ) {
    						$productIds[] = $doc['products_id'];
    						//$existingData[$doc['products_id']] = $doc['category_id'];
    						$existingCategoryData[$doc['products_id']] = array(
    							'category_id' => $doc['category_id'],
    							'category_facet' => $doc['category_facet'],
    							'category_text' => $doc['category_text'],
    							'category_boost' => $doc['category_boost'],
    							'category_boost_exact' => $doc['category_boost_exact'],
    							'category_relative_boost' => $doc['category_relative_boost'],
    							'category_path' => $doc['category_path']
    						);
    					}
    				}
    			}
    		}
    	}
    	//Retrieve all product ids which belong to category (in magento)
    	$positions = Mage::getResourceModel('catalog/category')->getProductsPosition($category);
    	$magentoProductIds = array_keys($positions);
    	//get product ids which match 
    	$matchedProductIds = array_intersect($magentoProductIds, $productIds);
    	//Mage::log(print_r($magentoProductIds, true), null, 'sbsolr.log');
    	//Mage::log(print_r($productIds, true), null, 'sbsolr.log');
    	$diff1 = array_diff($magentoProductIds, $productIds);
    	$diff2 = array_diff($productIds, $magentoProductIds);
    	$unmatchedProductIds = array_merge($diff1, $diff2);
    	//Mage::log(print_r($unmatchedProductIds, true), null, 'sbsolr.log');
    	$documents = '';
    	$store = Mage::app()->getStore($storeid);
    	foreach ($matchedProductIds as $productId)
    	{
    		$docData ['products_id'] = $productId;
    		$docData ['unique_id'] = $store->getId() . 'P' . $productId;
    		$docData ['store_id'] = $store->getId();
    		$docData ['website_id'] = $store->getWebsiteId();
    		if ( isset($positions[ $productId ]) )
    		{
    			$docData['sort_cat_'.$category->getId().'_pos_int'] = array('set' => $positions[ $productId ]);
    		}
    		$documents .= json_encode ( $docData ) . ",";
    	}
    	
    	foreach ($unmatchedProductIds as $productId)
    	{
    		if ( isset($existingCategoryData[$productId]) )
    		{
    			$docCategoryIds = array();
    			
    			//Prepare to update (add/remove) category id
    			if ( isset($existingCategoryData[$productId]['category_id']) )
    			{
    				foreach ($existingCategoryData[$productId]['category_id'] as $catId)
    				{
    					if ( (int)$catId !== (int)$category->getId() )
    					{
    						$docCategoryIds[] = $catId;
    					}
    				}
    			}
    			
    			//Prepare category boosts
    			$categoryTexts = array();
    			if ( isset($existingCategoryData[$productId]['category_text']) )
    			{
    				foreach ($existingCategoryData[$productId]['category_text'] as $catText)
    				{
    					if ( trim( strtolower($catText) ) !== trim( strtolower($category->getName()) ) )
    					{
    						$categoryTexts[] = $catText;
    					}
    				}
    			}
    			
    			//Prepare category facet
    			$categoryFacets = array();
    			if ( isset($existingCategoryData[$productId]['category_facet']) )
    			{
    				$facetFacetString = $category->getName().'/'.$category->getId().':'.$category->getPosition();
    				
    				foreach ($existingCategoryData[$productId]['category_facet'] as $catFacet)
    				{
    					if ( md5($catFacet) !== md5($facetFacetString) )
    					{
    						$categoryFacets[] = $catFacet;
    					}
    				}
    			}
    			
    			//Prepare category path
    			$categoryPaths = array();
    			if ( isset($existingCategoryData[$productId]['category_path']) )
    			{
    				$categoryPath = Mage::helper('solrsearch/index')->getCategoryPath($category, $store);
    				
    				foreach ($existingCategoryData[$productId]['category_path'] as $catPath)
    				{
    					if ( md5($catPath) !== md5($categoryPath) )
    					{
    						$categoryPaths[] = $catPath;
    					}
    				}
    			}
    			
    			$docData ['products_id'] = $productId;
    			$docData ['unique_id'] = $store->getId() . 'P' . $productId;
    			$docData ['store_id'] = $store->getId();
    			$docData ['website_id'] = $store->getWebsiteId();
    			
    			$docData['category_id'] = array('set' => $docCategoryIds);
    			$docData['category_text'] = array('set' => $categoryTexts);
    			$docData['category_boost'] = array('set' => $categoryTexts);
    			$docData['category_boost_exact'] = array('set' => $categoryTexts);
    			$docData['category_relative_boost'] = array('set' => $categoryTexts);
    			
    			$docData['category_facet'] = array('set' => $categoryFacets);
    			$docData['category_path'] = array('set' => $categoryPaths);
    			
    			$documents .= json_encode ( $docData ) . ",";
    		}
    	}
    	
    	if (!empty($documents))
    	{
    		$jsonData = '['.trim($documents,",").']';
    		//Mage::log($jsonData, null, 'sbsolr.log');
    		$updateurl = trim($solrServerUrl,'/').'/'.$solrcore.'/update/json?wt=json&commit=true&waitFlush=false&stream.contentType=application/json';
    		$this->postJsonData($jsonData, $updateurl, $solrcore);
    	}
    }
    /**
     * This function run to remove related documents when magento category got updated
     * @param int $categoryid
     */
    public function updateSingleCategory($categoryid)
    {
    	$availableCores = $this->ultility->getAvailableCores();
    
    	foreach ($availableCores as $solrcore => $infoArray)
    	{
    		if ( isset($infoArray['stores']) && strlen(trim($infoArray['stores'], ',')) > 0 )
    		{
    			$storeIdArray = trim($infoArray['stores'], ',');
    			$storeIdArray = explode(',', $storeIdArray);
    
    			foreach ($storeIdArray as $storeid)
    			{
    				try{
    					$category = Mage::getModel('catalog/category')->setStoreId($storeid)->load($categoryid);
    
    					if ( !$category || (isset($category) && !$category->getId()) )
    					{
    						$this->updateDocumentsByCategory($category, $solrcore, $storeid);
    					}
    					else if(!$category->getIsActive())
    					{
    						$this->updateDocumentsByCategory($category, $solrcore, $storeid);
    					}
    					else
    					{
    						$this->updateDocumentsByCategory($category, $solrcore, $storeid);
    					}
    				}
    				catch (Exception $e)
    				{
    					$msg = '[ERROR] - '.$e->getMessage();
    					$this->ultility->writeLog($msg, 0, '', 0, true);
    				}
    			}
    		}
    	}
    }
    /**
     * This function run to remove related documents when magento category got updated
     * @param int $categoryid
     */
    public function updateSingleCategoryOld($categoryid)
    {
    	$availableCores = $this->ultility->getAvailableCores();

    	foreach ($availableCores as $solrcore => $infoArray)
    	{
    		if ( isset($infoArray['stores']) && strlen(trim($infoArray['stores'], ',')) > 0 )
    		{
    			$storeIdArray = trim($infoArray['stores'], ',');
    			$storeIdArray = explode(',', $storeIdArray);

    			foreach ($storeIdArray as $storeid)
    			{
    				try{
    					$category = Mage::getModel('catalog/category')->setStoreId($storeid)->load($categoryid);

    					if ( !$category || (isset($category) && !$category->getId()) )
    					{
    						$this->deleteSolrDocumentByCategory($categoryid, $solrcore, $storeid);
    					}
    					else if(!$category->getIsActive())
    					{
    						$this->deleteSolrDocumentByCategory($categoryid, $solrcore, $storeid);
    					}
    					else
    					{
    						$this->deleteSolrDocumentByCategory($categoryid, $solrcore, $storeid);
    					}
    				}
    				catch (Exception $e)
    				{
    					$msg = '[ERROR] - '.$e->getMessage();
    					$this->ultility->writeLog($msg, 0, '', 0, true);
    				}
    			}
    		}
    	}
    }
    /**
     * Truncate solr index by core
     * @param string $solrcore
     */
    public function truncateSolrCore($solrcore = 'english')
    {
    	$solrServerUrl =$this->getSolrServerUrl();

    	$storeMappingString = Mage::getStoreConfig('solrbridgeindices/'.$solrcore.'/stores', 0);

    	$totalSolrDocuments = Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);

    	//Solr delete all docs from index
    	$clearnSolrIndexUrl = trim($solrServerUrl,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';

    	$this->doRequest($clearnSolrIndexUrl);

    	Mage::app()->getCache()->clean('matchingAnyTag', array('solrbridge_solrsearch'));
    }
    
    /**
     * Truncate solr index by core
     * @param string $solrcore
     */
    public function commitSolrCore($solrcore = 'english')
    {
    	$solrServerUrl =$this->getSolrServerUrl();
    	 
    	$commiturl = trim($solrServerUrl,'/').'/'.$solrcore.'/update/json?wt=json&commit=true&optimize=false&waitFlush=false';
    	return $this->doRequest($commiturl);
    }

    /**
     * Post json data to Solr Server for Indexing
     * @param string $jsonData
     * @param string $updateurl
     * @param string $solrcore
     * @return number
     */
    public function postJsonData($jsonData, $updateurl, $solrcore)
    {

    	if (!function_exists('curl_init')){
    		echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
    		exit;
    	}else{
    		if(!isset($jsonData) && empty($jsonData)) {
    			return 0;
    		}

    		$postFields = array('stream.body'=>$jsonData);

    		$output = $this->doRequest($updateurl, $postFields);

    		if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0)
    		{
    			return Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);
    		}else {
    			return 0;
    		}
    	}
    }
    public function getAdminCores()
    {
        $adminCoreUrl = $this->getSolrUrl( '/admin/cores?wt=json' );
        $cores = array();
        $response = $this->doRequest( $adminCoreUrl );
        if(isset($response['status']))
        {
            $cores = $response['status'];
        }
        return $cores;
    }
    public function delete($solrcore, $params = array()) {
        if ( !empty($solrcore) && is_array($params) )
        {
            $query = '';
            foreach ($params as $key => $value)
            {
                $query .= "<query>{$key}:{$value}</query>";
            }
            if (!empty($query))
            {
                $path = '/update?stream.body=<delete>'.$query.'</delete>&commit=true';
                $deleteUrl = $this->getSolrUrl($path, $solrcore);
                $this->doRequest($deleteUrl);
                //Clear log product ids
                $storeId = null;
                if (isset($params['store_id'])) {
                    $storeId = $params['store_id'];
                }
                $this->deleteIndexTable($solrcore, $storeId);
            }
            return true;
        }
    }
    /**
     * Remove solrbridge indices tables when Solr index data empty
     * @param array $storeids
     */
    public function deleteIndexTable($solrcore, $storeId = null) {
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $logtable = $resource->getTableName('solrsearch/logs');
    
        if (!empty($solrcore)) {
            $indexTableName = $this->getIndexTableName($logtable, $solrcore);
            if (!empty($indexTableName) && $this->isIndexTableNameExist($indexTableName)) {
                $truncateSql = "DELETE FROM {$indexTableName} WHERE 1";
                if( isset($storeId) && $storeId > 0) {
                    $truncateSql .= ' AND store_id = '.$storeId;
                }
                $connection->query($truncateSql);
            }
        }
    }
}