<?php

class Wizkunde_ConfigurableBundle_Model_Bundle_Mysql4_Option_Collection extends Mage_Bundle_Model_Mysql4_Option_Collection
{


	/**
	 * Append selection to options
	 * stripBefore - indicates to reload
	 * appendAll - indecates do we need to filter by saleable and required custom options
	 *
	 * @param Mage_Bundle_Model_Mysql4_Selection_Collection $selectionsCollection
	 * @param bool $stripBefore
	 * @param bool $appendAll
	 * @return array
	 */
	public function appendSelections($selectionsCollection, $stripBefore = false, $appendAll = true)
	{
		if ($stripBefore) {
			$this->_stripSelections();
		}

		if (!$this->_selectionsAppended) {
			foreach ($selectionsCollection->getItems() as $key=>$_selection) {
				if ($_option = $this->getItemById($_selection->getOptionId())) {
					if ((!$appendAll && $_selection->isSalable()) || $appendAll) {
						$_selection->setOption($_option);
						$_option->addSelection($_selection);
					} else {
						$selectionsCollection->removeItemByKey($key);
					}
				}
			}
			$this->_selectionsAppended = true;
		}
		return $this->getItems();
	}

	/**
	 * Get selection options
	 *
	 * @param Mage_Bundle_Model_Mysql4_Selection_Collection $selectionsCollection
	 * @param bool $stripBefore
	 * @param bool $appendAll
	 * @return array
	 */
	public function getConfSelections($selectionsCollection)
	{
		$products = array();

		foreach ($selectionsCollection->getItems() as $key=>$_selection) {
			if ($_option = $this->getItemById($_selection->getOptionId())) {
				$products[intval($_selection->getOptionId())][] = $_selection;
			}
		}

		return $products;
	}


}