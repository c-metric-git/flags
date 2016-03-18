<?php

class Wizkunde_ConfigurableBundle_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
	public function prepareProductCollection($collection)
	    {
	        $attributes = Mage::getSingleton('catalog/config')
	            ->getProductAttributes();
	        $collection->addAttributeToSelect($attributes)
	            ->addMinimalPrice()
	            ->addFinalPrice()
	            ->addTaxPercents()
	            //->addStoreFilter()
	            ;
	        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
	        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
	        $collection->addUrlRewrite($this->getCurrentCategory()->getId());

	        return $this;
	    }
}
?>