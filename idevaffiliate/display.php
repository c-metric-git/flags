<?PHP
#############################################################
## iDevAffiliate Version 5.2
## Copyright - iDevDirect.com L.L.C.
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
## Email:   support@idevdirect.com
#############################################################

/*
   --------------------------------------------------------------
   You can alter the CSS below but do not remove the Java Script
   Tags.  They are required because this file is being called
   via Java Script.

   Any invalid coding adjustments to this page will result in 
   Java Script errors in the page you've placed the Java Script
   code snippet.  Adjustments to this file are not supported by
   iDevDirect.  We highly suggest making a backup of the
   original display.php file before making changes.

   If you're wondering what the variable $idev represents, that
   is the affiliate ID number and is obtained from the
   /includes/tracking.php file.
   --------------------------------------------------------------
*/

	$token_tag_id = "Affiliate ID";
	$token_tag_username = "Affiliate Username";
	$token_tag_phone = "Affiliate Phone";
	$token_tag_name = "Affiliate Name";
	$token_tag_company = "Affiliate Company";
	$token_tag_city = "Affiliate City";
	$token_tag_state = "Affiliate State";
	$token_tag_zip = "Affiliate Zip Code";
	$token_tag_website = "Affiliate Website";

?>

document.write('<style type="text/css">');
document.write('<!--');
document.write('p.idev {');

document.write('font-size: 12px;');
document.write('font-family: Arial, Helvetica, sans-serif;');
document.write('font-weight: bold;');

document.write('}');
document.write('-->');
document.write('</style>');

<?PHP

$time_now = time();
include ("API/config.php");
include ("includes/validation_functions.php");
include ("includes/tracking.php");

// SANITIZE AND SET INCOMING TOKEN VARIABLE
// -----------------------------------------------------------
$token = check_type('token');

if ($idev) {
// GET AFFILIATE DATA
// -----------------------------------------------------------
$aff_data = mysql_query("select username, phone, f_name, l_name, company, city, state, zip, url from idevaff_affiliates where id = '$idev'");
if (mysql_num_rows($aff_data)) {
$aff_data = mysql_fetch_array($aff_data);
$affiliate_username = $aff_data['username'];
$affiliate_phone = $aff_data['phone'];
$affiliate_fname = $aff_data['f_name'];
$affiliate_lname = $aff_data['l_name'];
$affiliate_name = $affiliate_fname . "&nbsp;" . $affiliate_lname;
$affiliate_company = $aff_data['company'];
$affiliate_city = $aff_data['city'];
$affiliate_state = $aff_data['state'];
$affiliate_zip = $aff_data['zip'];
$affiliate_website = $aff_data['url'];
} }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE ID
// -----------------------------------------------------------
if (($token == 'id') || (!$token)) {
if ($idev)
// -----------------------------------------------------------
	{ $token_id = $idev;
	} else { $token_id = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_id: $token_id</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE USERNAME
// -----------------------------------------------------------
if ($token == 'username') {
if ($idev)
// -----------------------------------------------------------
	{ $token_username = $affiliate_username;
	} else { $token_username = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_username: $token_username</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE PHONE
// -----------------------------------------------------------
if ($token == 'phone') {
if (($idev) && ($affiliate_phone))
// -----------------------------------------------------------
	{ $token_phone = $affiliate_phone;
	} else { $token_phone = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_phone: $token_phone</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE NAME
// -----------------------------------------------------------
if ($token == 'name') {
if (($idev) && ($affiliate_name)) 
// -----------------------------------------------------------
	{ $token_name = $affiliate_name;
	} else { $token_name = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_name: $token_name</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE COMPANY
// -----------------------------------------------------------
if ($token == 'company') {
if (($idev) && ($affiliate_company))
// -----------------------------------------------------------
	{ $token_company = $affiliate_company;
	} else { $token_company = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_company: $token_company</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE CITY
// -----------------------------------------------------------
if ($token == 'city') {
if (($idev) && ($affiliate_city))
// -----------------------------------------------------------
	{ $token_city = $affiliate_city;
	} else { $token_city = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_city: $token_city</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE STATE
// -----------------------------------------------------------
if ($token == 'state') {
if (($idev) && ($affiliate_state))
// -----------------------------------------------------------
	{ $token_state = $affiliate_state;
	} else { $token_state = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_state: $token_state</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE ZIP
// -----------------------------------------------------------
if ($token == 'zip') {
if (($idev) && ($affiliate_zip))
// -----------------------------------------------------------
	{ $token_zip = $affiliate_zip;
	} else { $token_zip = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_zip: $token_zip</p>\");"; }

// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE WEBSITE
// -----------------------------------------------------------
if ($token == 'website') {
if (($idev) && ($affiliate_website) && ($affiliate_website != "http://")) 
// -----------------------------------------------------------
	{ $token_website = "<a href=\'$affiliate_website\' target=\'_blank\'>$affiliate_website</a>";
	} else { $token_website = "N/A"; }
// -----------------------------------------------------------
print "document.write(\"<p class='idev'>$token_tag_website: $token_website</p>\");"; }


$affiliate_logo = $logo_default;
$logo_location = $logo_default;
// -----------------------------------------------------------
// DISPLAY TOKEN : AFFILIATE LOGO
// -----------------------------------------------------------
if ($token == 'logo') {

// -----------------------------------------------------------
// GET LOGO/IMAGE DATA
// -----------------------------------------------------------
if (isset($idev)) {
$logo_data = mysql_query("select filename from idevaff_logos where id = '$idev' and approved = '1'");
if (mysql_num_rows($logo_data)) {
$logo_data = mysql_fetch_array($logo_data);
$affiliate_logo = $logo_data['filename'];

$logo_location = $base_url . "/assets/logos/" . $affiliate_logo;
} else {
$logo_location = $logo_default; }
}
// -----------------------------------------------------------
	list($width, $height, $type, $attr) = getimagesize("$logo_location");
// -----------------------------------------------------------
print "document.write(\"<img border='0' src='$logo_location' width='$width' height='$height'>\");";

}

?>


