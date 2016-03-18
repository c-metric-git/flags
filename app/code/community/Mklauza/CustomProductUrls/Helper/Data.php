<?php
/**
 * Marcin Klauza - Magento developer
 * http://www.marcinklauza.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to marcinklauza@gmail.com so we can send you a copy immediately.
 *
 * @category    Mklauza
 * @package     Mklauza_CustomProductUrls
 * @author      Marcin Klauza <marcinklauza@gmail.com>
 * @copyright   Copyright (c) 2015 (Marcin Klauza)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mklauza_CustomProductUrls_Helper_Data extends Mage_Core_Helper_Abstract {
    
    private $_attributesCollection;
    private $_productAttributes;
    private $_patternChunks;
    
    public function getIsEnabled() {
        return Mage::getStoreConfigFlag('mklauza_customproducturls/general/is_active');
    }
    
    public function getApplyToNewFlag() {
        return Mage::getStoreConfigFlag('mklauza_customproducturls/general/apply_to_new');
    }
    
    public function getConfigPattern() {
        return Mage::getStoreConfig('mklauza_customproducturls/general/pattern');
    }    
    
    public function getAttributesCollection() {
        if($this->_attributesCollection === null) {
            $this->_attributesCollection =  Mage::getResourceModel('catalog/product_attribute_collection')
                    ->addVisibleFilter('is_visible_on_front', array('=' => '1'))
                    ->addFieldTofilter('attribute_code', array('neq' => 'url_key'));
        }
        return $this->_attributesCollection;
    }    
    
    public function getProductAttributes() {
        if($this->_productAttributes === null) {
            
            $attributes = array();
            foreach ($this->getAttributesCollection() as $productAttr) { /** @var Mage_Catalog_Model_Resource_Eav_Attribute $productAttr */
                $attributes[$productAttr->getAttributeCode()] = $this->jsQuoteEscape($productAttr->getFrontendLabel());
            }
            $this->_productAttributes = $attributes;
        }
        return $this->_productAttributes;
    }    
    
    public function extractChunks($urlPattern = '') {
        $regex = '/(\{(\w+)\})?([^\{\}]+)?/';

        $chunks = array();
        $doesMatch = true;
        while(strlen($urlPattern) && $doesMatch)
        {
            $matches = array();
            $doesMatch = preg_match($regex, $urlPattern, $matches);      
            if(!empty($matches[2])) {
                $chunks[] = array('value' => $matches[2], 'type' => 'attribute');
            }
            if(!empty($matches[3])) {
                $chunks[] = array('value' => $matches[3], 'type' => 'text');
            }
            
            $urlPattern = str_replace($matches[0], '', $urlPattern);
        }   

        return $chunks;
    }    
    
    public function getPatternChunks($urlPattern) {
        if($this->_patternChunks === null) {
            $this->_patternChunks = $this->extractChunks($urlPattern);
        }
        return $this->_patternChunks;        
    }
    
    private function _getPatternAttributes() {
        $chunks = $this->getPatternChunks();
        foreach($chunks as $chunk) {
            if($chunk['type'] === 'attribute') {
                $attributes[] = $chunk['value'];
            }
        }
        $attributes = array_flip($attributes);        
        foreach($attributes as $_code => $val) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $_code);
            if ($attribute->usesSource()) {
                $htmlOptions = $attribute->getSource()->getAllOptions(false);
                if($htmlOptions && is_array($htmlOptions) && count($htmlOptions)) 
                {
                    $options = array();
                    foreach($htmlOptions as $option) {
                        $options[$option['value']] = $option['label'];
                    }
                    $attributes[$_code]= $options;                    
                }
            }            
        }
        return $attributes;
    }
    
    public function prepareUrlKey($productId, $urlPattern, $storeId) {
        $chunks = $this->getPatternChunks($urlPattern);
        $patternAttributes = $this->_getPatternAttributes();

        foreach($patternAttributes as $code => &$value) {
            $rawValue = Mage::getModel('catalog/product')->getResource()->getAttributeRawValue($productId, $code, $storeId);
            // options
            if(is_array($value)) {
                $textOption = $value[$rawValue];
                $value = $textOption;
            } elseif($code === 'price') {
                $value =  number_format($rawValue, 2);
            } else {
                $value = $rawValue;
            }
        }

        $url_str = '';
        foreach($chunks as $chunk) {
            if($chunk['type'] === 'attribute') {
                $attrCode = $chunk['value'];                 
                $url_str .= $patternAttributes[$attrCode];
            } elseif($chunk['type'] === 'text') {
                $url_str .= $chunk['value'];
            }
        }
        
        return $url_str;
    }   
    
    public function getRandomExample($pattern) {
        $storeId = Mage::app()->getStore()->getStoreId();
        
        // get random product Id
        $collection = Mage::getResourceModel('catalog/product_collection')
                ->addStoreFilter($storeId)
                ->addFieldToFilter('visibility', array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE));
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('entity_id')->order(new Zend_Db_Expr('RAND()'))->limit(1);
        $productId = $collection->getFirstItem()->getId();
        $url_key = $this->prepareUrlKey($productId, $pattern, $storeId);
        $url_key = Mage::getModel('catalog/product_url')->formatUrlKey($url_key);
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $url_key . Mage::getStoreConfig('catalog/seo/product_url_suffix');
    }
    
}