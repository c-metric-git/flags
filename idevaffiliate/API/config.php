<?PHP
#############################################################
## iDevAffiliate Version 8
## Copyright - iDevDirect LLC
## Website: http://www.idevdirect.com/
## Support: http://www.idevsupport.com/
#############################################################

	ob_start();

	// Get install folder
	include_once("install_folder.php");

	// Set session duration.
	$expire_time = 60 * 30; // seconds * minutes

	include("debug.php");

	if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	$path = $_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
	} else {  $path = $_SERVER['DOCUMENT_ROOT']; }
	$path = $path.'/'.$install_directory_name;

	// Include DB connection.
	include_once($path.'/API/database.php');

	// Include session data.
	if (isset($control_panel_session)) {
	include_once($path.'/API/session.class_affiliates.php');
	} else { include_once($path. '/API/session.class.php'); }

	// Include sanitation functions.
	if (!isset($exclude_validation)) {
	include_once($path.'/includes/validation_functions.php'); }

	// Activate session.
	$session = new session();
	$session->start_session("_s", false);

	// Include global data.
	include($path.'/API/data.php');

?>