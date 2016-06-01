<?php
class Stripedsocks_Productlisting_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /**
            * @desc code added by dhiraj for getting parent id for topsellers.
            */
            if($layer['current_category']['entity_id'])
             {
	            $parentId=Mage::getModel('catalog/category')->load($layer['current_category']['entity_id'])->getParentId();
	            $catName=Mage::getModel('catalog/category')->load($layer['current_category']['entity_id'])->getName();	 
             }
             if($catName =="Top Sellers")	
             { 
	            $layer['current_category']['entity_id'] = $parentId;
	            $layer->setId($parentId);
             }
             
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
			        $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                    $this->addModelTags($category);
                }
            }
			$filters = $this->getLayer()->getState()->getFilters();
			/**
            * @desc code added by dhiraj for sorting the OOS products in last on filters page only 
            */
            //$pageTitle = Mage::getSingleton('cms/page')->getTitle();			
			//if($pageTitle=="Filter Results" || (is_array($filters) && count($filters)>0))
			//{
					$this->_productCollection = $layer->getProductCollection();                        
                    /*->setOrder('inventory_in_stock','desc')
                     ->setOrder('priority','desc');   */
	        /*}
			else
			{    
				$this->_productCollection = $layer->getProductCollection();	
			}   */
			if(!empty($filters))
			{
				$this->_productCollection->setVisibility(array(
									Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
									Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG));
				//$this->_productCollection->addAttributeToFilter('type_id', array('nin' => array('configurable')));
				$this->_productCollection->joinAttribute('display_product_in_filter', 'catalog_product/display_product_in_filter', 'entity_id', null, 'left');
				$this->_productCollection->getSelect()->where("`e`.`type_id` NOT IN ('configurable') OR at_display_product_in_filter.value = 1");
				//echo $this->_productCollection->getSelect()->__toString();
				if(!$layer->getCurrentCategory()->getHasSets())
				{
					$this->_productCollection->addAttributeToFilter('type_id', array('nin' => array('grouped')));
				}
			}
			else
			{
				$this->_productCollection->setVisibility(array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH));
			}

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }
}
