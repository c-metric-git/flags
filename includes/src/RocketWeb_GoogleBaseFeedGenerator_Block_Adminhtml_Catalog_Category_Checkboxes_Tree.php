<?php

class RocketWeb_GoogleBaseFeedGenerator_Block_Adminhtml_Catalog_Category_Checkboxes_Tree
    extends Mage_Adminhtml_Block_Catalog_Category_Checkboxes_Tree
{

    public function getLoadTreeUrl($expanded = null)
    {
        $params = array('_current' => true, 'id' => null, 'store' => null);
        return Mage::helper("adminhtml")->getUrl('*/googlebasefeedgenerator/categoriesJson', $params);
    }

}