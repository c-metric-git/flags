<?php

class RocketWeb_GoogleBaseFeedGenerator_Block_Adminhtml_System_Config_Form_Field_Categorytree
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _prepareLayout()
    {
        $this->setTemplate('googlebasefeedgenerator/system/config/form/field/categorytree.phtml');

        $tree_block = $this->getLayout()
            ->createBlock('googlebasefeedgenerator/adminhtml_catalog_category_checkboxes_tree')
            ->setJsFormObject($this->getJsFormObject());
        $this->setChild('feed_categories_include_tree', $tree_block);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $ids = explode(',', $element->getValue());
        if (count($ids) == 1 && empty($ids[0])) {
            $ids = null;
        }
        $this->getChild('feed_categories_include_tree')->setCategoryIds($ids);
        return $this->_toHtml();
    }

    public function getJsFormObject()
    {
        return 'categories_include_form';
    }

}