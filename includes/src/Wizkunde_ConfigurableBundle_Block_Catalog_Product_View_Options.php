<?php
class Wizkunde_ConfigurableBundle_Block_Catalog_Product_View_Options extends Mage_Catalog_Block_Product_View_Options
{
	public function getOptionsForJson()
	{
		$ret = Array();
		$options = Array ();
		$bundles = Mage::registry('current_product');
		if (isset($bundles)) {
		foreach  ($bundles as $single) {
			$options[] =  $single->getOptions();
		}
		foreach ($options as $option) {
			$ret+=$option;
		}
		return $ret;
		}
		else {
			return $this->getProduct()->getOptions();
		}
	}

	public function getConfig()
	{
		$config = array();

		foreach ($this->getOptionsForJson() as $option) {
			/* @var $option Mage_Catalog_Model_Product_Option */
			$priceValue = 0;
			if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
				$_tmpPriceValues = array();
				foreach ($option->getValues() as $value) {
					/* @var $value Mage_Catalog_Model_Product_Option_Value */
				   $_tmpPriceValues[$value->getId()] = Mage::helper('core')->currency($value->getPrice(true), false, false);
				}
				$priceValue = $_tmpPriceValues;
			} else {
				$priceValue = Mage::helper('core')->currency($option->getPrice(true), false, false);
			}
			$config[$option->getId()] = $priceValue;
		}

        return $config;
	}

    public function getJsonConfig()
    {
        $configData = $this->getConfig();
        return Mage::helper('core')->jsonEncode($configData);
    }
}
