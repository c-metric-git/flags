<?php
class Foundation_CustomizableFraudFilters_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function checkState($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingState = $billingAddress["region"];
    $shippingState = $shippingAddress["region"];
    if ($billingState != $shippingState) {
      $flagReason = "Shipping and billing state does not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }  

  public function checkCity($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingCity = $billingAddress["city"];
    $shippingCity = $shippingAddress["city"];
    if ($billingCity != $shippingCity) {
      $flagReason = "Shipping and billing city do not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkZipCode($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingZip = $billingAddress["postcode"];
    $shippingZip = $shippingAddress["postcode"];
    if ($billingZip != $shippingZip) {
      $flagReason = "Shipping and billing zip code do not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkCountry($order) {
    $billingAddress = $order->getBillingAddress();
    $shippingAddress = $order->getShippingAddress();
    $billingCountry = $billingAddress["country_id"];
    $shippingCountry = $shippingAddress["country_id"];
    if ($billingCountry != $shippingCountry) {
      $flagReason = "Shipping and billing country do not match.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkGuest($order) {
    if($order["customer_is_guest"] == 1) {
      $flagReason = "Order was placed from a guest (not logged in) account.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkGrandTotalMax($order, $grandTotalMax) {
    $grandTotal = $order["grand_total"];
    if ($grandTotal > $grandTotalMax) {
      $flagReason = "Grand total of this order ($".number_format($grandTotal,2).") exceeds the maximum grand total limit ($".$grandTotalMax.").";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkGrandTotalMin($order, $grandTotalMin) {
    $grandTotal = $order["grand_total"];
    if ($grandTotal < $grandTotalMin) {
      $flagReason = "Grand total of this order ($".number_format($grandTotal,2).") is less than the minimum grand total limit ($".$grandTotalMin.").";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }


  public function checkOrderContainsProducts($order) {
    $flaggedItems = "";
    $filterProducts = Mage::getStoreConfig('customizablefraudfilters/filters/order_contains_products_flag');
    $filterProducts = explode(",", $filterProducts);
    foreach ($filterProducts as &$filterProduct) {
      $filterProduct = trim($filterProduct);
      unset($filterProduct);
    }
    $items = $order->getAllItems();
    $itemcount = count($items);
    foreach ($items as $item){
      $itemId = $item->getProductId();
      if (in_array($itemId, $filterProducts)){
        $flaggedItems = $flaggedItems."Product ID: ".$itemId." - ".$item->getName()."<br/>";
      }
    }
    if($flaggedItems != ""){
      $flagReason = "The order contained the following items which have been flagged for manual review: <br/><br/>".$flaggedItems;
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }


  public function checkShippingCountry($order) {
    $filterCountries = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_country_flag');
    $filterCountries = explode(",", $filterCountries);
    foreach ($filterCountries as &$filterCountry) {
      $filterCountry = trim($filterCountry);
      unset($filterCountry);
    }
    $shippingAddress = $order->getShippingAddress();
    $shippingCountry = $shippingAddress["country_id"];
    if(in_array($shippingCountry, $filterCountries)) {
      $flagReason = "The shipping country for this order (".$shippingCountry.") is on the filter list.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }


  public function checkBillingCountry($order) {
    $filterCountries = Mage::getStoreConfig('customizablefraudfilters/filters/billing_country_flag');
    $filterCountries = explode(",", $filterCountries);
    foreach ($filterCountries as &$filterCountry) {
      $filterCountry = trim($filterCountry);
      unset($filterCountry);
    }
    $billingAddress = $order->getBillingAddress();
    $billingCountry = $billingAddress["country_id"];
    if(in_array($billingCountry, $filterCountries)) {
      $flagReason = "The billing country for this order (".$billingCountry.") is on the filter list.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkShippingMethod($order) {
    $filterMethods = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_method_flag');
    $filterMethods = explode(",", $filterMethods);
    $orderShippingMethod = $order['shipping_method'];

    if(in_array($orderShippingMethod, $filterMethods)) {
      $flagReason = "The shipping method for this order (".$orderShippingMethod.") is on the filter list.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }


  public function checkBillingStreetContains($order) {
    $billingAddress = $order->getBillingAddress();
    $billingStreet = $billingAddress["street"];

    $filterStrings = Mage::getStoreConfig('customizablefraudfilters/filters/billing_street_contains_flag');
    $filterStrings = str_getcsv($filterStrings,',','"');
    foreach ($filterStrings as &$filterString) {
      $filterString = trim($filterString);
      if(stripos($billingStreet, $filterString) !== false){
        $flagReason = "The billing street address for this order contains a filtered phrase ('".$filterString."').";
        Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
      }
      unset($filterString);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }


  public function checkShippingStreetContains($order) {
    $shippingAddress = $order->getShippingAddress();
    $shippingStreet = $shippingAddress["street"];

    $filterStrings = Mage::getStoreConfig('customizablefraudfilters/filters/shipping_street_contains_flag');
    $filterStrings = str_getcsv($filterStrings,',','"');
    foreach ($filterStrings as &$filterString) {
      $filterString = trim($filterString);
      if(stripos($shippingStreet, $filterString) !== false){
        $flagReason = "The shipping street address for this order contains a filtered phrase ('".$filterString."').";
        Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
      }
      unset($filterString);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function checkRestrictedEmails($order) {
    $filterEmails = Mage::getStoreConfig('customizablefraudfilters/filters/restricted_email_flag');
    $filterEmails = explode(",", $filterEmails);
    foreach ($filterEmails as &$filterEmail) {
      $filterEmail = trim($filterEmail);
      unset($filterEmail);
    }
    $customerEmail = $order->getCustomerEmail();
    if(in_array($customerEmail, $filterEmails)) {
      $flagReason = "The customer email address used to place this order (".$customerEmail.") is on the filter list.";
      Mage::helper('customizablefraudfilters')->applyFraudFlag($order, $flagReason);
    }
    if(isset($flagReason)){
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, $flagReason);
    }
    else {
      Mage::helper('customizablefraudfilters')->logAction($order, __FUNCTION__, "");
    }
  }

  public function applyFraudFlag($order, $flagReason){
    $state = "holded";
    $status = "manual_review";
    $notice = "Flagged for manual review: ";
    $comment = $notice.$flagReason;
    $isCustomerNotified = false;
    $order->setState($state, $status, $comment, $isCustomerNotified);
    $order->save(); 

    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email') != null){
      $alertEmailAddresses = Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email');
      $alertEmailAddresses = explode(",", $alertEmailAddresses);
      foreach ($alertEmailAddresses as $alertEmailAddress) {
        $alertEmailAddress = trim($alertEmailAddress);
        Mage::helper('customizablefraudfilters')->sendAlertEmail($order, $alertEmailAddress, $flagReason);
      }
    }
  }


  public function sendAlertEmail($order, $alertEmailAddress, $flagReason) {
    $emailTemplate = Mage::getModel("core/email_template")->loadDefault("fraud_filter_alert");

    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_from') != "") {
      $from = Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_from');
    }
    else {
      $from = Mage::app()->getStore()->getFrontendName();
    }
    $emailTemplate->setSenderName($from);

    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_reply') != "") {
      $replyTo = Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_reply');
    }
    else {
      $replyTo = Mage::getStoreConfig('trans_email/ident_general/email');
    }    
    $emailTemplate->setSenderEmail($replyTo);

    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_subject') != "") {
      $subject = Mage::getStoreConfig('customizablefraudfilters/general_settings/alert_email_subject');
    }
    else {
      $subject = "Potential Fraud Alert";
    }

    $orderEntityId = $order['entity_id'];
    $orderUrl = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view',array('order_id' => $orderEntityId));

    $emailTemplate->setTemplateSubject($subject.": Order #".$order["increment_id"]);
    $emailTemplate->setType("html");

    $emailTemplateVariables = array();
    $emailTemplateVariables['orderNumber'] = $order["increment_id"];
    $emailTemplateVariables['storeName'] = Mage::app()->getStore()->getFrontendName();
    $emailTemplateVariables['flagReason'] = $flagReason;

    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/include_order_link') == 1){
      $emailTemplateVariables['orderUrl'] = $orderUrl;
    }

    $emailTemplate->send($alertEmailAddress, null, $emailTemplateVariables);   
  }

  public function logAction($order, $functionName, $flagReason) {
    if(Mage::getStoreConfig('customizablefraudfilters/general_settings/filter_logging') == 1) {
      if($flagReason != ""){
        $result = "failed";
      }
      else {
        $result = "passed";
      }
      $orderId = $order["increment_id"];
      $message = $orderId.": ".$functionName." - ".$result;
      Mage::log($message, null, 'customizablefraudfilters.log');
    }
  }
}
   