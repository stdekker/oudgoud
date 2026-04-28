<?php
/**
 * Template Name: Nieuws-pagina
 * A custom page template with sidebar.
 */

get_header(); ?>
	
	<div id="primary">
		<?php get_template_part( 'loop', 'news' ); ?>
	</div>
	
<?php get_footer(); ?>