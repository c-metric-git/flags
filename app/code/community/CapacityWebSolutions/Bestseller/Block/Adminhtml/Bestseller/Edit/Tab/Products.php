<?php
/***************************************************************************
	@extension	: Bestseller Product.
	@copyright	: Copyright (c) 2015 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/
class CapacityWebSolutions_Bestseller_Block_Adminhtml_Bestseller_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct() {
		parent::__construct();
		$this->setTemplate('bestseller/product.phtml');
	}

	protected function getProductIds() {
		$data = Mage::registry('bestseller_data');
		$prd_model = Mage::getModel('bestseller/bestseller')->getCollection();
		
		$_productList = array();
		
		foreach($prd_model as $prd_data){
			$_productList[] = $prd_data->getData('sku');
		} 
	
		return is_array($_productList) ? $_productList : array();
	}

	public function getIdsString() {
		return implode(', ', $this->getProductIds());
	}
	
}