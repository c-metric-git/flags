<?php
/*
by Redcocker
Last modified: 2012/2/16
License: GPL v2
http://www.near-mint.com/blog/
*/

//Load bootstrap file
require_once('post2pdf-converter-bootstrap.php');

if (!class_exists('POST2PDF_Converter') || !is_user_logged_in() || !current_user_can('manage_options'))
	wp_die(__("You are not allowed to access this file.", "post2pdf_conv"));

class POST2PDF_Converter_Font_Maker {

	var $post2pdf_conv_font_file;
	var $post2pdf_conv_font_file_dir;
	var $post2pdf_conv_font_file_path;
	var $post2pdf_conv_font_out_dir;

	function __construct() {
		$this->post2pdf_conv_font_file = "";
		$this->post2pdf_conv_font_file_dir = "";
		$this->post2pdf_conv_font_file_path = "";
		$this->post2pdf_conv_font_out_dir = "";

		if (isset($_POST['POST2PDF_Converter_Font_Conv']) && $_POST['post2pdf_conv_font_conv_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_font_conv", "_wpnonce_font_conv")) {

			$this->post2pdf_conv_font_file = esc_html(stripslashes($_POST['font_file']));
			$this->post2pdf_conv_font_file_dir = WP_CONTENT_DIR."/tcpdf-fonts/";
			$this->post2pdf_conv_font_file_path = $this->post2pdf_conv_font_file_dir.$this->post2pdf_conv_font_file;

			if ($_POST['font_output_path'] == "tcpdf") {
				$this->post2pdf_conv_font_out_dir = "";
			} else {
				$this->post2pdf_conv_font_out_dir = $this->post2pdf_conv_font_file_dir;
			}

			if (!file_exists($this->post2pdf_conv_font_file_dir)) {
				wp_die(__("<strong>Error: Directory not found.</strong><br /><br />Path: ", "post2pdf_conv").WP_CONTENT_DIR."/tcpdf-pdf/");
			} else {
				// Run converting font
				$this->post2pdf_conv_make_font();
			}
		}

	}

	function post2pdf_conv_make_font() {

		if (!is_file($this->post2pdf_conv_font_file_path)) {
			wp_die(__("<strong>Error: Font not found.</strong><br /><br />Path: ", "post2pdf_conv").$this->post2pdf_conv_font_file_path);
		}

		require_once('tcpdf/tcpdf.php');

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, false);
		// Convert font
		$fontname = $pdf->addTTFfont($this->post2pdf_conv_font_file_path, '', '', 32, $this->post2pdf_conv_font_out_dir);

		if ($fontname) {
			wp_die(__("<strong>Converting completed successfully.</strong><br /><br />Font name: ", "post2pdf_conv").$fontname.__("<br /><br />Output path:<br />", "post2pdf_conv").$this->post2pdf_conv_font_out_dir.$fontname.__(".php<br />", "post2pdf_conv").$this->post2pdf_conv_font_out_dir.$fontname.__(".z<br />", "post2pdf_conv").$this->post2pdf_conv_font_out_dir.$fontname.__(".ctg.z<br /><br />Make a note of this message!<br />Go to ", "post2pdf_conv")."<a href=\"".site_url()."/wp-admin/options-general.php?page=post2pdf-converter-options\">".__("the settig panel</a> and set this Font.", "post2pdf_conv"), __("POST2PDF Converter", "post2pdf_conv"));
		} else {
			wp_die(__("<strong>Error: Can't convert.</strong><br /><br />Font: ", "post2pdf_conv").$this->post2pdf_conv_font_file_path);
		}
	}

}

// Start
$POST2PDF_Converter_Font_Maker = new POST2PDF_Converter_Font_Maker();

?>