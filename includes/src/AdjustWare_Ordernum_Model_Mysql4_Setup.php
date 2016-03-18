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
class AdjustWare_Ordernum_Model_Mysql4_Setup extends Mage_Core_Model_Resource_Setup
{  
    /**
     * !Only for upgrade-0.2.1-5.0.0.
     * Set valid prefix in 'eav_entity_store.increment_prefix' value.
     */
    private function _setNumberPrefixes()
    {
        $connection = $this->getConnection();
        $hlp = Mage::helper('adminhtml');
        $entityStoreCollection = $this->getEavEntityStoreCollection();
        $adjordernumCollection = $this->getAdjordernumCollection();
 
        foreach ($entityStoreCollection as $entStoreField)
        {
            foreach ($adjordernumCollection as $adjField)
            {
                if($adjField['store_id'] !== $entStoreField['store_id'])
                {
                    continue;
                }
                $entityTypeCode = Mage::getModel('eav/config')
                    ->getEntityType($entStoreField['entity_type_id'])
                    ->getEntityTypeCode();
                $prefix = $adjField[$entityTypeCode.'_prefix'];
                if($adjField['dateformat'])
                {
                    $date = date($adjField['dateformat']);
                    $prefix = $adjField[$entityTypeCode.'_prefix'].$date;
                }
                
                $lastNumber = $entStoreField['increment_last_id'];
                
                if(isset($prefix) && $prefix !== '')
                {
                    $prefix = substr($lastNumber, 0, strlen($prefix));
                    $number = substr($lastNumber, strlen($prefix));
                }
                
                $typeId = $entStoreField['entity_type_id'];
                $storeId = $entStoreField['store_id'];
                //$EavEntityStoreBind = array('increment_last_id' => $lastNumber, 'increment_prefix' => null);
                $EavEntityStoreBind = array('increment_prefix' => $prefix );
                $EavEntityStoreWhere = 'store_id='.$storeId.' AND entity_type_id='.$typeId;
                $connection->update($this->getTable('eav_entity_store'), $EavEntityStoreBind, $EavEntityStoreWhere);
            }      
        }
    }
    /**
     *
     * @return array()
     */
    public function getEavEntityStoreCollection()
    {
        $connection = $this->getConnection();
        $entityTypeCollection = $this->_getEavEntityTypeCollection();
        $types = array();
        foreach ($entityTypeCollection as $key=>$typeField)
        {
            $types[$key]= $typeField['entity_type_id'];
        }
        $typesString = join(', ', $types);
        $select = $connection->select()
            ->from($this->getTable('eav_entity_store'))
            ->where('store_id>0')
            ->where('entity_type_id IN('.$typesString.')');
        $collection = $connection->fetchAll($select);
        return $collection;
    }
    /**
     *
     * @return array()
     */
    public  function getAdjordernumCollection()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('adjordernum'));
        $collection = $connection->fetchAll($select);
        return $collection;
    }
    /**
     *
     * @return array()
     */
    private function _getEavEntityTypeCollection()
    {
        $types = array(
            '\'order\'',
            '\'invoice\'',
            '\'shipment\'',
            '\'creditmemo\'');
        $typesStr = join(',', $types);
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('eav_entity_type'),array('entity_type_id','entity_type_code'))
            ->where('entity_type_code IN('.$typesStr.')');
        $collection = $connection->fetchAll($select);
        return $collection;
    }

    public function setDefaultModuleConfig()
    {
        $filePath = (Mage::getRoot()).DS.'code'.DS.'local'.DS.'AdjustWare'.DS.'Ordernum'.DS.'etc'.DS.'config.xml';
        $xmlConfig = new Varien_Simplexml_Config;
        if (!$xmlConfig->loadFile($filePath))
        {
            Mage::throwException('File "/etc/config.sys" not found ');
        }
        $collection = $xmlConfig->getNode('default/ordernum')->asArray();
        $section = 'ordernum';
        foreach ($collection as $group => $fields)
        {
            foreach ($fields as $field => $value)
            {
                $path = $section.'/'.$group.'/'.$field;
                $this->setConfigData($path, $value,'default');
            }
        }
    }

    public function formatNumber($number,$prefix = null)
    {
        $defaultModel = Mage::getModel('Mage_Eav_Model_Entity_Increment_Numeric');
        $defaultModel->setPrefix($prefix);
        $formatedNumber = $defaultModel->format($number);
        return $formatedNumber;
    }

    public function clearNumber($number,$prefix = null)
    {
        if (strpos($number, $prefix)===0)
        {
            $cleanNumber = (int)substr($number, strlen($prefix));
        } 
        else
        {
            $cleanNumber = (int)$number;
        }
        return $cleanNumber;
    }

    /**
     *
     * !Only for upgrade-0.2.1-5.0.0.
     */
    private function _updateConfig($scope, $scopeId)
    {
        $prefix = 'sales'.'/'.'adjordernum';
        $section = 'ordernum';
        $oldConfig = $this->_getOldConfig($scope, $scopeId);
        foreach($oldConfig as $oldValue)
        {
            switch($oldValue['path'])
            {
                case($prefix.'/'.'order_prefix'):
                    $this->setConfigData($section.'/'.'order'.'/'.'prefix', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'inv_prefix'):
                    $this->setConfigData($section.'/'.'invoice'.'/'.'prefix', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'ship_prefix'):
                    $this->setConfigData($section.'/'.'shipment'.'/'.'prefix', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'memo_prefix'):
                    $this->setConfigData($section.'/'.'creditmemo'.'/'.'prefix', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'start'):
                    $this->setConfigData($section.'/'.'order'.'/'.'number', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'invoice'.'/'.'number', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'shipment'.'/'.'number', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'creditmemo'.'/'.'number', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'increment'):
                    $this->setConfigData($section.'/'.'order'.'/'.'increment', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'invoice'.'/'.'increment', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'shipment'.'/'.'increment', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'creditmemo'.'/'.'increment', $oldValue['value'], $scope, $scopeId);
                    break;
                case($prefix.'/'.'pad'):
                    $this->setConfigData($section.'/'.'order'.'/'.'pad', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'invoice'.'/'.'pad', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'shipment'.'/'.'pad', $oldValue['value'], $scope, $scopeId);
                    $this->setConfigData($section.'/'.'creditmemo'.'/'.'pad', $oldValue['value'], $scope, $scopeId);
                    break;
            }
        }
    }
    /**
     *
     * !Only for upgrade-0.2.1-5.0.0.
     */
    public function updateDefaultConfig()
    {
        $scope = 'default';
        $scopeId = '0';
        $this->_updateConfig($scope, $scopeId);
        $path = 'ordernum'.'/'.'general'.'/'.'settings_level';
        $this->setConfigData($path, 'custom', $scope, $scopeId);
    }
    /**
     *
     * !Only for upgrade-0.2.1-5.0.0.
     */
    public function updateWebsiteConfig()
    {
        $scope = 'websites';
        $websites = Mage::app()->getWebsites();
        foreach($websites as $website)
        {
            $scopeId = $website->getId();
            $this->_updateConfig($scope, $scopeId);
        }
    }
    /**
     *
     * !Only for upgrade-0.2.1-5.0.0.
     */
    public function updateStoreConfigs()
    {
        $adjordernumCollection = $this->getAdjordernumCollection();
        $this->_setNumberPrefixes();

        foreach($adjordernumCollection as $adjordRow)
        {
            $scope = 'stores';
            $scopeId = $adjordRow['store_id'];
            $section = 'ordernum';
            $pad = $this->_getOldValue($scope, $scopeId, 'sales'.'/'.'adjordernum'.'/'.'pad');

            //order
            $this->setConfigData($section.'/'.'order'.'/'.'prefix', $adjordRow['order_prefix'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'order'.'/'.'number', $adjordRow['order_start'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'order'.'/'.'increment', $adjordRow['order_incr'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'order'.'/'.'dateformat', $adjordRow['dateformat'], $scope, $scopeId);
            if(isset($pad))
            {
                $this->setConfigData($section.'/'.'order'.'/'.'pad', $pad, $scope, $scopeId);
            }
            //invoice
            $this->setConfigData($section.'/'.'invoice'.'/'.'prefix', $adjordRow['invoice_prefix'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'invoice'.'/'.'number', $adjordRow['invoice_start'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'invoice'.'/'.'increment', $adjordRow['invoice_incr'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'invoice'.'/'.'dateformat', $adjordRow['dateformat'], $scope, $scopeId);
            if(isset($pad))
            {
                 $this->setConfigData($section.'/'.'invoice'.'/'.'pad', $pad, $scope, $scopeId);
            }
            //shipment
            $this->setConfigData($section.'/'.'shipment'.'/'.'prefix', $adjordRow['shipment_prefix'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'shipment'.'/'.'number', $adjordRow['shipment_start'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'shipment'.'/'.'increment', $adjordRow['shipment_incr'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'shipment'.'/'.'dateformat', $adjordRow['dateformat'], $scope, $scopeId);
            if(isset($pad))
            {
                 $this->setConfigData($section.'/'.'shipment'.'/'.'pad', $pad, $scope, $scopeId);
            }
            //creditmemo
            $this->setConfigData($section.'/'.'creditmemo'.'/'.'prefix', $adjordRow['creditmemo_prefix'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'creditmemo'.'/'.'number', $adjordRow['creditmemo_start'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'creditmemo'.'/'.'increment', $adjordRow['creditmemo_incr'], $scope, $scopeId);
            $this->setConfigData($section.'/'.'creditmemo'.'/'.'dateformat', $adjordRow['dateformat'], $scope, $scopeId);
            if(isset($pad))
            {
                 $this->setConfigData($section.'/'.'creditmemo'.'/'.'pad', $pad, $scope, $scopeId);
            }
        }
    }
    /**
         *!Only for upgrade-0.2.1-5.0.0.
         * @param string $scope
         * @param string $scopeId
         * @return array
         */
    private function _getOldConfig($scope, $scopeId)
    {
        $prefix = 'sales'.'/'.'adjordernum';
        $coreConfig = Mage::getModel('core/config_data');
        $collection = $coreConfig->getCollection()
            ->addScopeFilter($scope, $scopeId, $prefix)
            ->getData();
        return $collection;
    }

    /**
     *!Only for upgrade-0.2.1-5.0.0.
     * @param string $scope
     * @param string $scopeId
     * @param string $path
     * @return string
     */
    private function _getOldValue($scope, $scopeId, $path)
    {
        $collection = $this->_getOldConfig($scope, $scopeId);
        foreach($collection as $value)
        {
            if($path == $value['path'])
            {
                return $value['value'];
            }
        }
    }

    public function setMageStoreConfig()
    {
        $scope = 'stores';
        $section = 'ordernum';
        $mageConfig = $this->getEavEntityStoreCollection();
        $pad = Mage::getModel('Mage_Eav_Model_Entity_Increment_Numeric')
            ->getPadLength();
        $increment = '1';

        foreach($mageConfig as $field)
        {
            $scopeId = $field['store_id'];
            $typeCode = Mage::getModel('eav/config')
                ->getEntityType($field['entity_type_id'])
                ->getEntityTypeCode();
            $pathPrefix = $section.'/'.$typeCode.'/';
            $number = $this->clearNumber($field['increment_last_id'], $field['increment_prefix']);
            $this->setConfigData($pathPrefix.'number', $number, $scope, $scopeId);
            $this->setConfigData($pathPrefix.'prefix', $field['increment_prefix'], $scope, $scopeId);
            $this->setConfigData($pathPrefix.'pad', $pad, $scope, $scopeId);
            $this->setConfigData($pathPrefix.'increment', $increment, $scope, $scopeId);
        }
    }

}