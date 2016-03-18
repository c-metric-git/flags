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
abstract class SolrBridge_Solrsearch_Model_Resource_Thread_Abstract implements SolrBridge_Solrsearch_Model_Resource_Thread_Interface
{
	public function getThreadResponse($thread)
	{
		$response = '';
		if (($response = fread($thread, 64)) !== '' || feof($thread)) {
			while (!feof($thread)) {
				$response .= fread($thread, 1024);
			}
			return $response;
		}
		return false;
	}
}