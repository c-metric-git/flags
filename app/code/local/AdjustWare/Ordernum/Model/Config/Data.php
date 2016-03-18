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
class AdjustWare_Ordernum_Model_Config_Data extends Mage_Core_Model_Abstract
{
    /**@var AdjustWare_Orderum_Helper_Path */
    private $_helper;

    protected function  _construct()
    {
        parent::_construct();
        $this->_init('adjordernum/config_data');
        $this->_helper = Mage::helper('adjordernum/path');
    }

    public function getValues($scope, $scopeId)
    {
        return $this->_getCollection($scope, $scopeId, $this->_helper->getSection());
    }

    public function getValue($scope, $scopeId, $path)
    {
        $collection = $this->getValues($scope,$scopeId);
        foreach($collection as $value)
        {
            if($path == $value['path'])
            {
                return $value['value'];
            }
        }
        //Mage::throwException('Change '.__CLASS__.'::'.__METHOD__);
    }

    public function setValue($path, $value, $scope, $scopeId = '0')
    {
        $coreConfig = Mage::getModel('core/config');
        /*
         * @var $coreConfig Mage_Core_Model_Config
         */
        $coreConfig->saveConfig($path, $value, $scope, $scopeId);
    }

    private function _getCollection($scope, $scopeId, $section)
    {
        $coreConfig = Mage::getModel('core/config_data');
        $collection = $coreConfig->getCollection()->addScopeFilter($scope, $scopeId, $section)
            ->getData();
        /*@var $collection Mage_Core_Model_Mysql4_Config_Data_Collection */
        return $collection;
    }
}