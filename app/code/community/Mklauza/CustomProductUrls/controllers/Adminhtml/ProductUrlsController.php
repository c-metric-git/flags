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

class Mklauza_CustomProductUrls_Adminhtml_ProductUrlsController extends Mage_Adminhtml_Controller_Action {
    
    public function exampleAction() {
        $pattern = $this->getRequest()->getParam('pattern');
        $data['exampleUrl'] = Mage::helper('mklauza_customproducturls')->getRandomExample($pattern);
        $data['isSuccess'] = true;
        $this->getResponse()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($data));
    }
//    public function indexAction() {
//        $this->loadLayout();
//        $this->_setActiveMenu('system/mklauza_customproducturls');
//        $this->_title('Custom Product Urls')->_addContent($this->getLayout()->createBlock('mklauza_customproducturls/adminhtml_settingsform'));
//        $this->renderLayout();    
//    }
//    
//    public function saveAction() {
//        try {
//            $enabled = $this->getRequest()->getParam('is_active');
//            Mage::getSingleton('core/config')->saveConfig('mklauza_customproducturls/general/is_active', $enabled, 'default', '0');
//            
//            $pattern = $this->getRequest()->getParam('pattern');
//            Mage::getSingleton('core/config')->saveConfig('mklauza_customproducturls/general/pattern', $pattern, 'default', '');
//            
//            $applyToNew = $this->getRequest()->getParam('apply_to_new');
//            Mage::getSingleton('core/config')->saveConfig('mklauza_customproducturls/general/apply_to_new', $applyToNew, 'default', '0');            
//            
//            $this->_getSession()->addSuccess($this->__('Successfully saved.'));
//        } catch (Exception $e) {
//            $this->_getSession()->addError($e->getMessage());
//        }
//        $this->_redirect('*/*/index');        
//    }
    
}