<?php
/*
Plugin Name: Auto-Schedule Posts
Plugin URI: http://plugins.davidjmiller.org/auto-schedule-posts/
Description: Controls the flow of posts preventing posts from publishing too close together and managing the posts between multiple authors for maximum variety
Version: 3.6
Author: David Miller
Author URI: http://www.davidjmiller.org/
*/

/* if a database change is necessary */
function install_asp() {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	                $old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_install_asp();
			}
			switch_to_blog($old_blog);
			return;
		}	
	} 
	_install_asp();		
}
 function _install_asp() {
	global $wpdb;
	$table_name = $wpdb->prefix . "users";
	if($wpdb->get_var("SHOW COLUMNS FROM $wpdb->users like 'last_published'") != 'last_published') {

		$sql = "ALTER TABLE " . $wpdb->users . " 
			ADD last_published DATETIME default '1000-01-01 00:00:00' NOT NULL;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$wpdb->query($sql);
	}
	$wpdb->query("UPDATE $wpdb->users SET last_published = (SELECT MAX(post_date_gmt) from $wpdb->posts where $wpdb->posts.post_author = $wpdb->users.ID AND $wpdb->posts.post_status = 'publish');");
}

register_activation_hook(__FILE__,'install_asp');
/* end if database change is required */

/* code to make use of wp_cron */
function asp_cron() {
	global $wpdb;
 
	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	                $old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_asp_cron();
			}
			switch_to_blog($old_blog);
			return;
		}	
	} 
	_asp_cron();		
}
 function _asp_cron() {
	wp_schedule_event(mktime(date('H'),0,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),5,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),10,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),15,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),20,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),25,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),30,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),35,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),40,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),45,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),50,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
	wp_schedule_event(mktime(date('H'),55,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
}

register_activation_hook(__FILE__, 'asp_cron');
add_action( 'wpmu_new_blog', 'new_blog_asp', 10, 6); 		
 
// Register Custom Status
function asp_post_status() {
	$args = array(
		'label'                     => _x( 'auto-schedule', 'Status General Name', 'auto_schedule_posts' ),
		'label_count'               => _n_noop( 'Queued (%s)',  'Queued (%s)', 'auto_schedule_posts' ),
		'public'                    => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'exclude_from_search'       => true,
	);

	register_post_status( 'auto-schedule', $args );
}

// Hook into the 'init' action
add_action( 'init', 'asp_post_status', 0 );

/* Function to activate on new blogs in a multiblog install */
function new_blog_asp($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $wpdb;
 
	if (is_plugin_active_for_network('auto-schedule-posts/auto-schedule-posts.php')) {
		$old_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		_install_asp();
		_asp_cron();
		switch_to_blog($old_blog);
	}
}

add_action('asp_pulse', 'drip_publish');

add_action('pending_to_publish','auto_schedule',1);
add_action('draft_to_publish','auto_schedule',1);
add_action('private_to_publish','auto_schedule',1);
add_action('future_to_publish','future_schedule',1);
add_action('new_to_publish','auto_schedule',1);
//add_action('auto-schedule_to_publish','auto_schedule',1);

function auto_schedule($post) {
	global $wpdb;
/* captured a publish_post event - set publish_time_gmt to '9999-12-31 09:59:59' and publish_time to the gmt offset of that */
	$gmtoffset = (int) (3600 * ((double) get_option('gmt_offset')));
	$asp_post = array();
		$asp_post['ID'] = $post->ID;
		$asp_post['post_date_gmt'] = gmdate('Y-m-d H:i:s', time());
		$asp_post['post_date'] = gmdate('Y-m-d H:i:s', time() + $gmtoffset);
		$asp_post['post_status'] = 'auto-schedule';
	wp_update_post($asp_post);
	drip_publish();
}

function future_schedule($post) {
	$options = get_option(basename(__FILE__, ".php"));
	$catch_future = $options['catch_future'];
	if ($catch_future == 'true') auto_schedule($post);
}

function drip_publish() {
	global $wpdb;
	$options = get_option(basename(__FILE__, ".php"));
	$query = "select id, rand() as mix from $wpdb->posts where post_status = 'auto-schedule'";
	$results = $wpdb->get_results($query);
//	if (count($results) || $options['method'] == 'set') { //posts scheduled or marking set times
	if (count($results)) { //posts scheduled
		$attempt = 'true'; // it's time unless something says otherwise
		$weekday = date('l');
		if ($options['day_type'] == 'days') {
			switch ($weekday) {
			case "Sunday":
				if ($options['sunday'] == 'no') $attempt = 'false';
				break;
			case "Monday":
				if ($options['monday'] == 'no') $attempt = 'false';
				break;
			case "Tuesday":
				if ($options['tuesday'] == 'no') $attempt = 'false';
				break;
			case "Wednesday":
				if ($options['wednesday'] == 'no') $attempt = 'false';
				break;
			case "Thursday":
				if ($options['thursday'] == 'no') $attempt = 'false';
				break;
			case "Friday":
				if ($options['friday'] == 'no') $attempt = 'false';
				break;
			case "Saturday":
				if ($options['saturday'] == 'no') $attempt = 'false';
				break;
			}
		}
		$open = explode(":",$options['start']);
		$close = explode(":",$options['end']);
		$gmtoffset = (int) (3600 * ((double) get_option('gmt_offset')));
		$open = mktime((double)$open[0],(double)$open[1]);
		$close = mktime((double)$close[0],(double)$close[1]);
		$bump = 0;
		if ( (date("H") + get_option('gmt_offset')) > 23 ) $bump = -24;
		if ( (date("H") + get_option('gmt_offset')) < 0 ) $bump = 24;
		$tod = mktime(date("H") + get_option('gmt_offset' + $bump),date("i"));
		$graveyard = 'false';
		if ($close < $open) $graveyard = 'true';
		if (($tod > $open) && ($tod < $close) && ($graveyard == 'true')) $attempt = 'false';
		if (($tod < $open) || ($tod > $close) && ($graveyard == 'false')) $attempt = 'false';
		$interval = $options['interval'] * 60;
		$published = $wpdb->get_results("SELECT MAX(post_date_gmt) as pt from $wpdb->posts where $wpdb->posts.post_status = 'publish'");
		foreach ($published as $last)  $lp = strtotime($last->pt);
		if (time() < ($lp + $interval)) $attempt = 'false'; // not time yet
		if ($randomize == 'true') { // publish at random intervals
			if (rand(0, (($options['interval'] * 20) - 1)) > ($options['interval'] * $options['percentage'] / 5)) $attempt = 'false'; // not this time
		}
		if ($attempt == 'true') { //time to post
			if ($options['method'] == 'set') {
				$options['lpa'] = gmdate('Y-m-d H:i:s', time() + $gmtoffset);
				// store the option values under the plugin filename
				update_option(basename(__FILE__, ".php"), $options);
			}
			if (count($results)) { // there are posts scheduled
				$cue_post = $query;
				if (in_array($options['priority'], array('lra', 'rlra'))) $cue_post .= " and post_author in (SELECT id from $wpdb->users where last_published = (SELECT MIN(last_published) from $wpdb->users where id in (SELECT post_author from $wpdb->posts where post_status = 'auto-schedule')))";
				if (in_array($options['priority'], array('rand', 'rlra'))) { //Assign order
					$cue_post .= " ORDER BY mix LIMIT 1";
				} else {
					$cue_post .= " ORDER BY id LIMIT 1";
				}
				$posts = $wpdb->get_results($cue_post);
				foreach ($posts as $post) {
					$asp_post = array();
						$asp_post['ID'] = $post->id;
						$asp_post['post_date_gmt'] = gmdate('Y-m-d H:i:s', time());
						$asp_post['post_date'] = gmdate('Y-m-d H:i:s', time() + $gmtoffset);
						$asp_post['post_status'] = 'future';
					wp_update_post($asp_post);
						$asp_post['ID'] = $post->id;
						$asp_post['post_date_gmt'] = gmdate('Y-m-d H:i:s', time() - 100);
						$asp_post['post_date'] = gmdate('Y-m-d H:i:s', time() + $gmtoffset - 100);
						$asp_post['post_status'] = 'publish';
					wp_update_post($asp_post);
				$options['lpa'] = gmdate('Y-m-d H:i:s', time());
				// store the option values under the plugin filename
				update_option(basename(__FILE__, ".php"), $options);
				}
			} //there are posts scheduled
		}// it was time to post
	}
	$wpdb->query("UPDATE $wpdb->users SET last_published = (SELECT MAX(post_date_gmt) from $wpdb->posts where $wpdb->posts.post_author = $wpdb->users.ID AND $wpdb->posts.post_status = 'publish');"); // update last_published
update_option(basename(__FILE__, ".php"), $options);
}


// code to cancel a scheduled event (call when deactivating)
register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
	global $wpdb;
 
	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the deactivation function for each blog id
		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_my_deactivation();
			}
			switch_to_blog($old_blog);
			return;
		}	
	} 
	_my_deactivation();		
}

function _my_deactivation() {
	global $wpdb;
	wp_clear_scheduled_hook('asp_pulse');
	$wpdb->query("UPDATE $wpdb->posts SET post_status = 'draft' where post_status = 'auto-schedule';");
}

/*
	Define the options menu
*/

function auto_schedule_posts_option_menu() {
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) return;
	} else {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 8) return;
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('Auto-Schedule Posts Options', 'auto_schedule_posts'), __('Auto-Schedule Posts', 'auto_schedule_posts'), "manage_options", __FILE__, 'auto_schedule_posts_options_page');
	}

}

// Install the options page
add_action('admin_menu', 'auto_schedule_posts_option_menu');

// Prepare the default set of options
$default_options['start'] = '00:00';
$default_options['end'] = '23:59';
$default_options['interval'] = 5;
$default_options['randomize'] = 'false';
// the plugin options are stored in the options table under the name of the plugin file sans extension
add_option(basename(__FILE__, ".php"), $default_options, 'options for the Auto-Schedule Posts plugin');

// This method displays, stores and updates all the options
function auto_schedule_posts_options_page(){
	global $wpdb;
	$bit = explode("&",$_SERVER['REQUEST_URI']);
	$url = $bit[0];
	$action = $bit[1];
	$id = $bit[2];
	// This bit stores any updated values when the Update button has been pressed
	if (isset($_POST['update_options'])) {
		// Fill up the options array as necessary
		$options['lpa'] = $_POST['lpa']; // maintain record of last post attempt when changing options.
		$options['method'] = $_POST['method']; // 'set' times or 'min' gap
		$options['interval'] = $_POST['interval'];
		$options['randomize'] = $_POST['randomize'];
		$options['percentage'] = $_POST['percentage'];
		$options['priority'] = $_POST['priority']; // Least Recent Author 'lra' or post 'lrp'
		$options['catch_future'] = $_POST['catch_future'];
		$options['start'] = $_POST['start']; // like having business hours
		$options['end'] = $_POST['end'];
		$options['day_type'] = $_POST['day_type']; // 'daily' or 'days'
		$options['sunday'] = $_POST['sunday'];
		$options['monday'] = $_POST['monday'];
		$options['tuesday'] = $_POST['tuesday'];
		$options['wednesday'] = $_POST['wednesday'];
		$options['thursday'] = $_POST['thursday'];
		$options['friday'] = $_POST['friday'];
		$options['saturday'] = $_POST['saturday'];
		if (is_numeric($options['interval'])) {
			$options['interval'] = round($options['interval']);
			if ($options['interval'] < 5) $options['interval'] = 5;
			if ($options['interval'] > 144000) $options['interval'] = 144000;
		} else { $options['interval'] = 5; }
		if (is_numeric($options['percentage'])) {
			if ($options['percentage'] < 0) $options['percentage'] = 1;
			if ($options['percentage'] > 100) $options['percentage'] = 100;
		} else { $options['percentage'] = 50; }
		while (strlen($options['start']) < 5) $options['start'] = "0" . $options['start'];
		while (strlen($options['end']) < 5) $options['end'] = "0" . $options['end'];
		if (!gmdate('H:i',$options['start'])) $options['start'] = '00:00'; //guarantee a valid time
		if (!gmdate('H:i',$options['end'])) $options['end'] = '23:59';
		$time = explode(":",$options['start']);
		if (strlen($time[0]) < 2) $time[0] = '0' . $time[0];
		if (strlen($time[1]) < 2) $time[1] = '0' . $time[1];
		$options['start'] = date("H:i",mktime($time[0],$time[1],0,9,11,2001)); // convert overruns
		$time = explode(":",$options['end']);
		if (strlen($time[0]) < 2) $time[0] = '0' . $time[0];
		if (strlen($time[1]) < 2) $time[1] = '0' . $time[1];
		$options['end'] = date("H:i",mktime($time[0],$time[1],0,9,11,2001));

		// store the option values under the plugin filename
		update_option(basename(__FILE__, ".php"), $options);
		
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Options saved', 'auto_schedule_posts') . '</p></div>';
	} else if (isset($_POST['attempt_publish'])) {
		drip_publish();
		
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Attempted to publish a currently scheduled post', 'auto_schedule_posts') . '</p></div>';
		$options = get_option(basename(__FILE__, ".php"));
	} else if (isset($_POST['mass_publish'])) {
		$wpdb->query("UPDATE $wpdb->posts SET post_status = 'publish' where post_status = 'auto-schedule';");
		
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Published all queued posts', 'auto_schedule_posts') . '</p></div>';
		$options = get_option(basename(__FILE__, ".php"));
	} else if (isset($_POST['CPR'])) {
		wp_clear_scheduled_hook('asp_pulse');
		wp_schedule_event(mktime(date('H')+1,0,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,5,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,10,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,15,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,20,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,25,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,30,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,35,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,40,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,45,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,50,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		wp_schedule_event(mktime(date('H')+1,55,0,date('m'),date('d'),date('Y')), 'hourly', 'asp_pulse');
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Restarted Pulse', 'auto_schedule_posts') . '</p></div>';
		$options = get_option(basename(__FILE__, ".php"));
	} else {
		$gmtoffset = (int) (3600 * ((double) get_option('gmt_offset')));
		if ($action == 'manage') {
			if (isset($_POST['manually_publish'])) {
				$asp_post = array();
					$asp_post['ID'] = $id;
					$asp_post['post_date_gmt'] = gmdate('Y-m-d H:i:s', time() - 10);
					$asp_post['post_date'] = gmdate('Y-m-d H:i:s', time() + $gmtoffset - 10);
					$asp_post['post_status'] = 'publish';
				$id = wp_update_post($asp_post);

				// Show a message to say we've done something
				echo '<div class="updated"><p>'.__('Forced Publish of post ID', 'auto_schedule_posts') . ' '.$id.'</p></div>';
			} elseif (isset($_POST['remove'])) {
				wp_delete_post($id);

				// Show a message to say we've done something
				echo '<div class="updated"><p>'.__('Trashed post ID', 'auto_schedule_posts') . ' '.$id.'</p></div>';
			}
		}
		// If we are just displaying the page we first load up the options array
		$options = get_option(basename(__FILE__, ".php"));
	}
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php echo ucwords(str_replace('-', ' ', basename(__FILE__, ".php"). __(' Options', 'auto_schedule_posts'))); ?></h2>
		<h3><a href="http://plugins.davidjmiller.org/auto-schedule-posts/"><?php _e('Help and Instructions', 'auto_schedule_posts') ?></a></h3>
<?php 
		$gmtoffset = (int) (3600 * ((double) get_option('gmt_offset')));
		echo '<h5>Now: ' . gmdate('Y-m-d H:i:s', time() + $gmtoffset) . '<br/>Next: ' . gmdate('Y-m-d H:i:s', wp_next_scheduled('asp_pulse') + $gmtoffset) . '<br/>';
		$open = explode(":",$options['start']);
		$close = explode(":",$options['end']);
		$interval = $options['interval'] * 60;
		$open = mktime((double)$open[0],(double)$open[1]);
		$close = mktime((double)$close[0],(double)$close[1]);
		$bump = 0;
		if ( (date("H") + get_option('gmt_offset')) > 23 ) $bump = -24;
		if ( (date("H") + get_option('gmt_offset')) < 0 ) $bump = 24;
		$tod = mktime(date("H") + get_option('gmt_offset' + $bump),date("i"));
//		echo '<h5>Open: ' . gmdate('H:i', $open) . '<br/>Close: ' . gmdate('H:i', $close) . '<br/>Current: ' . gmdate('H:i', $tod) . '<br/>';
		$graveyard = 'false';
		$attempt = 'true';
		if ($close < $open) $graveyard = 'true';
//if ($graveyard == 'true') { echo 'graveyard<br/>'; } else { echo 'regular<br/>'; }
//	if (($tod > $open) && ($graveyard == 'true')) echo 'Too late for graveyard<br/>';
//	if (($tod < $close) && ($graveyard == 'true')) echo 'Too early for graveyard<br/>';
//	if (($tod < $open) && ($graveyard == 'false')) echo 'Too early for business<br/>';
//	if (($tod > $close) && ($graveyard == 'false')) echo 'Too late for business<br/>';
		if (($tod > $open) && ($tod < $close) && ($graveyard == 'true')) $attempt = 'false';
		if (($tod < $open) || ($tod > $close) && ($graveyard == 'false')) $attempt = 'false';
		echo 'We should ';
		if ($attempt == 'false') echo 'not ';
		echo 'be open to publish now</h5>';
		$published = $wpdb->get_results("SELECT MAX(post_date_gmt) as pt from $wpdb->posts where $wpdb->posts.post_status = 'publish'");
		foreach ($published as $last)  $lp = strtotime($last->pt);
//echo '<h5>interval is set to ' . $options['interval'] . ' minutes</h5>';
//echo '<h5>Publishing should next be an option at ' . gmdate('H:i:s', $lp + $interval) . '</h5><br/>';
//echo gmdate('H:i:s', time()) . '<br/>' . gmdate('H:i:s', $lp + $interval + $gmtoffset) . '<br/>';
		if (time() < ($lp + $interval)) {
			$attempt = 'false'; // not time yet
//echo '<h5>Most recent publication was at ' . gmdate('H:i:s', $lp + $gmtoffset) . ' publishing would be held now</h5><br/>';
		} else {
			$attempt = 'true'; // not time yet
//echo '<h5>Most recent publication was at ' . gmdate('H:i:s', $lp + $gmtoffset) . ' this would publish now</h5><br/>';
		}
?>
		<form method="post" action="">
		<fieldset class="options">
		<table class="optiontable">
<!--			<tr valign="top">
				<th scope="row" align="right"><?php _e('Scheduler Type', 'auto_schedule_posts') ?>:</th>
				<td>
					<select name="method" id="method">
						<option value="set"<?php if ($options['method'] == 'set') echo ' selected'; ?>><?php _e('Set Interval', 'auto_schedule_posts') ?></option>
						<option value="min"<?php if ($options['method'] == 'min') echo ' selected'; ?>><?php _e('Minimum Interval', 'auto_schedule_posts') ?></option>
					</select> <?php _e('"Set Interval" means if a post time is missed nothing posts until the next interval', 'auto_schedule_posts') ?>
				</td>
			</tr>
-->			<tr valign="top">
				<th scope="row" align="right"><?php _e('Interval', 'auto_schedule_posts') ?>:</th>
				<td><input name="interval" type="text" id="interval" value="<?php echo $options['interval']; ?>" size="10" /> <?php _e('Enter a value in minutes', 'auto_schedule_posts') ?>
					<input type="hidden" name="lpa" id="lpa" value="<?php echo $options['lpa']; ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Randomize Posting', 'auto_schedule_posts') ?>:</th>
				<td colspan="2">
					<input type="radio" name="randomize" id="randomize" value="true"<?php if ($options['randomize'] == 'true') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>&nbsp;
					<input type="radio" name="randomize" id="randomize" value="false"<?php if ($options['randomize'] == 'false') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>&nbsp;
					<strong><?php _e('Posting probability', 'auto_schedule_posts') ?></strong><?php _e(' (if randomizing)', 'auto_schedule_posts') ?>:&nbsp; 
					<input name="percentage" type="text" id="percentage" value="<?php echo $options['percentage']; ?>" size="5" />%
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Schedule Priority', 'auto_schedule_posts') ?>:</th>
				<td>
					<select name="priority" id="priority">
						<option value="old"<?php if ($options['priority'] == 'old') echo ' selected'; ?>><?php _e('Oldest Unpublished Post', 'auto_schedule_posts') ?></option>
						<option value="rand"<?php if ($options['priority'] == 'rand') echo ' selected'; ?>><?php _e('Random Post from Queue', 'auto_schedule_posts') ?></option>
						<option value="lra"<?php if ($options['priority'] == 'lra') echo ' selected'; ?>><?php _e('Least Recent Author (LRA)', 'auto_schedule_posts') ?></option>
						<option value="rlra"<?php if ($options['priority'] == 'rlra') echo ' selected'; ?>><?php _e('Random Post from LRA', 'auto_schedule_posts') ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Include Manually Scheduled Posts', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="catch_future" id="catch_future" value="true"<?php if ($options['catch_future'] == 'true') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="catch_future" id="catch_future" value="false"<?php if ($options['catch_future'] != 'true') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Start Time', 'auto_schedule_posts') ?>:</th>
				<td><input name="start" type="text" id="start" value="<?php echo $options['start']; ?>" size="10" /><?php _e('Military time (defaults to 00:00)', 'auto_schedule_posts') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('End Time', 'auto_schedule_posts') ?>:</th>
				<td><input name="end" type="text" id="end" value="<?php echo $options['end']; ?>" size="10" /><?php _e('Military time (defaults to 23:59)', 'auto_schedule_posts') ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Posting Days Type', 'auto_schedule_posts') ?>:</th>
				<td>
					<select name="day_type" id="day_type">
						<option value="daily"<?php if ($options['day_type'] != 'days') echo ' selected'; ?>><?php _e('Daily', 'auto_schedule_posts') ?></option>
						<option value="days"<?php if ($options['day_type'] == 'days') echo ' selected'; ?>><?php _e('Specific Days', 'auto_schedule_posts') ?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="center" colspan="2"><?php _e('Days to publish (if not daily)', 'auto_schedule_posts') ?>:</th>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Sunday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="sunday" id="sunday" value="yes"<?php if ($options['sunday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="sunday" id="sunday" value="no"<?php if ($options['sunday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Monday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="monday" id="monday" value="yes"<?php if ($options['monday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="monday" id="monday" value="no"<?php if ($options['monday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Tuesday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="tuesday" id="tuesday" value="yes"<?php if ($options['tuesday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="tuesday" id="tuesday" value="no"<?php if ($options['tuesday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Wednesday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="wednesday" id="wednesday" value="yes"<?php if ($options['wednesday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="wednesday" id="wednesday" value="no"<?php if ($options['wednesday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Thursday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="thursday" id="thursday" value="yes"<?php if ($options['thursday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="thursday" id="thursday" value="no"<?php if ($options['thursday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Friday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="friday" id="friday" value="yes"<?php if ($options['friday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="friday" id="friday" value="no"<?php if ($options['friday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" align="right"><?php _e('Saturday', 'auto_schedule_posts') ?>:</th>
				<td>
					<input type="radio" name="saturday" id="saturday" value="yes"<?php if ($options['saturday'] != 'no') echo ' checked'; ?>><?php _e('Yes', 'auto_schedule_posts') ?></input>
					<input type="radio" name="saturday" id="saturday" value="no"<?php if ($options['saturday'] == 'no') echo ' checked'; ?>><?php _e('No', 'auto_schedule_posts') ?></input>
				</td>
			</tr>
		</table>
		</fieldset>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Update', 'auto_schedule_posts') ?>"  style="font-weight:bold;" />
<!--				 <input type="submit" name="CPR" value="<?php _e('Resurrect', 'auto_schedule_posts') ?>"  style="font-weight:bold;" />
-->		<?php
			$query = "select id, post_title from $wpdb->posts where post_status = 'auto-schedule'";
			$results = $wpdb->get_results($query);
			if (count($results)) {
			?>
				 <input type="submit" name="attempt_publish" value="<?php _e('Publish Now', 'auto_schedule_posts') ?>"  style="font-weight:bold;" />
				 <input type="submit" name="mass_publish" value="<?php _e('Publish All', 'auto_schedule_posts') ?>"  style="font-weight:bold;" />
		</div>
		</form>
		<table class="optiontable" width="80%"><tr><th width="70%">Currently Scheduled</th><th></th><th></th></tr>
			<?php
				foreach($results as $result) {
					echo '<tr><td>'.$result->post_title.'</td>';
edit_post_link( 'Edit Post', '<td>', '</td>', $result->id );
echo '<td><form method="post" action="'.$url.'&manage&'.$result->id.'" style="margin: -25px;"><div class="submit"><input type="submit" name="manually_publish" value="'.__('Publish This', 'auto_schedule_posts').'" />';
echo '<input type="submit" name="remove" value="'.__('Delete This', 'auto_schedule_posts').'" /></div></form></td></tr>';
				}
			?>
				</table>
			<?php
			} else {
				echo '</div></form>';
			}
		?>
	</div>
	<?php	
}

$options = get_option(basename(__FILE__, ".php"));
?>