<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Searchrequest_Block_Adminhtml_Request_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'searchrequest';
        $this->_controller = 'adminhtml_request';
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('searchrequest')->__('Save Search Request'));
    }
    public function getHeaderText()
    {
            return Mage::helper('searchrequest')->__('Add New Search Request');
        
    }
}