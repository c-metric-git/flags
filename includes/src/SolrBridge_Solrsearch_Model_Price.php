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
class SolrBridge_Solrsearch_Model_Price
{
	/**
	 * Get product price
	 * @param Mage_Catalog_Model_Product $_product
	 * @param int $storeId
	 * @return decimal
	 */
	public function getProductPrice($product, $store, $customerGroupId = 0)
	{
		$oldStore = Mage::app ()->getStore ();

		Mage::app ()->setCurrentStore ( $store );

		$_product = $product;

		$priceIncTax = 0;
		$priceExcTax = 0;
		$specialPriceIncTax = 0;
		$specialPriceExcTax = 0;

		$_coreHelper = Mage::helper('core');
		$_weeeHelper = Mage::helper('weee');
		$_taxHelper  = Mage::helper('tax');


		//Bundle product price
		if($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
			$_minimalBundlePrice = $this->getProductPriceBundle($_product, $store);
			$minimalPrice = $this->currencyByStore($_minimalBundlePrice, $store, false, false);
			$finalPrice = $_product->getFinalPrice();
			$finalPrice = $this->currencyByStore($finalPrice, $store, false, false);


			$priceData = array(
					'price' => $finalPrice?$finalPrice:$minimalPrice,
					'special_price' => $specialPriceIncTax?$specialPriceIncTax:$specialPriceExcTax,
					'product' => $_product,
					'product_id' => $_product->getId(),
					'customer_group_id' => $customerGroupId,
					'store' => $store,
					'website_id' => $store->getWebsiteId()
			);

			Mage::app ()->setCurrentStore ( $oldStore );

			return $this->calculateSpecialEarliestEndDate($priceData);
		}


		$_storeId = $store->getId();
		$_id = $_product->getId();
		$_weeeSeparator = '';
		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
		$_minimalPriceValue = $_product->getMinimalPrice();
		$_minimalPrice = $_taxHelper->getPrice($_product, $_minimalPriceValue, $_simplePricesTax);

		if (!$_product->isGrouped()){
			$_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($_product);
			if ($_weeeHelper->typeOfDisplay($_product, array(Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL, 4)))
			{
				$_weeeTaxAmount = $_weeeHelper->getAmount($_product);
				$_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForDisplay($_product);
			}
			$_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
			if ($_weeeHelper->isTaxable() && !$_taxHelper->priceIncludesTax($_storeId)){
				$_attributes = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null, null, true);
				$_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_attributes);
			}

			$_price = $_taxHelper->getPrice($_product, $_product->getPrice());
			$_regularPrice = $_taxHelper->getPrice($_product, $_product->getPrice(), $_simplePricesTax);
			$_finalPrice = $_taxHelper->getPrice($_product, $_product->getFinalPrice());
			$_finalPriceInclTax = $_taxHelper->getPrice($_product, $_product->getFinalPrice(), true);
			$_weeeDisplayType = $_weeeHelper->getPriceDisplayType();
			if ($_finalPrice >= $_price){
				//DISPLAY BOTH PRICE INC & EXC TAX
				if ($_taxHelper->displayBothPrices()){
					if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)){ // including
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
						//Including tax
						$priceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
					}
					elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) // incl. + weee
					{
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
						//Including tax
						$priceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
					}
					elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)) // incl. + weee
					{
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
						//Including tax
						$priceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
					}
					elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) // excl. + weee + final
					{
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price, $store, false, false);
						//Including tax
						$priceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
					}
					else
					{
						//Excluding tax
						if ($_finalPrice == $_price)
						{
							$priceExcTax = $this->currencyByStore($_price, $store, false, false);
						}
						else
						{
							$priceExcTax = $this->currencyByStore($_finalPrice, $store, false, false);
						}
						//Including tax
						$priceIncTax = $this->currencyByStore($_finalPriceInclTax, $store, false, false);
					}
				}
				else //DISPLAY ONLY EXCLUDING TAX
				{
					if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)){ // including
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
					}elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)){ // incl. + weee
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
					}elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)){ // incl. + weee
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
					}elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)){ // excl. + weee + final
						//Excluding tax
						$priceExcTax = $this->currencyByStore($_price + $_weeeTaxAmount, $store, false, false);
					}else{
						//Excluding tax
						if ($_finalPrice == $_price){
							$priceExcTax = $this->currencyByStore($_price, $store, false, false);
						}else{
							$priceExcTax = $this->currencyByStore($_finalPrice, $store, false, false);
						}
					}
				}
			}else{ /* if ($_finalPrice == $_price): */
				$_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);

				if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)){ // including
					//Regular price excluding tax
					$priceExcTax = $this->currencyByStore($_regularPrice + $_originalWeeeTaxAmount, $store, false, false);

					if ($_taxHelper->displayBothPrices()){
						//Special price excluding tax
						$specialPriceExcTax = $this->currencyByStore($_finalPrice + $_weeeTaxAmount, $store, false, false);
						//Special price including tax
						$specialPriceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
					}else{
						//Special price excluding tax
						echo $this->currencyByStore($_finalPrice + $_weeeTaxAmountInclTaxes, $store, false, false);
					}
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) // incl. + weee
				{
					//Regular price excluding tax
					$priceExcTax = $this->currencyByStore($_regularPrice + $_originalWeeeTaxAmount, $store, false, false);
					//Special price excluding tax
					$specialPriceExcTax = $this->currencyByStore($_finalPrice + $_weeeTaxAmount, $store, false, false);
					//Special price including tax
					$specialPriceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)) // incl. + weee
				{
					//Regular price excluding tax
					$priceExcTax = $this->currencyByStore($_regularPrice + $_originalWeeeTaxAmount, $store, false, false);
					//Special price excluding tax
					$specialPriceExcTax = $this->currencyByStore($_finalPrice + $_weeeTaxAmount, $store, false, false);
					//Special price including tax
					$specialPriceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) // excl. + weee + final
				{
					//Regular price excluding tax
					$priceExcTax = $this->currencyByStore($_regularPrice, $store, false, false);
					//Special price excluding tax
					$specialPriceExcTax = $this->currencyByStore($_finalPrice, $store, false, false);

					//Special price including tax
					$specialPriceIncTax = $this->currencyByStore($_finalPriceInclTax + $_weeeTaxAmountInclTaxes, $store, false, false);

				}else{ // excl.
					//Regular price excluding tax
					$priceExcTax = $this->currencyByStore($_regularPrice, $store, false, false);

					if ($_taxHelper->displayBothPrices()){
						//Special price excluding tax
						$specialPriceExcTax = $this->currencyByStore($_finalPrice, $store, false, false);
						//Special price including tax
						$specialPriceIncTax = $this->currencyByStore($_finalPriceInclTax, $store, false, false);
					}else{
						//Special price excluding tax
						$specialPriceExcTax = $this->currencyByStore($_finalPrice, $store, false, false);
					}
				}

			} /* if ($_finalPrice == $_price): */
		}else{ /* if (!$_product->isGrouped()): */
			$_exclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue);
			$_inclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue, true);

			if ($this->getDisplayMinimalPrice() && $_minimalPriceValue){
				$priceIncTax = $_inclTax;
				$priceExcTax = $_exclTax;
			} /* if ($this->getDisplayMinimalPrice() && $_minimalPrice): */
		} /* if (!$_product->isGrouped()): */

		$priceData = array(
				'price' => $priceIncTax?$priceIncTax:$priceExcTax,
				'special_price' => $specialPriceIncTax?$specialPriceIncTax:$specialPriceExcTax,
				'product' => $_product,
				'product_id' => $_product->getId(),
				'customer_group_id' => $customerGroupId,
				'store' => $store,
				'website_id' => $store->getWebsiteId()
		);

		Mage::app ()->setCurrentStore ( $oldStore );

		return $this->calculateSpecialEarliestEndDate($priceData);
	}

	public function calculateSpecialEarliestEndDate($data)
	{
		$specialPrice = $data['special_price'];

		$specialPriceFromDate = 0;

		$specialPriceToDate = 0;

		if ($specialPrice > 0 && isset($data['product']) && is_object($data['product']))
		{
			$specialPriceFromDate = $data['product']->getSpecialFromDate();
			$specialPriceToDate = $data['product']->getSpecialToDate();
		}

		if ($specialPriceToDate < 1) {
			$resource = Mage::getSingleton('core/resource');
			$connection = $resource->getConnection('core_read');
			$table_rule_product = $resource->getTableName('catalogrule/rule_product');
			$table_rule = $resource->getTableName('catalogrule/rule');


			$select = $connection->select()
			->from(array('rp' => $table_rule_product), array('max_to_time'=>'MAX(rp.to_time)', 'min_from_time'=>'MIN(rp.from_time)'))
			->join(
					array('cr' => $table_rule),
					'rp.rule_id = cr.rule_id',
					array('rule_active' => 'cr.is_active')
			)
			->where('rp.product_id=?',$data['product_id'])
			->where('rp.customer_group_id=?',$data['customer_group_id'])
			->where('rp.website_id=?',$data['website_id'])
			->where('rp.to_time >= ?',Mage::app()->getLocale()->storeTimeStamp($data['store']->getId()))
			->where('cr.is_active=?',1);

			$result = $connection->fetchRow($select);

			if (isset($result['max_to_time']) && intval($result['max_to_time']) > 0) {
				$specialPriceToDate = $result['max_to_time'];
			}
			if (isset($result['min_from_time']) && intval($result['min_from_time']) > 0) {
				$specialPriceFromDate = $result['min_from_time'];
			}
		}
		$data['special_price_from_time'] = $specialPriceFromDate;
		$data['special_price_to_time'] = $specialPriceToDate;
		return $data;
	}

	public function getDisplayMinimalPrice()
	{
		return true;
	}

	/**
	 * Convert and format price value for specified store
	 *
	 * @param   float $value
	 * @param   int|Mage_Core_Model_Store $store
	 * @param   bool $format
	 * @param   bool $includeContainer
	 * @return  mixed
	 */
	public function currencyByStore($value, $store = null, $format = true, $includeContainer = true)
	{
		$_coreHelper = Mage::helper('core');
		if (method_exists($_coreHelper,'currencyByStore'))
		{
			return $_coreHelper->currencyByStore($value, $store, $format, $includeContainer);
		}

		try {
			if (!($store instanceof Mage_Core_Model_Store)) {
				$store = Mage::app()->getStore($store);
			}

			$value = $store->convertPrice($value, $format, $includeContainer);
		}
		catch (Exception $e){
			$value = $e->getMessage();
		}

		return $value;
	}

	/**
	 * Get bundle product price
	 * @param Mage_Catalog_Model_Product $_product
	 * @param Mage_Core_Model_Store $currentstore
	 * @return decimal
	 */
	public function getProductPriceBundle($_product, $currentstore){
		$_priceModel  = $_product->getPriceModel();
		list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getTotalPrices($_product, null, null, false);
		list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getTotalPrices($_product, null, true, false);
		return $_minimalPriceInclTax;
	}
}