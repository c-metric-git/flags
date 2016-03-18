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
class SolrBridge_Solrsearch_Block_Faces_Price extends Mage_Core_Block_Template
{
	public function __construct()
	{
		$this->setTemplate('solrsearch/standard/searchfaces/price.phtml');
	}


	public function _prepareLayout()
    {
		//Load js for price slider
		$usePriceSilder = (int)Mage::helper('solrsearch')->getSetting('use_price_slider');
		if ($usePriceSilder > 0) {
			$this->setTemplate('solrsearch/standard/searchfaces/price-slider.phtml');
			$head = $this->getLayout()->getBlock('head');
			$head->addJs('solrsearch/slider.js');
		}

		return parent::_prepareLayout();
	}

	public function getSolrData(){
		$solrData = $this->getParentBlock()->getSolrData();
		
		$priceFieldName = Mage::helper('solrsearch')->getPriceFieldName();
		//Update min value for special price
		$priceFields = Mage::helper('solrsearch')->getPriceFields();
		if ( isset($priceFields[1]) )
		{
			$specialPriceFieldName = $priceFields[1];
			if (isset($solrData['stats']['stats_fields']['sort_'.$specialPriceFieldName]['min'])) {
				$solrData['stats']['stats_fields'][$priceFieldName]['min'] = $solrData['stats']['stats_fields']['sort_'.$specialPriceFieldName]['min'];
			}
		}
		
		return $solrData;
	}

	/**
	 * Calculate price ranges
	 * @param array $priceRanges
	 * @param decimal $min
	 * @param decimal $max
	 * @return array:
	 */
	protected function calculatePriceRanges()
	{

		$solrData = $this->getSolrData();

		$priceFieldName = Mage::helper('solrsearch')->getPriceFieldName();

		$priceRanges = array();

		if ( isset($solrData['facet_counts']['facet_ranges'][$priceFieldName]['counts']) && is_array($solrData['facet_counts']['facet_ranges'][$priceFieldName]['counts'])) {
			$priceRanges = $solrData['facet_counts']['facet_ranges'][$priceFieldName]['counts'];
		}

		$min = 0.0;
		if (isset($solrData['stats']['stats_fields'][$priceFieldName]['min'])) {
			$min = $solrData['stats']['stats_fields'][$priceFieldName]['min'];
		}

		$max = 0.0;
		if (isset($solrData['stats']['stats_fields'][$priceFieldName]['max'])) {
			$max = $solrData['stats']['stats_fields'][$priceFieldName]['max'];
		}

		$tempPriceRanges = array();
		$tempPriceRanges[] = $min;
		if (is_array($priceRanges)) {
			$index = 0;
			foreach ($priceRanges as $key=>$value) {
				if ($index > 0) {
					$tempPriceRanges[] = $key;
				}
				$index++;
			}
		}
		//$tempPriceRanges[] = $max;

		$returnPriceRanges = array();
		$index = 0;
		foreach ($tempPriceRanges as $item) {
			$start = $item;
			$end = $item;

			if (isset($tempPriceRanges[($index + 1)])) {
				$end = ($tempPriceRanges[($index + 1)] - 1);
				if (($index + 1) == (count($priceRanges) - 1)) {
					$end = $max;
				}
			}
			if ($index < (count($tempPriceRanges) - 1)) {
				$returnPriceRanges[] = array('start' => $start, 'end' => $end);
			}

			$index++;
		}
		return $returnPriceRanges;
	}

	protected function applyPriceRangeProductCount(){

		$priceFieldName = Mage::helper('solrsearch')->getPriceFieldName();

		$priceRanges = $this->calculatePriceRanges();

		$appliedPriceRanges = array();
		$solrData = $this->getSolrData();
		$priceFacets = array();

		if ( isset($solrData['facet_counts']['facet_fields'][$priceFieldName]) && is_array($solrData['facet_counts']['facet_fields'][$priceFieldName])) {
			$priceFacets = $solrData['facet_counts']['facet_fields'][$priceFieldName];
		}

		$currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();

		$currencyPositionSetting = $this->helper('solrsearch')->getSetting('currency_position');

		foreach ($priceRanges as $range) {
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
			foreach ($priceFacets as $price => $count) {
				$price = floor($price);
				if (floatval($price) >= floatval($start) && floatval($price) <= floatval($end)) {
					$rangeItemArray['count'] = ($rangeItemArray['count'] + $count);
				}
			}

			$appliedPriceRanges[] = $rangeItemArray;
		}

		return $appliedPriceRanges;
	}

	public function getPriceFormat($price)
	{
		$formattedPrice = $price;
		$currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$currencyPositionSetting = Mage::helper('solrsearch')->getSetting('currency_position');

		if ($currencyPositionSetting < 1) {
			//After
			$formattedPrice = $price.'&nbsp;'.$currencySign;
		}else{
			$formattedPrice = $currencySign.'&nbsp;'.$price;
		}
		return $formattedPrice;
	}

	public function getFacetPriceRanges()
	{
		return $this->applyPriceRangeProductCount();
	}

	public function getFacesUrl($params = array())
	{
		return $this->getParentBlock()->getFacesUrl($params);
	}
}