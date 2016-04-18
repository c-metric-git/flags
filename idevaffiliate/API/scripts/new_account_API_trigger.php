<?PHP
#############################################################
## iDevAffiliate Version 7
## Copyright - iDevDirect.com L.L.C.
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
## Email:   support@idevdirect.com
#############################################################

// CHECK AUTHORIZATION
// ------------------------------------------------------------------------------
if ($NewAccountAPITrigger == true) {

// DEFINE AVAILABLE VARIABLES
// - The variable is already sanitized.
// - This is affiliate information from the signup form or new_affiliate.php API.
// ------------------------------------------------------------------------------
$username = check_type('username');
$company = check_type('company');
$f_name = check_type('f_name');
$l_name = check_type('l_name');
$email = check_type('email');
$address_one = check_type('address_one');
$address_two = check_type('address_two');
$city = check_type('city');
$state = check_type('state');
$zip = check_type('zip');
$country = check_type('country');
$phone = check_type('phone');
$fax = check_type('fax');
$website = check_type('website');
// ------------------------------------------------------------------------------



###################################################################
##  Write queries here.
##  Tip: Create your custom API code/functions here.
##  Pass the above variables into your external scripts here.
###################################################################



}

?>