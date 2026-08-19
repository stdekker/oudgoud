<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Oudgoud
 */

get_header(); ?>
<div id="primary">
	<?php if ( have_posts() ) : ?>
					<h1><?php printf( __( 'Zoekresultaten voor: %s', 'oudgoud' ), esc_html( get_search_query() ) ); ?></h1>
					<?php get_template_part( 'loop', 'search' ); ?>
	<?php else : ?>
						<h1>Pagina niet gevonden</h1>
						<p>We vinden het heel vervelend, maar we kunnen niet vinden wat je zoekt. Misschien dat ons zoekformulier je verder kan helpen.</p>
						<?php get_search_form(); ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
