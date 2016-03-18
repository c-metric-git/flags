<?php
/***************************************************************************
	@extension	: Bestseller Product.
	@copyright	: Copyright (c) 2015 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/
class CapacityWebSolutions_Bestseller_Block_Adminhtml_Bestseller_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('bestseller_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Bestseller Information');
    }
    
	protected function _beforeToHtml()
    {
	  	$this->addTab('product_section', array(
            'label'     => Mage::helper('bestseller')->__('Products'),
            'title'     => Mage::helper('bestseller')->__('Products'),
            'content'   => $this->getLayout()->createBlock('bestseller/adminhtml_bestseller_edit_tab_products')->toHtml(),
        ));
			
		return parent::_beforeToHtml();
    }
}