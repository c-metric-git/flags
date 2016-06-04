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

    
}
