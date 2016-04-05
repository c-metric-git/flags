<?php
/*
Plugin Name: POST2PDF Converter
Plugin URI: http://www.near-mint.com/blog/software/post2pdf-converter
Description: This plugin converts your post/page to PDF for visitors and visitors can download it easily.
Version: 0.4.2
Author: redcocker
Author URI: http://www.near-mint.com/blog/
Text Domain: post2pdf_conv
Domain Path: /languages
*/
/*
Last modified: 2012/3/5
License: GPL v2(Except "TCPDF" libraries)
*/
/*  Copyright 2011 M. Sumitomo

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
"POST2PDF Converter" uses TCPDF by Nicola Asuni.
http://www.tcpdf.org/
TCPDF is licensed under the LGPL 3.
*/

class POST2PDF_Converter {
	var $get_by_http_request = 0;
	var $post2pdf_conv_plugin_url;
	var $post2pdf_conv_ver = "0.4.2";
	var $post2pdf_conv_db_ver = "0.4.2";
	var $post2pdf_allowed_str = "3";
	var $post2pdf_conv_setting_opt;
	var $post2pdf_conv_exc;
	var $post2pdf_conv_sig;

	function __construct() {
		load_plugin_textdomain('post2pdf_conv', false, dirname(plugin_basename(__FILE__)).'/languages');
		$this->post2pdf_conv_plugin_url = plugin_dir_url(__FILE__);

		$this->post2pdf_conv_setting_opt = get_option('post2pdf_conv_setting_opt');
		$this->post2pdf_conv_exc = get_option('post2pdf_conv_exc');
		$this->post2pdf_conv_sig = get_option('post2pdf_conv_sig');

		add_action('plugins_loaded', array(&$this, 'post2pdf_conv_check_db_ver'));
		add_action('admin_menu', array(&$this, 'post2pdf_conv_register_menu_item'));
		add_filter('plugin_action_links', array(&$this, 'post2pdf_conv_setting_link'), 10, 2);
		add_filter('the_content', array(&$this, 'post2pdf_conv_add_download_lnk'));
		if ($this->post2pdf_conv_setting_opt['css'] == 1) {
			add_filter('wp_head', array(&$this, 'post2pdf_conv_add_style'));
		}
		if ($this->post2pdf_conv_setting_opt['shortcode'] == 1) {
			add_shortcode('pdf', array(&$this, 'post2pdf_conv_shortcode_handler'));
		}
		if ($this->get_by_http_request == 1) {
			add_filter('the_content', array(&$this, 'post2pdf_conv_add_marker'), 999999);
		}
	}

	// Create settings array
	function post2pdf_conv_setting_array() {
		$this->post2pdf_conv_setting_opt = array(
			"post" => 1,
			"page" => 1,
			"icon_size" => '16',
			"icon_file" => '',
			"link_text" => __("Download this page in PDF format", "post2pdf_conv"),
			"link_text_size" => '12',
			"position" => 'before',
			"margin_top" => '10',
			"margin_bottom" => '10',
			"right_justify" => 1,
			"css" => 1,
			"destination" => 'D',
			"access" => 'referrer',
			"shortcode" => 0,
			"nofollow" => 1,
			"cache" => 0,
			"temp_cache" => 0,
			"lang" => 'eng',
			"file" => 'title',
			"font" => 'helvetica',
			"monospaced_font" => 'courier',
			"font_path" => 0,
			"font_size" => '10',
			"font_subsetting" => 1,
			"header" => 1,
			"image_ratio" => '1.25',
			"logo_enable" => 1,
			"logo_file" => 'tcpdf_logo.jpg',
			"logo_width" => '30',
			"title" => 1,
			"wrap_title" => 0,
			"sig_enable" => 0,
			"sig_border" => 0,
			"sig_fill" => 0,
			"footer" => 1,
			"filters" => 1,
			"shortcode_handling" => 'parse',
			"add_to_font_family" => 0,
			);

		// Re-define setting value
		switch (WPLANG) {
			case 'ja';
				$this->post2pdf_conv_setting_opt['lang'] = "jpn";
				$this->post2pdf_conv_setting_opt['font'] = "cid0jp";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0jp";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'af':
				$this->post2pdf_conv_setting_opt['lang'] = "afr";
				break;
			case 'ar':
				$this->post2pdf_conv_setting_opt['lang'] = "ara";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'azb_AZB':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'azr_AZR':
				$this->post2pdf_conv_setting_opt['lang'] = "aze";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'be_BY':
				$this->post2pdf_conv_setting_opt['lang'] = "bel";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'bg_BG':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'bs_BA':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'pt_BR':
				$this->post2pdf_conv_setting_opt['lang'] = "bra";
				break;
			case 'ca':
				$this->post2pdf_conv_setting_opt['lang'] = "cat";
				break;
			case 'ckb':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'cs_CZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ces";
				break;
			case 'cy':
				$this->post2pdf_conv_setting_opt['lang'] = "cym";
				break;
			case 'da_DK':
				$this->post2pdf_conv_setting_opt['lang'] = "dan";
				break;
			case 'el':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'es_CL':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'es_ES':
				$this->post2pdf_conv_setting_opt['lang'] = "spa";
				break;
			case 'es_PE':
				$this->post2pdf_conv_setting_opt['lang'] = "spa";
				break;
			case 'eo':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'et':
				$this->post2pdf_conv_setting_opt['lang'] = "est";
				break;
			case 'eu':
				$this->post2pdf_conv_setting_opt['lang'] = "eus";
				break;
			case 'fa_IR':
				$this->post2pdf_conv_setting_opt['lang'] = "far";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'fi':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'fo':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'fr_FR':
				$this->post2pdf_conv_setting_opt['lang'] = "fra";
				break;
			case 'de_DE':
				$this->post2pdf_conv_setting_opt['lang'] = "ger";
				break;
			case 'ge_GE':
				$this->post2pdf_conv_setting_opt['lang'] = "kat";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'gl_ES':
				$this->post2pdf_conv_setting_opt['lang'] = "glg";
				break;
			case 'he_IL':
				$this->post2pdf_conv_setting_opt['lang'] = "heb";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'hr':
				$this->post2pdf_conv_setting_opt['lang'] = "hrv";
				break;
			case 'hu_HU':
				$this->post2pdf_conv_setting_opt['lang'] = "hun";
				break;
			case 'id_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ind";
				break;
			case 'is_IS':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'it_IT':
				$this->post2pdf_conv_setting_opt['lang'] = "ita";
				break;
			case 'kk':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'kk_KZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'kk-Cyrl':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'kk_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'kk-Arab':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'kk_TR':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'kk-Latn':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'ko_KR':
				$this->post2pdf_conv_setting_opt['lang'] = "kor";
				$this->post2pdf_conv_setting_opt['font'] = "cid0kr";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0kr";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'lt_LT':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'lv':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'mk_MK':
				$this->post2pdf_conv_setting_opt['lang'] = "mkd";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'mg_MG':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'ms_MY':
				$this->post2pdf_conv_setting_opt['lang'] = "msa";
				break;
			case 'ni_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'nb_NO':
				$this->post2pdf_conv_setting_opt['lang'] = "nob";
				break;
			case 'nl_NL':
				$this->post2pdf_conv_setting_opt['lang'] = "nld";
				break;
			case 'nn_NO':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'pl_PL':
				$this->post2pdf_conv_setting_opt['lang'] = "pol";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'pt_PT':
				$this->post2pdf_conv_setting_opt['lang'] = "por";
				break;
			case 'ro_RO':
				$this->post2pdf_conv_setting_opt['lang'] = "ron";
				break;
			case 'ru_RU':
				$this->post2pdf_conv_setting_opt['lang'] = "rus";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'sk_SK':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'sl_SI':
				$this->post2pdf_conv_setting_opt['lang'] = "slv";
				break;

			case 'su_ID':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'sq':
				$this->post2pdf_conv_setting_opt['lang'] = "sqi";
				break;
			case 'sr_RS':
				$this->post2pdf_conv_setting_opt['lang'] = "srp";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'sv_SE':
				$this->post2pdf_conv_setting_opt['lang'] = "swe";
				break;
			case 'tg':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'th':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "freesans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "freemono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'tr_TR':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'uk':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				$this->post2pdf_conv_setting_opt['font'] = "dejavusans";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "dejavusansmono";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'ug_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "rtl";
				$this->post2pdf_conv_setting_opt['font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "aealarabiya";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'uz_UZ':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'vi':
				$this->post2pdf_conv_setting_opt['lang'] = "ltr";
				break;
			case 'zh_CN':
				$this->post2pdf_conv_setting_opt['lang'] = "chi";
				$this->post2pdf_conv_setting_opt['font'] = "cid0cs";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0cs";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'zh_TW':
				$this->post2pdf_conv_setting_opt['lang'] = "zho";
				$this->post2pdf_conv_setting_opt['font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
			case 'zh_HK':
				$this->post2pdf_conv_setting_opt['lang'] = "zho";
				$this->post2pdf_conv_setting_opt['font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "cid0ct";
				$this->post2pdf_conv_setting_opt['file'] = "id";
				break;
		}

		$this->post2pdf_conv_exc = "";
		$this->post2pdf_conv_sig = "";

		// Store in DB
		add_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);
		add_option('post2pdf_conv_exc', $this->post2pdf_conv_exc);
		add_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);
		add_option('post2pdf_conv_updated', 'false');
	}

	// Check DB table version and create table
	function post2pdf_conv_check_db_ver() {
		$current_checkver_stamp = get_option('post2pdf_conv_checkver_stamp');
		if (!$current_checkver_stamp || version_compare($current_checkver_stamp, $this->post2pdf_conv_db_ver, "!=")) {
			$updated_count = 0;
			// For new installation
			if (!$current_checkver_stamp) {
				// Register array
				$this->post2pdf_conv_setting_array();

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1", "<=")) {
				$this->post2pdf_conv_setting_opt['file'] = "title";
				$this->post2pdf_conv_setting_opt['monospaced_font'] = "courier";
				$this->post2pdf_conv_setting_opt['font_path'] = 0;
				$this->post2pdf_conv_setting_opt['add_to_font_family'] = 0;
				unset($this->post2pdf_conv_setting_opt['enable_font']);
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.3 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.3", "<=")) {
				$this->post2pdf_conv_setting_opt['logo_enable'] = 1;
				$this->post2pdf_conv_setting_opt['logo_file'] = "tcpdf_logo.jpg";
				$this->post2pdf_conv_setting_opt['logo_width'] = "30";
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.5 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.5", "<=")) {
				$this->post2pdf_conv_setting_opt['image_ratio'] = "1.25";
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.1.6 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.1.6", "<=")) {
				$this->post2pdf_conv_setting_opt['shortcode'] = 0;
				$this->post2pdf_conv_setting_opt['header'] = 1;
				$this->post2pdf_conv_setting_opt['wrap_title'] = 0;
				$this->post2pdf_conv_setting_opt['sig_enable'] = 0;
				$this->post2pdf_conv_sig = "";
				$this->post2pdf_conv_setting_opt['sig_border'] = 0;
				$this->post2pdf_conv_setting_opt['sig_fill'] = 0;
				$this->post2pdf_conv_setting_opt['footer'] = 1;
				$this->post2pdf_conv_setting_opt['filters'] = 1;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);
				$this->post2pdf_conv_sig = "";
				update_option('post2pdf_conv_sig', $this->post2pdf_conv_sig);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.2 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.2", "<=")) {
				$this->post2pdf_conv_exc = "";
				update_option('post2pdf_conv_exc', $this->post2pdf_conv_exc);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.2.3 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.2.3", "<=")) {
				$this->post2pdf_conv_setting_opt['nofollow'] = 1;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.2.4 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.2.4", "<=")) {
				$this->post2pdf_conv_setting_opt['icon_file'] = '';
				$this->post2pdf_conv_setting_opt['css'] = 1;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.3.3 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.3.3", "<=")) {
				$this->post2pdf_conv_setting_opt['title'] = 1;
				$this->post2pdf_conv_setting_opt['cache'] = 0;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}
			// For update from ver.0.4.1 or older
			if ($current_checkver_stamp && version_compare($current_checkver_stamp, "0.4", "<=")) {
				$this->post2pdf_conv_setting_opt['temp_cache'] = 0;
				update_option('post2pdf_conv_setting_opt', $this->post2pdf_conv_setting_opt);

				$updated_count = $updated_count + 1;
			}

			update_option('post2pdf_conv_checkver_stamp', $this->post2pdf_conv_db_ver);
			// Stamp for showing messages
			if ($updated_count != 0) {
				update_option('post2pdf_conv_updated', 'true');
			}
		}
	}

	// Register the setting panel and hooks
	function post2pdf_conv_register_menu_item() {
		$post2pdf_conv_page_hook = add_options_page('POST2PDF Converter Options', 'POST2PDF Converter', 'manage_options', 'post2pdf-converter-options', array(&$this, 'post2pdf_conv_options_panel'));
		if ($post2pdf_conv_page_hook != null) {
			$post2pdf_conv_page_hook = '-'.$post2pdf_conv_page_hook;
		}
		add_action('admin_print_scripts'.$post2pdf_conv_page_hook, array(&$this, 'post2pdf_conv_load_jscript_for_admin'));
		if (get_option('post2pdf_conv_updated') == "true" && !(isset($_POST['POST2PDF_Converter_Setting_Submit']) && $_POST['post2pdf_conv_hidden_value'] == "true") && !(isset($_POST['POST2PDF_Converter_Reset']) && $_POST['post2pdf_conv_reset'] == "true")) {
			add_action('admin_notices', array(&$this, 'post2pdf_conv_admin_updated_notice'));
		}
	}

	// Message for admin when DB table updated
	function post2pdf_conv_admin_updated_notice(){
		echo '<div id="message" class="updated"><p>'.__("POST2PDF Converter has successfully created new DB table.<br />If you upgraded to this version, some setting options may be added or reset to the default values.<br />Go to the <a href=\"options-general.php?page=post2pdf-converter-options\">setting panel</a> and configure POST2PDF Converter now. Once you save your settings, this message will be cleared.", "post2pdf_conv").'</p></div>';
	}

	// Show plugin info in the footer
	function post2pdf_conv_add_admin_footer() {
		$post2pdf_conv_plugin_data = get_plugin_data(__FILE__);
		printf('%1$s by %2$s<br />', $post2pdf_conv_plugin_data['Title'].' '.$post2pdf_conv_plugin_data['Version'], $post2pdf_conv_plugin_data['Author']);
	}

	// Register the setting panel
	function post2pdf_conv_setting_link($links, $file) {
		static $this_plugin;
		if (! $this_plugin) $this_plugin = plugin_basename(__FILE__);
		if ($file == $this_plugin){
			$settings_link = '<a href="options-general.php?page=post2pdf-converter-options">'.__("Settings", "post2pdf_conv").'</a>';
			array_unshift($links, $settings_link);
		}  
		return $links;
	}

	// Load script in setting panel
	function post2pdf_conv_load_jscript_for_admin() {
		wp_enqueue_script('rc_admin_js', $this->post2pdf_conv_plugin_url.'rc-admin-js.js', false, '1.3');
	}

	// Add download link
	function post2pdf_conv_add_download_lnk($content) {
		global $post;
		$nofollow = "";

		if ($this->post2pdf_conv_setting_opt['nofollow'] == 1) {
			$nofollow = ' rel="nofollow"';
		}

		if ($this->post2pdf_conv_setting_opt['icon_size'] == "custom" && $this->post2pdf_conv_setting_opt['icon_file'] != "") {
			$icon = WP_CONTENT_URL."/tcpdf-images/".$this->post2pdf_conv_setting_opt['icon_file'];
		} else if ($this->post2pdf_conv_setting_opt['icon_size'] == "custom" && $this->post2pdf_conv_setting_opt['icon_file'] == "") {
			$icon = $this->post2pdf_conv_plugin_url."img/pdf16.png";
		} else if ($this->post2pdf_conv_setting_opt['icon_size'] != "none" && $this->post2pdf_conv_setting_opt['icon_size'] != "custom") {
			$icon = $this->post2pdf_conv_plugin_url."img/pdf".$this->post2pdf_conv_setting_opt['icon_size'].".png";
		}

		$link_text = $this->post2pdf_conv_setting_opt['link_text'];

		// For qTranslate
		$qlang = "";

		if (function_exists('qtrans_use')) {
			global $q_config;
			$qlang = "&qlang=".$q_config['language'];
		}

		$anchor = '<a href="'.$this->post2pdf_conv_plugin_url.'post2pdf-converter-pdf-maker.php?id='.$post->ID.$qlang.'"'.$nofollow.'>';

		if ($this->post2pdf_conv_setting_opt['icon_size'] == "none") {
			$link = '<div id="downloadpdf">'.$anchor.$link_text.'</a></div>';
		} else {
			$link = '<div id="downloadpdf">'.$anchor.'<img src="'.$icon.'" alt="download as a pdf file" /> '.$link_text.'</a></div>';
		}

		if ($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in()) {
			$link = "";
		}

		// Exclusion posts/pages
		if ($this->post2pdf_conv_exc != "") {
			$exclusion = explode(",", $this->post2pdf_conv_exc);
			foreach ($exclusion as $val) {
				if ($val == $post->ID) {
					return $content;
				}
			}
		}
		// Return content
		if (($this->post2pdf_conv_setting_opt['post'] == 1 && is_single()) || ($this->post2pdf_conv_setting_opt['page'] == 1 && is_page())) {

			if ($this->post2pdf_conv_setting_opt['position'] == "before") {
				return $link.$content;
			} else if ($this->post2pdf_conv_setting_opt['position'] == "after") {
				return $content.$link;
			} else if ($this->post2pdf_conv_setting_opt['position'] == "both") {
				return $link.$content.$link;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	}

	// Add CSS for download link
	function post2pdf_conv_add_style() {
		global $post;

		if ($this->post2pdf_conv_setting_opt['right_justify'] == 1) {
			$aligin = "text-align: right; ";
		} else {
			$aligin = "";
		}
		$margin_top = $this->post2pdf_conv_setting_opt['margin_top'];
		if ($margin_top == "") {
			$margin_top = "0";
		}
		$margin_bottom = $this->post2pdf_conv_setting_opt['margin_bottom'];
		if ($margin_bottom == "") {
			$margin_bottom = "0";
		}
		$css = "\n<!-- POST2PDF Converter CSS Begin -->
<style type='text/css'>
#downloadpdf {".$aligin."font-size: ".$this->post2pdf_conv_setting_opt['link_text_size']."px; margin: ".$margin_top."px 0px ".$margin_top."px 0px;}
#downloadpdf a {text-decoration: none;}
</style>
<!-- POST2PDF Converter CSS End -->\n";

		if (!($this->post2pdf_conv_setting_opt['access'] == "login" && !is_user_logged_in())) {
			// Exclusion posts/pages
			if ($this->post2pdf_conv_exc != "") {
				$exclusion = explode(",", $this->post2pdf_conv_exc);
				foreach ($exclusion as $val) {
					if ($val == $post->ID) {
						return;
					}
				}
			}
			// Return css
			if (($this->post2pdf_conv_setting_opt['post'] == 1 && is_single()) || ($this->post2pdf_conv_setting_opt['page'] == 1 && is_page())) {
				echo $css;
			} else {
				return;
			}
		}
	}

	// Shortcode handler
	function post2pdf_conv_shortcode_handler($atts, $content = null) {
		global $post;

		extract(shortcode_atts(array(
			'id' => $post->ID,
			'lang' => $this->post2pdf_conv_setting_opt['lang'],
			'file' => $this->post2pdf_conv_setting_opt['file'],
			'font' => $this->post2pdf_conv_setting_opt['font'],
			'monospaced' => $this->post2pdf_conv_setting_opt['monospaced_font'],
			'fontsize' => $this->post2pdf_conv_setting_opt['font_size'],
			'subsetting' => $this->post2pdf_conv_setting_opt['font_subsetting'],
			'ratio' => $this->post2pdf_conv_setting_opt['image_ratio'],
			'header' => $this->post2pdf_conv_setting_opt['header'],
			'logo' => $this->post2pdf_conv_setting_opt['logo_enable'],
			'logo_file' => $this->post2pdf_conv_setting_opt['logo_file'],
			'logo_width' => $this->post2pdf_conv_setting_opt['logo_width'],
			'title' => $this->post2pdf_conv_setting_opt['title'],
			'wrap_title' => $this->post2pdf_conv_setting_opt['wrap_title'],
			'footer' => $this->post2pdf_conv_setting_opt['header'],
			'filters' => $this->post2pdf_conv_setting_opt['filters'],
			'shortcode' => $this->post2pdf_conv_setting_opt['shortcode_handling'],
			'ffamily' => $this->post2pdf_conv_setting_opt['add_to_font_family'],
		), $atts));

		$id = intval($id);
		$lang = esc_html($lang);
		$file = esc_html($file);
		if ($file != "title" && $file != "id") {
			$file = $this->post2pdf_conv_setting_opt['file'];
		}
		$font = esc_html($font);
		$monospaced = esc_html($monospaced);
		$fontsize = intval($fontsize);
		if ($subsetting != 1 && $subsetting != 0) {
			$subsetting = $this->post2pdf_conv_setting_opt['font_subsetting'];
		}
		$ratio = floatval($ratio);
		if ($header != 1 && $header != 0) {
			$header = $this->post2pdf_conv_setting_opt['header'];
		}
		if ($logo != 1 && $logo != 0) {
			$logo = $this->post2pdf_conv_setting_opt['logo_enable'];
		}
		$logo_file = esc_html($logo_file);
		$logo_width = intval($logo_width);
		if ($title != 1 && $title != 0) {
			$title = $this->post2pdf_conv_setting_opt['wrap_title'];
		}
		if ($wrap_title != 1 && $wrap_title != 0) {
			$wrap_title = $this->post2pdf_conv_setting_opt['wrap_title'];
		}
		if ($footer != 1 && $footer != 0) {
			$footer = $this->post2pdf_conv_setting_opt['footer'];
		}
		if ($filters != 1 && $filters != 0) {
			$filters = $this->post2pdf_conv_setting_opt['filters'];
		}
		$shortcode = esc_html($shortcode);
		if ($ffamily != 1 && $ffamily != 0) {
			$ffamily = $this->post2pdf_conv_setting_opt['add_to_font_family'];
		}

		$nofollow = "";

		if ($this->post2pdf_conv_setting_opt['nofollow'] == 1) {
			$nofollow = ' rel="nofollow"';
		}

		// For qTranslate
		$qlang = "";

		if (function_exists('qtrans_use')) {
			global $q_config;
			$qlang = "&qlang=".$q_config['language'];
		}

		return '<a href="'.$this->post2pdf_conv_plugin_url.'post2pdf-converter-pdf-maker.php?id='.$id.$qlang.'&file='.$file.'&font='.$font.'&monospaced='.$monospaced.'&fontsize='.$fontsize.'&subsetting='.$subsetting.'&ratio='.$ratio.'&header='.$header.'&title='.$title.'&wrap_title='.$wrap_title.'&logo='.$logo.'&logo_file='.$logo_file.'&logo_width='.$logo_width.'&footer='.$footer.'&filters='.$filters.'&shortcode='.$shortcode.'&ffamily='.$ffamily.'"'.$nofollow.'>'.$content.'</a>';

	}

	// Validate free style text data
	function post2pdf_conv_valid_text($text, $level) {
		global $allowedposttags, $allowedtags;

		if ($level == "0") {
			return $text;
		} elseif ($level == "1") {
			if (preg_match("/<meta[^>]*?>/is", $text) ||
			preg_match("/<title[^>]*?>/is", $text) ||
			preg_match("/<plaintext[^>]*?>/is", $text) ||
			preg_match("/<marquee[^>]*?>/is", $text) ||
			preg_match("/<isindex[^>]*?>/is", $text) ||
			preg_match("/<xmp[^>]*?>/is", $text) ||
			preg_match("/<listing[^>]*?>/is", $text)) {
				return "invalid";
			} else {
				return $text;
			}
		} elseif ($level == "2") {
			if (preg_match("/<script[^>]*?>/is", $text) ||
			preg_match("/<input[^>]*?>/is", $text) ||
			preg_match("/<textarea[^>]*?>/is", $text) ||
			preg_match("/<\/textarea>/is", $text) ||
			preg_match("/<object[^>]*?>/is", $text) ||
			preg_match("/<applet[^>]*?>/is", $text) ||
			preg_match("/<embed[^>]*?>/i", $text) ||
			preg_match("/<table[^>]*?>/is", $text) ||
			preg_match("/<form[^>]*?>/is", $text) ||
			preg_match("/<meta[^>]*?>/is", $text) ||
			preg_match("/<title[^>]*?>/is", $text) ||
			preg_match("/<frame[^>]*?>/is", $text) ||
			preg_match("/<plaintext[^>]*?>/is", $text) ||
			preg_match("/<marquee[^>]*?>/is", $text) ||
			preg_match("/<isindex[^>]*?>/is", $text) ||
			preg_match("/<xmp[^>]*?>/is", $text) ||
			preg_match("/<listing[^>]*?>/is", $text) ||
			preg_match("/<body[^>]*?>/is", $text) ||
			preg_match("/<style[^>]*?>/is", $text) ||
			preg_match("/<link[^>]*?>/is", $text) ||
			preg_match("/on.{4,}?=/is", $text) ||
			preg_match("/background[^=]*?=/is", $text) ||
			preg_match("/(http|https):\/\/.*?\.js/is", $text)) {
				return "invalid";
			} else {
				return $text;
			}
		} elseif ($level == "3") {
			$filtered_text = wp_kses($text, $allowedposttags);
			if ($text != $filtered_text) {
				return "invalid";
			} else {
				return $filtered_text;
			}
		} elseif ($level == "4") {
			$filtered_text = wp_kses($text, $allowedtags);
			if ($text != $filtered_text) {
				return "invalid";
			} else {
				return $filtered_text;
			}
		} elseif ($level == "5") {
			$text = esc_html($text);
			return $text;
		} else {
			return $text;
		}
	}

	function post2pdf_conv_add_marker($content) {
		return 	"<!-- post2pdf-converter-begin -->".$content."<!-- post2pdf-converter-end -->";
	}

	// Setting panel
	function post2pdf_conv_options_panel(){

		if (is_admin()) {
			include_once('post2pdf-converter-admin.php');
		}

	}

}

// Start this plugin
$POST2PDF_Converter = new POST2PDF_Converter();

?>