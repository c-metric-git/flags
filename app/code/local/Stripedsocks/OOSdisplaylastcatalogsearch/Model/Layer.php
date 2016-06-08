<?php
class Stripedsocks_OOSdisplaylastcatalogsearch_Model_Layer extends Mage_CatalogSearch_Model_Layer
{
	 public function getProductCollection()
    {              
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = Mage::getResourceModel('catalogsearch/fulltext_collection');
			$collection->joinField('inventory_in_stock', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id','is_in_stock>=0', 'left')->setOrder('inventory_in_stock', 'desc');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }
}
