<?php
/**
 * The main template file.
 */

	get_header(); ?>
	
	<section id="primary" class="post-grid">
		<?php get_template_part( 'loop', 'news' ); ?>
	</section>
	
	<?php 
	get_sidebar();
	get_footer(); 

	?>