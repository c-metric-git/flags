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

class Mklauza_CustomProductUrls_Block_Adminhtml_MassActionForm extends Mklauza_CustomProductUrls_Block_Adminhtml_Form_Abstract {
    
    public function _construct() {
        parent::_construct();
        $this->setTemplate('mklauza/customproducturls/massaction.phtml');
    }
    
    public function getSubmitUrl() {
        return Mage::helper('adminhtml')->getUrl('adminhtml/ProductUrlsMassAction/save');
    }
    
    public function getSaveRewritesHistory() {
        return Mage::getStoreConfigFlag('catalog/seo/save_rewrites_history');
    }
    
}