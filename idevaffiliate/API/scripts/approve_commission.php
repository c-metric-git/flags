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
require_once("../../includes/validation_functions.php");

// QUERY THE DATABASE FOR SECRET KEY
// ----------------------------------------------------------------
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
$order_number = check_type_api('order_number');

// CHECK IF ORDER NUMBER EXISTS
// ----------------------------------------------------------------
if ($order_number) {

// GATHER COMMISSION DATA
// ----------------------------------------------------------------
$check_order_number = mysql_query("select * from idevaff_sales where tracking = '$order_number' and approved = '0'");
if (mysql_num_rows($check_order_number)) {
$commission_data = mysql_fetch_array($check_order_number);
$record = $commission_data['record'];
$aff_id = $commission_data['id'];
$cust_ip = $commission_data['ip'];
$payment = $commission_data['payment'];

$getpaylevel=mysql_query("select level, type from idevaff_affiliates where id = $aff_id");
$paylevel=mysql_fetch_array($getpaylevel);
$level=$paylevel['level'];
$type=$paylevel['type'];

// APPROVE THE COMMISSION
// ----------------------------------------------------------------
mysql_query("update idevaff_sales set approved = 1 where tracking = '$order_number'");

if ($email_html_delivery == true) {
$content = "The API file (approve_commission.php) successfully approved a commission.<br/><br />Order Number: ". $order_number . "<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (approve_commission.php) successfully approved a commission.\n\nOrder Number: ". $order_number . "\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

// UPDATE MARKETING STATS
// ----------------------------------------------------------------
if ($aff_lock == 1) { $unlock = " order by id desc"; } else { $unlock = null; }
$checkip = mysql_query("select src1, src2 from idevaff_iptracking where ip = '$cust_ip'{$unlock}");				
$ipdata = mysql_fetch_array($checkip);
$src1 = $ipdata['src1'];
$src2 = $ipdata['src2'];

if (($src1) && ($src2)) {
if ($src1 == 1) { $table = "banners"; $col = "number"; }
if ($src1 == 2) { $table = "ads"; $col = "id"; }
if ($src1 == 3) { $table = "links"; $col = "id"; }
if ($src1 == 4) { $table = "htmlads"; $col = "id"; }
if ($src1 == 5) { $table = "email_templates"; $col = "id"; }
if ($src1 == 6) { $table = "peels"; $col = "number"; }
mysql_query("update idevaff_$table set conv = conv+1 where $col = '$src2'"); }
if ($type == 3) { mysql_query("update idevaff_affiliates set conv = conv+1 where id = '$aff_id'"); }

// EMAIL AFFILIATE - NEW COMMISSION: IF ENABLED
// ----------------------------------------------------------------
if ($sale_notify_affiliate == 1) {
$id = $aff_id;
$email = 'top';
$payoute = $payment;
include($path . "/templates/email/affiliate.new_commission.php"); }

// INSERT TIER COMMISSION IF REQUIRED
// ----------------------------------------------------------------
$idev_tier_1 = mysql_query("select parent from idevaff_tiers where child = '$id' order by id");
$idev_tier_1 = mysql_fetch_array($idev_tier_1);
$texist = $idev_tier_1['parent'];
if ($texist > 0) {

$acct = mysql_query("select * from idevaff_sales where record = '$record'");
$qry = mysql_fetch_array($acct);
$uid = $qry['record'];
$id = $qry['id'];
$idev_id_override = $qry['id']; // for overrides
$payment = $qry['payment'];
$tracking_code = $qry['tracking'];
$sales_code = $qry['code'];
$recstatus = $qry['recurring'];
$op1 = $qry['op1'];
$op2 = $qry['op2'];
$op3 = $qry['op3'];
$profile = $qry['profile'];
$type = $qry['type'];
$ip = $qry['ip'];
$amount = $qry['amount'];
$sub_id = $qry['sub_id'];
$tid1 = $qry['tid1'];
$tid2 = $qry['tid2'];
$tid3 = $qry['tid3'];
$tid4 = $qry['tid4'];
$target_url = $qry['target_url'];
$referring_url = $qry['referring_url'];
$currency_to_write = $qry['currency'];
$converted_amount = $qry['converted_amount'];

$tiernumber = $texist;
$idev_ordernum = $tracking_code;
$avar = $amount;
$r_url = $referring_url;
$idev = $id;
$ip_addr = $ip;
$ov1 = $op1;
$ov2 = $op2;
$ov3 = $op3;
$commission_time = $sales_code;
 } else {
 $tiernumber = 0; }
$payout = $payment;
if ($tier_numbers > 0) { include ($path . "/includes/tiers.php"); }

// PROCESS OVERRIDE COMMISSIONS
// $commission_time = $sales_code;
$idev_id_override = $aff_id;
include ($path . "/includes/overrides.php");
// -------------------------------------


// PROCESS PERFORMANCE REWARDS: IF ENABLED
// ----------------------------------------------------------------
if ($rewards == 1) {
$afftype = $type;															
if (($rew_app == 1) && ($afftype == 1)) { $process = 1; }
if (($rew_app == 1) && ($afftype == 2)) { $process = 1; }
if (($rew_app == 2) && ($afftype == 3)) { $process = 1; }
if ($rew_app == 3) { $process = 1; }
if ($process == 1) {
$update_account_process = $aff_id;
include($path . "/includes/process_rewards.php");
} }

} else {

// COMMISSION NOT FOUND
// ----------------------------------------------------------------
if ($email_html_delivery == true) {
$content = "The API file (approve_commission.php) tried to approve a commission and couldn't.<br/><br />Reason:<br />- No commission was found with the provided order number.<br /><br />Order Number Received: ". $order_number . "<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (approve_commission.php) tried to approve a commission and couldn't.\n\nReason:\n- No commission was found with the provided order number.\n\nOrder Number Received: ". $order_number . "\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

} } else {

// ORDER NUMBER NOT RECEIVED
// ----------------------------------------------------------------
if ($email_html_delivery == true) {
$content = "The API file (approve_commission.php) tried to approve a commission and couldn't.<br/><br />Reason:<br />- No order number was received.<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$content = "The API file (approve_commission.php) tried to approve a commission and couldn't.\n\nReason:\n- No order number was received.\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
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
$content = "The iDevAffiliate API script was successfully run and a commission was approved.<br/><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$mail->isHTML(false);
$content = "The iDevAffiliate API script was successfully run and a commission was approved.\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

$mail->Subject = "iDevAffiliate API - Commission Approval Notification";
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
$content = "Invalid or missing secret key.  No commission was removed.<br/><br />Key Used: ". $secret . "<br /><br />--------<br />Message Auto-Sent By iDevAffiliate " . $version;
} else {
$mail->isHTML(false);
$content = "Invalid or missing secret key.  No commission was removed.\n\nKey Used: ". $secret . "\n\n--------\nMessage Auto-Sent By iDevAffiliate " . $version;
}

$mail->Subject = "iDevAffiliate API - Commission Approval Failure";
$mail->From = "$address";
$mail->FromName = "iDevAffiliate System";
$mail->AddAddress("$address","iDevAffiliate System");
if($cc_email == true) { $mail->AddCC("$cc_email_address","iDevAffiliate System"); }
$mail->Body = $content;

$mail->Send();
$mail->ClearAddresses();

}

?>
