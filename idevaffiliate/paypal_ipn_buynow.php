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

// Commission Calculation Definitions
// --------------------------------------
// 1 = yes, 0 = no

// Subtract fee charged by paypal?

	$subtract_fee = 0;

// Subtract tax from sale amount?

	$subtract_tax = 0;

// Subtract shipping from sale amount?

	$subtract_shipping = 0;

/*

This stock IPN file will subtract shipping from a single item purchase.
If you're using PayPal cart with multiple items you need to edit this
file with your item specific details.

IMPORTANT:
This is a "stock" IPN file designed to generate commissions.  Please
feel free to alter this file to suit your product and/or website needs.

We rarely accept custom contracts to alter this file for customers so
please have your website designer and/or programmer make any modifications
necessary.

If adding your own code to this file it should be done in this section:

if ($payment_status == "Completed") {

Please be sure to leave the sale.php include in tact, this is the file that
processes commissions for the referring affiliates.

*/

// --------------------------------------

$header = null;
$connection_method = null;
include ("API/config.php");
$profile = 1;

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

##   ------------------------------
##   Get General Sales Detail
##   ------------------------------

$item_name = check_type('item_name');
$item_number = check_type('item_number');
$payment_status = check_type('payment_status');
$payment_currency = check_type('mc_currency');
$receiver_email = check_type('receiver_email');
$payer_email = check_type('payer_email');
$last_name = check_type('last_name');
$payer_business_name = check_type('payer_business_name');
$custom = check_type('custom');
$txn_type = check_type('txn_type');
if (!$payer_business_name) { $customer_name = "$last_name"; } else { $customer_name = "$payer_business_name"; }

##   ------------------------------
##   Get Order Numbers
##   ------------------------------
$idev_ordernum = check_type('txn_id');

##   ------------------------------
##   Get Payment Amounts
##   ------------------------------
$idev_saleamt = check_type('mc_gross');
$idev_fee = check_type('mc_fee');

$idev_tax = check_type('tax');
$idev_tax_cart = check_type('tax_cart');
$idev_shipping = check_type('shipping');

##   ------------------------------
##   Convert Payment Amounts
##   ------------------------------

if ($subtract_fee == 1) { $idev_saleamt = $idev_saleamt - $idev_fee; }
if ($subtract_tax == 1) { $idev_saleamt = $idev_saleamt - $idev_tax - $idev_tax_cart; }
if ($subtract_shipping == 1) { $idev_saleamt = $idev_saleamt - $idev_shipping; }

##   ------------------------------
##   Pass Optional Variables
##   ------------------------------
$idev_option_1 = $customer_name;
$idev_option_2 = $payer_email;
$idev_option_3 = 'Standard Purchase';

if (strcmp ($result, "VERIFIED") == 0) {

##   ------------------------------
##   Process Commissions
##   ------------------------------

if ($payment_status == "Completed") {
include_once ("sale.php"); }

} else {

##   ------------------------------
##   Processing Failed
##   ------------------------------

// echo "failed";

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
$fp = @fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

##   ------------------------------
##   Get General Sales Detail
##   ------------------------------

$item_name = check_type('item_name');
$item_number = check_type('item_number');
$payment_status = check_type('payment_status');
$payment_currency = check_type('mc_currency');
$receiver_email = check_type('receiver_email');
$payer_email = check_type('payer_email');
$last_name = check_type('last_name');
$payer_business_name = check_type('payer_business_name');
$custom = check_type('custom');
$txn_type = check_type('txn_type');
if (!$payer_business_name) { $customer_name = "$last_name"; } else { $customer_name = "$payer_business_name"; }

##   ------------------------------
##   Get Order Numbers
##   ------------------------------
$idev_ordernum = check_type('txn_id');

##   ------------------------------
##   Get Payment Amounts
##   ------------------------------
$idev_saleamt = check_type('mc_gross');
$idev_fee = check_type('mc_fee');

$idev_tax = check_type('tax');
$idev_tax_cart = check_type('tax_cart');
$idev_shipping = check_type('shipping');

##   ------------------------------
##   Convert Payment Amounts
##   ------------------------------

if ($subtract_fee == 1) { $idev_saleamt = $idev_saleamt - $idev_fee; }
if ($subtract_tax == 1) { $idev_saleamt = $idev_saleamt - $idev_tax - $idev_tax_cart; }
if ($subtract_shipping == 1) { $idev_saleamt = $idev_saleamt - $idev_shipping; }

##   ------------------------------
##   Pass Optional Variables
##   ------------------------------
$idev_option_1 = $customer_name;
$idev_option_2 = $payer_email;
$idev_option_3 = 'Standard Purchase';

if (!$fp) {} else {
fputs ($fp, $header . $req);
while (!feof($fp)) {
$res = fgets ($fp, 1024);

if (strcmp ($res, "VERIFIED") == 0) {

##   ------------------------------
##   Process Commissions
##   ------------------------------

if ($payment_status == "Completed") {
include_once ("sale.php");
fclose ($fp);
exit(); }

} else if (strcmp ($res, "INVALID") == 0) {

##   ------------------------------
##   Processing Failed
##   ------------------------------

// Do whatever you want here.

fclose ($fp);
exit();

} }

fclose ($fp); }

break;

	// ----------------
	   default :
	// ----------------

include("$path/templates/email/admin.paypal_failure.php");

break;

}

?>