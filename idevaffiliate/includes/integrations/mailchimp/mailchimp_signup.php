<?PHP

function storeAddress() {

$getmailchimp=mysql_query("select api_key, list_id from idevaff_newsletter_mailchimp");
$getmailchimp=mysql_fetch_array($getmailchimp);
$mailchimp_key=$getmailchimp['api_key'];
$mailchimp_listid=$getmailchimp['list_id'];

	require_once('MCAPI.class.php');
	$api = new MCAPI($mailchimp_key);

	$mergeVars = array('FNAME'=>quote_smart($_POST['f_name']), 'LNAME'=>quote_smart($_POST['l_name']));
	
	$api->listSubscribe($mailchimp_listid, quote_smart($_POST['email']), $mergeVars);

}

?>
