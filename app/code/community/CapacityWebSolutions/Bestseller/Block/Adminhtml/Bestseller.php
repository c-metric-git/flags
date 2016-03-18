<?php
/***************************************************************************
	@extension	: Bestseller Product.
	@copyright	: Copyright (c) 2015 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/
class CapacityWebSolutions_Bestseller_Block_Adminhtml_Bestseller extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_bestseller';
		$this->_blockGroup = 'bestseller';
		$this->_headerText = Mage::helper('bestseller')->__('Manage Products Manually');
		$this->_addButtonLabel = Mage::helper('bestseller')->__('Select Products');
		parent::__construct();
	}
}