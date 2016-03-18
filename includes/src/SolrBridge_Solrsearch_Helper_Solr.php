<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade MongoBridge to newer
 * versions in the future.
 *
 * @category    SolrBridge
 * @package     SolrBridge_Solrsearch
 * @author      Hau Danh
 * @copyright   Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 */
class SolrBridge_Solrsearch_Helper_Solr extends Mage_Core_Helper_Abstract
{
/**
	 * Return solr cores list
	 * @return multitype:
	 */
	public function getAvailableCores() {
		return Mage::getResourceModel('solrsearch/solr')->getAdminCores();
	}
}