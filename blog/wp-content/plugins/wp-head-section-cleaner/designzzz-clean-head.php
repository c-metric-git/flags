<?php
/*
Plugin Name: WP Head Section Cleaner
Plugin URI: http://wordpress.designzzz.com/meta-head-cleaner-free-plugin/
Description: A plugin to Remove selected stuff/junk from Wordpress Head meta section like extra feed links, wlmanifest link, next/previous post link, wp_generator etc.
Author: Ayaz Malik
Author URI: http://www.designzzz.com/
Version: 1.1
*/

/**** variables globals
********/
$dch_options = get_option('dch_settings');
///

include('includes/do-functions.php');
include('includes/options_page.php');

///Settings Link in plugins page
function dch_plugin_admin_action_links($links, $file) {
    static $my_plugin;
    if (!$my_plugin) {
        $my_plugin = plugin_basename(__FILE__);
    }
    if ($file == $my_plugin) {
        $settings_link = '<a href="options-general.php?page=dch-options">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'dch_plugin_admin_action_links', 10, 2);
?>