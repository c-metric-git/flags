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

class POST2PDF_Converter_Del_PDF {

	function __construct() {

		if ((isset($_POST['POST2PDF_Converter_Del_Pdf']) || isset($_POST['POST2PDF_Converter_Del_All'])) && $_POST['post2pdf_conv_del_pdf_hidden_value'] == "true" && check_admin_referer("post2pdf_conv_del_pdf", "_wpnonce_del_pdf")) {
			if (!file_exists(WP_CONTENT_DIR."/tcpdf-pdf/")) {
				wp_die(__("<strong>Error: Directory not found.</strong><br /><br />Path: ", "post2pdf_conv").WP_CONTENT_DIR."/tcpdf-pdf/");
			} else {
				// Run deleting
				$this->post2pdf_conv_del_pdf();
			}
		} else {
			wp_die(__("You are not allowed to access this file.", "post2pdf_conv"));
		}

	}

	function post2pdf_conv_del_pdf() {

		if (file_exists(WP_CONTENT_DIR."/tcpdf-pdf/")) {
			if (isset($_POST['POST2PDF_Converter_Del_All'])) {
				// Delete all
				$pdf_dir = opendir(WP_CONTENT_DIR."/tcpdf-pdf/");

				while($file_name = readdir($pdf_dir)){
					if (strpos($file_name, ".pdf")) {
						unlink(WP_CONTENT_DIR."/tcpdf-pdf/".$file_name);
					}
				}

				closedir($pdf_dir);
			} else if (isset($_POST['POST2PDF_Converter_Del_Pdf'])) {
				// Delete selected PDF
				foreach ($_POST as $id => $val) {
					if ($val == "delete") {
						unlink(WP_CONTENT_DIR."/tcpdf-pdf/".$id.".pdf");
					}
				}
			} else {
				wp_die(__("<strong>Error: Can't delete PDFs.</strong>", "post2pdf_conv"));
			}
			wp_die(__("<strong>Deleting completed successfully.</strong><br /><br />Go back to ", "post2pdf_conv")."<a href=\"".site_url()."/wp-admin/options-general.php?page=post2pdf-converter-options\">".__("the settig panel</a>.", "post2pdf_conv"), __("POST2PDF Converter", "post2pdf_conv"));
		} else {
			wp_die(__("<strong>Error: Directory not found.</strong><br /><br />Path: ", "post2pdf_conv").WP_CONTENT_DIR."/tcpdf-pdf/");
		}

	}

}

// Start
$POST2PDF_Converter_Del_PDF = new POST2PDF_Converter_Del_PDF();

?>