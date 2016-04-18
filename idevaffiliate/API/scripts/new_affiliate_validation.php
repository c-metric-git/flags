<?PHP

$error = null;

// ------------------------------------------------------------------
// Check Username Exists
// ------------------------------------------------------------------
$check_username = mysql_query("select id from idevaff_affiliates where username = '$username'");
if (mysql_num_rows($check_username)) { $error .= "- Username is taken.\r\n"; }

// ------------------------------------------------------------------
// Check Username Is Short
// ------------------------------------------------------------------
function username_short($credential) {
$user_min = mysql_query("select user_min from idevaff_config");
$user_min = mysql_fetch_array($user_min);
$user_min = $user_min['user_min'];
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if ((strlen($credential) < $user_min)) {
$rtn_value=true; } return $rtn_value; }
if (username_short($username)) { $error .= "- Username is too short or missing. " . $user_min . " charaters min.\r\n"; }

// ------------------------------------------------------------------
// Check Username Is Long
// ------------------------------------------------------------------
function username_long($credential) {
$user_max = mysql_query("select user_max from idevaff_config");
$user_max = mysql_fetch_array($user_max);
$user_max = $user_max['user_max'];
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if((strlen($credential) > $user_max)) {
$rtn_value=true; } return $rtn_value; }
if (username_long($username)) { $error .= "- Username is too long. " . $user_max . " characters max.\r\n"; }

// ------------------------------------------------------------------
// Check Username Is Valid
// ------------------------------------------------------------------
function username_valid($credential) {
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if (!(preg_match("/[^a-z0-9_]/i", $credential))) {
$rtn_value=true; } return $rtn_value; }
if (!username_valid($username)) { $error .= "- Username is invalid.  Can only be letters, numbers and underscores."; }

// ------------------------------------------------------------------
// Check Password Is Short
// ------------------------------------------------------------------
function password_short($credential) {
$pass_min = mysql_query("select pass_min from idevaff_config");
$pass_min = mysql_fetch_array($pass_min);
$pass_min = $pass_min['pass_min'];
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if ((strlen($credential) < $pass_min)) {
$rtn_value=true; } return $rtn_value; }
if (password_short($password)) { $error .= "- Password is too short or missing. " . $pass_min . " charaters min.\r\n"; }

// ------------------------------------------------------------------
// Check Password Is Long
// ------------------------------------------------------------------
function password_long($credential) {
$pass_max = mysql_query("select pass_max from idevaff_config");
$pass_max = mysql_fetch_array($pass_max);
$pass_max = $pass_max['pass_max'];
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if((strlen($credential) > $pass_max)) {
$rtn_value=true; } return $rtn_value; }
if (password_long($password)) { $error .= "- Password is too long. " . $pass_max . " characters max.\r\n"; }

// ------------------------------------------------------------------
// Check Password Is Valid
// ------------------------------------------------------------------
function password_valid($credential) {
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if (!(preg_match("/[^a-z0-9_]/i", $credential))) {
$rtn_value=true; } return $rtn_value; }
if (!password_valid($password)) { $error .= "- Password is invalid.  Can only be letters, numbers and underscores.\r\n"; }

// ------------------------------------------------------------------
// Check Email Address Is Present
// ------------------------------------------------------------------
// if (!$email) { $error .= "- Missing email address.\r\n"; }

// ------------------------------------------------------------------
// Check Email Is Valid
// ------------------------------------------------------------------
function email_valid($credential) {
$rtn_value = false;
if (get_magic_quotes_gpc()) {
$credential = stripslashes($credential); }
if ((preg_match("/^([a-zA-Z0-9_]|\\-|\\.)+@(([a-zA-Z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i", $credential))) {
$rtn_value=true; } return $rtn_value; }
if (!email_valid($email)) { $error .= "- Email address is missing or invalid.\r\n"; }

// ------------------------------------------------------------------
// ALL OTHER VALUES ARE SANITIZED BUT NOT CHECKED AGAINST RULES.
// You can do that below if you want.
// ------------------------------------------------------------------

?>