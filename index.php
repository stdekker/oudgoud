<?php
/**
 * The main template file.
 */

	get_header(); ?>
	
	<main class="main-layout main-layout--sidebar">
	<section id="primary" class="post-grid">
		<?php get_template_part( 'loop', 'news' ); ?>
	</section>
	
	<?php get_sidebar(); ?>

</main>
<?php get_footer(); ?>