<?php
class Foundation_CustomizableFraudFilters_Adminhtml_Model_System_Config_Source_Shipping
{
  public function toOptionArray() {
    $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
    $options = array();
    foreach($methods as $_ccode => $_carrier) {
        $_methodOptions = array();
        if($_methods = $_carrier->getAllowedMethods()) {
          foreach($_methods as $_mcode => $_method) {
            $_code = $_ccode . '_' . $_mcode;
                $_methodOptions[] = array('value' => $_code, 'label' => $_method);
          }
            if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                $_title = $_ccode;
            $options[] = array('value' => $_methodOptions, 'label' => $_title, 'code' => $_code);
        }
    }
    $optionsArray = array();
    foreach ($options as $option){
      array_push($optionsArray, array('value' => $option['code'], 'label'=>Mage::helper('adminhtml')->__($option['label'])));
    }
    return $optionsArray;
  }
}