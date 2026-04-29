<?php
/**
 * Template Name: Met zijbalk
 * A custom page template with sidebar.
 */

get_header(); ?>

<section id="primary" class="sidebar">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( is_front_page() ) { ?>
					<h2 class="entry-title"><?php the_title(); ?></h2>
				<?php } else { ?>	
					<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php } ?>
					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'oudgoud' ), 'after' => '' ) ); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-## -->
<?php endwhile; ?>
</section>

<?php 
		get_sidebar();
		get_footer(); 
?>