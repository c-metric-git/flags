<?php
/**
 * @category SolrBridge
 * @package SolrBridge_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2014 Solr Bridge (http://www.solrbridge.com)
 *
 */
class SolrBridge_Solrsearch_Block_Adminhtml_Logs extends Mage_Core_Block_Template {
	protected $ultility = null;
	public function __construct() {
		$this->ultility = Mage::getModel ( 'solrsearch/ultility' );
		$this->setTemplate ( 'solrsearch/logs.phtml' );
	}
	public function getLogs() {
		$resource = Mage::getSingleton ( 'core/resource' );
		
		$logtable = $this->ultility->getLogTable ();
		
		$query = 'SELECT * FROM ' . $logtable . ' WHERE 1 ORDER BY `update_at` DESC LIMIT 1000';
		
		$result = $this->ultility->getReadConnection ()->query ( $query );
		return $result->fetchAll ();
	}
	function time_elapsed_string($date) {
		if (empty ( $date )) {
			
			return "No date provided";
		}
		
		$periods = array (
				"second",
				"minute",
				"hour",
				"day",
				"week",
				"month",
				"year",
				"decade" 
		);
		
		$lengths = array (
				"60",
				"60",
				"24",
				"7",
				"4.35",
				"12",
				"10" 
		);
		
		$now = Mage::getModel('core/date')->timestamp(time());
		
		$unix_date = Mage::getModel('core/date')->timestamp(strtotime ( $date ));
		
		// check validity of date
		
		if (empty ( $unix_date )) {
			
			return "Bad date";
		}
		
		// is it future date or past date
		
		if ($now > $unix_date) {
			
			$difference = $now - $unix_date;
			
			$tense = "ago";
		} else {
			
			$difference = $unix_date - $now;
			$tense = "from now";
		}
		
		for($j = 0; $difference >= $lengths [$j] && $j < count ( $lengths ) - 1; $j ++) {
			
			$difference /= $lengths [$j];
		}
		
		$difference = round ( $difference );
		
		if ($difference != 1) {
			
			$periods [$j] .= "s";
		}
		
		return "$difference $periods[$j] {$tense}";
	}
}