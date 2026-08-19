<?php
/**
 * The main template file.
 */

	get_header(); ?>
	
	<div class="main-layout main-layout--sidebar">
	<div id="primary" class="post-grid">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Nieuws', 'oudgoud' ); ?></h1>
		<?php get_template_part( 'loop', 'news' ); ?>
	</div>
	
	<?php get_sidebar(); ?>

	</div>
<?php get_footer(); ?>
