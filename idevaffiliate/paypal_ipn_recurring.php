<?PHP
#############################################################
## iDevAffiliate Version 7
## Copyright - iDevDirect.com L.L.C.
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
## Email:   support@idevdirect.com
#############################################################

/*

In this IPN file you will find two versions of the IPN.  The first connection
method is done using cURL which is the preferred method.  The second connection
method is done using fsockopen.  Rather than chopping up the code and
combining the two functions, we've left them both completely in tact in case
you wish to make modifications to this file.

What we're going to do here is the following.

- Use cURL if it's enabled.
- If not, we'll try fsockopen.
- If neither are available, send a failure email to the admin.

*/

$ipn_debug = true;

$header = null;
$connection_method = null;
include ("API/config.php");

// Define the socket connection.
// --------------------------------------
if (function_exists("curl_init")) {
$connection_method = "curl"; }
if ($connection_method == '') {
$fp = @fsockopen ('www.paypal.com', 80, $errno, $errstr, 10);
if (isset($fp)) {
$connection_method = "fsockopen"; } }
// --------------------------------------

switch ($connection_method) {

	// ----------------
	   case "curl" :
	// ----------------

$req = 'cmd=_notify-validate'; 
foreach ($_POST as $key => $value) {
if (get_magic_quotes_gpc()) { 
$_POST[$key] = stripslashes($value); 
$value = stripslashes($value); } 
$value = urlencode($value); 
$req .= "&$key=$value"; } 

$url = "http://www.paypal.com/cgi-bin/webscr"; 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
$result = curl_exec($ch);
curl_close($ch);

##   --------------------------------------
##   Get General Sales Detail
##   --------------------------------------
##   Retrieve Variables and Sanitize Them
##   --------------------------------------

$item_name = check_type('item_name');
$item_number = check_type('item_number');
$payment_currency = check_type('mc_currency');
$receiver_email = check_type('receiver_email');
$payer_email = check_type('payer_email');
$last_name = check_type('last_name');
$payer_business_name = check_type('payer_business_name');
$custom = check_type('custom');
$txn_type = check_type('txn_type');
$txn_id = check_type('txn_id');
$subscr_date = check_type('subscr_date');
if (!$payer_business_name) { $customer_name = "$last_name"; } else { $customer_name = "$payer_business_name"; }

##   ------------------------------
##   Get Order Numbers & Amounts
##   ------------------------------

$idev_ordernum = check_type('subscr_id');
$mc_gross = check_type('mc_gross');
$subscr_amount_1 = check_type('mc_amount1');
$subscr_amount_2 = check_type('mc_amount2');
$subscr_amount_3 = check_type('mc_amount3');

##   ------------------------------------------------------------------------------------------
##   Commission Affiliate For Each Customer Payment? This Is A Recurring Commission Option
##   ------------------------------------------------------------------------------------------
	## 1 = Yes, generate a commission each time the customer makes a payment on a subscription.
	## 0 = No, I only want to commission the affiliate for the initial customer payment.

	$paypal_recurring_commissions = 1;

##   ------------------------------------------------------------------------------------------
##   DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
##   ------------------------------------------------------------------------------------------

$profile = 1;

##   ------------------------------
##   Pass Optional Variables
##   ------------------------------

$idev_option_1 = $customer_name;
$idev_option_2 = $payer_email;

##   ------------------------------
##   Process Commissions
##   ------------------------------

if (strcmp ($result, "VERIFIED") == 0) {

if ($txn_type == "subscr_signup") {

// Do nothing, we'll do it all in the payment section.

}

if ($txn_type == "subscr_payment") {

##   Determine Commission Type
##   -----------------------------
     $chktxn = mysql_query("select aff_id from idevaff_pp_transactions where order_num = '$idev_ordernum'");
##   -----------------------------

if (!mysql_num_rows($chktxn)) {

// This is a new subscriber.

$record_type = 1;
$idev_option_3 = "New Subscription - CURL";
$idev_saleamt = $mc_gross;
if ($ipn_debug == true) { include("templates/email/ipn_debug.php"); }
include ("sale.php");


} else {

// This is a subscription payment.

if ($paypal_recurring_commissions == 1) {

##   Get Affiliate ID
##   -----------------------------
     $chktxn_result = mysql_fetch_array($chktxn);
##   -----------------------------

$affiliate_id = $chktxn_result['aff_id'];
$idev_option_3 = "Subscription Payment - CURL";
$idev_saleamt = $mc_gross;
if ($ipn_debug == true) { include("templates/email/ipn_debug.php"); }
include ("sale.php");
} } }

##   -----------------------------
##   Subscription Cancellation
##   -----------------------------

if ($txn_type == "subscr_cancel") {
mysql_query("update idevaff_pp_transactions set rec = '0' where order_num = '$idev_ordernum'");
}


##   -----------------------------
##   Subscription End
##   -----------------------------

if ($txn_type == "subscr_eot") {
mysql_query("update idevaff_pp_transactions set rec = '0' where order_num = '$idev_ordernum'");
}

} else {

##   ------------------------------
##   Processing Failed
##   ------------------------------

// Do Whatever You Want Here

} 

break;

	// ----------------
	   case "fsockopen" :
	// ----------------

$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
$value = urlencode(stripslashes($value));
$req .= "&$key=$value"; }
$header .= "POST https://www.paypal.com/cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

##   --------------------------------------
##   Get General Sales Detail
##   --------------------------------------
##   Retrieve Variables and Sanitize Them
##   --------------------------------------

$item_name = check_type('item_name');
$item_number = check_type('item_number');
$payment_currency = check_type('mc_currency');
$receiver_email = check_type('receiver_email');
$payer_email = check_type('payer_email');
$last_name = check_type('last_name');
$payer_business_name = check_type('payer_business_name');
$custom = check_type('custom');
$txn_type = check_type('txn_type');
$txn_id = check_type('txn_id');
$subscr_date = check_type('subscr_date');
if (!$payer_business_name) { $customer_name = "$last_name"; } else { $customer_name = "$payer_business_name"; }

##   ------------------------------
##   Get Order Numbers & Amounts
##   ------------------------------

$idev_ordernum = check_type('subscr_id');
$mc_gross = check_type('mc_gross');
$subscr_amount_1 = check_type('mc_amount1');
$subscr_amount_2 = check_type('mc_amount2');
$subscr_amount_3 = check_type('mc_amount3');

##   ------------------------------------------------------------------------------------------
##   Commission Affiliate For Each Customer Payment? This Is A Recurring Commission Option
##   ------------------------------------------------------------------------------------------
	## 1 = Yes, generate a commission each time the customer makes a payment on a subscription.
	## 0 = No, I only want to commission the affiliate for the initial customer payment.

	$paypal_recurring_commissions = 1;

##   ------------------------------------------------------------------------------------------
##   DO NOT EDIT BELOW UNLESS YOU KNOW WHAT YOU'RE DOING
##   ------------------------------------------------------------------------------------------

$profile = 1;

##   ------------------------------
##   Pass Optional Variables
##   ------------------------------

$idev_option_1 = $customer_name;
$idev_option_2 = $payer_email;

##   ------------------------------
##   Process Commissions
##   ------------------------------

if ($fp) {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);

if (strcmp ($res, "VERIFIED") == 0) {

## Subscription Signup
##   -----------------------------
if ($txn_type == "subscr_signup") {

// Do nothing, we'll do it all in the payment section.

}

## Subscription Payment
##   -----------------------------
if ($txn_type == "subscr_payment") {

##   Determine Commission Type
##   -----------------------------
     $chktxn = mysql_query("select aff_id from idevaff_pp_transactions where order_num = '$idev_ordernum'");
##   -----------------------------

if (!mysql_num_rows($chktxn)) {

// This is a new subscriber.

$record_type = 1;
$idev_option_3 = "New Subscription - FSOCKOPEN";
$idev_saleamt = $mc_gross;
if ($ipn_debug == true) { include("templates/email/ipn_debug.php"); }
include ("sale.php");

} else {

// This is a subscription payment.

if ($paypal_recurring_commissions == 1) {

##   Get Affiliate ID
##   -----------------------------
     $chktxn_result = mysql_fetch_array($chktxn);
##   -----------------------------

$affiliate_id = $chktxn_result['aff_id'];
$idev_option_3 = "Subscription Payment - FSOCKOPEN";
$idev_saleamt = $mc_gross;
if ($ipn_debug == true) { include("templates/email/ipn_debug.php"); }
include ("sale.php");

} } }

## Subscription Cancellation
##   -----------------------------
if ($txn_type == "subscr_cancel") {
mysql_query("update idevaff_pp_transactions set rec = '0' where order_num = '$idev_ordernum'");
fclose ($fp);
exit(); }

## Subscription End
##   -----------------------------
if ($txn_type == "subscr_eot") {
mysql_query("update idevaff_pp_transactions set rec = '0' where order_num = '$idev_ordernum'");
 }

} else {

##   ------------------------------
##   Processing Failed
##   ------------------------------

// Do Whatever You Want Here

} } }

fclose ($fp);
exit();

break;

	// ----------------
	   default :
	// ----------------

// Do something here.

break;

}

?>
