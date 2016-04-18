<?PHP

function storeAddress() {

$getlastid=mysql_query("select f_name, l_name, email from idevaff_affiliates ORDER BY id DESC");
$getlastid=mysql_fetch_array($getlastid);
$f_name=$getlastid['f_name'];
$l_name=$getlastid['l_name'];
$email=$getlastid['email'];

$getmailchimp=mysql_query("select api_key, list_id from idevaff_newsletter_mailchimp");
$getmailchimp=mysql_fetch_array($getmailchimp);
$mailchimp_key=$getmailchimp['api_key'];
$mailchimp_listid=$getmailchimp['list_id'];

	require_once('MCAPI.class.php');
	$api = new MCAPI($mailchimp_key);

	$mergeVars = array('FNAME'=>$f_name, 'LNAME'=>$l_name);
	
	$api->listSubscribe($mailchimp_listid, $email, $mergeVars);

}

?>
