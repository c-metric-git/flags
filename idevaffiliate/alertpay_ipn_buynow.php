<?PHP
#############################################################
## iDevAffiliate Version 7
## Copyright - iDevDirect.com L.L.C.
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
## Email:   support@idevdirect.com
#############################################################

$header = null;
$connection_method = null;
include ("API/config.php");

##   ------------------------------------
##   Lookup Security Code - Do Not Alter
##   ------------------------------------
$alert_code = mysql_query("select option_1 from idevaff_integration where type = '49'");
$alert_code = mysql_fetch_array($alert_code);
$security_code = $alert_code['option_1'];

if (isset($_POST['ap_securitycode'])) { $ipn_security_code = $_POST['ap_securitycode']; } else { $ipn_security_code = null; }

if($security_code == $ipn_security_code) {

##   ------------------------------
##   Get General Sales Detail
##   ------------------------------
if (isset($_POST['ap_itemname'])) { $item_name = $_POST['ap_itemname']; } else { $item_name = null; }
if (isset($_POST['ap_itemcode'])) { $item_number = $_POST['ap_itemcode']; } else { $item_number = null; }
if (isset($_POST['ap_status'])) { $payment_status = $_POST['ap_status']; } else { $payment_status = null; }
if (isset($_POST['ap_currency'])) { $payment_currency = $_POST['ap_currency']; } else { $payment_currency = null; }
if (isset($_POST['ap_merchant'])) { $receiver_email = $_POST['ap_merchant']; } else { $receiver_email = null; }
if (isset($_POST['ap_custemailaddress'])) { $payer_email = $_POST['ap_custemailaddress']; } else { $payer_email = null; }
if (isset($_POST['ap_custlastname'])) { $last_name = $_POST['ap_custlastname']; } else { $last_name = null; }
if (isset($_POST['apc_1'])) { $custom = $_POST['apc_1']; } else { $custom = null; }
if (isset($_POST['ap_purchasetype'])) { $txn_type = $_POST['ap_purchasetype']; } else { $txn_type = null; }
$profile = 49;

if (!$payer_business_name) { $customer_name = "$last_name"; } else { $customer_name = "$payer_business_name"; }

##   ------------------------------
##   Get Order Numbers
##   ------------------------------
if (isset($_POST['ap_referencenumber'])) { $idev_ordernum = $_POST['ap_referencenumber']; } else { $idev_ordernum = null; }

##   ------------------------------
##   Get Payment Amounts
##   ------------------------------
if (isset($_POST['ap_totalamount'])) { $idev_saleamt = $_POST['ap_totalamount']; } else { $idev_saleamt = null; }

##   ------------------------------
##   Pass Optional Variables
##   ------------------------------
$idev_option_1 = $customer_name;
$idev_option_2 = $payer_email;
$idev_option_3 = 'Standard Purchase';

##   ------------------------------
##   Process Commissions
##   ------------------------------

if ($payment_status == "Success") {
include_once ("sale.php"); 

} else {

##   ------------------------------
##   Processing Failed
##   ------------------------------

// Do Whatever You Want Here

}

} else {

include("$path/templates/email/admin.paypal_failure.php");

}

?>
