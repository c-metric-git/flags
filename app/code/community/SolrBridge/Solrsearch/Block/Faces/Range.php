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
class SolrBridge_Solrsearch_Block_Faces_Range extends Mage_Core_Block_Template
{
	protected $_solrData = null;

	protected $_solrModel = null;

	protected $_rangeField = null;

	public function __construct()
	{
		$this->setTemplate('solrsearch/standard/searchfaces/range.phtml');
	}

	public function setRangeField($fieldName){
		$this->_rangeField = $fieldName;
	}

	public function getRangeField(){
		return $this->_rangeField;
	}

	public function getSolrData(){
		//return $this->getParentBlock()->getSolrData();
		$solrModel = Mage::registry('solrbridge_loaded_solr');

		if ($solrModel) {
			$this->_solrModel = $solrModel;
			$this->_solrData = $this->_solrModel->getSolrData();
		}
		else
		{
			$this->_solrModel = Mage::getModel('solrsearch/solr');
			$queryText = Mage::helper('solrsearch')->getParam('q');
			$this->_solrData = $this->_solrModel->query($queryText);
		}

		return $this->_solrData;
	}

	public function getFacetLabel($key){
		return $this->getParentBlock()->getFacetLabel($key);
	}

	/**
	 * Calculate price ranges
	 * @param array $fieldRanges
	 * @param decimal $min
	 * @param decimal $max
	 * @return array:
	 */
	protected function calculateRanges()
	{

		$solrData = $this->getSolrData();

		$fieldName = $this->getRangeField();

		$fieldRanges = array();

		if ( isset($solrData['facet_counts']['facet_ranges'][$fieldName]['counts']) && is_array($solrData['facet_counts']['facet_ranges'][$fieldName]['counts'])) {
			$fieldRanges = $solrData['facet_counts']['facet_ranges'][$fieldName]['counts'];
		}

		$min = 0.0;
		if (isset($solrData['stats']['stats_fields'][$fieldName]['min'])) {
			$min = $solrData['stats']['stats_fields'][$fieldName]['min'];
		}

		$max = 0.0;
		if (isset($solrData['stats']['stats_fields'][$fieldName]['max'])) {
			$max = $solrData['stats']['stats_fields'][$fieldName]['max'];
		}

		$tempFieldRanges = array();
		$tempFieldRanges[] = $min;
		if (is_array($fieldRanges)) {
			$index = 0;
			foreach ($fieldRanges as $key=>$value) {
				if ($index > 0) {
					$tempFieldRanges[] = $key;
				}
				$index++;
			}
		}
		//$tempFieldRanges[] = $max;

		$returnFieldRanges = array();
		$index = 0;
		foreach ($tempFieldRanges as $item) {
			$start = $item;
			$end = $item;

			if (isset($tempFieldRanges[($index + 1)])) {
				$end = ($tempFieldRanges[($index + 1)] - 1);
				if (($index + 1) == (count($fieldRanges) - 1)) {
					$end = $max;
				}
			}
			if ($index < (count($tempFieldRanges) - 1)) {
				$returnFieldRanges[] = array('start' => $start, 'end' => $end);
			}

			$index++;
		}
		return $returnFieldRanges;
	}

	protected function applyRangeProductCount($fieldName){

		$fieldRanges = $this->calculateRanges();

		$appliedFieldRanges = array();
		$solrData = $this->getSolrData();
		$fieldFacets = array();

		if ( isset($solrData['facet_counts']['facet_fields'][$fieldName]) && is_array($solrData['facet_counts']['facet_fields'][$fieldName])) {
			$fieldFacets = $solrData['facet_counts']['facet_fields'][$fieldName];
		}

		$currencySign = '';

		$currencyPositionSetting = $this->helper('solrsearch')->getSetting('currency_position');

		foreach ($fieldRanges as $range) {
			$start = floor(floatval($range['start']));
			$end = ceil(floatval($range['end']));

			if ($currencyPositionSetting > 0)
			{
				$formatted = $currencySign.'&nbsp;'.$start.' - '.$currencySign.'&nbsp;'.$end;
			}else {
				$formatted = $start.'&nbsp;'.$currencySign.' - '.$end.'&nbsp;'.$currencySign;
			}

			$rangeItemArray = array(
					'start' => $start,
					'end' => $end,
					'count' => 0,
					'formatted' => $formatted,
					'value' => $start.' TO '.$end,
			);
			foreach ($fieldFacets as $rangeValue => $count) {
				$rangeValue = floor($rangeValue);
				if (floatval($rangeValue) >= floatval($start) && floatval($rangeValue) <= floatval($end)) {
					$rangeItemArray['count'] = ($rangeItemArray['count'] + $count);
				}
			}

			$appliedFieldRanges[] = $rangeItemArray;
		}

		return $appliedFieldRanges;
	}

	public function getFacetFieldRanges()
	{
		$fieldName = $this->getRangeField();
		return $this->applyRangeProductCount($fieldName);
	}

	public function getFacesUrl($params = array())
	{
		return $this->getParentBlock()->getFacesUrl($params);
	}
}