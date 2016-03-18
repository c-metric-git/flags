<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Searchrequest_Block_Adminhtml_Request extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    { 
        $this->_blockGroup = 'searchrequest'; //module name
        $this->_controller = 'adminhtml_request';
        $this->_headerText = Mage::helper('searchrequest')->__('Search Request Management');
        parent::__construct();
    }
    
}