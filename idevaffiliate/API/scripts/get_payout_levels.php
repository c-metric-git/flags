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

$data2 = array();
$results2 = mysql_query("select currency from idevaff_config");
$row2 = mysql_fetch_array($results2);
$currency = $row2['currency'];
//echo json_encode($data2);

if (($ap_1 == '1') && ($ap_2 == '1')) { $added = "type != '3'"; }
if (($ap_1 == '1') && ($ap_2 != '1')) { $added = "type != '3' and type != '2'"; }
if (($ap_1 != '1') && ($ap_2 == '1')) { $added = "type != '3' and type != '1'"; }

$data1 = array();
$results = mysql_query("select * from idevaff_paylevels where {$added} order by type, level");
while ($row = mysql_fetch_assoc($results)) {
$row['currency'] = $currency;
$data1[] = $row;
}
echo json_encode($data1);

}

?>
