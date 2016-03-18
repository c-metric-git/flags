<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade SolrBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Model_Adminhtml_System_Source_Config_Cores
{
    /**
     * Fetch options array
     * 
     * @return array
     */
    public function toOptionArray()
    {
	    $cores = Mage::helper('solrsearch/solr')->getAvailableCores();
	    $options = array();
	    if ( !empty($cores) )
	    {
	    	foreach ($cores as $corename => $infoArr)
	    	{
	    		$options[] = array('label' => $corename, 'value' => $corename);
	    	}
	    }
	    
	    return $options;
    }
    
    public function toArray() {
        $cores = Mage::helper('solrsearch/solr')->getAvailableCores();
        $data = array();
        if ( !empty($cores) ) {
            foreach ($cores as $corename => $infoArr) {
                $storeIds = '';
                $storeIdArray = Mage::getModel('solrsearch/ultility')->getMappedStoreIdsFromSolrCore($corename);
                if ( !empty($storeIdArray) ) {
                    $storeIds = $storeIdArray;
                }
                
                $data[$corename] = array('label' => $corename, 'value' => $corename, 'stores' => @implode(',', $storeIds));
            }
        }
        return $data;
    }
}
