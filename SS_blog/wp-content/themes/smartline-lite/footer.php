	<?php do_action('smartline_before_footer'); ?>
	
	<footer id="footer" class="clearfix" role="contentinfo">
		
		<?php // Display Footer Navigation
		if ( has_nav_menu( 'footer' ) ) : ?>
		
		<nav id="footernav" class="clearfix" role="navigation">
			<?php wp_nav_menu(	array(
				'theme_location' => 'footer', 
				'container' => false, 
				'menu_id' => 'footernav-menu', 
				'fallback_cb' => '', 
				'depth' => 1)
			);
			?>
			<h5 id="footernav-icon"><?php _e('Menu', 'smartline-lite'); ?></h5>
		</nav>
		
		<?php endif; ?>
		
		<div id="footer-text">
			
			<?php do_action('smartline_footer_text'); ?>
		
		</div>
	</footer>
	<div style="width:100%"><img src="https://www.stripedsocks.com/blog/wp-content/uploads/2015/09/footer.png"></div>

</div><!-- end #wrapper -->

<?php wp_footer(); ?>
</body>
</html>