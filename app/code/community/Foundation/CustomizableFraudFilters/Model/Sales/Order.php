<?php
class Foundation_CustomizableFraudFilters_Model_Sales_Order extends Mage_Sales_Model_Order {
  public function getStatusLabel() {
    $status = $this->getConfig()->getStatusLabel($this->getStatus());
    if($status == "Manual Review Required"){
      $replaceStatus = Mage::getStoreConfig('customizablefraudfilters/general_settings/frontend_status');
      if($replaceStatus != ""){
        $status = $replaceStatus;
      }
    }
    return $status;
  }
}
