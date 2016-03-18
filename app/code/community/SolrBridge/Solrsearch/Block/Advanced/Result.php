<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author    Hau Danh
 * @copyright    Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Advanced_Result extends SolrBridge_Solrsearch_Block_Product_List
{
    protected function prepareSolrData()
    {
        $this->_solrModel = Mage::getModel('solrsearch/solr');
        $this->_solrData = Mage::getModel('solrsearch/solr')->advQuery();
    }
}