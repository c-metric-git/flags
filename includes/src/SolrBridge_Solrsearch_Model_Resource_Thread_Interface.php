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
interface SolrBridge_Solrsearch_Model_Resource_Thread_Interface
{
	public function prepareThreadCommand($params, $options);

	public function startThread($command, array $options = null);

	public function getThreadResponse($thread);

	public function closeThread($thread);
}