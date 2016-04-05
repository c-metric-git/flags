<?php
/*  
Plugin Name:WP Scroll To Top
Description: Scrolls the page to top. 
Version: 1.2
Author: umarbajwa
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.web-settler.com
*/

//Please Buy the plugin if you like it.
//If you want to alter code buy the premium version.

function  scr_html_structure(){
  ?>
	 <div id='scr_wrapper'>
 
     
     <a href='#top'><img src="<?php echo get_option('scr_icons_select');?>" class='scr_icon'></a>
    
     
	</div>
  <?php

  include 'scr_style.css';
}

add_action('wp_footer','scr_html_structure');

add_action( 'init', 'register_scr_styles' );
function register_scr_styles() {
	wp_register_style( 'scroller-plugin', plugins_url('wp-scroll-to-top/scr_style.css'));
	wp_enqueue_style( 'scroller-plugin' );
}
add_action( 'wp_enqueue_script', 'load_jquery' );
function load_jquery() {
    wp_enqueue_script( 'jquery' );
}

 function my_scr_script() {
	wp_enqueue_script( 'scroller-js', plugins_url('wp-scroll-to-top/scr_js.js'));
}
wp_enqueue_script("jquery");
add_action( 'init', 'my_scr_script' );

//Adding Options 
register_activation_hook(__FILE__,'scr_f_activate');

function scr_f_activate(){
  add_option('scr_icons_select');
}

//Register Settings

function scr_reg_settings(){
  register_setting('scr_f_setting_group','scr_icons_select');
}
add_action('admin_init','scr_reg_settings');

//Assigning Variables
add_action('wp_head','assign_var_settings');
function assign_var_settings(){
  $scr_icons_select = get_option('scr_icons_select');
}

add_action('admin_menu','scr_modify_menu');
function scr_modify_menu(){
	add_menu_page(
    'scr_options',
    'WP Scroll To Top Free',
    'administrator',
    'scr_options_free',
    'admin_scr_options'
    );}
function admin_scr_options(){
   ?>
   <h1>WP Scroll To Top Options</h1>
   <form method="post" action="options.php">
    <?php settings_fields('scr_f_setting_group');
          do_settings_sections('scr_f_setting_group');
          ?>

      <br>
      <br>
      <label for="scr_icons_select"><b>Select an Icon : </b></label>
    <select name="scr_icons_select">
      <option value="<?php echo plugins_url("/scr_icons/icon-1.png",__FILE__);?>"
<?php selected( plugins_url("/scr_icons/icon-1.png",__FILE__) , get_option('scr_icons_select')); ?>
        >Icon-1</option>

      <option value="<?php echo plugins_url("/scr_icons/icon-2.png",__FILE__);?>"
<?php selected( plugins_url("/scr_icons/icon-2.png",__FILE__) , get_option('scr_icons_select')); ?>
        >Icon-2</option>

      <option value="<?php echo plugins_url("/scr_icons/icon-3.png",__FILE__);?>"

<?php selected(plugins_url("/scr_icons/icon-3.png",__FILE__), get_option('scr_icons_select')); ?>

        >Icon-3</option>
      <option value="<?php echo plugins_url("/scr_icons/icon-4.png",__FILE__);?>"
<?php selected(plugins_url("/scr_icons/icon-4.png",__FILE__), get_option('scr_icons_select')); ?>


        >Icon-4</option>
      <option value="<?php echo plugins_url("/scr_icons/icon-5.png",__FILE__);?>"
<?php selected(plugins_url("/scr_icons/icon-5.png",__FILE__), get_option('scr_icons_select')); ?>


        >Icon-5</option>
    </select>
    <br>
    <br>
    <br>

<?php submit_button( 'Save Changes');  ?>
<hr>
</form>


   <h2 class='h_plus'><a href="http://web-settler.com/wp-scroll-to-top/" target="_blank">More Options are only available in PREMIUM version.</a></h2>
   <div style="margin-bottom:-150px;" id="scr_plus">
    <a href="http://web-settler.com/wp-scroll-to-top/" target="_blank">Get Premium Version</a>
  </div>

  <div id="settings-screen" alt="Settings Page Screen-Shot">
    <a  href="http://web-settler.com/wp-scroll-to-top/"><img style="height:600px;margin-right:10px; width:300px;" height="400px" width="150px" src="<?php echo plugins_url('/scr_icons/WP-Scroll-To-Top-Banner.jpg',__FILE__);?>">

<img style="height:600px; width:550px;" src="<?php echo plugins_url('/scr_icons/Settings-page.png',__FILE__);?>"></a>
  </div>



<?php
include 'scr_style_options.css';
}
// Remove the empty lines if any, after last character.
?>