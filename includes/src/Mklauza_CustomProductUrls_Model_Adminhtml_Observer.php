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

class Mklauza_CustomProductUrls_Model_Adminhtml_Observer {
    
    public function addCustomUrlsMassAction(Varien_Event_Observer $observer) { // adminhtml_block_html_before
        
        $block = $observer->getEvent()->getBlock();
        if (Mage::helper('mklauza_customproducturls')->getIsEnabled() && $block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid) {
            $block->setMassactionIdField('mklauza_customproducturls_massaction');
            $block->getMassactionBlock()->setFormFieldName('product');
            $block->getMassactionBlock()->setUseSelectAll(false);

            $block->getMassactionBlock()->addItem('mklauza_customproducturls', array(
                'label' => 'Set custom URL',
//                'url' => $block->getUrl('*/productUrls'),
                'url' => $block->getUrl('*/productUrlsMassAction/edit'),
            ));

//            $columnId = 'massaction';
//
//            $massactionColumn = array(
//                'index' => $block->getMassactionIdField(),
//                'filter_index' => $block->getMassactionIdFilter(),
//                'type' => 'massaction',
//                'name' => $block->getMassactionBlock()->getFormFieldName(),
//                'align' => 'center',
//                'is_system' => true
//            );
//
//            if ($block->getNoFilterMassactionColumn()) {
//                $massactionColumn->setData('filter', false);
//            }
//
//            // rearrange the columns;
//            $oldColumns = $block->getColumns();
//            foreach($oldColumns as $column){
//               $block->removeColumn($column->getId());  
//            }

//            $block->addColumn($columnId, $massactionColumn);
//            $block->addColumn('block_id', array(
//                'header' => Mage::helper('cms')->__('ID'),
//                'width' => '50px',
//                'type' => 'number',
//                'index' => 'block_id',
//            ));

            // put back the original columns
//            foreach($oldColumns as $column){
//                $block->addColumn($column->getId(),$column->getData());
//            }

            return $this;
        }
    }
    
    public function addClearPermanentRedirectsButton(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        if (Mage::helper('mklauza_customproducturls')->getIsEnabled() && $block instanceof Mage_Adminhtml_Block_Urlrewrite) {
            $block->addButton('clear', array(
                'label'     => Mage::helper('mklauza_customproducturls')->__('Clear Rewrite History'),
                'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('adminhtml/ProductUrlsMassAction/clearRedirects') .'\')',
                'class'     => 'delete',
            ));
        }        
    }
    
    public function generateUrlKey(Varien_Event_Observer $observer) { // catalog_product_save_after
        $product = $observer->getEvent()->getProduct();

        if(Mage::registry('generate_url') && $product->getId()) {   // in case we save product with empty url key               
            // && !($product->getTypeId() == 'simple' && $product->getAttributeSetId() == $this->getAttributeSetId('prenumerata'))
            
            $urlPattern = Mage::helper('mklauza_customproducturls')->getConfigPattern();
            $storeId = Mage::app()->getStore()->getId();
            
            $url_key = Mage::helper('mklauza_customproducturls')->prepareUrlKey($product->getId(), $urlPattern, $storeId);
            $product->setUrlKey($url_key)->getResource()->saveAttribute($product, 'url_key'); 
            Mage::unregister('generate_url');
        }      

        return $this;
    }
        
    /*
     * Checks whether 
     */
    public function checkUrl(Varien_Event_Observer $observer) { // catalog_product_prepare_save
        $product = $observer->getEvent()->getProduct();

        if(Mage::helper('mklauza_customproducturls')->getApplyToNewFlag()) {
            $generate_url = Mage::registry('generate_url');
            if(!$generate_url && $product && !$product->getUrlKey()) {
                Mage::register('generate_url', true, $graceful=true); // we set up session flag to workaround default magento url generation in save after
            }
        }
        
        return $this;
    }

    

    
    
}
