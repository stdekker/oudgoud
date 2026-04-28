<?php
/**
 * The template for displaying all pages.
 */

get_header(); ?>
<section id="primary">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-content">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-## -->
<?php endwhile; ?>
</section>
<?php get_footer(); ?>