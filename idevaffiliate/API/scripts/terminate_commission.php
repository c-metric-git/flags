<?PHP
#############################################################
## iDevAffiliate Version 7
## Copyright - iDevDirect.com L.L.C.
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
## Email:   support@idevdirect.com
#############################################################

// ----------------------------------------------------------------
// We've designed this API file as simple as possible.  We didn't use any 
// complex queries and everything should be fairly self explanatory.
// Have fun customizing this API file to meet your needs.
// ----------------------------------------------------------------

// CONNECT TO THE DATABASE & MAKE SITE CONFIG SETTINGS AVAILABLE
// ----------------------------------------------------------------
require_once("../../API/config.php");
include_once("../../includes/validation_functions.php");

// QUERY THE DATABASE FOR SECRET KEY
// ------------------------------------------------------------------------------
$s_key = mysql_query("select secret from idevaff_config");
$s_key = mysql_fetch_array($s_key);
$s_key = $s_key['secret'];

// CHECK VALID SECRET KEY IS PRESENT AND VALID
// - The variable is already sanitized.
// - The variable is already validated through _GET, or _POST.
// ------------------------------------------------------------------------------

$secret = check_type_api('secret');
if ($secret == $s_key) {

// QUERY & SANITIZE ALL INCOMING DATA
// ----------------------------------------------------------------
$order_number = check_type('order_number');

// CHECK IF ORDER NUMBER EXISTS
// ----------------------------------------------------------------
if ($order_number) {

$check_order_number = mysql_query("select record from idevaff_sales where tracking = '$order_number'");
if (mysql_num_rows($check_order_number)) {

// REMOVE THE COMMISSIONS
// ----------------------------------------------------------------
mysql_query("delete from idevaff_sales where tracking = '$order_number'");

if ($email_html_delivery == true) {
$content = "The API file (terminate_commission.php) successfully removed a commission.<br/><br />Order Number: " . $order_number . "<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (terminate_commission.php) successfully removed a commission.\n\nOrder Number: " . $order_number . "\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

} else {

// COMMISSION NOT FOUND
// ----------------------------------------------------------------
if ($email_html_delivery == true) {
$content = "The API file (terminate_commission.php) tried to remove a commission and couldn't.<br/><br />Reason:<br />- No commission was found with the provided order number.<br /><br />Order Number Received: " . $order_number . "<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (terminate_commission.php) tried to remove a commission and couldn't.\n\nReason:\n- No commission was found with the provided order number.\n\nOrder Number Received: " . $order_number . "\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

} } else {

// ORDER NUMBER NOT RECEIVED
// ----------------------------------------------------------------
if ($email_html_delivery == true) {
$content = "The API file (terminate_commission.php) tried to remove a commission and couldn't.<br/><br />Reason:<br />- No order number was received.<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (terminate_commission.php) tried to remove a commission and couldn't.\n\nReason:\n- No order number was received.\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

}

// EMAIL NOTIFICATION TO ADMIN
// ----------------------------------------------------------------
include_once($path . "/templates/email/class.phpmailer.php");
include_once($path . "/templates/email/class.smtp.php");
$mail = new PHPMailer();

if ($email_smtp_delivery == true) {
$mail->IsSMTP();
$mail->SMTPAuth = $smtp_auth;
$mail->SMTPSecure = "$smtp_security";
$mail->Host = "$smtp_host";
$mail->Port = $smtp_port;
$mail->Username = "$smtp_user";
$mail->Password = "$smtp_pass"; }
$mail->CharSet = "$smtp_char_set";

if ($email_html_delivery == true) {
$mail->isHTML(true);
} else {
$mail->isHTML(false);
}

$mail->Subject = "iDevAffiliate API - Commission Removal Notification";
$mail->From = "$address";
$mail->FromName = "iDevAffiliate System";
$mail->AddAddress("$address","iDevAffiliate System");
if($cc_email == true) { $mail->AddCC("$cc_email_address","iDevAffiliate System"); }
$mail->Body = $content;

$mail->Send();
$mail->ClearAddresses();

} else {

// EMAIL FAILED SECRET NOTIFICATION
// ----------------------------------------------------------------
if (!$secret) { $secret = "None"; }
include_once($path . "/templates/email/class.phpmailer.php");
include_once($path . "/templates/email/class.smtp.php");
$mail = new PHPMailer();

if ($email_smtp_delivery == true) {
$mail->IsSMTP();
$mail->SMTPAuth = $smtp_auth;
$mail->SMTPSecure = "$smtp_security";
$mail->Host = "$smtp_host";
$mail->Port = $smtp_port;
$mail->Username = "$smtp_user";
$mail->Password = "$smtp_pass"; }
$mail->CharSet = "$smtp_char_set";

if ($email_html_delivery == true) {
$mail->isHTML(true);
$content = "Invalid or missing secret key.  No commission was removed.<br /><br />Key Used: ". $secret;
} else {
$mail->isHTML(false);
$content = "Invalid or missing secret key.  No commission was removed.\n\nKey Used: ". $secret;
}

$mail->Subject = "iDevAffiliate API - Commission Removal Failure";
$mail->From = "$address";
$mail->FromName = "iDevAffiliate System";
$mail->AddAddress("$address","iDevAffiliate System");
if($cc_email == true) { $mail->AddCC("$cc_email_address","iDevAffiliate System"); }
$mail->Body = $content;

$mail->Send();
$mail->ClearAddresses();

}

?>
