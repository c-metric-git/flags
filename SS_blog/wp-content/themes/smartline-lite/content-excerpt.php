		
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
		<h2 class="post-title entry-title"><a href="<?php esc_url(the_permalink()) ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		
		<div class="postmeta"><?php smartline_display_postmeta(); ?></div>

		<div class="entry clearfix">
			<?php smartline_display_thumbnail_index(); ?>
			<?php the_excerpt(); ?>
			<a href="<?php esc_url(the_permalink()) ?>" class="more-link"><?php _e('&raquo; Read more', 'smartline-lite'); ?></a>
		</div>
		
		<div class="postinfo clearfix"><?php smartline_display_postinfo(); ?></div>

	</article>