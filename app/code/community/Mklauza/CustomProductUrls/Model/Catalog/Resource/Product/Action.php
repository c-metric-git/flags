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

class Mklauza_CustomProductUrls_Model_Catalog_Resource_Product_Action extends Mage_Catalog_Model_Resource_Abstract
{
    /**
     * Intialize connection
     *
     */
    protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(Mage_Catalog_Model_Product::ENTITY)
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $entityIds
     * @param array $attrData
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Action
     */
    public function updateUrlKeyAttributes($entityIds, $urlPattern, $urlKeyCreateRedirect, $storeId)
    {
        $object = new Varien_Object();
        $object->setIdFieldName('entity_id')
            ->setStoreId($storeId);
        
//        $origSaveHistory = Mage::getStoreConfigFlag('catalog/seo/save_rewrites_history');
//        Mage::register('save_rewrite_history', $origSaveHistory);
        Mage::getSingleton('core/config')->saveConfig('catalog/seo/save_rewrites_history', (bool) $urlKeyCreateRedirect);
        Mage::app()->getConfig()->reinit();

        $this->_getWriteAdapter()->beginTransaction();
        try {
            $attribute = $this->getAttribute('url_key');
            if (!$attribute->getAttributeId()) {
                return null;
            }

            $i = 0;
            foreach ($entityIds as $entityId) {
                $i++;
                $object->setId($entityId);
      
//                $object->setData('save_rewrites_history', $urlKeyCreateRedirect);
//                $object->save();
//                $rewriteAttribute = $this->getAttribute('save_rewrites_history');
//                $this->_saveAttributeValue($object, $rewriteAttribute, $urlKeyCreateRedirect);
                
                $value = $this->_getHelper()->prepareUrlKey($entityId, $urlPattern, $storeId);
                // collect data for save
                $this->_saveAttributeValue($object, $attribute, $value);
                // save collected data every 1000 rows
                if ($i % 1000 == 0) {
                    $this->_processAttributeValues();
                }
            }
            $this->_processAttributeValues();
            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }

//        Mage::getSingleton('core/config')->saveConfig('catalog/seo/save_rewrites_history', $origSaveHistory);
        return $this;
    }
    
    private function _getHelper() {
        return Mage::helper('mklauza_customproducturls');
    }
}
