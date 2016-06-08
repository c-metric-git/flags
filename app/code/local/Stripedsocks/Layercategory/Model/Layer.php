<?php

class  Stripedsocks_Layercategory_Model_Layer extends  Mage_Catalog_Model_Layer {

 public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if(!Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
            if (is_null($category)) {
                if ($category = Mage::registry('current_category')) {
                    $this->setData('current_category', $category);
                }
                else {
                    $category = Mage::getModel('catalog/category')->load($this->getCurrentStore()->getRootCategoryId());
                    $this->setData('current_category', $category);
                }
            }
        }
        else
        {
                $category = Mage::getModel('catalog/category')->load(11909);
                $this->setData('current_category', $category);
        }
        return $category;
    }

  /**
  * @desc function added by dinesh for showing OOS products into last  
  */
  public function getProductCollection()
    {    
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $collection->joinField('inventory_in_stock', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id','is_in_stock>=0', 'left')->setOrder('inventory_in_stock', 'desc');
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }
    /*************End of code added by dinesh *****************/
}
