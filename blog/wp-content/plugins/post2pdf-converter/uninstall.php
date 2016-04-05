<?php
/*
by Redcocker
Last modified: 2012/1/10
License: GPL v2
http://www.near-mint.com/blog/
*/
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {exit();}
delete_option('post2pdf_conv_setting_opt');
delete_option('post2pdf_conv_exc');
delete_option('post2pdf_conv_sig');
delete_option('post2pdf_conv_checkver_stamp');
delete_option('post2pdf_conv_updated');
?>
