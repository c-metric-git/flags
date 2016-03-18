<?php
/**
 * @category SolrBridge
 * @package SolrBridge_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Adminhtml_SolrsearchController extends Mage_Adminhtml_Controller_Action {
	public $ultility = null;
	public $itemsPerCommit = 50;
	public $threadEnable = false;
	protected $indexer = null;
	protected function _construct() {
		$this->ultility = Mage::getSingleton ( 'solrsearch/ultility' );

		$this->threadEnable = Mage::getResourceModel ( 'solrsearch/solr' )->threadEnable;

		$action = $this->getRequest ()->getParam ('action');
		if (!empty($action) && $action == 'REINDEXPRICE') {
			$this->indexer = Mage::getResourceModel ( 'solrsearch/indexer_price' );
		}else{
			$this->indexer = Mage::getResourceModel ( 'solrsearch/indexer' );
		}

		parent::_construct ();
	}
	protected function _initAction() {
		$this->loadLayout ()->_setActiveMenu ( 'solrbridge/indices' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Solr Bridge Indices' ), Mage::helper ( 'adminhtml' )->__ ( 'Solr Bridge Indices' ) );
		return $this;
	}

	/**
	 * Index action
	 */
	public function indexAction() {
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );

		$this->_title ( $this->__ ( 'Solr Bridge Indices' ) )->_title ( $this->__ ( 'Solr Bridge Indices' ) )
		->_setActiveMenu('solrbridge/indices');

		$ping = Mage::getResourceModel ( 'solrsearch/solr' )->pingSolrCore ();

		if (! $ping) {
			Mage::getSingleton('adminhtml/session')->addError( '[Ping Solr Server Failed] Solr Server Url is empty or Magento store and Solr index not yet mapped. Please go to System > Configuration > Solr Bridge > General Settings' );
		}

		$messges = $this->ultility->checkStoresSolrIndexAssign();

		if (!empty($messges)) {
		    $messges = @implode("\n", $messges);
		    Mage::getSingleton('adminhtml/session')->addWarning($messges);
		}

		$this->renderLayout();
	}
	/**
	 * Logs action
	 */
	public function logsAction() {
		$this->loadLayout ();
		$this->_initLayoutMessages ( 'customer/session' );
		$this->_initLayoutMessages ( 'catalog/session' );

		$this->_title ( $this->__ ( 'Solr Bridge Logs' ) )
		->_title ( $this->__ ( 'Solr Bridge Logs' ) )
		->_setActiveMenu('solrbridge/logs');

		$ping = Mage::getResourceModel( 'solrsearch/solr' )->pingSolrCore();

		if (! $ping) {
			Mage::getSingleton('adminhtml/session')->addWarning( 'Solr Server Url is empty or Magento store and Solr index not yet mapped. Please go to System > Configuration > Solr Bridge > Basic Settings' );
		}

		$this->renderLayout ();
	}

	/**
	 * This is the start point for process data indexing
	 */
	public function processAction() {
		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );
		try {
			if ($this->threadEnable) {
				$solrcore = '';
				if ($solrcore = $this->getRequest ()->getParam ( 'solrcore' )) {
					if (!$this->getRequest ()->getParam ( 'status' )) {

					    //Empty logs table
					    $logTableName = $this->ultility->getLogTable();
					    $writeConnection = $this->ultility->getWriteConnection();

					    if (Mage::getResourceModel('solrsearch/solr')->isTableExists($logTableName)) {
					        $truncateSql = "TRUNCATE TABLE {$logTableName}";
					        $writeConnection->query($truncateSql);
					    }

					    //Syn solrdocuments and mysql index table
					    Mage::getResourceModel('solrsearch/solr')->synchronizeData($solrcore);

					    $this->ultility
					         ->getThreadManager()
					         ->addThread ( array ('updateindex' => $solrcore) )
					         ->run();
					}

					sleep ( 5 );
					echo $this->getJsonResponeData ($solrcore);
				}
			} else {
				$request = $this->getRequest ()->getParams ();
				$request ['starttime'] = time ();

				if (isset($request ['status']) && $request ['status'] == 'WAITING') {
					sleep ( 2 );
					$message = $request['message'];
					$request['message'] = array();
					$request['message'][] = $message;
					$totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($request ['solrcore']);
					if ($totalSolrDocuments >= (int) $request['totalmagentoproducts']) {
						$request['message'][] = Mage::helper('solrsearch')->__('Indexed %s products into Solr core (%s) successfully', $request['totalmagentoproducts'], $request ['solrcore']);
						$request['percent'] = 100;
						$request['status'] = 'FINISH';
					}else{
						$request['message'][] = '.........';
					}
					echo json_encode ( $request );
					exit ();
				}
				
				if ($request['action'] == 'REINDEX')
				{
					$emptyRequest = $request;
					$emptyRequest['action'] = 'TRUNCATE';
					$emptyRequest['status'] = 'NEW';
					$this->indexer->start ( $emptyRequest );
					$this->indexer->truncateIndex ();
					$request['action'] = 'UPDATEINDEX';
				}

				$this->indexer->start ( $request );
				$this->indexer->execute ();
				$this->indexer->checkIndexStatus ();
				$response = $this->indexer->end ();
				echo json_encode ( $response );
			}
		} catch ( Exception $e ) {
			$errors = array (
					'status' => 'ERROR',
					'message' => array($e->getMessage ())
			);

			if(isset($errors['message']) && $errors['message'] == $this->__('Image file was not found.')){
				$errors['status'] = 'CONTINUE';
				$errors['message'] = '';
			}
			$request = $this->getRequest ()->getParams ();
			$errors = array_merge($request, $errors);

			echo json_encode ( $errors );
		}
		exit ();
	}
	public function getJsonResponeData($solrcore) {
		$lastLogId = 0;
		if ($this->getRequest ()->getParam ( 'lastlogid' )) {
			$lastLogId = $this->getRequest ()->getParam ( 'lastlogid' );
		}

		$percent = 0;
		if ($this->getRequest ()->getParam ( 'percent' )) {
			$percent = $this->getRequest ()->getParam ( 'percent' );
		}

		$logChunks = $this->readLogChunk ( $lastLogId );

		$returnData = array ();
		$returnData ['lastlogid'] = isset ( $logChunks ['lastlogid'] ) ? $logChunks ['lastlogid'] : $lastLogId;
		$returnData ['message'] = ! empty ( $logChunks ['message'] ) ? $logChunks ['message'] : array ();
		$returnData ['percent'] = isset ( $logChunks ['percent'] ) ? $logChunks ['percent'] : $percent;
		$returnData ['status'] = (isset ( $returnData ['percent'] ) && ( int ) $returnData ['percent'] == 100) ? 'FINISH' : 'CONTINUE';
		$returnData ['remaintime'] = '';
		$returnData ['solrcore'] = $solrcore;
		if ( (int)$returnData ['percent'] < 100 )
		{
		    $totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);
		    $this->indexer->prepareCollectionMetaData($solrcore);
		    $totalMagentoProducts = (int) $this->indexer->collectionMetaData['totalProductCount'];
		    if ($totalSolrDocuments >= $totalMagentoProducts)
		    {
		        $returnData ['percent'] = 100;
		        $returnData ['status'] = 'FINISH';
		        if ( empty($returnData ['message']) ) {
		            $returnData ['message'][] = $this->__('Update solr index successfully %s magento products/%s solr documents.', $totalMagentoProducts, $totalSolrDocuments);
		        }
		        return json_encode ( $returnData );
		    }
		}

		if ((int)$returnData ['percent'] >= 100) {
		  $totalSolrDocuments = (int) Mage::helper('solrsearch')->getTotalDocumentsByCore($solrcore);
		    $this->indexer->prepareCollectionMetaData($solrcore);
		    $totalMagentoProducts = (int) $this->indexer->collectionMetaData['totalProductCount'];
		    $returnData ['message'] = array();
		    if ($totalSolrDocuments >= $totalMagentoProducts)
		    {
		        $returnData ['percent'] = 100;
		        $returnData ['status'] = 'FINISH';
		        if ( empty($returnData ['message']) ) {
		            $returnData ['message'][] = $this->__('Update solr index successfully %s magento products/%s solr documents.', $totalMagentoProducts, $totalSolrDocuments);
		        }
		        return json_encode ( $returnData );
		    }
		}


		return json_encode ( $returnData );
	}
	/**
	 * Read logs from solr bridge logs table
	 *
	 * @param number $lastLogId
	 * @return array
	 */
	public function readLogChunk($lastLogId = 0) {
		$readConnection = $this->ultility->getReadConnection ();
		$logTable = $this->ultility->getLogTable ();
		$select = $readConnection->select ()->from ( $logTable, '*' );

		$where = '';
		if ($lastLogId > 0) {
			$where = " AND logs_id > {$lastLogId} AND logs_id <= (SELECT MAX(logs_id) FROM {$logTable})";
		}
		$select->where ( "1 {$where}" );
		$rows = $readConnection->fetchAll ( $select );

		$logChunks = array ();
		$logString = "";
		$logChunks['message'] = array();
		$percent = 0;
		if ($logcount = count ( $rows ) > 0) {
			$index = 1;
			foreach ( $rows as $row ) {
				if ($index == $logcount) {
					$logChunks ['lastlogid'] = $row ['logs_id'];
				}
				if (( int ) $row ['percent'] > 0) {
					$percent = $row ['percent'];
				}
				$messages = explode("\n", $row ['message']);
				$logChunks['message'] = array_merge($logChunks['message'], $messages);
				$index ++;
			}
		}
		$logChunks ['percent'] = $percent;
		return $logChunks;
	}

	/**
	 * Empty the whole solr core index
	 */
	public function truncateAction() {
		// get solr core
		$request = $this->getRequest ()->getParams ();

		$request['action'] = 'TRUNCATE';
		$request['status'] = 'NEW';

		$this->indexer->start ( $request );

		$this->indexer->truncateIndex ();

		$response = $this->indexer->end ();

		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );

		echo json_encode ( $response );

		exit ();
	}

	/**
	 * Empty the whole index
	 */
	public function genthumbsAction() {
		$this->getResponse ()->setHeader ( "Content-Type", "application/json", true );
		try {
			if ($this->threadEnable) {
				$solrcore = '';
				if ($solrcore = $this->getRequest ()->getParam ( 'solrcore' )) {
					if (!$this->getRequest ()->getParam ( 'status' )) {

					    $this->ultility
					        ->getThreadManager()
					        ->addThread ( array ('generatethumb' => $solrcore) )
					        ->run();
					}

					sleep ( 5 );
					echo $this->getJsonResponeData ($solrcore);
				}
			} else {
				// get solr core
				$request = $this->getRequest ()->getParams ();
				$this->indexer->start ( $request );
				$this->indexer->generateThumbs ();
				$response = $this->indexer->end ();
				echo json_encode ( $response );
			}
		} catch ( Exception $e ) {
			$errors = array (
					'status' => 'ERROR',
					'message' => array($e->getMessage ())
			);
			if(isset($errors['message']) && $errors['message'] == $this->__('Image file was not found.')){
				$errors['status'] = 'CONTINUE';
				$errors['message'] = '';
			}
			echo json_encode ( $errors );
		}
		exit ();
	}
}
?>