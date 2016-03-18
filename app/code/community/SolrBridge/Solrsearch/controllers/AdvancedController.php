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
class SolrBridge_Solrsearch_AdvancedController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
    	$this->loadLayout();

    	$this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();

        $this->renderLayout();
    }
}