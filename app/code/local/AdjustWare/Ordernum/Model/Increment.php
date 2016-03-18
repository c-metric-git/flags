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
 *
 * @copyright  Copyright (c) 2011 AITOC, Inc.
 * @package    AdjustWare_Ordernum
 * @author lyskovets
 */
class AdjustWare_Ordernum_Model_Increment extends Mage_Eav_Model_Entity_Increment_Abstract
{
    private $_format = array();
    private $_hlpr;
    private $_group;
    private $_next;

    public function getNextId()
    {
        $this->_init();

        if (!$this->_format) {
            return $this->_getDefaultNextId();
        }

        $incr = $this->_getIncrement();
        $prefix = $this->_getPrefix();
        $suffix = $this->_getSuffix();

        $this->_next = $this->_format['number'];
        $result = $prefix . str_pad((string)$this->_next, $this->_format['pad'], $this->getPadChar(), STR_PAD_LEFT);
        $validResult = $this->_getValidResult($result, $prefix, $incr) . $suffix;

        $this->_setConfigNextNumber($this->_next + $incr);
        Mage::app()->getStore($this->getStoreId())->resetConfig();
        $this->setPrefix($prefix);

        return $validResult;
    }

    private function _init()
    {    
        $this->_hlpr = Mage::helper('adjordernum/path');
        $this->_group = Mage::getModel('eav/config')
            ->getEntityType($this->getEntityTypeId())
            ->getEntityTypeCode();
		Mage::app()->getStore($this->getStoreId())->resetConfig();
        $this->_format = $this->_getFormat();
    }

    private function _getFormat()
    {
        $format = array();

        $backendConfigFields = Mage::getModel('adjordernum/backend_config')
            ->getFieldsCollection('stores', $this->_group);

        foreach ($backendConfigFields as $fieldName => $field)
        {
            $path = $this->_hlpr->compilePath($this->_group, $fieldName);
            $format[$fieldName] = Mage::getStoreConfig($path, $this->getStoreId());
        }
        
        return $format;
    }

    private function _getIncrement()
    {
        $incr = 1;
        if (isset($this->_format['increment']))
        {
            $incr = max(1, intVal($this->_format['increment']));
        }
        if ($this->_getRandom()) {
            $incr = rand($incr, $this->_getRandom());
        }
        return $incr;
    }

    private function _getSuffix()
    {
        $suffix = '';
        $suffixLetters = $this->_getLetters();
        $suffixLength = (int) $this->_getLengthSuffix();
        if($suffixLength && $suffixLetters) {
            for($i = 0; $i < $suffixLength; $i++) {
                $suffix .= substr($suffixLetters, rand(0, strlen($suffixLetters)-1), 1);
            }
        }
        return $suffix;
    }

    private function _getPrefix()
    {
        $prefix = $this->_format['prefix'];
        if($this->_format['dateformat'] && $this->_format['dateformat']!=='No')
        {
            //$prefix .= date($this->_format['dateformat']);
            $prefix .= Mage::app()->getLocale()->date()->toString($this->_format['dateformat'], 'php');
        }
        return $prefix;
    }

    private function _getRandom()
    {
        if(isset($this->_format['random']) && $this->_format['random']) {
            return $this->_format['random'];
        }
        return false;
    }

    private function _getLengthSuffix()
    {
        if(isset($this->_format['suffixlength']) && $this->_format['suffixlength']) {
            return $this->_format['suffixlength'];
        }
        return false;
    }

    private function _getLetters()
    {
        if(isset($this->_format['letter'])) {
            return $this->_format['letter'];
        }
        return false;
    }

    private function _setConfigNextNumber($next)
    {
        $path = $this->_hlpr->compilePath($this->_group, 'number');
        $storeValue = Mage::getModel('core/config_data')->getCollection()
            ->addFieldToFilter('scope', 'stores')
            ->addFieldToFilter('scope_id', $this->getStoreId())
            ->addFieldToFilter('path', $path)
            ->getFirstItem()->getValue();
        if ($storeValue && ($storeValue < $next))
        {
            Mage::getConfig()->saveConfig($path, $next, 'stores', $this->getStoreId());
            return;
        }

        $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        $websiteValue = Mage::getModel('core/config_data')->getCollection()
            ->addFieldToFilter('scope', 'websites')
            ->addFieldToFilter('scope_id', $websiteId)
            ->addFieldToFilter('path', $path)
            ->getFirstItem()->getValue();
        if ($websiteValue && ($websiteValue < $next))
        {
            Mage::getConfig()->saveConfig($path, $next, 'websites', $websiteId);
            return;
        }

        $defaultValue = Mage::getModel('core/config_data')->getCollection()
            ->addFieldToFilter('scope', 'default')
            ->addFieldToFilter('scope_id', '0')
            ->addFieldToFilter('path', $path)
            ->getFirstItem()->getValue();
        if ($defaultValue && ($defaultValue < $next))
        {
            Mage::getConfig()->saveConfig($path, $next, 'default');
            return true;
        }
    
        return Mage::helper('adjordernum')->__('Please try again as order can\'t be placed.');
    }

    private function _getValidResult($result, $prefix, $incr)
    {
        while($this->_hasDuplicateResult($result)) {
            $this->_next = $this->_next + $incr;
            $result = $prefix . str_pad((string)$this->_next, $this->_format['pad'], $this->getPadChar(), STR_PAD_LEFT);
        }
        return $result;
    }

    private function _getDefaultNextId()
    {
        $numericIncrement = Mage::getModel('eav/entity_increment_numeric');
        $numericIncrement->setData($this->getData());
        return $numericIncrement->getNextId();
    }

    private function _hasDuplicateResult($result)
    {
        if($this->_group == 'order') {
            $entity =  Mage::getModel('sales/order');
        }
        else {
            $entity =  Mage::getModel('sales/order_' . $this->_group);
        }

        if(!$entity) {
            Mage::throwException('Invalid model.');
        }
       
        $ids = $entity->getCollection()
            ->addAttributeToFilter('increment_id', array('like' => $result.'%'))->getSize();
        return ($ids > 0);
    }

}