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
class SolrBridge_Solrsearch_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
	/**
	 * Set collection to pager
	 *
	 * @param Varien_Data_Collection $collection
	 * @return Mage_Catalog_Block_Product_List_Toolbar
	 */
	public function setCollection($collection)
	{
		$this->_collection = $collection;
		$this->_collection->setCurPage($this->getCurrentPage());

		// we need to set pagination only if passed value integer and more that 0
		$limit = (int)$this->getLimit();
		if ($limit) {
			$this->_collection->setPageSize($limit);
		}
		return $this;
	}

	public function getFirstNum()
	{
		$collection = $this->getCollection();
		return (int) $collection->getRows() * ($this->getCurrentPage()-1)+1;
	}

	public function getLastNum()
	{
		$collection = $this->getCollection();
		return (int) $collection->getRows()*($this->getCurrentPage()-1)+count($collection->getDocs());
	}

	public function getTotalNum()
	{
		$collection = $this->getCollection();
		return (int) $collection->getSize();
	}

	public function isFirstPage()
	{
		return $this->getCurrentPage() == 1;
	}
	public function getLastPageNum()
	{
		$collection = $this->getCollection();

		$collectionSize = (int) $collection->getSize();
		if (0 === $collectionSize) {
			return 1;
		}
		else {
			return ceil($collectionSize / (int) $collection->getRows());
		}
		return 1;
	}

	/**
	 * Render pagination HTML
	 *
	 * @return string
	 */
	public function getPagerHtml()
	{
		$pagerBlock = $this->getChild('product_list_toolbar_pager');

		if ($pagerBlock instanceof Varien_Object) {

			/* @var $pagerBlock Mage_Page_Block_Html_Pager */
			$pagerBlock->setAvailableLimit($this->getAvailableLimit());

			$pagerBlock->setUseContainer(false)
			->setShowPerPage(false)
			->setShowAmounts(false)
			->setLimitVarName($this->getLimitVarName())
			->setPageVarName($this->getPageVarName())
			->setLimit($this->getLimit())
			->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
			->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
			->setCollection($this->getCollection());

			return $pagerBlock->toHtml();
		}

		return '';
	}
}