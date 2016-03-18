<?php
class Magestore_Sociallogin_Block_Adminhtml_Twlogin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_twlogin';
    $this->_blockGroup = 'sociallogin';
    $this->_headerText = Mage::helper('sociallogin')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('sociallogin')->__('Add Item');
    parent::__construct();
  }
}