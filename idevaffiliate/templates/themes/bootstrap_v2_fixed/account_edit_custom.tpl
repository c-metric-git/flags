{*
-------------------------------------------------------
	iDevAffiliate HTML Front-End Template
-------------------------------------------------------
	Template   : Bootstrap 2 - Fixed Width Responsive
-------------------------------------------------------
	Copyright  : iDevDirect.com LLC
	Website    : www.idevdirect.com
-------------------------------------------------------
*}

{php}
$get_user_id=mysql_query("select id from idevaff_affiliates where username = '" . $_SESSION[$install_directory_name.'_idev_LoggedUsername'] . "'");
$result = mysql_fetch_array($get_user_id);
$linkid = $result['id'];
$getcustomrows = mysql_query("select id, title, name from idevaff_form_fields_custom where edit = '1' order by sort");
if (mysql_num_rows($getcustomrows)) {
{/php}
<legend style="color:{$legend};">{$custom_fields_title}</legend>
{php}
while ($qry = mysql_fetch_array($getcustomrows)) {
echo "<div class='row-fluid'>";
$group_id = $qry['id'];
$custom_title = $qry['title'];
$custom_name = $qry['name'];
$getvars = mysql_query("select id, custom_value from idevaff_form_custom_data where custom_id = '$group_id' and affid = '$linkid'");
$getvars = mysql_fetch_array($getvars);
$custom_value = $getvars['custom_value'];
$entry_id = $getvars['id'];
echo "<form method=\"POST\" action=\"account.php\" class=\"form-horizontal\">";
echo "<div class=\"control-group\">";
echo "<label class=\"control-label\">" . $custom_title . "</label>";
echo "<div class='controls'><input class='input-xxlarge span12' type='text' name='custom_value' value='" . $custom_value ."'></div>";
echo "</div>";
echo "<input type=\"hidden\" name=\"custom_id\" value=\"" . $group_id . "\">
        <input type=\"hidden\" name=\"page\" value=\"17\">
        <input type=\"hidden\" name=\"id\" value=\"" . $linkid . "\">
        <input type=\"hidden\" name=\"id_update\" value=\"$entry_id\">
		<div class=\"control-group\">
			<label class=\"control-label\" >&nbsp;</label>
			<div class=\"controls\"><input class=\"btn btn-primary\" type=\"submit\" value='"; {/php} {$edit_custom_button} {php} echo "'></div>
		</div>
        ";
echo "</form>"; 
echo "</div>";
}
}
{/php}