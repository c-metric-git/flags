<?php
class Foundation_CustomizableFraudFilters_Model_Observer {
  public function applyFilters(Varien_Event_Observer $observer) {
    $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

    //set flags
    $stateFlag = Mage::getStoreConfig('customizablefraudfilters/filters/state_match_flag');
    $cityFlag = Mage::getStoreConfig('customizablefraudfilters/filters/city_match_flag');
    $zipCodeFlag = Mage::getStoreConfig('customizablefraudfilters/filters/zip_code_match_flag');
    $countryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/country_match_flag');
    $guestFlag = Mage::getStoreConfig('customizablefraudfilters/filters/guest_flag');
    $grandTotalMaxFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
    $grandTotalMinFlag = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
    $orderContainsProductsFlag = Mage::getStoreConfig('customizablefraudfilters/filters/order_contains_products_flag');
    $shippingCountryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_country_flag');
    $billingCountryFlag = Mage::getStoreConfig('customizablefraudfilters/filters/billing_country_flag');
    $restrictedEmailFlag = Mage::getStoreConfig('customizablefraudfilters/filters/restricted_email_flag');
    $billingStreetContainsFlag = Mage::getStoreConfig('customizablefraudfilters/filters/billing_street_contains_flag');
    $shippingStreetContainsFlag = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_street_contains_flag');
    $shippingMethodFlag = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_method_flag');
    

    // Logging for all flags
    // Mage::log("&stateFlag: ".$stateFlag);
    // Mage::log("&cityFlag: ".$cityFlag);
    // Mage::log("&zipCodeFlag: ".$zipCodeFlag);
    // Mage::log("&countryFlag: ".$countryFlag);
    // Mage::log("&guestFlag: ".$guestFlag);
    // Mage::log("&grandTotalMaxFlag: ".$grandTotalMaxFlag);
    // Mage::log("&grandTotalMinFlag: ".$grandTotalMinFlag);
    // Mage::log("&orderContainsProductsFlag: ".$orderContainsProductsFlag); 
    // Mage::log("&shippingCountryFlag: ".$shippingCountryFlag); 
    // Mage::log("&billingCountryFlag: ".$billingCountryFlag);
    // Mage::log("&restrictedEmailFlag: ".$restrictedEmailFlag);
    // Mage::log("&billingStreetContainsFlag: ".$billingStreetContainsFlag);
    // Mage::log("&shippingStreetContainsFlag: ".$shippingStreetContainsFlag);
    // Mage::log("&shippingMethodFlag: ".$shippingMethodFlag);
    

    //begin filters
    if ($stateFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkState($order);
    }
    if ($cityFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCity($order);
    }
    if ($zipCodeFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkZipCode($order);
    }
    if ($countryFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkCountry($order);
    }
    if ($guestFlag == 1) {
      Mage::helper('customizablefraudfilters')->checkGuest($order);
    }
    if ($grandTotalMaxFlag != null && $grandTotalMaxFlag > 0) {
      $grandTotalMax = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_max_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMax($order, $grandTotalMax);
    } 
    if ($grandTotalMinFlag != null && $grandTotalMinFlag > 0) {
      $grandTotalMin = Mage::getStoreConfig('customizablefraudfilters/filters/grand_total_min_flag');
      Mage::helper('customizablefraudfilters')->checkGrandTotalMin($order, $grandTotalMin);
    } 
    if ($orderContainsProductsFlag != null) {
      Mage::helper('customizablefraudfilters')->checkOrderContainsProducts($order);
    }     
    if ($shippingCountryFlag != null) {
      Mage::helper('customizablefraudfilters')->checkShippingCountry($order);
    }   
    if ($billingCountryFlag != null) {
      Mage::helper('customizablefraudfilters')->checkBillingCountry($order);
    }   
    if ($restrictedEmailFlag != null) {
      Mage::helper('customizablefraudfilters')->checkRestrictedEmails($order);
    } 
    if ($billingStreetContainsFlag != null) {
      Mage::helper('customizablefraudfilters')->checkBillingStreetContains($order);
    } 
    if ($shippingStreetContainsFlag != null) {
      Mage::helper('customizablefraudfilters')->checkShippingStreetContains($order);
    } 
    if ($shippingMethodFlag != null) {
      Mage::helper('customizablefraudfilters')->checkShippingMethod($order);
    } 
  }
}