<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Advanced extends Mage_CatalogSearch_Block_Advanced_Form
{
    public function getSearchPostUrl()
    {
        return $this->getUrl('solrsearch/advanced/result');
    }

    /**
     * Build attribute select element html string
     *
     * @param $attribute
     * @return string
     */
    public function getAttributeSelectElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);

        $newOptions = array();
        foreach ($options as $option)
        {
            $newOptions[] = array('value' => $option['label'], 'label' => $option['label']);
        }

        $name = $attribute->getAttributeCode();

        // 2 - avoid yes/no selects to be multiselects
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('catalogsearch')->__('All')));
        }

        return $this->_getSelectBlock()
        ->setName($name)
        ->setId($attribute->getAttributeCode())
        ->setTitle($this->getAttributeLabel($attribute))
        ->setExtraParams($extra)
        ->setValue($this->getAttributeValue($attribute))
        ->setOptions($newOptions)
        ->setClass('multiselect')
        ->getHtml();
    }
}