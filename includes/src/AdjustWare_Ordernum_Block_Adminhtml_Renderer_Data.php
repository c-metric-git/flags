<?php
/**
 * Custom Order Number Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Ordernum
 * @version      5.1.5
 * @license:     d0NyTkcYcW64yuyl9Cf2M6q3gBilLUVMAwQSumkwPP
 * @copyright:   Copyright (c) 2015 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2011 AITOC, Inc. 
 */
class AdjustWare_Ordernum_Block_Adminhtml_Renderer_Data
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders order/invoice data
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $section = Mage::helper('adjordernum/path')->getSection();
        $group = $this->getColumn()->getId();
		$storeId = $row->getStoreId();
        $path = $section . '/' . $group . '/';

        $prefix = $this->_getTypeValue($path . 'prefix', $storeId);
        $number = $this->_getTypeValue($path . 'number', $storeId);
        $increment = $this->_getTypeValue($path . 'increment', $storeId);
        $random = $this->_getTypeValue($path . 'random', $storeId);
        $suffixlength = $this->_getTypeValue($path . 'suffixlength', $storeId);
        $letter = $this->_getTypeValue($path . 'letter', $storeId);

        return $this->_getHtml($prefix, $number, $increment, $random, $suffixlength, $letter);
    }

    private function _getTypeValue($path, $storeId)
    {
        if ($storeValue = Mage::getStoreConfig($path, $storeId)) {
            return $storeValue;
        }
        if ($websiteValue = Mage::app()->getStore($storeId)->getWebsite()->getConfig($path)) {
            return $websiteValue;
        }
        if ($defaultValue = Mage::getStoreConfig($path)) {
            return $defaultValue;
        }
        return false;
    }

    private function _getHtml($prefix, $number, $increment, $random, $suffixlength, $letter)
    {
        return Mage::helper('adjordernum')->__(
            'Prefix: %s <br/>Starting Number: %s <br/>Increment: %s <br/>Random max value: %s <br/>Suffix Length: %s <br/>Letters: %s <br/>',
            $prefix, $number, $increment, $random, $suffixlength, $letter
        );
    }
}