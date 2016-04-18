<?PHP

// Basic function for outputting errors to screen.  Designed to
// be very basic in case you want to add your own functionality.
// --------------------------------------------------------------


	$debug_mode = "off";
	
// --------------------------------------------------------------
	switch ($debug_mode) {

	case "on" :
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL);
	break;
	
	case "off" :
		error_reporting(0);
	break;
}
// --------------------------------------------------------------

?>