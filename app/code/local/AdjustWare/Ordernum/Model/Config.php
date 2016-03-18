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
class AdjustWare_Ordernum_Model_Config 
{
    /**
     * Delete all configs by scope for this module
     */
    public function deleteByScope($scope)
    {
        $entityes = $this->_getEntityesByScope($scope);
        foreach ($entityes as $entity)
        {
            $this->_deleteEntityConfig($scope, $entity);
        }
    }
    /**
     * Set inherited data for all configs in this scope
     */
    public function setInheritedByScope($scope)
    {
        $entityes = $this->_getEntityesByScope($scope);
        foreach ($entityes as $entity)
        {
            $this->_setInheritedEntityConfig($scope, $entity);
        }
    }

    private function _getEntityesByScope($scope)
    {
        switch ($scope)
        {
            case('default'):
                Mage::throwException('Change '.__CLASS__.'::'.__METHOD__);
                break;
            case('websites'):
                return Mage::app()->getWebsites();
            case('stores'):
                return Mage::app()->getStores();
            default:
                exit(__CLASS__.' Past code </br>');
        }
    }

    public function _deleteEntityConfig($scope, $entity)
    {
        $paths = Mage::helper('adjordernum/path')->getValidPaths($scope);
        $scopeId = $entity->getId();
        foreach ($paths as $path)
        {
            Mage::getConfig()->deleteConfig($path, $scope, $scopeId);
        }
    }

    public function _setInheritedEntityConfig($scope, $entity)
    {
        $paths = Mage::helper('adjordernum/path')->getValidPaths($scope);
        foreach ($paths as $path)
        {
            $value = $entity->getConfig($path);
            $scopeId = $entity->getId();
            Mage::getConfig()->saveConfig($path, $value, $scope, $scopeId);
        }
    }
}