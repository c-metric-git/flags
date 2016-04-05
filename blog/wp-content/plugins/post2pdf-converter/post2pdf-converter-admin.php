<?php
/*
For dashboard
by Redcocker
Last modified: 2012/3/5
License: GPL v2
http://www.near-mint.com/blog/
*/

if(!function_exists('current_user_can') || !current_user_can('manage_options')){
	die(__('Cheatin&#8217; uh?'));
}

add_action('in_admin_footer', array(&$this, 'post2pdf_conv_add_admin_footer'));

// Delete all cached PDFs
if ($this->post2pdf_conv_setting_opt['cache'] == 1 &&
	((isset($_POST['POST2PDF_Converter_Setting_Submit']) && $_POST['post2pdf_conv_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_update_options", "_wpnonce_update_options")) ||
	(isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true" && check_admin_referer("post2pdf_conv_reset_options", "_wpnonce_reset_options")) ||
	(isset($_POST['POST2PDF_Converter_Clear']) && $_POST['post2pdf_conv_clear_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_clear_cache", "_wpnonce_clear_cache")))
) {
	if (($_POST['cache'] != 1) ||
		$this->post2pdf_conv_setting_opt['lang'] != $_POST['lang'] ||
		$this->post2pdf_conv_setting_opt['font'] != stripslashes($_POST['font']) ||
		$this->post2pdf_conv_setting_opt['monospaced_font'] != stripslashes($_POST['monospaced_font']) ||
		($this->post2pdf_conv_setting_opt['font_path'] == 1 && $_POST['font_path'] != 1) ||
		($this->post2pdf_conv_setting_opt['font_path'] == 0 && $_POST['font_path'] == 1) ||
		$this->post2pdf_conv_setting_opt['font_size'] != $_POST['font_size'] ||
		($this->post2pdf_conv_setting_opt['font_subsetting'] == 1 && $_POST['font_subsetting'] != 1) ||
		($this->post2pdf_conv_setting_opt['font_subsetting'] == 0 && $_POST['font_subsetting'] == 1) ||
		$this->post2pdf_conv_setting_opt['image_ratio'] != $_POST['image_ratio'] ||
		($this->post2pdf_conv_setting_opt['header'] == 1 && $_POST['header'] != 1) ||
		($this->post2pdf_conv_setting_opt['header'] == 0 && $_POST['header'] == 1) ||
		($this->post2pdf_conv_setting_opt['logo_enable'] == 1 && $_POST['logo_enable'] != 1) ||
		($this->post2pdf_conv_setting_opt['logo_enable'] == 0 && $_POST['logo_enable'] == 1) ||
		$this->post2pdf_conv_setting_opt['logo_file'] != stripslashes($_POST['logo_file']) ||
		$this->post2pdf_conv_setting_opt['logo_width'] != stripslashes($_POST['logo_width']) ||
		($this->post2pdf_conv_setting_opt['title'] == 1 && $_POST['title'] != 1) ||
		($this->post2pdf_conv_setting_opt['title'] == 0 && $_POST['title'] == 1) ||
		($this->post2pdf_conv_setting_opt['wrap_title'] == 1 && $_POST['wrap_title'] != 1) ||
		($this->post2pdf_conv_setting_opt['wrap_title'] == 0 && $_POST['wrap_title'] == 1) ||
		($this->post2pdf_conv_setting_opt['sig_enable'] == 1 && $_POST['sig_enable'] != 1) ||
		($this->post2pdf_conv_setting_opt['sig_enable'] == 0 && $_POST['sig_enable'] == 1) ||
		$this->post2pdf_conv_sig != stripslashes($_POST['post2pdf_conv_sig']) ||
		($this->post2pdf_conv_setting_opt['sig_border'] == 1 && $_POST['sig_border'] != 1) ||
		($this->post2pdf_conv_setting_opt['sig_border'] == 0 && $_POST['sig_border'] == 1) ||
		($this->post2pdf_conv_setting_opt['sig_fill'] == 1 && $_POST['sig_fill'] != 1) ||
		($this->post2pdf_conv_setting_opt['sig_fill'] == 0 && $_POST['sig_fill'] == 1) ||
		($this->post2pdf_conv_setting_opt['footer'] == 1 && $_POST['footer'] != 1) ||
		($this->post2pdf_conv_setting_opt['footer'] == 0 && $_POST['footer'] == 1) ||
		($this->post2pdf_conv_setting_opt['filters'] == 1 && $_POST['filters'] != 1) ||
		($this->post2pdf_conv_setting_opt['filters'] == 0 && $_POST['filters'] == 1) ||
		$this->post2pdf_conv_setting_opt['shortcode_handling'] != $_POST['shortcode_handling'] ||
		($this->post2pdf_conv_setting_opt['add_to_font_family'] == 1 && $_POST['add_to_font_family'] != 1) ||
		($this->post2pdf_conv_setting_opt['add_to_font_family'] == 0 && $_POST['add_to_font_family'] == 1) ||
		(isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true")
	) {
		$dir_path = WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/pdfs/";

		if (!file_exists($dir_path)) {
			wp_die(__("<strong>Error: Directory not found.</strong><br /><br />Path: ", "post2pdf_conv").$dir_path);
		}

		$cache_dir = opendir($dir_path);

		while($file_name = readdir($cache_dir)){
			if (strpos($file_name, ".pdf") && !is_dir($dir_path.$file_name)) {
				unlink($dir_path.$file_name);
			}
		}

		closedir($cache_dir);

		// Show message for admin
		echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("All cached PDF files were deleted.", "post2pdf_conv")."</strong></p></div>";
	}
}

// Update setting options
if (isset($_POST['POST2PDF_Converter_Setting_Submit']) && $_POST['post2pdf_conv_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_update_options", "_wpnonce_update_options")) {
	// Get new value
	if ($_POST['post'] == 1) {
		$this->post2pdf_conv_setting_opt['post'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['post'] = 0;
	}
	if ($_POST['page'] == 1) {
		$this->post2pdf_conv_setting_opt['page'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['page'] = 0;
	}
	$this->post2pdf_conv_exc = stripslashes($_POST['post2pdf_conv_exc']);
	$this->post2pdf_conv_setting_opt['icon_size'] = $_POST['icon_size'];
	$this->post2pdf_conv_setting_opt['icon_file'] = stripslashes($_POST['icon_file']);
	$this->post2pdf_conv_setting_opt['link_text'] = stripslashes($_POST['link_text']);
	$this->post2pdf_conv_setting_opt['link_text_size'] = $_POST['link_text_size'];
	$this->post2pdf_conv_setting_opt['position'] = $_POST['position'];
	$this->post2pdf_conv_setting_opt['margin_top'] = stripslashes($_POST['margin_top']);
	$this->post2pdf_conv_setting_opt['margin_bottom'] = stripslashes($_POST['margin_bottom']);
	if ($_POST['right_justify'] == 1) {
		$this->post2pdf_conv_setting_opt['right_justify'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['right_justify'] = 0;
	}
	if ($_POST['css'] == 1) {
		$this->post2pdf_conv_setting_opt['css'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['css'] = 0;
	}
	$this->post2pdf_conv_setting_opt['destination'] = $_POST['destination'];
	$this->post2pdf_conv_setting_opt['access'] = $_POST['access'];
	if ($_POST['shortcode'] == 1) {
		$this->post2pdf_conv_setting_opt['shortcode'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['shortcode'] = 0;
	}
	if ($_POST['nofollow'] == 1) {
		$this->post2pdf_conv_setting_opt['nofollow'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['nofollow'] = 0;
	}
	if ($_POST['cache'] == 1) {
		$this->post2pdf_conv_setting_opt['cache'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['cache'] = 0;
	}
	if ($_POST['temp_cache'] == 1) {
		$this->post2pdf_conv_setting_opt['temp_cache'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['temp_cache'] = 0;
	}
	$this->post2pdf_conv_setting_opt['lang'] = $_POST['lang'];
	$this->post2pdf_conv_setting_opt['file'] = $_POST['file'];
	$this->post2pdf_conv_setting_opt['font'] = stripslashes($_POST['font']);
	$this->post2pdf_conv_setting_opt['monospaced_font'] = stripslashes($_POST['monospaced_font']);
	if ($_POST['font_path'] == 1) {
		$this->post2pdf_conv_setting_opt['font_path'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['font_path'] = 0;
	}
	$this->post2pdf_conv_setting_opt['font_size'] = $_POST['font_size'];
	if ($_POST['font_subsetting'] == 1) {
		$this->post2pdf_conv_setting_opt['font_subsetting'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['font_subsetting'] = 0;
	}
	$this->post2pdf_conv_setting_opt['image_ratio'] = $_POST['image_ratio'];
	if ($_POST['header'] == 1) {
		$this->post2pdf_conv_setting_opt['header'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['header'] = 0;
	}
	if ($_POST['logo_enable'] == 1) {
		$this->post2pdf_conv_setting_opt['logo_enable'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['logo_enable'] = 0;
	}
	$this->post2pdf_conv_setting_opt['logo_file'] = stripslashes($_POST['logo_file']);
	$this->post2pdf_conv_setting_opt['logo_width'] = stripslashes($_POST['logo_width']);
	if ($_POST['title'] == 1) {
		$this->post2pdf_conv_setting_opt['title'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['title'] = 0;
	}
	if ($_POST['wrap_title'] == 1) {
		$this->post2pdf_conv_setting_opt['wrap_title'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['wrap_title'] = 0;
	}
	if ($_POST['sig_enable'] == 1) {
		$this->post2pdf_conv_setting_opt['sig_enable'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['sig_enable'] = 0;
	}
	$this->post2pdf_conv_sig = stripslashes($_POST['post2pdf_conv_sig']);
	if ($_POST['sig_border'] == 1) {
		$this->post2pdf_conv_setting_opt['sig_border'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['sig_border'] = 0;
	}
	if ($_POST['sig_fill'] == 1) {
		$this->post2pdf_conv_setting_opt['sig_fill'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['sig_fill'] = 0;
	}
	if ($_POST['footer'] == 1) {
		$this->post2pdf_conv_setting_opt['footer'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['footer'] = 0;
	}
	if ($_POST['filters'] == 1) {
		$this->post2pdf_conv_setting_opt['filters'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['filters'] = 0;
	}
	$this->post2pdf_conv_setting_opt['shortcode_handling'] = $_POST['shortcode_handling'];
	if ($_POST['add_to_font_family'] == 1) {
		$this->post2pdf_conv_setting_opt['add_to_font_family'] = 1;
	} else {
		$this->post2pdf_conv_setting_opt['add_to_font_family'] = 0;
	}
	// Transforming
	$this->post2pdf_conv_exc = preg_replace("/,\s+?([0-9]+?)/", ",$1", $this->post2pdf_conv_exc);
	if (preg_match('/^[^\.].+?\.(jpg|jpeg|png|gif)$/i', $this->post2pdf_conv_setting_opt['icon_file'])) {
		$this->post2pdf_conv_setting_opt['icon_file'] = $this->post2pdf_conv_setting_opt['icon_file'];
	} else {
		$this->post2pdf_conv_setting_opt['icon_file'] = "";
	}
	$this->post2pdf_conv_setting_opt['link_text'] = strip_tags($this->post2pdf_conv_setting_opt['link_text']);
	$this->post2pdf_conv_setting_opt['margin_top']  = intval($this->post2pdf_conv_setting_opt['margin_top']);
	$this->post2pdf_conv_setting_opt['margin_bottom']  = intval($this->post2pdf_conv_setting_opt['margin_bottom']);
	$this->post2pdf_conv_setting_opt['font'] = strip_tags($this->post2pdf_conv_setting_opt['font']);
	if (preg_match('/^[^\.].+?\.(jpg|jpeg|png|gif)$/i', $this->post2pdf_conv_setting_opt['logo_file'])) {
		$this->post2pdf_conv_setting_opt['logo_file'] = $this->post2pdf_conv_setting_opt['logo_file'];
	} else {
		$this->post2pdf_conv_setting_opt['logo_file'] = "";
	}
	$this->post2pdf_conv_setting_opt['logo_width']  = intval($this->post2pdf_conv_setting_opt['logo_width']);
	$this->post2pdf_conv_sig = str_replace(array("\r\n", "\r", "\n"), "<br />", $this->post2pdf_conv_sig);
	// Validate values
	if ($this->post2pdf_conv_exc != "" && !preg_match("/^([0-9]+?,)*?[0-9]+?$/i", $this->post2pdf_conv_exc)) {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Exclusion" contains some character strings that are not allowed to use.', 'post2pdf_conv'));
	}
	if ($this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str) == "invalid") {
		wp_die(__('Invalid value. Settings could not be saved.<br />Your "Signature" contains some character strings that are not allowed to use.', 'post2pdf_conv'));
	} else {
		$this->post2pdf_conv_sig = $this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str);
	}
	// Store in DB
	update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);
	update_option('post2pdf_conv_exc', $this->post2pdf_conv_exc);
	update_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);
	update_option('post2pdf_conv_updated', 'false');
	// Show message for admin
	echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("Settings saved.","post2pdf_conv")."</strong></p></div>";
}
// Reset all settings
if (isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true" && check_admin_referer("post2pdf_conv_reset_options", "_wpnonce_reset_options")) {
	include_once('uninstall.php');
	$this->post2pdf_conv_setting_array();
	update_option('post2pdf_conv_checkver_stamp', $this->post2pdf_conv_db_ver);
	// Show message for admin
	echo "<div id='setting-error-settings_updated' class='updated fade'><p><strong>".__("All settings were reset. Please <a href=\"options-general.php?page=post2pdf-converter-options\">reload the page</a>.", "post2pdf_conv")."</strong></p></div>";
}

// Define language array to select config file and font
$languages = array(
	"afr" => __("Afrikaans", "post2pdf_conv"),
	"sqi" => __("Albanian", "post2pdf_conv"),
	"ara" => __("Arabic", "post2pdf_conv"),
	"aze" => __("Azerbaijanian", "post2pdf_conv"),
	"eus" => __("Basque", "post2pdf_conv"),
	"bel" => __("Belarusian", "post2pdf_conv"),
	"bra" => __("Portuguese(Brazil)", "post2pdf_conv"),
	"cat" => __("Catalan", "post2pdf_conv"),
	"chi" => __("Chinese(Simplified)", "post2pdf_conv"),
	"zho" => __("Chinese(Traditional)", "post2pdf_conv"),
	"hrv" => __("Croatian", "post2pdf_conv"),
	"ces" => __("Czech", "post2pdf_conv"),
	"dan" => __("Danish", "post2pdf_conv"),
	"nld" => __("Dutch", "post2pdf_conv"),
	"eng" => __("English", "post2pdf_conv"),
	"est" => __("Estonian", "post2pdf_conv"),
	"far" => __("Farsi", "post2pdf_conv"),
	"fra" => __("French", "post2pdf_conv"),
	"ger" => __("German", "post2pdf_conv"),
	"gle" => __("Irish", "post2pdf_conv"),
	"glg" => __("Galician", "post2pdf_conv"),
	"kat" => __("Georgian", "post2pdf_conv"),
	"hat" => __("Haitian Creole", "post2pdf_conv"),
	"heb" => __("Hebrew", "post2pdf_conv"),
	"hun" => __("Hungarian", "post2pdf_conv"),
	"hye" => __("Armenian", "post2pdf_conv"),
	"ind" => __("Indonesian", "post2pdf_conv"),
	"ita" => __("Italian", "post2pdf_conv"),
	"jpn" => __("Japanese", "post2pdf_conv"),
	"kor" => __("Korean", "post2pdf_conv"),
	"mkd" => __("Macedonian", "post2pdf_conv"),
	"msa" => __("Malay", "post2pdf_conv"),
	"mlt" => __("Maltese", "post2pdf_conv"),
	"ron2" => __("Moldavian", "post2pdf_conv"),
	"ron3" => __("Moldovan", "post2pdf_conv"),
	"nob" => __("Norwegian Bokmål", "post2pdf_conv"),
	"pol" => __("Polish", "post2pdf_conv"),
	"por" => __("Portuguese", "post2pdf_conv"),
	"ron1" => __("Romanian", "post2pdf_conv"),
	"rus" => __("Russian", "post2pdf_conv"),
	"srp" => __("Serbian", "post2pdf_conv"),
	"slv" => __("Slovenian", "post2pdf_conv"),
	"spa" => __("Spanish", "post2pdf_conv"),
	"swa" => __("Swahili", "post2pdf_conv"),
	"swe" => __("Swedish", "post2pdf_conv"),
	"urd" => __("Urdu", "post2pdf_conv"),
	"cym" => __("Welsh", "post2pdf_conv"),
	"yid" => __("Yiddish", "post2pdf_conv"),
	"ltr" => __("Other(Text direction: LTR)", "post2pdf_conv"),
	"rtl" => __("Other(Text direction: RTL)", "post2pdf_conv"),
	);

?> 
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>POST2PDF Converter</h2>
	<form method="post" action="">
	<?php wp_nonce_field("post2pdf_conv_update_options", "_wpnonce_update_options"); ?>
	<input type="hidden" name="post2pdf_conv_hidden_value" value="true" />
	<h3><?php _e("1. Download Link Settings", "post2pdf_conv") ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Add download link to', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="post" value="1" <?php if($this->post2pdf_conv_setting_opt['post'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Posts", "post2pdf_conv") ?> <input type="checkbox" name="page" value="1" <?php if($this->post2pdf_conv_setting_opt['page'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Pages", "post2pdf_conv") ?><br />
					<p><small><?php _e("Put a download link on the posts/pages.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Exclusion', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="post2pdf_conv_exc" size="80" value="<?php echo esc_html($this->post2pdf_conv_exc); ?>" /><br />
					<p><small><?php _e("Enter comma-separated Post_id(s).<br />The download link will not be shown on Posts/Pages with enterd Post_id.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Icon size", "post2pdf_conv") ?></th> 
				<td>
					<select name="icon_size">
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="22" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "22") {echo 'selected="selected"';} ?>>22 px</option>
						<option value="32" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "32") {echo 'selected="selected"';} ?>>32 px</option>
						<option value="48" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "48") {echo 'selected="selected"';} ?>>48 px</option>
						<option value="64" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "64") {echo 'selected="selected"';} ?>>64 px</option>
						<option value="128" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "128") {echo 'selected="selected"';} ?>>128 px</option>
						<option value="none" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "none") {echo 'selected="selected"';} ?>><?php _e("None", "post2pdf_conv") ?></option>
						<option value="custom" <?php if ($this->post2pdf_conv_setting_opt['icon_size'] == "custom") {echo 'selected="selected"';} ?>><?php _e("Custom icon", "post2pdf_conv") ?></option>
					</select><br /><?php _e("Custom icon file name", "post2pdf_conv") ?> <input type="text" name="icon_file" size="15" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['icon_file']); ?>" />
					<p><small><?php _e("Choose PDF icon size.<br />If you want to use your cutom icon, upload your image to /wp-content/tcpdf-iamges/ directory(If not exist, create it).<br />After uploading, choose 'Custom icon' and enter iamge file name.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Link text', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="link_text" size="80" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['link_text']); ?>" /><br />
					<p><small><?php _e("Enter text for the download link.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Font size", "post2pdf_conv") ?></th> 
				<td>
					<select name="link_text_size">
						<option value="6" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "6") {echo 'selected="selected"';} ?>>6 px</option>
						<option value="7" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "7") {echo 'selected="selected"';} ?>>7 px</option>
						<option value="8" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "8") {echo 'selected="selected"';} ?>>8 px</option>
						<option value="9" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "9") {echo 'selected="selected"';} ?>>9 px</option>
						<option value="10" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "10") {echo 'selected="selected"';} ?>>10 px</option>
						<option value="11" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "11") {echo 'selected="selected"';} ?>>11 px</option>
						<option value="12" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "12") {echo 'selected="selected"';} ?>>12 px</option>
						<option value="13" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "13") {echo 'selected="selected"';} ?>>13 px</option>
						<option value="14" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "14") {echo 'selected="selected"';} ?>>14 px</option>
						<option value="15" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "15") {echo 'selected="selected"';} ?>>15 px</option>
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="17" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "17") {echo 'selected="selected"';} ?>>17 px</option>
						<option value="18" <?php if ($this->post2pdf_conv_setting_opt['link_text_size'] == "18") {echo 'selected="selected"';} ?>>18 px</option>
					</select>
					<p><small><?php _e("Choose font size for the link text.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Position", "post2pdf_conv") ?></th> 
				<td>
					<select name="position">
						<option value="before" <?php if ($this->post2pdf_conv_setting_opt['position'] == "before") {echo 'selected="selected"';} ?>><?php _e("Before the post/page content block", "post2pdf_conv") ?></option>
						<option value="after" <?php if ($this->post2pdf_conv_setting_opt['position'] == "after") {echo 'selected="selected"';} ?>><?php _e("After the post/page content block", "post2pdf_conv") ?></option>
						<option value="both" <?php if ($this->post2pdf_conv_setting_opt['position'] == "both") {echo 'selected="selected"';} ?>><?php _e("Before and After the post/page content block", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Choose display position of the download link.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Margin', 'post2pdf_conv') ?></th>
				<td>
					<?php _e("Top margin", "post2pdf_conv") ?> <input type="text" name="margin_top" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['margin_top']); ?>" /><?php _e("px", "post2pdf_conv") ?> <?php _e("Bottom margin", "post2pdf_conv") ?> <input type="text" name="margin_bottom" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['margin_bottom']); ?>" /><?php _e("px", "post2pdf_conv") ?><br />
					<p><small><?php _e("Set margin for the download link block.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Right justification', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="right_justify" value="1" <?php if($this->post2pdf_conv_setting_opt['right_justify'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Justify the download link block to the right.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('CSS for download link', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="css" value="1" <?php if($this->post2pdf_conv_setting_opt['css'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("If you want to apply your own css to the download link, disable this option.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Link action", "post2pdf_conv") ?></th> 
				<td>
					<select name="destination">
						<option value="D" <?php if ($this->post2pdf_conv_setting_opt['destination'] == "D") {echo 'selected="selected"';} ?>><?php _e("Download", "post2pdf_conv") ?></option>
						<option value="I" <?php if ($this->post2pdf_conv_setting_opt['destination'] == "I") {echo 'selected="selected"';} ?>><?php _e("Open with the browser", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("When 'Open with the browser' is chosen, This plugin will output PDF to the browser.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Access", "post2pdf_conv") ?></th> 
				<td>
					<select name="access">
						<option value="unrestraint" <?php if ($this->post2pdf_conv_setting_opt['access'] == "unrestraint") {echo 'selected="selected"';} ?>><?php _e("Unrestraint", "post2pdf_conv") ?></option>
						<option value="referrer" <?php if ($this->post2pdf_conv_setting_opt['access'] == "referrer") {echo 'selected="selected"';} ?>><?php _e("Deny any access with the download URL directly", "post2pdf_conv") ?></option>
						<option value="login" <?php if ($this->post2pdf_conv_setting_opt['access'] == "login") {echo 'selected="selected"';} ?>><?php _e("Allow only registered users", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("You can restrict access to the download link.<br/>When 'Deny any access with the download URL directly' is chosen, HTTP referrer must contain your WordPress URL.<br />When 'Allow only registered users' is chosen, Only registered users can download a PDF.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Shortcode', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="shortcode" value="1" <?php if($this->post2pdf_conv_setting_opt['shortcode'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("You can insert the download link into your posts/pages using 'Shortcode'.<br />e.g.: <code>[pdf]Click here to get a PDF[/pdf]</code>", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Nofollow', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="nofollow" value="1" <?php if($this->post2pdf_conv_setting_opt['nofollow'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("This plugin will add rel=\"nofollow\" into the download link to prevent search engines from crawling and indexing PDF files.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
	<h3><?php _e("2. Cache Settings", 'post2pdf_conv') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Cache', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="cache" value="1" <?php if($this->post2pdf_conv_setting_opt['cache'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Created PDFs will be cached locally.<br />It will improve performance and reduce the server load.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Temporary cache', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="temp_cache" value="1" <?php if($this->post2pdf_conv_setting_opt['temp_cache'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("When this plugin is creating a PDF, temporary data is cached on serevr disk.<br />It will reduce server memory usage. However it may also cause slow performance or fatal error.<br />Temporary cached files will be cleared automatically.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
	<h3><?php _e("3. PDF Settings", 'post2pdf_conv') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e("Language", "post2pdf_conv") ?></th> 
				<td>
					<select name="lang">
					<?php foreach ($languages as $lang => $lang_name) { ?>
						<option value="<?php echo $lang ?>" <?php if ($this->post2pdf_conv_setting_opt['lang'] == $lang) {echo 'selected="selected"';} ?>><?php echo $lang_name ?></option>
					<?php } ?>
					</select>
					<p><small><?php _e("Choose content language.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("File name", "post2pdf_conv") ?></th> 
				<td>
					<select name="file">
						<option value="title" <?php if ($this->post2pdf_conv_setting_opt['file'] == "title") {echo 'selected="selected"';} ?>><?php _e("Title", "post2pdf_conv") ?></option>
						<option value="id" <?php if ($this->post2pdf_conv_setting_opt['file'] == "id") {echo 'selected="selected"';} ?>><?php _e("Post id", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Define PDF file name.<br />You can choose title-based filename or post id-based filename.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Font', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="font" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['font']); ?>" /><br />
					<p><small><?php _e("This plugin choose the best suited font and set it as the default font automatically.<br />You can also enter another font name to change font.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Monospaced font', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="monospaced_font" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['monospaced_font']); ?>" /><br />
					<p><small><?php _e("Set default monospaced font.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Safe fonts directory', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="font_path" value="1" <?php if($this->post2pdf_conv_setting_opt['font_path'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Place font files in /wp-content/tcpdf-fonts", "post2pdf_conv") ?><br />
					<p><small><?php _e("This plugin allow you to place font files in /wp-content/tcpdf-fonts.<br />Once you enable this option, after automatic updating, your fonts will never be removed.<br />If you adds your own fonts, you should enable this option.<br />Note: Before this option is enabled, you must create /wp-content/tcpdf-fonts/ directory manually.<br />Next, upload/move fonts to new directory and enable this option.<br />Orignal fonts: Unzip a plugin zip file and you can find orignal fonts in /post2pdf-converter/tcpdf/fonts.<br />Original fonts directory: /YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts<br />Now you can remove original fonts directory.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Font size", "post2pdf_conv") ?></th> 
				<td>
					<select name="font_size">
						<option value="6" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "6") {echo 'selected="selected"';} ?>>6 px</option>
						<option value="7" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "7") {echo 'selected="selected"';} ?>>7 px</option>
						<option value="8" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "8") {echo 'selected="selected"';} ?>>8 px</option>
						<option value="9" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "9") {echo 'selected="selected"';} ?>>9 px</option>
						<option value="10" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "10") {echo 'selected="selected"';} ?>>10 px</option>
						<option value="11" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "11") {echo 'selected="selected"';} ?>>11 px</option>
						<option value="12" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "12") {echo 'selected="selected"';} ?>>12 px</option>
						<option value="13" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "13") {echo 'selected="selected"';} ?>>13 px</option>
						<option value="14" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "14") {echo 'selected="selected"';} ?>>14 px</option>
						<option value="15" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "15") {echo 'selected="selected"';} ?>>15 px</option>
						<option value="16" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "16") {echo 'selected="selected"';} ?>>16 px</option>
						<option value="17" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "17") {echo 'selected="selected"';} ?>>17 px</option>
						<option value="18" <?php if ($this->post2pdf_conv_setting_opt['font_size'] == "18") {echo 'selected="selected"';} ?>>18 px</option>
					</select>
					<p><small><?php _e("Choose font size.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Font subsetting', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="font_subsetting" value="1" <?php if($this->post2pdf_conv_setting_opt['font_subsetting'] == 1){echo 'checked="checked" ';} ?>/><?php _e("enable", "post2pdf_conv") ?><br />
					<p><small><?php _e("Enable/Disable 'Font subsetting' to reduce the size of documents using large unicode font files.<br />If this option is diseabled, the whole font will be embeded in the PDF file and file size will become enlarged.<br />Note: there are some fonts that can not be embedded.(e.g. Courier, Helvetica, Times New Roman etc.)", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Image ratio", "post2pdf_conv") ?></th> 
				<td>
					<select name="image_ratio">
						<option value="0.8" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "0.8") {echo 'selected="selected"';} ?>>0.8</option>
						<option value="0.85" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "0.85") {echo 'selected="selected"';} ?>>0.85</option>
						<option value="0.9" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "0.9") {echo 'selected="selected"';} ?>>0.9</option>
						<option value="0.95" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "0.95") {echo 'selected="selected"';} ?>>0.95</option>
						<option value="1.0" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.0") {echo 'selected="selected"';} ?>>1.0</option>
						<option value="1.05" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.05") {echo 'selected="selected"';} ?>>1.05</option>
						<option value="1.1" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.1") {echo 'selected="selected"';} ?>>1.1</option>
						<option value="1.15" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.15") {echo 'selected="selected"';} ?>>1.15</option>
						<option value="1.2" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.2") {echo 'selected="selected"';} ?>>1.2</option>
						<option value="1.25" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.25") {echo 'selected="selected"';} ?>>1.25</option>
						<option value="1.3" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.3") {echo 'selected="selected"';} ?>>1.3</option>
						<option value="1.35" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.35") {echo 'selected="selected"';} ?>>1.35</option>
						<option value="1.4" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.4") {echo 'selected="selected"';} ?>>1.4</option>
						<option value="1.45" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.45") {echo 'selected="selected"';} ?>>1.45</option>
						<option value="1.5" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.5") {echo 'selected="selected"';} ?>>1.5</option>
						<option value="1.55" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.55") {echo 'selected="selected"';} ?>>1.55</option>
						<option value="1.6" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.6") {echo 'selected="selected"';} ?>>1.6</option>
						<option value="1.65" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.65") {echo 'selected="selected"';} ?>>1.65</option>
						<option value="1.7" <?php if ($this->post2pdf_conv_setting_opt['image_ratio'] == "1.7") {echo 'selected="selected"';} ?>>1.7</option>
					</select>
					<p><small><?php _e("Set image ratio.<br />Note: With increasing numerical value, you will get smaller images.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Header', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="header" value="1" <?php if($this->post2pdf_conv_setting_opt['header'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Show/Hide header.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Header logo', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="logo_enable" value="1" <?php if($this->post2pdf_conv_setting_opt['logo_enable'] == 1){echo 'checked="checked" ';} ?>/><?php _e("Show a logo on the header", "post2pdf_conv") ?><br /><?php _e("Image file", "post2pdf_conv") ?> <input type="text" name="logo_file" size="15" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['logo_file']); ?>" /> <?php _e("Logo width", "post2pdf_conv") ?> <input type="text" name="logo_width" size="2" value="<?php echo esc_html($this->post2pdf_conv_setting_opt['logo_width']); ?>" />mm<br />
					<p><small><?php _e("You can set your own logo on the header.<br />First, create /wp-content/tcpdf-images/ directory manually.<br />Next, upload iamge file(jpeg, jpg, png or gif) there and enter your image file name.<br />You can also upload your image to /YOUR PLUGIN DIRECTORY/post2pdf-converter/tcpdf/images directory.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Title', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="title" value="1" <?php if($this->post2pdf_conv_setting_opt['title'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Show/Hide title.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Wrap title', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="wrap_title" value="1" <?php if($this->post2pdf_conv_setting_opt['wrap_title'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("If title is too long, wrap the title on a new line.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Signature', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="sig_enable" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_enable'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Add signature', 'post2pdf_conv') ?><br />
					<textarea name="post2pdf_conv_sig" rows="8" style="width:500px"><?php echo esc_html($this->post2pdf_conv_valid_text($this->post2pdf_conv_sig, $this->post2pdf_conv_allowed_str)); ?></textarea><br />
					<input type="checkbox" name="sig_border" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_border'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Border', 'post2pdf_conv') ?> <input type="checkbox" name="sig_fill" value="1" <?php if($this->post2pdf_conv_setting_opt['sig_fill'] == 1){echo 'checked="checked" ';} ?>/> <?php _e('Fill', 'post2pdf_conv') ?>
					<p><small><?php _e("Enter TEXT or HTML. It will be inserted below your content.<br />For showing your signature, license agreement, message.", 'post2pdf_conv') ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Footer', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="footer" value="1" <?php if($this->post2pdf_conv_setting_opt['footer'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Show/Hide footer.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Filters', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="filters" value="1" <?php if($this->post2pdf_conv_setting_opt['filters'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("Apply WordPress default filters to the title and content.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Shortcode handling", "post2pdf_conv") ?></th> 
				<td>
					<select name="shortcode_handling">
						<option value="parse" <?php if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "parse") {echo 'selected="selected"';} ?>><?php _e("Parse", "post2pdf_conv") ?></option>
						<option value="remove" <?php if ($this->post2pdf_conv_setting_opt['shortcode_handling'] == "remove") {echo 'selected="selected"';} ?>><?php _e("Remove", "post2pdf_conv") ?></option>
					</select>
					<p><small><?php _e("Choose shortcode handling.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Add default font to font-family', 'post2pdf_conv') ?></th>
				<td>
					<input type="checkbox" name="add_to_font_family" value="1" <?php if($this->post2pdf_conv_setting_opt['add_to_font_family'] == 1){echo 'checked="checked" ';} ?>/><br />
					<p><small><?php _e("If a PDF file has garbled characters, try to enable this option.<br />When enabled, if your post has 'font-family' properties in 'style' attributes,<br />this plugin will add the default font for PDF to them.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="POST2PDF_Converter_Setting_Submit" value="<?php _e("Save Changes", "post2pdf_conv") ?>" />
		</p>
	</form>
	<h3><?php _e("4. Restore all settings to default and Clear Cache", "post2pdf_conv") ?></h3>
	<form method="post" action="" onsubmit="return confirmreset()">
	<?php wp_nonce_field("post2pdf_conv_reset_options", "_wpnonce_reset_options"); ?>
		<p class="submit">
		<input type="hidden" name="post2pdf_conv_reset" value="true" />
		<input type="submit" name="POST2PDF_Converter_Reset" value="<?php _e("Reset All Settings", "post2pdf_conv") ?>" />
		</p>
	</form>
	<form method="post" action="" onsubmit="return confirmcache()">
	<?php wp_nonce_field("post2pdf_conv_clear_cache", "_wpnonce_clear_cache"); ?>
		<p class="submit">
		<input type="hidden" name="post2pdf_conv_clear_hidden_value" value="true" />
		<input type="submit" name="POST2PDF_Converter_Clear" value="<?php _e("Clear Cache", "post2pdf_conv") ?>" />
		</p>
	</form>
	<h3><a href="javascript:showhide('id1');" name="pdf_generater"><?php _e("5. PDF Converter", "post2pdf_conv") ?></a></h3>
	<div id="id1" style="display:none; margin-left:20px">
	<form method="post" action="<?php echo $this->post2pdf_conv_plugin_url."post2pdf-converter-pdf-maker.php"; ?>">
	<?php wp_nonce_field("post2pdf_conv_pdf_generater", "_wpnonce_pdf_generater"); ?>
	<input type="hidden" name="post2pdf_conv_pdf_generater_hidden_value" value="true" />
		<table class="form-table">
			<tr valign="top"> 
				<th scope="row"></th>
				<td>
					<p><small><?php _e("This plugin also provides the PDF Converting feature only for administrators here.<br />You can convert a specified post/page to a PDF manually and download it.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Post id', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="target_id" value="" /><br />
					<p><small><?php _e("Enter Post id.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
		</table>
		<?php
		if (function_exists('qtrans_use')) {
			global $q_config;
			echo '		<input type="hidden" name="qlang" value="'.$q_config['default_language'].'" />';
		}
		?>
		<p class="submit">
		<input type="submit" name="POST2PDF_Converter_PDF_Generater" value="<?php _e("Generate ", "post2pdf_conv") ?>" />
		</p>
	</form>
	<h4><?php _e("Your created PDF files", "post2pdf_conv") ?></h4>
	<form method="post" action="<?php echo $this->post2pdf_conv_plugin_url."post2pdf-converter-pdf-delete.php"; ?>" onsubmit="return confirmdelete()">
	<?php wp_nonce_field("post2pdf_conv_del_pdf", "_wpnonce_del_pdf"); ?>
	<input type="hidden" name="post2pdf_conv_del_pdf_hidden_value" value="true" />
		<table class="form-table">
	<?php

	if (file_exists(WP_CONTENT_DIR."/tcpdf-pdf/")) {
		$pdf_dir = opendir(WP_CONTENT_DIR."/tcpdf-pdf/");
		$count = 0;
		while($file_name = readdir($pdf_dir)){
			$post_id= basename($file_name, ".pdf");
			$post_data = get_post($post_id);
			$title = $post_data->post_title;
			// For qTranslate
			if (function_exists('qtrans_use')) {
				global $q_config;
				$title = qtrans_use($q_config['default_language'], $title, false);
			}
			$title = strip_tags($title);

			if (strpos($file_name, ".pdf")) {
				$count = $count + 1;
				if ($count == 1) {
					echo "		<tr valign=\"top\">\n";
				} else {
					echo "			<tr valign=\"top\">\n";
				}
				echo "				<th scope=\"row\"></th>\n";
				echo "				<td>\n";	
				echo "					<input type=\"checkbox\" name=\"".$post_id."\" value=\"delete\" /> <a href=\"".WP_CONTENT_URL."/tcpdf-pdf/".$post_id.".pdf\">".$title."</a>&nbsp;&nbsp;".__("Last update: ", "post2pdf_conv");
				echo date("Y/m/d H:i", filemtime(WP_CONTENT_DIR."/tcpdf-pdf/".$file_name) + 3600 * get_option('gmt_offset'))."&nbsp;&nbsp;".__("Size: ", "post2pdf_conv").round(filesize(WP_CONTENT_DIR."/tcpdf-pdf/".$file_name)/1024)." KB <br />\n";
				echo "				</td>\n";
				echo "			</tr>\n";
			}
		}

		closedir($pdf_dir);

		if ($count == 0) {
			echo "		<tr valign=\"top\">\n";
			echo "				<th scope=\"row\"></th>\n";
			echo "				<td>\n";
			echo "					<p>".__("Empty", "post2pdf_conv")."</p>\n";
			echo "				</td>\n";
			echo "			</tr>\n";
		}
	} else {
			echo "		<tr valign=\"top\">\n";
			echo "				<th scope=\"row\"></th>\n";
			echo "				<td>\n";
			echo "					<p>".__("Empty", "post2pdf_conv")."</p>\n";
			echo "				</td>\n";
			echo "			</tr>\n";
	}

	?>
		</table>
		<p class="submit">
		<input type="submit" name="POST2PDF_Converter_Del_Pdf" value="<?php _e("Delete", "post2pdf_conv") ?>" /> <input type="submit" name="POST2PDF_Converter_Del_All" value="<?php _e("Delete All", "post2pdf_conv") ?>" />
		</p>
	</form>
	</div>
	<h3><a href="javascript:showhide('id2');" name="font_converter"><?php _e("6. Font converter", "post2pdf_conv") ?></a></h3>
	<div id="id2" style="display:none; margin-left:20px">
	<form method="post" action="<?php echo $this->post2pdf_conv_plugin_url."post2pdf-converter-font-maker.php"; ?>">
	<?php wp_nonce_field("post2pdf_conv_font_conv", "_wpnonce_font_conv"); ?>
	<input type="hidden" name="post2pdf_conv_font_conv_hidden_value" value="true" />
		<table class="form-table">
			<tr valign="top"> 
				<th scope="row"></th>
				<td>
					<p><small><?php _e("You can convert your TrueType font to a font for TCPDF(This plugin) here.<br />Before converting, rename font file name in lower-case.<br />If not exist, create /wp-content/tcpdf-fonts/ directory manually.<br />And upload your font file to /wp-content/tcpdf-fonts/ directory.<br />Note: Make sure you can modify and use the font in public PDF file under your font's license.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Font file name', 'post2pdf_conv') ?></th>
				<td>
					<input type="text" name="font_file" value="" /><br />
					<p><small><?php _e("Enter font file name.", "post2pdf_conv") ?></small></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Output path', 'post2pdf_conv') ?></th>
				<td>
					<input type="radio" name="font_output_path" value="wp" checked="checked" /><?php _e('/wp-content/tcpdf-fonts/', 'post2pdf_conv') ?> <input type="radio" id="" name="font_output_path" value="tcpdf" /><?php _e('/PLUGIN DIRECTORY/post2pdf-converter/tcpdf/fonts', 'post2pdf_conv') ?><br />
					<p><small><?php _e("Choose output path for generated font files.", 'post2pdf_conv') ?></small></p>
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="POST2PDF_Converter_Font_Conv" value="<?php _e("Convert", "post2pdf_conv") ?>" />
		</p>
	</form>
	</div>
	<h3><a href="javascript:showhide('id3');" name="system_info"><?php _e("7. Your System Info", "post2pdf_conv") ?></a></h3>
	<div id="id3" style="display:none; margin-left:20px">
	<p>
	<?php _e("Server OS:", "post2pdf_conv") ?> <?php echo php_uname('s').' '.php_uname('r'); ?><br />
	<?php _e("PHP version:", "post2pdf_conv") ?> <?php echo phpversion(); ?><br />
	<?php _e("MySQL version:", "post2pdf_conv") ?> <?php echo mysql_get_server_info(); ?><br />
	<?php _e("Safemode:", "post2pdf_conv") ?> <?php if (ini_get('safe_mode')) { _e("On", "post2pdf_conv"); } else { _e("Off", "post2pdf_conv"); } ?><br />
	<?php _e("memory_limit:", "post2pdf_conv") ?> <?php echo(ini_get('memory_limit')); ?><br />
	<?php _e("allow_url_fopen:", "post2pdf_conv") ?> <?php if (ini_get('allow_url_fopen')) { _e("On", "post2pdf_conv"); } else { _e("Off", "post2pdf_conv"); } ?><br />
	<?php _e("cURL:", "post2pdf_conv") ?> <?php if (function_exists('curl_init')) { _e("Installed", "post2pdf_conv"); } else { _e("No", "post2pdf_conv"); } ?><br />
	<?php _e("WordPress version:", "post2pdf_conv") ?> <?php bloginfo("version"); ?><br />
	<?php _e("Site URL:", "post2pdf_conv") ?> <?php if(function_exists("home_url")) { echo home_url(); } else { echo get_option('home'); } ?><br />
	<?php _e("WordPress URL:", "post2pdf_conv") ?> <?php echo site_url(); ?><br />
	<?php _e("WordPress language:", "post2pdf_conv") ?> <?php bloginfo("language"); ?><br />
	<?php _e("WordPress character set:", "post2pdf_conv") ?> <?php bloginfo("charset"); ?><br />
	<?php _e("WordPress theme:", "post2pdf_conv") ?> <?php $post2pdf_conv_theme = get_theme(get_current_theme()); echo $post2pdf_conv_theme['Name'].' '.$post2pdf_conv_theme['Version']; ?><br />
	<?php _e("POST2PDF Converter version:", "post2pdf_conv") ?> <?php echo $this->post2pdf_conv_ver; ?><br />
	<?php _e("POST2PDF Converter DB version:", "post2pdf_conv") ?> <?php echo get_option('post2pdf_conv_checkver_stamp'); ?><br />
	<?php _e("POST2PDF Converter URL:", "post2pdf_conv") ?> <?php echo $this->post2pdf_conv_plugin_url; ?><br />
	<?php _e("Cache directory:", "post2pdf_conv") ?> <?php if (is_writable(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/tcpdf/cache/")) { _e("Writable", "post2pdf_conv"); } else { _e("Unwritable", "post2pdf_conv"); } ?><br />
	<?php _e("PDF cache directory:", "post2pdf_conv") ?> <?php if (is_writable(WP_PLUGIN_DIR."/".dirname(plugin_basename(__FILE__))."/pdfs/")) { _e("Writable", "post2pdf_conv"); } else { _e("Unwritable", "post2pdf_conv"); } ?><br />
	<?php _e("Your browser:", "post2pdf_conv") ?> <?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?>
	</p>
	</div>
	<p>
	<?php _e("To report a bug ,submit requests and feedback, ", "post2pdf_conv") ?><?php _e("Use <a href=\"http://wordpress.org/tags/post2pdf-converter?forum_id=10\">Forum</a> or <a href=\"http://www.near-mint.com/blog/contact\">Mail From</a>", "post2pdf_conv") ?>
	</p>
	</div>
<?php 