<?php
global $dch_options;
	$rsd = $dch_options['rsdl'];
	$wpg = $dch_options['wp_gen'];
	$gflink = $dch_options['gfl'];
	$wlmf = $dch_options['wlm'];
	$ril = $dch_options['ril'];
	$adj = $dch_options['adj'];
	$pre = $dch_options['prev'];
	$start = $dch_options['str'];
	$cfl = $dch_options['cfl'];
	
function disable_stuff( $data ) {
	return false;
}
if($rsd == true){
remove_action( 'wp_head', 'rsd_link' );
}
if($wpg == true) {
remove_action( 'wp_head', 'wp_generator' );
}
if($gflink == true) {
remove_action( 'wp_head', 'feed_links', 2 );
}
if($wlmf == true) {
remove_action( 'wp_head', 'wlwmanifest_link' );
}
if($ril == true) {
remove_action( 'wp_head', 'index_rel_link' );
}
if($adj == true) {
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
}
if($pre == true) {
remove_action('wp_head', 'previous_post_rel_link', 10, 0);
add_filter( 'previous_post_rel_link', 'disable_stuff' );
remove_action('wp_head', 'next_post_rel_link', 10, 0);
add_filter( 'next_post_rel_link', 'disable_stuff' );
}
if($start == true) {
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
}
if($cfl == true) {
remove_action( 'wp_head', 'feed_links_extra', 3 );
}
if($alk == true) {
remove_action('wp_head', '_ak_framework_meta_tags');
}
?>