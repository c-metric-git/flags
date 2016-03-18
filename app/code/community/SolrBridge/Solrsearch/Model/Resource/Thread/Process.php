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
class SolrBridge_Solrsearch_Model_Resource_Thread_Process extends SolrBridge_Solrsearch_Model_Resource_Thread_Abstract {
	const LOGPATH = '/tmp/solrbridge-thead.log';
	
	public function startThread($command, array $options = null) {
		//$process = popen ( $command, 'r' );
		//stream_set_blocking ( $process, false );
		$outPut = array();
		$process = shell_exec ( $command );
		return $process;
	}
	public function closeThread($thread) {
		pclose ( $thread );
	}
	public function prepareThreadCommand($params, $options) {
		$scriptPath = isset ( $options ['scriptPath'] ) ? $options ['scriptPath'] : null;
		$process = ! empty ( $options ['process'] ) ? $options ['process'] : 'php';

		if (! $scriptPath || ! file_exists ( $scriptPath )) {
			throw new \Exception ( $scriptPath . ' does not exists.' );
		}

		//$args = str_replace ( '&', '\\&', http_build_query ( ( array ) $params ) );

		$argsString = '';

		foreach ($params as $k=>$v)
		{
			$argsString .= ' -'.$k.' '.$v;
		}
		$logPath = dirname($scriptPath);
		$logPath = self::LOGPATH;
		//return "{$process} {$scriptPath} {$argsString} > {$logPath} &";
		return "{$process} {$scriptPath} {$argsString} > {$logPath} &";
	}
}