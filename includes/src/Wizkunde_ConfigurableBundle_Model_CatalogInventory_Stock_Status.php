<?php

class Wizkunde_ConfigurableBundle_Model_CatalogInventory_Stock_Status extends Mage_CatalogInventory_Model_Stock_Status
{

    /**
     * Assign Stock Status to Product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $stockId
     * @param int $stockStatus
     * @return Mage_CatalogInventory_Model_Stock_Status
     */
    public function assignProduct(Mage_Catalog_Model_Product $product, $stockId = 1, $stockStatus = null)
    {
		
        if (is_null($stockStatus)) {
            $websiteId = $product->getStore()->getWebsiteId();
            $status = $this->getProductStatus($product->getId(), $websiteId, $stockId);
            $stockStatus = isset($status[$product->getId()]) ? $status[$product->getId()] : null;
        }

		if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
			$product->setIsSalable(1);
		}else{
			$product->setIsSalable($stockStatus);
		}
		
        return $this;
    }

}