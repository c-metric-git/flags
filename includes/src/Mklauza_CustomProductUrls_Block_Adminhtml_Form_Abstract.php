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

abstract class Mklauza_CustomProductUrls_Block_Adminhtml_Form_Abstract extends Mage_Adminhtml_Block_Template {
    
//    private $_attributesCollection;
//    private $_productAttributes;
    private $_patternChunks;
    private $_attributesChunks;
    private $_patternStr;
    private $_allowedFormats = array('price');//, 'date', 'price', 'wee');
    
    public function getSubmitUrl() {
        throw new Exception($this->_getHelper->__('Implemet ' . get_class($this) . '::getSubmitUrl() method.'));
    }
    
    public function getExampleUrl() {
        return Mage::helper('adminhtml')->getUrl('adminhtml/ProductUrls/example');
    }
    
    public function setPatternStr($pattern) {
        $this->_patternStr = $pattern;
    }
    
    public function getPatternStr() {
        if($this->_patternStr === null) {
            if(Mage::app()->getRequest()->getParam('pattern', null)) {
                $this->_patternStr = Mage::app()->getRequest()->getParam('pattern');
            } else {
                $this->_patternStr = Mage::getStoreConfig('mklauza_customproducturls/general/pattern');
            }
        }
        return $this->_patternStr;
    }
    
    private function chunkToHtmlElement(array $chunk = null) {
        if(!$chunk || !isset($chunk['value']) || !isset($chunk['type'])) {
            return '';
        }
        
        if($chunk['type'] === 'attribute') {
            $attributes = $this->getProductAttributes();
            $attrId = $chunk['value'];
            return '<span class="inputTags-item blocked" data-value="' . $attrId . '">'
                    . '<span class="value">' . $attributes[$attrId] . '</span>'
                .'</span>';            
        } elseif($chunk['type'] === 'text') {
            return '<input type="text" class="inputTags-field" value="' . $chunk['value'] . '"/>';
        }
    }
    
    public function getAttributesCollection() {
        return $this->_getHelper()->getAttributesCollection();
    }    
    
    public function getProductAttributes() {
        return $this->_getHelper()->getProductAttributes();
    }     
    
    public function getPatternChunks() {
        if($this->_patternChunks === null) {
            $urlPattern = $this->getPatternStr();
            $this->_patternChunks = $this->_getHelper()->extractChunks($urlPattern);
        }
        return $this->_patternChunks;
    }
    
    public function getPatternHtml() {
        
        $chunks = $this->getPatternChunks();
        $html = '';
        foreach ($chunks as $_chunk) {
            $html .= $this->chunkToHtmlElement($_chunk);
        }
        
        return $html;
    }    
    
    public function getAttributesChunks() {
        if($this->_attributesChunks === null) {
            $attributes = $this->getProductAttributes();
            $patternChunks = $this->getPatternChunks();
            foreach($patternChunks as $chunk) {
                if($chunk['type'] === 'attribute') {
                    $attrId = $chunk['value'];
                    unset($attributes[$attrId]);
                }
            }
            $this->_attributesChunks = array();
            foreach($attributes as $id => $name) {
                $this->_attributesChunks[] = array('value' => $id, 'type' => 'attribute');
            }
        }
        return $this->_attributesChunks;
    }
    
    public function getAttributesCloudHtml() {
        $chunks = $this->getAttributesChunks();
        $html = '';
        foreach ($chunks as $_chunk) {
            $html .= $this->chunkToHtmlElement($_chunk);
        }
        return $html;
    }
    
    protected function _getHelper() {
        return Mage::helper('mklauza_customproducturls');
    }
    
}