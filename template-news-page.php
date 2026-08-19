<?php
/**
 * Template Name: Nieuws-pagina
 * A custom page template with sidebar.
 */

get_header(); ?>
	
		<div id="primary">
			<h1 class="screen-reader-text"><?php the_title(); ?></h1>
			<?php get_template_part( 'loop', 'news' ); ?>
		</div>
	
<?php get_footer(); ?>
