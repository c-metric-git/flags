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

class SolrBridge_Base{
	protected $config = array();

	public function __construct($config = array())
	{
		$this->config = $config;
	}

	public function getConfig()
	{
		return $this->config;
	}
	
	static public function loadZend() {
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Controller'.SBDS.'Request'.SBDS.'Abstract.php';
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Controller'.SBDS.'Request'.SBDS.'Http.php';
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Filter'.SBDS.'Interface.php';
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Locale'.SBDS.'Data'.SBDS.'Translation.php';
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Locale.php';
	    include_once SOLRBRIDGE_ROOT.SBDS.'lib'.SBDS.'Zend'.SBDS.'Filter'.SBDS.'Alnum.php';
	}
	
	static public function loadMage() {
	    //Generate config file
	    require_once (SOLRBRIDGE_ROOT.SBDS.'app/Mage.php');
	    Mage::app();
	}
	/**
	 * Load magento config data and save into static file
	 * @param string $baseDir
	 * @return string
	 */
	static public function prepareConfigData($baseDir) {
	    self::loadMage();
	     
	    $configFilePath = $baseDir.'/var/cache/solrbridge.conf';
	    if ( !file_exists($configFilePath) ) {
	        //Generate config file
	        
	        $config = Mage::getStoreConfig('solrbridge', 0);
	         
	        $stores = Mage::app()->getStores();
	        foreach ($stores as $store) {
	            $config['stores'][$store->getId()] = Mage::getStoreConfig('solrbridge', $store->getId());
	            $config['stores'][$store->getId()]['currencycode'] = $store->getCurrentCurrencyCode();
	            $config['stores'][$store->getId()]['website_id'] = $store->getWebsiteId();
	             
	            $index = Mage::getModel('solrsearch/index')->load($store->getId(), 'store_id');
	             
	            $config['stores'][$store->getId()]['settings']['solr_index'] = $index->getSolrCore();
	        }
	         
	        $solrcore = 'english';
	         
	        $options = array('solrcore' => $solrcore, 'queryText' => 'PLACEHOLDER', 'rows' => 20, 'facetlimit' => 200, 'autocomplete' => true);
	         
	        $solrModel = Mage::getModel('solrsearch/solr')->init($options)->prepareQueryData();
	         
	        $facetFields = $solrModel->getFacetFields();
	        $boostFields = $solrModel->getBoostFields();
	         
	        $config['facetfields'] = $facetFields;
	        $config['boostfields'] = $boostFields;
	         
	        $configFile = fopen($configFilePath, 'w');
	        fwrite($configFile, json_encode($config));
	        fclose($configFile);
        }
        $configData = file_get_contents($configFilePath);
        $config = json_decode($configData, true);
        return $config;
	}

	/**
	 * Get parameters value
	 * @return array
	 */
	static public function getParams() {

		$httpRequest = new Zend_Controller_Request_Http();

		$params = $httpRequest->getParams();
		if ( !is_array($params) )
		{
			$params = array();
		}

		return $params;
	}

	/**
	 * Get parameter value
	 * @param $key
	 * @return mixed
	 */
	static public function getParam($key) {
		$params = self::getParams();
		$returnValue = '';
		if (!empty($key) && isset($params[$key]) && !empty($params[$key])) {
			$returnValue = $params[$key];
		}
		return self::escapeHtml($returnValue);
	}

	static public function escapeHtml($data, $allowedTags = null)
	{
		if (is_array($data)) {
			$result = array();
			foreach ($data as $item) {
				$result[] = self::escapeHtml($item);
			}
		} else {
			// process single item
			if (strlen($data)) {
				if (is_array($allowedTags) and !empty($allowedTags)) {
					$allowed = implode('|', $allowedTags);
					$result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
					$result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
					$result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
				} else {
					$result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
				}
			} else {
				$result = $data;
			}
		}
		return $result;
	}

	static public function hightlight($words,$text)
	{
		$newtext = $text;

		$split_words = explode( " " , trim($words) );

		$specialChars = array('/', '\\', '*', '.', ')', '(');

		foreach($split_words as $word)
		{
			$word = trim($word);
			if (!in_array($word, $specialChars)) {
				$text = preg_replace("/($word)(?=[^>]*(<|$))/Ui" ,"<strong>$1</strong>", $text );
			}
		}
		if (!empty($text) && $text != null) {
		    return $text;
		}
		return $newtext;
	}
	
	/**
	 * Parse and remove some special characters
	 * @param string $text
	 * @return string
	 */
	static public function getPreparedBoostTextOld($text)
	{
		$boostText =  (strrpos(trim($text,':'), ':') > -1)?'"'.trim($text,':').'"':trim($text,':');
		return $boostText;
	}
	
	/**
	 * Parse and remove some special characters
	 * @param string $text
	 * @return string
	 */
	static public function getPreparedBoostText($text)
	{
		$boostText =  (strrpos(trim($text,':'), ':') > -1)?'"'.trim($text,':').'"':trim($text,':');
		$filter = new Zend_Filter_Alnum(array('allowwhitespace' => true));
		return $filter->filter($boostText);
	}

	/**
	 * convert Boost Settings Array To String
	 * @param array $boostFieldsArr
	 * $boostFieldsArr = array(
	 * 						'att1' => array(
	 * 										array('field' => 'field_x', 'weight' => 'n', 'value' => 'value'),
	 * 										array('field' => 'field_y', 'weight' => 'n', 'value' => 'value')
	 * 									   )
	 * @return string
	 */
	protected function convertBoostFieldsToString()
	{
		$boostQueryString = '';

		if (is_array($this->boostFields) && !empty($this->boostFields))
		{
			foreach( $this->boostFields as $attributeCode => $configArray)//Foreach attributes
			{
				foreach ($configArray as $config) // Foreach attribute config
				{
					$boostField = $config['field'];
					$boostWeight = $config['weight'];
					$boostValue = $config['value'];

					if (isset($config['type']) && $config['type'] == 'absolute')
					{
						$boostQueryString .= $boostField.':"'.$boostValue.'"^'.$boostWeight.' ';
					}
					else
					{
						$boostQueryString .= $boostField.':'.$boostValue.'^'.$boostWeight.' ';
					}
				}
			}
		}

		return $boostQueryString;
	}
	/**
	 * Convert facetfields from array to param string
	 * @return string
	 */
	protected function convertFacetFieldsToString()
	{
		$facetFieldString = '';

		if (is_array($this->facetFields) && !empty($this->facetFields))
		{
			foreach ($this->facetFields as $fieldKey) {
				$facetFieldString .= 'facet.field='.$fieldKey.'&';
			}
		}
		if (!empty($facetFieldString)) {
			$facetFieldString = trim($facetFieldString,'&');
		}
		return $facetFieldString;
	}
	/**
	 * Convert rangeFields from array to param string
	 * @return string
	 */
	protected function convertRangeFieldsToString()
	{
		$rangeFieldString = '';

		if (is_array($this->rangeFields) && !empty($this->rangeFields))
		{
			foreach ($this->rangeFields as $fieldItem)
			{
				$rangeFieldString .= '&facet.range='.$fieldItem;
				$rangeFieldString .= '&f.'.$fieldItem.'.facet.range.start=0';
				$rangeFieldString .= '&f.'.$fieldItem.'.facet.range.end=1000000';
				$rangeFieldString .= '&f.'.$fieldItem.'.facet.range.gap=100';
				$rangeFieldString .= '&f.'.$fieldItem.'.facet.mincount=1';
			}
		}
		if (!empty($rangeFieldString)) {
			$rangeFieldString = trim($rangeFieldString,'&');
		}
		return $rangeFieldString;
	}

	static public function getPriceFieldPrefix($currencyCode = 'USD', $customerGroupId = 0)
	{
		if (!$customerGroupId) {
			$customerGroupId= '0';
		}
		$code = $currencyCode.'_'.(string)$customerGroupId;
		return $code;
	}

	static public function getPriceFieldsName($currencyCode = 'USD', $customerGroupId = 0)
	{
		$code = self::getPriceFieldPrefix($currencyCode, $customerGroupId);

		return array(
			$code.'_price_decimal',
			$code.'_special_price_decimal',
			$code.'_special_price_fromdate_int',
			$code.'_special_price_todate_int',
		);
	}

	/**
	 * Get config value by key
	 * @param string $key
	 * @return string
	 */
	public function getConfigValue($key)
	{
		$storeid = $this->getParam('storeid');
		$value = '';
		if (isset($this->config['stores'][$storeid]['settings'][$key]) && !empty($this->config['stores'][$storeid]['settings'][$key]))
		{
			$value = $this->config['stores'][$storeid]['settings'][$key];
		}
		return $value;
	}

	public function getPriceFields()
	{
		$storeid = $this->getParam('storeid');
		$customerGroupId = $this->getParam('customergroupid');
		$currencyCode = $this->getCurrencyCode();
		return self::getPriceFieldsName($currencyCode, $customerGroupId);
	}

	public function getCurrencyCode()
	{
		$currencyCode = 'USD';
		$storeid = $this->getParam('storeid');

		if (isset($this->config['stores'][$storeid]['currencycode']) && !empty($this->config['stores'][$storeid]['currencycode']))
		{
			$currencyCode = $this->config['stores'][$storeid]['currencycode'];
		}

		$code = $this->getParam('currencycode');
		if (!empty($code)) {
			$currencyCode = trim($code);
		}

		return $currencyCode;
	}

	public function prepareQueryData()
	{
		$this->prepareFacetAndBoostFields();
		return $this;
	}

	/**
	 * Request Solr Server by CURL
	 * @param string $url
	 * @param mixed $postFields
	 * @param string $type
	 * @return array
	 */
	public function doRequest($url, $postFields = null, $type='array'){

		$sh = curl_init($url);
		curl_setopt($sh, CURLOPT_HEADER, 0);
		if(is_array($postFields)) {
			curl_setopt($sh, CURLOPT_POST, true);
			curl_setopt($sh, CURLOPT_POSTFIELDS, $postFields);
		}
		curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);

		if ($type == 'json') {
			curl_setopt( $sh, CURLOPT_HEADER, true );
		}
		/*
		if (isset(self::getParam('user_agent')) || isset($_SERVER['HTTP_USER_AGENT'])) {
			curl_setopt( $sh, CURLOPT_USERAGENT, isset(self::getParam('user_agent')) ? self::getParam('user_agent') : $_SERVER['HTTP_USER_AGENT'] );
		}
		*/

		$this->setupSolrAuthenticate($sh);

		if ($type == 'json') {
			list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec($sh), 2 );
			$output = preg_split( '/[\r\n]+/', $contents );
		}else{
			$output = curl_exec($sh);
			$output = json_decode($output,true);
		}

		curl_close($sh);
		return $output;
	}

	/**
	 * Setup Solr authentication user/pass if neccessary
	 * @param resource $sh
	 */
	public function setupSolrAuthenticate(&$sh)
	{
		$isAuthentication = 0;
		$authUser = '';
		$authPass = '';

		$isAuthentication = $this->getConfigValue('solr_server_url_auth');
		$authUser = $this->getConfigValue('solr_server_url_auth_username');
		$authPass = $this->getConfigValue('solr_server_url_auth_password');

		if (isset($isAuthentication) && $isAuthentication > 0 ) {
			curl_setopt($sh, CURLOPT_USERPWD, $authUser.':'.$authPass);
			curl_setopt($sh, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
	}
	static public function cleanUpCategoryFacet(&$response)
	{
		if (isset($response['facet_counts']['facet_fields']['category_path']) && is_array($response['facet_counts']['facet_fields']['category_path']))
		{
			$cleanCategoryPaths = array();
			foreach ($response['facet_counts']['facet_fields']['category_path'] as $path => $count)
			{
	
				$categoryPathArray = self::pathToArray($path);
				$cleanCategoryPaths[@implode('/', $categoryPathArray)] = $count;
			}
			$response['facet_counts']['facet_fields']['category_path'] = $cleanCategoryPaths;
		}
	}
	
	/**
	 * Convert string path to array
	 * @param string $path
	 * @return array
	 */
	static public function pathToArray($path)
	{
		$chunks = explode('/', $path);
		$result = array();
		for ($i = 0; $i < sizeof($chunks) - 1; $i+=2)
		{
			//CatId format x:y, x is category id, y is position
			$catIdArr = explode(':', $chunks[($i+1)]);
			//$result[] = array('id' => $catIdArr[0], 'position' => $catIdArr[1], 'name' => $chunks[$i]);
			$result[] = $chunks[$i];
			$result[] = $catIdArr[0];
		}
		return $result;
	}
}