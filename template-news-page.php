<?php
/**
 * Template Name: Nieuws-pagina
 * A custom page template with sidebar.
 */

get_header(); ?>
	
	<section id="primary">
		<?php get_template_part( 'loop', 'news' ); ?>
	</section>
	
<?php get_footer(); ?>