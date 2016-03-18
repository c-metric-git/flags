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
class SolrBridge_Solrsearch_Model_Resource_Base extends Mage_Core_Model_Resource_Abstract
{
	public $ultility;

	public $threadEnable = false;

	public $autoIndex = false;

	public $writeConnection = null;

	public function _construct(){
		$this->ultility = Mage::getSingleton('solrsearch/ultility');

		$this->writeConnection = $this->ultility->getWriteConnection();

		$threadEnableSetting = $this->getSetting('thread_enable', 0);
		if (is_numeric($threadEnableSetting) && $threadEnableSetting > 0) {
			$this->threadEnable = true;
		}

		$autoIndex = $threadEnableSetting = $this->getSetting('solr_index_auto_when_product_save', 0);
		if (is_numeric($autoIndex) && $autoIndex > 0) {
			$this->autoIndex = true;
		}
	}

	protected function _getReadAdapter(){}

	/**
	 * Retrieve connection for write data
	 */
	protected function _getWriteAdapter(){}

	/**
	 * This function is used to wrap Mage::helper('solrsearch')->getSetting
	 * For later call by this
	 * @param string $key
	 * @param number $storeId
	 * @return string
	 */
	public function getSetting($key, $storeId = 0)
	{
		return Mage::helper('solrsearch')->getSetting($key, $storeId);
	}

	/**
	 * Check is a table exists
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return boolean
	 */
	public function isTableExists($tableName, $schemaName = null)
	{
		return $this->showTableStatus($tableName, $schemaName) !== false;
	}
	/**
	 * Show table status
	 *
	 * @param string $tableName
	 * @param string $schemaName
	 * @return array|false
	 */
	public function showTableStatus($tableName, $schemaName = null)
	{
		$fromDbName = null;
		if ($schemaName !== null) {
			$fromDbName = ' FROM ' . $this->writeConnection->quoteIdentifier($schemaName);
		}
		$query = sprintf('SHOW TABLE STATUS%s LIKE %s', $fromDbName,  $this->writeConnection->quote($tableName));

		return $this->writeConnection->raw_fetchRow($query);
	}
}