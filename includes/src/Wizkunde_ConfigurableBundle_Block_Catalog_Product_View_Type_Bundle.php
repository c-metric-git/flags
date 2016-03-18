<?php

class Wizkunde_ConfigurableBundle_Block_Catalog_Product_View_Type_Bundle extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle
{

	/**
	 * Validating of super product option value
	 *
	 * @param array $attribute
	 * @param array $value
	 * @param array $options
	 * @return boolean
	 */
	protected function _validateAttributeValue($attributeId, &$value, &$options)
	{
		if(isset($options[$attributeId][$value['value_index']])) {
			return true;
		}

		return false;
	}

	/**
	 * Validation of super product option
	 *
	 * @param array $info
	 * @return boolean
	 */
	protected function _validateAttributeInfo(&$info)
	{
		if(count($info['options']) > 0) {
			return true;
		}
		return false;
	}

	protected function _registerJsPrice($price)
	{
		$jsPrice = str_replace(',', '.', $price);
		return $jsPrice;
	}

	protected function _convertPrice($price, $round=false)
	{
		if (empty($price)) {
			return 0;
		}

		$price = Mage::app()->getStore()->convertPrice($price);
		if ($round) {
			$price = Mage::app()->getStore()->roundPrice($price);
		}


		return $price;
	}

	private function _configurableGetAllowAttributes($product)
	{
		return $product->getTypeInstance(true)
			->getConfigurableAttributes($product);
	}
	
	public function getBundleAssignedProductsIds()
	{
		$bundled_product = new Mage_Catalog_Model_Product();
		$bundled_product->load($this->getProduct()->getId());
		$selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection($bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product);
		$bundled_items = array();
		foreach($selectionCollection as $option)
		{
			$bundled_items[] = $option->product_id;
		}
		return $bundled_items;
	}

	public function getAllowProducts($product)
	{
		$products = array();
		$allProducts = $product->getTypeInstance(true)
			->getUsedProducts(null, $product);
		foreach ($allProducts as $prod) {
			if ($prod->isSaleable()) {
				$products[] = $prod;
			}
		}
		//remove products that are not in the bundle assigned product collection
		$assigned_products = $this->getBundleAssignedProductsIds();
		foreach($products as $key => $prod)
		{
			if(!in_array($prod->getId(), $assigned_products))
				unset($products[$key]);
		}
		return $products;
	}

	public function getAllowAttributes($pr)
	{
		return $pr->getTypeInstance(true)
			->getConfigurableAttributes($pr);
	}

	protected function _getConfigurableOptions($_selection)
	{

		if( $_selection->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
			return array();
		}

		$pr = Mage::getModel('catalog/product')->load($_selection->getProductId());

		$attributes = array();
		$options = array();
		$store = Mage::app()->getStore();
		foreach ($this->getAllowProducts($pr) as $product) {
			$productId  = $product->getId();

			if($pr->getId() == 383)
				Mage::log($productId, null, 'bp.log');

			foreach ($this->getAllowAttributes($pr) as $attribute) {

				$productAttribute = $attribute->getProductAttribute();

				$attributeValue = $product->getData($productAttribute->getAttributeCode());
				if (!isset($options[$pr->getId() . '_' . $productAttribute->getId()])) {
					$options[$pr->getId() . '_' . $productAttribute->getId()] = array();
				}

				if (!isset($options[$pr->getId() . '_' . $productAttribute->getId()][$attributeValue])) {
					$options[$pr->getId() . '_' . $productAttribute->getId()][$attributeValue] = array();
				}
				$options[$pr->getId() . '_' . $productAttribute->getId()][$attributeValue][] = $productId;
			}

		}

		if($pr->getId() == 383)
			Mage::log($options, null, 'bp.log');

		$this->_resPrices = array(
			$this->_preparePrice($pr->getFinalPrice(), false, $pr)
		);

		foreach ($this->getAllowAttributes($pr) as $attribute) {
			$productAttribute = $attribute->getProductAttribute();
			//$attributeId = $productAttribute->getId();
			$attributeId = $pr->getId() . '_' . $productAttribute->getId();

			$info = array(
			   'id'        => $productAttribute->getId(),
			   'code'      => $productAttribute->getAttributeCode(),
			   'label'     => $attribute->getLabel(),
			   'options'   => array()
			);

			$optionPrices = array();
			$prices = $attribute->getPrices();
			if (is_array($prices)) {
				foreach ($prices as $value) {
					if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
						continue;
					}

					$info['options'][] = array(
						'id'    => $value['value_index'],
						'label' => $value['label'],
						'price' => $this->_preparePrice($value['pricing_value'], $value['is_percent'], $pr),
						'products'   => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
					);
					$optionPrices[] = $this->_preparePrice($value['pricing_value'], $value['is_percent'], $pr);
					//$this->_registerAdditionalJsPrice($value['pricing_value'], $value['is_percent']);
				}
			}
			/**
			 * Prepare formated values for options choose
			 */
			foreach ($optionPrices as $optionPrice) {
				foreach ($optionPrices as $additional) {
					$this->_preparePrice(abs($additional-$optionPrice), false, $pr);
				}
			}
			if($this->_validateAttributeInfo($info)) {
			   $attributes[$attributeId] = $info;
			}
		}

		$_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
		$_request->setProductClassId($pr->getTaxClassId());
		$defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		$_request = Mage::getSingleton('tax/calculation')->getRateRequest();
		$_request->setProductClassId($pr->getTaxClassId());
		$currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		$taxConfig = array(
			'includeTax'        => Mage::helper('tax')->priceIncludesTax(),
			'showIncludeTax'    => Mage::helper('tax')->displayPriceIncludingTax(),
			'showBothPrices'    => Mage::helper('tax')->displayBothPrices(),
			'defaultTax'        => $defaultTax,
			'currentTax'        => $currentTax,
			'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax'),
		);

		$config = array(
			'attributes'        => $attributes,
			'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
			'basePrice'         => $this->_registerJsPrice($this->_convertPrice($pr->getFinalPrice())),
			'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($pr->getPrice())),
			// 'productId'         => $this->getProduct()->getId(),
			'productId'         => $pr->getId(),
			'chooseText'        => Mage::helper('catalog')->__('Choose option...'),
			'taxConfig'         => $taxConfig,
		);

		if($pr->getId() == 383)
			Mage::log($config, null, 'bp.log');

		return $config;
	}

	protected function _preparePrice($price, $isPercent=false, $product)
	{
		if ($isPercent && !empty($price)) {
			$price = $product->getFinalPrice()*$price/100;
		}

		return $this->_registerJsPrice($this->_convertPrice($price, true));
	}

    /**
     * Returns JSON encoded config to be used in JS scripts
     *
     * @return string
     */
    public function getJsonConfig()
    {
        Mage::app()->getLocale()->getJsPriceFormat();
        $optionsArray = $this->getOptions();
        $options      = array();
        $selected     = array();
        $currentProduct = $this->getProduct();
        /* @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper   = Mage::helper('core');
        /* @var $bundlePriceModel Mage_Bundle_Model_Product_Price */
        $bundlePriceModel = Mage::getModel('bundle/product_price');

        if ($preConfiguredFlag = $currentProduct->hasPreconfiguredValues()) {
            $preConfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($optionsArray as $_option) {
            /* @var $_option Mage_Bundle_Model_Option */
            if (!$_option->getSelections()) {
                continue;
            }

            $optionId = $_option->getId();
            $option = array (
                'selections' => array(),
                'title'      => $_option->getTitle(),
                'isMulti'    => in_array($_option->getType(), array('multi', 'checkbox'))
            );

            $selectionCount = count($_option->getSelections());

            foreach ($_option->getSelections() as $_selection) {
                /* @var $_selection Mage_Catalog_Model_Product */
                $selectionId = $_selection->getSelectionId();
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;
                // recalculate currency
                $tierPrices = $_selection->getTierPrice();
                foreach ($tierPrices as &$tierPriceInfo) {
                    $tierPriceInfo['price'] = $coreHelper->currency($tierPriceInfo['price'], false, false);
                }
                unset($tierPriceInfo); // break the reference with the last element

                $itemPrice = $bundlePriceModel->getSelectionFinalTotalPrice($currentProduct, $_selection,
                    $currentProduct->getQty(), $_selection->getQty(), false, false
                );

                $canApplyMAP = false;

                /* @var $taxHelper Mage_Tax_Helper_Data */
                $taxHelper = Mage::helper('tax');

                $_priceInclTax = $taxHelper->getPrice($_selection, $itemPrice, true);
                $_priceExclTax = $taxHelper->getPrice($_selection, $itemPrice);

                if ($currentProduct->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
                    $_priceInclTax = $taxHelper->getPrice($currentProduct, $itemPrice, true);
                    $_priceExclTax = $taxHelper->getPrice($currentProduct, $itemPrice);
                }

                $selection = array (
                    'qty'              => $_qty,
                    'customQty'        => $_selection->getSelectionCanChangeQty(),
                    'price'            => $coreHelper->currency($_selection->getFinalPrice(), false, false),
                    'priceInclTax'     => $coreHelper->currency($_priceInclTax, false, false),
                    'priceExclTax'     => $coreHelper->currency($_priceExclTax, false, false),
                    'priceValue'       => $coreHelper->currency($_selection->getSelectionPriceValue(), false, false),
                    'priceType'        => $_selection->getSelectionPriceType(),
                    'tierPrice'        => $tierPrices,
                    'name'             => $_selection->getName(),
                    'plusDisposition'  => 0,
                    'minusDisposition' => 0,
                    'canApplyMAP'      => $canApplyMAP
                );

                if($_selection->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
                    $selection ['confProductId'] = $_selection->getProductId();
                    $selection ['configurableOptions'][$_selection->getProductId() . '_' . $_selection->getSelectionId()]= $this->_getConfigurableOptions($_selection);
                    $selection ['confattributes'][$_selection->getProductId() . '_' . $_selection->getSelectionId()]= $this->getAllowAttributes(Mage::getModel('catalog/product')->load($_selection->getProductId()))->toArray();
                }

                $responseObject = new Varien_Object();
                $args = array('response_object' => $responseObject, 'selection' => $_selection);
                Mage::dispatchEvent('bundle_product_view_config', $args);
                if (is_array($responseObject->getAdditionalOptions())) {
                    foreach ($responseObject->getAdditionalOptions() as $o => $v) {
                        $selection[$o] = $v;
                    }
                }
                $option['selections'][$selectionId] = $selection;

                if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired()))
                    && $_selection->isSalable()
                ) {
                    $selected[$optionId][] = $selectionId;
                }
            }
            $options[$optionId] = $option;

            // Add attribute default value (if set)
            if ($preConfiguredFlag) {
                $configValue = $preConfiguredValues->getData('bundle_option/' . $optionId);
                if ($configValue) {
                    $defaultValues[$optionId] = $configValue;
                }
            }
        }

        $config = array(
            'options'       => $options,
            'selected'      => $selected,
            'bundleId'      => $currentProduct->getId(),
            'priceFormat'   => Mage::app()->getLocale()->getJsPriceFormat(),
            'basePrice'     => $coreHelper->currency($currentProduct->getPrice(), false, false),
            'priceType'     => $currentProduct->getPriceType(),
            'specialPrice'  => $currentProduct->getSpecialPrice(),
            'includeTax'    => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'isFixedPrice'  => $this->getProduct()->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED,
            'isMAPAppliedDirectly' => Mage::helper('catalog')->canApplyMsrp($this->getProduct(), null, false)
        );

        if ($preConfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        return $coreHelper->jsonEncode($config);
    }
}