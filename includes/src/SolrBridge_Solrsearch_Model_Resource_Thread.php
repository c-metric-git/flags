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
class SolrBridge_Solrsearch_Model_Resource_Thread
{
	private $_options = array(
			'timeout'            => 80,
			'scriptPath'         => null,
			'process'            => 'php',
			'maxProcess'         => 10,
			'onCompliteCallback' => null,
			'adapter'            => 'UnixProcess',
	),
	$_adapter          = null,
	$_isrunning			= false,
	$_runningProcesses = array(),
	$_threadQueue      = array();
	
	public function isRunning()
	{
		return $this->_isrunning;
	}
	
	public function setStatus($status)
	{
		$this->_isrunning = $status;
	}
	
	public function getOptions()
	{
		return $this->_options;
	}
	
	public function getThreads()
	{
		return $this->_threadQueue;
	}
	
	/**
	 * @param array|null $options
	 * @return ThreadManager
	 */
	public function init(array $options = null)
	{
		$instance = new self;
	
		if ($options) {
			$instance->_options = array_merge($instance->_options, $options);
		}
	
		return $instance;
	}
	
	public function setCompliteCallback($callback)
	{
		$this->_options['onCompliteCallback'] = $callback;
		return $this;
	}
	
	public function setAdapter(SolrBridge_Solrsearch_Model_Resource_Thread_Interface $adapter)
	{
		$this->_adapter = $adapter;
		return $this;
	}
	
	/**
	 * Adds a job to the queue
	 *
	 * @param $requestParams array of parameters to be passed to the process
	 * @return ThreadManager
	 */
	public function addThread($requestParams = null)
	{
		$this->_threadQueue[] = $this->_createThreadCommand($requestParams);
		return $this;
	}
	
	
	/**
	 * @return ThreadManager
	 */
	public function run()
	{
		$this->setStatus(true);
		$maxProcess = $this->_getOption('maxProcess');
		$count      = 0;
	
		foreach ($this->_threadQueue as $i => $thread) {
			if ($count < $maxProcess) {
				$this->_runProcess($thread);
				unset($this->_threadQueue[$i]);
			}
			$count++;
		}
	
		//$this->_startIterations();
	
		$this->setStatus(false);
		return $this;
	}
	
	private function _runProcess($command)
	{
		$this->_runningProcesses[] = $this->_getAdapter()->startThread($command, $this->_options);
		return $this;
	}
	
	private function _startIterations()
	{
		$_startTime = microtime(true);
		$_timeout   = $this->_getOption('timeout');
		$adapter    = $this->_getAdapter();
	
		//Loop through active processes
		while ($this->_runningProcesses) {
	
			//If the timeout is exceeded the total of the remaining outstanding job nailed
			if ($_timeout && (microtime(true) - $_startTime) > $_timeout) {
				foreach ($this->_runningProcesses as $i => $thread) {
					$adapter->closeThread($thread);
					unset($this->_runningProcesses[$i]);
				}
			}
	
			// Iterate through them and wait until the answer comes
			foreach ($this->_runningProcesses as $i => $thread) {
	
				// If the answer came or the process completed
				$response = $adapter->getThreadResponse($thread);
	
				if ($response !== false) {
	
					$adapter->closeThread($thread); // Close the process
					unset($this->_runningProcesses[$i]);  // Remove it from the list of active
	
					$this->_notifyComplite($response); // Inform the client and pass it the response process
	
	
					/**
					* If the queue still have tasks to be performed
					* And do not exceed the total execution time - start another process from a queue to a stack of active
					*/
					if ($this->_threadQueue && !($_timeout && (microtime(true) - $_startTime) > $_timeout)) {
	
						$nextThread = array_shift($this->_threadQueue);
						$this->_runProcess($nextThread);
					}
				}
			}
			//0.01 second delay in the execution of the cycle. too often ask the answer is not necessarily
			usleep(10000);
		}
	}
	
	private function _notifyComplite($response)
	{
		$callback = $this->_getOption('onCompliteCallback');
	
		if ($callback && is_callable($callback)) {
			call_user_func($callback, $response);
		}
	}
	
	private function _createThreadCommand($params = null)
	{
		return $this->_getAdapter()->prepareThreadCommand($params, $this->_options);
	}
	
	
	/**
	 * @return AdapterInterface
	 */
	private function _getAdapter()
	{
		if ($this->_adapter === null) {
			$this->_adapter = Mage::getResourceModel('solrsearch/thread_process');
		}
		return $this->_adapter;
	}
	
	private function _getOption($name)
	{
		return isset($this->_options[$name]) ? $this->_options[$name] : null;
	}
}