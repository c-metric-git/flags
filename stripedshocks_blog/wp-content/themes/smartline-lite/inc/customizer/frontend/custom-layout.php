<?php 
add_action('wp_head', 'smartline_css_layout');
function smartline_css_layout() {
	
	// Get Theme Options from Database
	$theme_options = smartline_theme_options();
		
	// Switch Sidebar to left
	if ( isset($theme_options['layout']) and $theme_options['layout'] == 'left-sidebar' ) :
	
		echo '<style type="text/css">
			@media only screen and (min-width: 60em) {
				#content {
					float: right;
				}
				#sidebar {
					margin-left: 0;
					margin-right: 70%;
					background: -moz-linear-gradient(left, #f3f3f3 0%, #e6e6e6 100%); /* FF3.6+ */
					background: -webkit-gradient(linear, left top, right top, color-stop(0%,#f3f3f3), color-stop(100%,#e6e6e6)); /* Chrome,Safari4+ */
					background: -webkit-linear-gradient(left, #f3f3f3 0%,#e6e6e6 100%); /* Chrome10+,Safari5.1+ */
					background: -o-linear-gradient(left, #f3f3f3 0%,#e6e6e6 100%); /* Opera 11.10+ */
					background: -ms-linear-gradient(left, #f3f3f3 0%,#e6e6e6 100%); /* IE10+ */
					background: linear-gradient(to right, #f3f3f3 0%,#e6e6e6 100%); /* W3C */
				}
					
			}
			@media only screen and (max-width: 70em) {
				#sidebar {
					margin-right: 67%;
				}
			}
		</style>';
	
	endif;
	
}