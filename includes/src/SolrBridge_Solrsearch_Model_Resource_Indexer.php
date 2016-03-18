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
class SolrBridge_Solrsearch_Model_Resource_Indexer extends SolrBridge_Solrsearch_Model_Resource_Base
{
	public $request = array();
	public $response = array();
	public $collectionMetaData = array();
	public $solrcore = null;
	public $status = null;
	public $totalSolrDocuments = 0;
	public $totalMagentoProducts = 0;
	public $loadedStores = array();
	public $loadedStoresName = array();
	public $messages = array();
	public $page = 1;
	public $percent = 0;
	public $storeids = array();
	public $currentStoreId = null;
	public $itemsPerCommit = 50;
	public $totalFetchedProducts = 0;
	public $solrServerUrl = null;
	public $endtime = 0;
	public $seconds = 0;
	public $starttime = 0;
	public $totalMagentoProductsNeedToUpdate = 0;
	public $action = 'REINDEX';
	public $totalFetchedProductsByStore = 0;
	public $count = 0;

	/**
	 * Prepare collection meta data for index (1)
	 * @param string $solrcore
	 * @return array
	 */
	public function prepareCollectionMetaData($solrcore) {
		$this->collectionMetaData = $this->ultility->getProductCollectionMetaData($solrcore);
	}
	/**
	 * Start collection parameters requied for indexing
	 * @param array $requestData
	 * @return SolrBridge_Solrsearch_Model_Resource_Indexer
	 */
	public function start($requestData) {
		$this->request = $requestData;

		$this->messages = array();

		$this->solrServerUrl = Mage::getResourceModel('solrsearch/solr')->getSolrServerUrl();

		//get count of how many process executed
		if ( isset($this->request['count']) ) {
			$this->count = ($this->request['count'] + 1);
		}

		if ( isset($this->request['solrcore']) && !empty($this->request['solrcore'])) {
			$this->solrcore = $this->request['solrcore'];
		}

		//get status NEW/UPDATE/TRUNCATE/CONTINUE
		$status = 'NEW';
		if ( isset($this->request['status']) && !empty($this->request['status'])) {
			$this->status = $this->request['status'];
		}
		//Pick action
		if ( isset($this->request['action']) && !empty($this->request['action'])) {
			$this->action = $this->request['action'];
		}

		//get page
		if ( isset($this->request['page']) && !empty($this->request['page'])) {
			$this->page = $this->request['page'];
		}
		if ($this->action !== 'TRUNCATE') {
			$this->messages[] = Mage::helper('solrsearch')->__("#{$this->count}------------------------------------------------");
		}


		/**
		 * get solr core
		 * check if the solr core exist for the first time
		 */
		if ( isset($this->solrcore) && !empty($this->solrcore) && $this->status === 'NEW') {
			$availableCores = $this->ultility->getAvailableCores();

			if (!isset($availableCores[$this->solrcore])) {
				$this->setStatus('ERROR');
				$this->messages[] = Mage::helper('solrsearch')->__('The solr core %s not found', $this->solrcore);
				return $this;
			}

			if (!Mage::getResourceModel('solrsearch/solr')->pingSolrCore($this->solrcore)) {
    			$this->setStatus('ERROR');
				$this->messages[] = Mage::helper('solrsearch')->__('Failed to ping Solr Server with core %s', $this->solrcore);
				return $this;
    		}else{
    			$this->messages[] = Mage::helper('solrsearch')->__('Ping Solr Server with core %s successfully...', $this->solrcore);
    		}
		}

		//Set current store id
		if ( isset($this->request['currentstoreid']) && !empty($this->request['currentstoreid'])) {
			$this->currentStoreId = $this->request['currentstoreid'];
		}

		$itemsPerCommitConfig = $this->getSetting('items_per_commit');
		if( intval($itemsPerCommitConfig) > 0 ) {
			$this->itemsPerCommit = $itemsPerCommitConfig;
		}
		//Pick totalfetchedproducts
		if (isset($this->request['totalfetchedproducts'])) {
			$this->totalFetchedProducts = $this->request['totalfetchedproducts'];
		}
		if (isset($this->request['totalfetchedproductsbystore'])) {
			$this->totalFetchedProductsByStore = $this->request['totalfetchedproductsbystore'];
		}
		//Pick start time
		$this->starttime = time();
		if (isset($this->request['starttime'])) {
			$this->starttime = $this->request['starttime'];
		}
		if (isset($this->request['totalMagentoProductsNeedToUpdate'])) {
			$this->totalMagentoProductsNeedToUpdate = $this->request['totalMagentoProductsNeedToUpdate'];
		}

		//Assign store ids as array to $this->storeids for later use
		$this->storeids = $this->ultility->getMappedStoreIdsFromSolrCore($this->solrcore);

		//Set current store id
		if ( isset($this->request['storeids']) && !empty($this->request['storeids'])) {
			$this->storeids = explode(',', $this->request['storeids']);
		}
		return $this;
	}
	/**
	 * Execute index process
	 * @return SolrBridge_Solrsearch_Model_Resource_Indexer
	 */
	public function execute()
	{
	    //Prepare collection metadata
	    $this->prepareCollectionMetaData($this->solrcore);
	
	    $this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
	    $this->totalMagentoProducts = (int) $this->collectionMetaData['totalProductCount'];
	
	    if ($this->totalSolrDocuments >= $this->totalMagentoProducts)
	    {
	        Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
	        $this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
	    }
	
	    $this->loadedStores = $this->collectionMetaData['loadedStores'];
	    $this->loadedStoresName = $this->collectionMetaData['loadedStoresName'];
	    if (!$this->totalFetchedProducts) {
	        $this->messages[] = Mage::helper('solrsearch')->__('Start indexing process for core (%s)', $this->solrcore);
	    }
	
	    $this->messages[] = Mage::helper('solrsearch')->__('Magento product count : %s', $this->totalMagentoProducts);
	
	    $this->messages[] = Mage::helper('solrsearch')->__('Existing solr documents : %s', $this->totalSolrDocuments);
	
	    if ($this->totalSolrDocuments >= $this->totalMagentoProducts)
	    {
	        $this->messages[] = Mage::helper('solrsearch')->__('There is no new products to update');
	        $this->response['status'] = 'FINISH';
	        $this->response['message'] =$this->messages;
	        $this->percent = 100;
	        return $this;
	    }
	
	    if ( $this->action == 'REINDEX' ) { // There is no any solr document exists
	        if ($this->status == 'NEW') {
	            $this->truncateIndex();
	        }
	        $this->reindexSolrData(false);
	        return $this;
	    }
	
	    if ( $this->action == 'UPDATEINDEX' ) // update new/modified products to Solr
	    {
	        if ($this->page < 2)
	        {
	            Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
	        }
	
	        $this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($this->solrcore);
	        $this->totalMagentoProductsNeedToUpdate = ($this->totalMagentoProducts - $this->totalSolrDocuments);
	
	        $this->updateindexSolrData();
	        return $this;
	    }
	}

	/**
     * Process New Indexing
     * @param string $core
     */
    public function reindexSolrData($reindexPrice = false)
    {
    	$this->messages[] = Mage::helper('solrsearch')->__('Magento stores mapped ('.count($this->loadedStoresName).'): '.@implode(', ', $this->loadedStoresName));

    	if (empty($this->currentStoreId)) {
    		$this->currentStoreId = array_shift($this->storeids);
    	}

    	if (!empty($this->currentStoreId) && isset($this->loadedStores[$this->currentStoreId]))
    	{
    		$store = $this->loadedStores[$this->currentStoreId];
    		$storeid = $this->currentStoreId;

    		//Total products number of current store $storeid
    		$totalMagentoProductsByStore = $this->collectionMetaData['stores'][$storeid]['productCount'];

    		//Fetching products from Magento Database
    		$productCollection = $this->collectionMetaData['stores'][$storeid]['collection'];

    		$productCollection->clear();

    		$productCollection->getSelect()->limitPage(intval($this->page),$this->itemsPerCommit);

    		//Parse json data from product collection
    		$this->ultility->indexer = $this;
    		$dataArray = $this->ultility->parseJsonData($productCollection, $store, $reindexPrice);

    		$jsonData = $dataArray['jsondata'];

    		$this->totalFetchedProducts = ($this->totalFetchedProducts + $dataArray['fetchedProducts']);

    		$this->totalFetchedProductsByStore = ($this->totalFetchedProductsByStore + $dataArray['fetchedProducts']);

    		/*
    		if ($this->totalFetchedProducts > $this->totalMagentoProducts)
    		{
    			$this->totalFetchedProducts = $this->totalMagentoProducts;
    		}
    		*/

    		//$this->percent = $this->calculatePercent($this->totalMagentoProducts, $this->totalFetchedProducts);
    		$this->percent = $this->calculatePercent($this->totalMagentoProducts, $this->totalFetchedProductsByStore);

    		//Post json data to Solr
    		$numberOfIndexedDocuments = $this->postJsonData($jsonData);

    		unset($jsonData);

    		$this->page = ($this->page + 1);

    		if ($numberOfIndexedDocuments > 0)
    		{
    			$this->totalSolrDocuments = $numberOfIndexedDocuments;
    		}
    		if ($this->totalFetchedProductsByStore >= $totalMagentoProductsByStore)
    		{
    			$this->currentStoreId = array_shift($this->storeids);
    			$this->totalFetchedProductsByStore = 0;
    			$this->page = 1;
    		}
    		$this->prepareIndexProgressMessage($store, $this->totalFetchedProductsByStore, $totalMagentoProductsByStore);
    	}
    }

    /**
     * Update new/modified products to Solr - this function will be called inside the function $this->runIndexingByCore
     * @param string $core
     */
    public function updateindexSolrData()
    {
    	$numberOfIndexedDocuments = 0;
    	//Existing solr documents before indexing
    	$existingSolrDocuments = $this->totalSolrDocuments;

    	$this->messages[] = Mage::helper('solrsearch')->__('Magento stores mapped (%s): %s', count($this->loadedStoresName), @implode(', ', $this->loadedStoresName));

    	if (empty($this->currentStoreId)){
    		$this->currentStoreId = array_shift($this->storeids);
    	}

    	if (!empty($this->currentStoreId) && isset($this->loadedStores[$this->currentStoreId]))
    	{
    		$store = $this->loadedStores[$this->currentStoreId];
    		$storeid = $this->currentStoreId;

    		$productCollection = $this->collectionMetaData['stores'][$storeid]['collection'];

    		///echo $productCollection->getSelect().'______';

    		$collectionMetaData = $this->ultility->getProductCollectionMetaDataForUpdate($productCollection, $this->solrcore);
    		//Total magento products need to be updated for the current store
    		$totalMagentoProductsByStore = $collectionMetaData['productCount'];

    		$productCollectionUpdate = $collectionMetaData['collection'];

    		//echo $productCollectionUpdate->getSelect().'______';

    		$productCollectionUpdate->clear();

    		$productCollectionUpdate->getSelect()->limitPage(intval($this->page), $this->itemsPerCommit);

    		//die($productCollectionUpdate->getSelect());

    		$this->messages[] = Mage::helper('solrsearch')->__('Fetched %s products from Magento database', $this->itemsPerCommit);

    		//Parse json data from product collection (productCollection cleared inside the function parseJsonData)
    		$this->ultility->indexer = $this;
    		$dataArray = $this->ultility->parseJsonData($productCollectionUpdate, $store, false);

    		$jsonData = $dataArray['jsondata'];

    		$this->totalFetchedProducts = ($this->totalFetchedProducts + $dataArray['fetchedProducts']);
    		$this->totalFetchedProductsByStore = ($this->totalFetchedProductsByStore + $dataArray['fetchedProducts']);

    		if ($this->totalFetchedProducts >= $this->totalMagentoProductsNeedToUpdate) {
    			$this->totalFetchedProducts = $this->totalMagentoProductsNeedToUpdate;
    		}

    		//$this->percent = $this->calculatePercent($this->totalMagentoProducts, ($this->totalFetchedProducts + $existingSolrDocuments));
    		$this->percent = $this->calculatePercent($this->totalMagentoProducts, $this->totalFetchedProductsByStore);

    		//Post json data to Solr
    		$numberOfIndexedDocuments = $this->postJsonData($jsonData);

    		unset($jsonData);

    		$this->page = ($this->page + 1);
    		if ($this->totalFetchedProductsByStore >= $totalMagentoProductsByStore) {
    			$this->currentStoreId = array_shift($this->storeids);
    			$this->totalFetchedProductsByStore = 0;
    			$this->page = 1;
    		}

    		$this->prepareIndexProgressMessage($store, $this->totalFetchedProductsByStore, $totalMagentoProductsByStore);
    	}
    }
	/**
	 * Check Index status to see how many solr documents indexed
	 */
    public function checkIndexStatus()
    {
    	if ($this->action == 'REINDEXPRICE') {
    		if ($this->totalFetchedProducts >= $this->totalMagentoProducts) {
    			$this->messages[] = Mage::helper('solrsearch')->__('Indexed %s products into Solr core (%s) successfully', $this->totalMagentoProducts, $this->solrcore);
    			$this->setStatus('FINISH');
    			$this->percent = 100;
    		}else{
    			$this->setStatus('CONTINUE');
    		}
    	}else{
    		//Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
    		$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getDocumentCount($this->solrcore, $this->currentStoreId);
    		if ($this->totalSolrDocuments >= $this->totalMagentoProducts) {
    			$this->messages[] = Mage::helper('solrsearch')->__('Indexed %s products into Solr core (%s) successfully', $this->totalMagentoProducts, $this->solrcore);
    			$this->setStatus('FINISH');
    			$this->percent = 100;
    		}else{
    			$this->setStatus('CONTINUE');
    		}
    	}
    }
    
    /**
     * Post solrdata to solr
     * @param string $jsonData
     * @return int
     */
    public function postJsonData($jsonData)
    {
    	$postNumer = ($this->totalFetchedProducts > $this->itemsPerCommit)?$this->itemsPerCommit:$this->totalFetchedProducts;
    	$updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrcore.'/update/json?wt=json';
    	$this->messages[] = Mage::helper('solrsearch')->__('Started posting json of %s products to Solr', $postNumer);
    	return Mage::getResourceModel('solrsearch/solr')->postJsonData($jsonData, $updateurl, $this->solrcore);
    }

    /**
     * Post solrdata to solr
     * @param string $jsonData
     * @return int
     */
    public function postJsonDataOld($jsonData)
    {
    	$helper = Mage::helper('solrsearch/index');
    	
    	if ( $helper->isFnActive('shell_exec') && shell_exec('php -v') )
    	{
    		$dataDir = Mage::helper('solrsearch/index')->getDataDir();
    		$fileName = $dataDir.DS.uniqid('solrbridge_');
    		$dataFile = fopen($fileName, 'w');
    		fwrite($dataFile, $jsonData);
    		fclose($dataFile);
    	
    		$this->ultility->getThreadManager()
    			->addThread(array('postdata' => $this->solrcore, 'filename' => $fileName))
    			->run();
    	}else{
    		$postNumer = ($this->totalFetchedProducts > $this->itemsPerCommit)?$this->itemsPerCommit:$this->totalFetchedProducts;
    		$updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrcore.'/update/json?wt=json';
    		$this->messages[] = Mage::helper('solrsearch')->__('Started posting json of %s products to Solr', $postNumer);
    		return Mage::getResourceModel('solrsearch/solr')->postJsonData($jsonData, $updateurl, $this->solrcore);
    	}
    }

    /**
     * Truncate Solr Data Index
     * @param string $solrcore
     */
    public function truncateIndex()
    {
    	$starttime = time();
    	$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getDocumentCount($this->solrcore, $this->currentStoreId);
    	Mage::getResourceModel('solrsearch/solr')->truncateSolrCore($this->solrcore);
    	sleep(2);
    	while( $currentSolrDocuments = Mage::helper('solrsearch')->getDocumentCount($this->solrcore, $this->currentStoreId) > 0 )
    	{
    		$endtime = time();
    		//Terminate the script if it takes over 1 minute
    		if (($endtime - $starttime) > 300 && $currentSolrDocuments > 0)
    		{
    			$this->messages[] = Mage::helper('solrsearch')->__('The script is terminated because it takes so long...');
    			return $this;
    		}
    	}

    	if($currentSolrDocuments < 1)
    	{
    		Mage::getResourceModel('solrsearch/solr')->truncateIndexTables($this->solrcore);
    		$this->messages[] = Mage::helper('solrsearch')->__('Truncate %s documents from core (%s) successfully', $this->totalSolrDocuments, $this->solrcore);
    		$this->setStatus('FINISH');
    		$this->percent = 100;
    	}
    	else
    	{
    		$$this->messages[] = Mage::helper('solrsearch')->__('There should be problem with the Solr server, please try to restart the solr server and try it again...');
    	}
    	return $this;
    }
    
    public function deleteByStore() {
        $solrcore = $this->solrcore;
        $params = array('store_id' => $this->currentStoreId);
        Mage::getResourceModel('solrsearch/solr')->delete($solrcore, $params);
        return $this;
    }
	/**
	 * Prepare index progress message
	 * @param unknown $store
	 * @param int $totalMagentoProductsByStore
	 * @param boolean $update
	 */
    public function prepareIndexProgressMessage($store, $totalMagentoProductsByStore, $update = false)
    {
    	if ($update) {
    		$this->messages[] = Mage::helper('solrsearch')->__('Posted %s/%s/%s products to Solr', $this->totalFetchedProducts, $totalMagentoProductsByStore, $this->totalMagentoProducts);
    	}else{
    		$this->messages[] = Mage::helper('solrsearch')->__('Posted %s/%s products to Solr', $this->totalFetchedProducts, $this->totalMagentoProducts);
    	}

    	$this->messages[] = Mage::helper('solrsearch')->__('Current Solr indexed: %s documents/%s products', $this->totalSolrDocuments, $this->totalMagentoProducts);

    	$this->messages[] = Mage::helper('solrsearch')->__('Progress: %s', $this->percent.'%');

    	$this->messages[] = Mage::helper('solrsearch')->__('Total fetch products: %s', $this->totalFetchedProducts);
    	$this->messages[] = Mage::helper('solrsearch')->__('Total fetch products from store '.$store->getName().': %s', $this->totalFetchedProductsByStore);

    	$this->messages[] = Mage::helper('solrsearch')->__('Current store: %s(%s/%s products)', $store->getName(), $this->totalFetchedProductsByStore, $totalMagentoProductsByStore);
    }
	/**
	 * Calculate percent finished
	 * @param int $totalMagentoProducts
	 * @param int $totalFetchedProducts
	 * @return float
	 */
    public function calculatePercent($totalMagentoProducts, $totalFetchedProducts)
    {
    	if ($totalMagentoProducts > 0) {
    	    return number_format((($totalFetchedProducts * 100) / $totalMagentoProducts), 0);
    	}
        return 0;
    }

    /**
     * This function used to regenerate thumbs without reindex solr index
     * @param string $solrcore
     */
    public function generateThumbs()
    {
    	$writeConnection = $this->ultility->getWriteConnection();
    	$logtable = $this->ultility->getLogTable();

    	$indexTableName =  Mage::getResourceModel('solrsearch/solr')->getIndexTableName($logtable, $this->solrcore, $writeConnection);

    	if (Mage::getResourceModel('solrsearch/solr')->isIndexTableNameExist($indexTableName))
    	{

    		$select = $this->ultility->getReadConnection()->select()->from($indexTableName, 'count(*)');
    		$this->totalMagentoProducts = $this->ultility->getReadConnection()->fetchOne($select);

    		$this->totalSolrDocuments = (int) Mage::helper('solrsearch')->getDocumentCount($this->solrcore, $this->currentStoreId);

    		if ($this->totalMagentoProducts <= $this->totalSolrDocuments && $this->page == 1) {
    		    Mage::getResourceModel('solrsearch/solr')->synchronizeData($this->solrcore);
    		}

    		$totalPages = ceil($this->totalMagentoProducts/$this->itemsPerCommit);

    		$select = $this->ultility->getReadConnection()->select()->from($indexTableName, '*');
    		$select->limitPage(intval($this->page), $this->itemsPerCommit);

    		$rows = $this->ultility->getReadConnection()->fetchAll($select);

    		foreach ($rows as $row)
    		{
    			$this->totalFetchedProducts++;
    			if(isset($row['value']) && is_numeric($row['value']) && isset($row['store_id']) && is_numeric($row['store_id']))
    			{
    				$_product = Mage::getModel('catalog/product')->setStoreId($row['store_id'])->load($row['value']);

    				try{
	    				if($this->ultility->generateThumb($_product))
	    				{
	    					$this->percent = $this->calculatePercent($this->totalMagentoProducts, $this->totalFetchedProducts);
	    					$storeName = Mage::getModel('core/store')->load($row['store_id'])->getName();
	    					$this->messages[] = Mage::helper('solrsearch')->__('#%s Genenated thumb for the product %s[%s] in store [%s] successfully',
	    																		$this->totalFetchedProducts, $_product->getId(), $_product->getName(), $storeName);
	    				}
    				}catch (Exception $e){
    					$message = Mage::helper('solrsearch')->__('#%s %s at product %s[%s] in store [%s]', $this->totalFetchedProducts, $e->getMessage(), $_product->getId(), $_product->getName(), $storeName);
    					$this->writeLog($message, is_numeric($row['store_id'])?$row['store_id']:0, 'english', 0, true);
    				}

    			}
    		}

    		$this->page = ($this->page + 1);

    		if ($this->totalFetchedProducts >= $this->totalMagentoProducts) {
    			$this->messages[] = Mage::helper('solrsearch')->__('Generated %s product thumbs of Solr core (%s) successfully', $this->totalFetchedProducts, $this->solrcore);
    			$this->setStatus('FINISH');
    			$this->percent = 100;
    		}else{
    			$this->setStatus('CONTINUE');
    		}
    		$this->response['remaintime'] = $this->calculateRemainTime();
    	}else{
    	    $this->setStatus('ERROR');
    	    $this->response['remaintime'] = $this->calculateRemainTime();
    	    $this->messages[] = Mage::helper('solrsearch')->__('The index table %s does not exists, please go to SolrBridge > Indices settings and click the button Save config and try again.', $indexTableName);
    	}
    }

    public function setStatus($status) {
        if ($this->status == 'ERROR') {
            return $this->status;
        }
        $this->status = $status;
    }

	/**
	 * Calculate remained time
	 * @return string
	 */
    public function calculateRemainTime()
    {
        $this->calculateMemoryUsage();

    	$remainProducts = ($this->totalMagentoProducts - $this->totalFetchedProducts);
    	$remainSeconds = (($remainProducts * $this->seconds) / $this->itemsPerCommit);

    	$this->messages[] = Mage::helper('solrsearch')->__('Remain products: %s', $remainProducts);
    	$this->messages[] = Mage::helper('solrsearch')->__('Time per commit: %s seconds', $this->seconds);

    	//$init = $remainSeconds;
    	$hours = floor($remainSeconds/60/60);
    	if ($hours > 0) {
    		$minutes = ($remainSeconds/60) - ($hours*60);
    		$remainedTime = $hours.':'. ceil($minutes) .' (h:m)';
    		$this->messages[] = Mage::helper('solrsearch')->__('Estimated remained time: %s', $remainedTime);
    		return $remainedTime;
    	}
    	$minutes = ceil($remainSeconds/60);
    	if ($minutes > 0) {
    		$remainedTime = $minutes.' minute(s)';
    		$this->messages[] = Mage::helper('solrsearch')->__('Estimated remained time: %s', $remainedTime);
    		return $remainedTime;
    	}

    	$remainedTime = $remainSeconds.' second(s)';
    	$this->messages[] = Mage::helper('solrsearch')->__('Estimated remained time: %s', $remainedTime);

    	return $remainedTime;
    }
	/**
	 * Calculate script memory usage
	 */
    public function calculateMemoryUsage()
    {
    	//Memory usage in byte
    	$memoryUsage = memory_get_usage();
    	//Convert to MB
    	$memoryUsage = $memoryUsage/1024/1024;

    	$currentMemoryLimitStr = '';
    	if (ini_get('memory_limit')){
    		$currentMemoryLimitStr = ini_get('memory_limit');
    	}

    	$currentMemoryLimit = 2048;
    	$phpIniSetAllow = true;
    	if (ini_set('memory_limit', '2048M') === false) {
    		$this->messages[] = Mage::helper('solrsearch')->__('Warning: The current script was not allowed to set memory_limit, please check your php ini ...');
    		$phpIniSetAllow = false;
    	}

    	if ( -1 !== ($position = strpos($currentMemoryLimitStr, 'M')) ){
    		$currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
    	}
    	elseif ( -1 !== ($position = strpos($currentMemoryLimitStr, 'G')) )
    	{
    		$currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
    		$currentMemoryLimit = $currentMemoryLimit / 1024;
    	}

    	if ($currentMemoryLimit > 0 && ($currentMemoryLimit - $memoryUsage) < 100 && $phpIniSetAllow)
    	{
    		$currentMemoryLimit = $currentMemoryLimit + 100;
    		ini_set('memory_limit', $currentMemoryLimit);
    	}

    	if ($phpIniSetAllow)
    	{
    		ini_set('max_execution_time', 18000);
    	}

    	$this->messages[] = Mage::helper('solrsearch')->__('System memory limit: %sM', $currentMemoryLimit);
    	$this->messages[] = Mage::helper('solrsearch')->__('Current memory used: %sM', number_format($memoryUsage));
    }
	/**
	 * Prepare response data
	 */
    public function prepareResponseData() {
    	$this->response['page'] = $this->page;
    	$this->response['status'] = $this->status;
    	$this->response['solrcore'] = $this->solrcore;
    	$this->response['percent'] = $this->percent;
    	$this->response['currentstoreid'] = (int)$this->currentStoreId;
    	$this->response['totalsolrdocuments'] = $this->totalSolrDocuments;
    	$this->response['documents'] = $this->totalSolrDocuments;
    	$this->response['totalmagentoproducts'] = $this->totalMagentoProducts;
    	$this->response['totalfetchedproducts'] = $this->totalFetchedProducts;
    	$this->response['remainproduct'] = ($this->totalMagentoProducts - $this->totalFetchedProducts);
    	$this->response['totalMagentoProductsNeedToUpdate'] = $this->totalMagentoProductsNeedToUpdate;
    	$this->response['storeids'] = @implode(',', $this->storeids);
    	$this->response['action'] = $this->action;
    	$this->response['totalfetchedproductsbystore'] = $this->totalFetchedProductsByStore;
    	$this->response['message'] = $this->messages;
    	$this->response['count'] = $this->count;
    	
    	if ($this->percent >= 100 && $this->totalSolrDocuments > 0) {
    	    $this->response['documents'] = $this->totalMagentoProducts;
    	}

    	$this->messages[] = Mage::helper('solrsearch')->__('Current store ids: %s', @implode(', ', $this->storeids));
    }
	/**
	 * End the process
	 * @return array
	 */
	public function end() {
		$this->commit();
		
		if ($this->totalFetchedProducts == $this->totalMagentoProducts && $this->action !== 'TRUNCATE') {
			sleep(3);
			$totalSolrDocs = (int) Mage::helper('solrsearch')->getDocumentCount($this->solrcore, $this->currentStoreId);
			if ($totalSolrDocs < $this->totalMagentoProducts) {
				$this->messages = array();
				$this->messages[] = Mage::helper('solrsearch')->__('Waiting until solr indexed all products ........');
				$this->commit();
				$this->status = 'WAITING';

				$this->prepareResponseData();

				return $this->response;
			}
		}

		$this->endtime = time();
		$this->seconds = ($this->endtime - $this->starttime);


		if ($this->action !== 'TRUNCATE') {
			$this->response['remaintime'] = $this->calculateRemainTime();
			$this->messages[] = Mage::helper('solrsearch')->__(ucfirst($this->status).'...');
		}

		$this->prepareResponseData();

		return $this->response;
	}
	public function commit() {
		$commiturl = trim($this->solrServerUrl,'/').'/'.$this->solrcore.'/update/json?wt=json&commit=true&optimize=false&waitFlush=false&waitSearcher=false';
		return Mage::getResourceModel('solrsearch/solr')->doRequest($commiturl);
	}
}