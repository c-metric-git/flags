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
class SolrBridge_Solrsearch_Helper_Index extends Mage_Core_Helper_Abstract
{
	/**
	 * Get directory where json file store for indexing
	 * @throws Exception
	 * @return string
	 */
	public function getDataDir()
	{
		$dataDir = Mage::getBaseDir('var').DS.'solrbridge';
	
		if (!file_exists($dataDir))
		{
			try
			{
				mkdir($dataDir);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		return $dataDir;
	}
	
	public function getFileCountInDir($dirpath)
	{
		if (glob($dirpath . "*.*") != false)
		{
			$filecount = count(glob($dirpath . "*.*"));
			return $filecount;
		}
		return 0;
	}
	
	public function isFnActive($functionName)
	{
		$exec_enabled =
			function_exists($functionName) &&
			!in_array($functionName, array_map('trim', explode(', ', ini_get('disable_functions')))) &&
			strtolower(ini_get('safe_mode')) != 1;
		return $exec_enabled;
	}
	
	/**
	 * This is slow, but it was working well
	 * Get category path
	 * @param unknown_type $category
	 */
	public function getCategoryPath($category, $store)
	{
		$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId().':'.$category->getData('position');
		while ($category->getParentId() > 0)
		{
			$parentCategory = $category->getParentCategory();
			$category = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($parentCategory->getId());
	
			if ( $category && $category->getIsActive() && $category->getIncludeInMenu() && $category->getId() != $store->getRootCategoryId())
			{
				$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId().':'.$category->getData('position').'/'.$categoryPath;
			}
			if ($category->getId() == $store->getRootCategoryId())
			{
				break;
			}
		}
		return trim($categoryPath, '/');
	}
	/**
	 * Calculate percent finished
	 * @param int $totalMagentoProducts
	 * @param int $totalFetchedProducts
	 * @return float
	 */
	public function getPercent($total, $part) {
	    if ($total > 0){
	        return round( (($part * 100) / $total) );
	    }
	    return 0;
	}
}