<?php 
function dch_options_page() {
	
	global $dch_options;
 
	ob_start(); ?>
		<div class="wrap">
		<h2>Cleaning WordPress Head Section</h2>
 
		<form method="post" action="options.php">
 			<?php settings_fields('dch_settings_group'); ?>
			
			<h4><?php _e('Check Elements you Want to remove from WordPress Head Meta Section', 'dch_domain'); ?></h4>
				<p><input id="dch_settings[wp_gen]" type="checkbox" name="dch_settings[wp_gen]" value="1" <?php checked(1, $dch_options['wp_gen']); ?> />
			<label class="description" for="dch_settings[wp_gen]"><?php _e('Remove XHTML generator showing WP version', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[rsdl]" type="checkbox" name="dch_settings[rsdl]" value="1" <?php checked(1, $dch_options['rsdl']); ?> />
			<label class="description" for="dch_settings[rsdl]"><?php _e('Remove RSD Link', 'dch_domain'); ?></label> <p>

			<p><input id="dch_settings[gfl]" type="checkbox" name="dch_settings[gfl]" value="1" <?php checked(1, $dch_options['gfl']); ?> />
			<label class="description" for="dch_settings[gfl]"><?php _e('Remove links to the general feeds (e.g. posts and comments)', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[cfl]" type="checkbox" name="dch_settings[cfl]" value="1" <?php checked(1, $dch_options['cfl']); ?> />
			<label class="description" for="dch_settings[cfl]"><?php _e('Remove the links to the extra feeds such as category feeds', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[wlm]" type="checkbox" name="dch_settings[wlm]" value="1" <?php checked(1, $dch_options['wlm']); ?> />
			<label class="description" for="dch_settings[wlm]"><?php _e('Remove link to the Windows Live Writer manifest file', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[ril]" type="checkbox" name="dch_settings[ril]" value="1" <?php checked(1, $dch_options['ril']); ?> />
			<label class="description" for="dch_settings[ril]"><?php _e('Remove index link', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[adj]" type="checkbox" name="dch_settings[adj]" value="1" <?php checked(1, $dch_options['adj']); ?> />
			<label class="description" for="dch_settings[adj]"><?php _e('Remove relational links for adjacent posts', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[prev]" type="checkbox" name="dch_settings[prev]" value="1" <?php checked(1, $dch_options['prev']); ?> />
			<label class="description" for="dch_settings[prev]"><?php _e('Remove prev posts Link', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[str]" type="checkbox" name="dch_settings[str]" value="1" <?php checked(1, $dch_options['str']); ?> />
			<label class="description" for="dch_settings[str]"><?php _e('Remove Start posts Link', 'dch_domain'); ?></label> <p>
			
			<p><input id="dch_settings[alk]" type="checkbox" name="dch_settings[alk]" value="1" <?php checked(1, $dch_options['alk']); ?> />
			<label class="description" for="dch_settings[alk]"><?php _e('Remove Alkivia Framework Meta Link', 'dch_domain'); ?></label> <p>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Options', 'dch_domain'); ?>" />
			</p>
 
		</form>
 <p class="description">This Plugins is powered and created by <a href="http://wordpress.designzzz.com/" target="_blank">WordPress @ Designzzz</a>, For support, suggestions, New plugin ideas etc. visit our contact form <a href="http://wordpress.designzzz.com/contact/" target="_blank">here!</a></p>
 <p>Don't Forget to rate this plugin :} <a href="http://wordpress.org/extend/plugins/wp-head-section-cleaner/">Rate Wp Head Section Cleaner</a>  or Donate</p>
 <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="K7KU5W8GQ9NWY">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
	</div>
	<?php
	echo ob_get_clean();
}

function dch_add_options_link() {
	add_options_page('WP Head Section Cleaner', 'Wp Head Cleaner', 'manage_options', 'dch-options', 'dch_options_page');
}
add_action('admin_menu', 'dch_add_options_link');

function dch_register_settings() {
	// creates our settings in the options table
	register_setting('dch_settings_group', 'dch_settings');
}
add_action('admin_init', 'dch_register_settings');
?>