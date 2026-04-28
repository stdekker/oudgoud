<?php
/**
 * The Template for displaying all single posts.
 */

get_header(); ?>

<section id="primary">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
					
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<section class="entry-meta">
		<?php the_time('j F Y'); ?>
		</section><!-- .entry-meta -->
						
					<div class="entry-content">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
					

					<footer class="entry-utility">
					</footer><!-- .entry-utility -->
					
				</article><!-- #post-## -->
				<nav id="nav-below" class="articles">
					 <span class="ni prev-posts">
						<?php previous_post_link('%link', '%title'); ?>
					</span>
					<span class="ni next-posts">
						<?php next_post_link( '%link', '%title'); ?>
					</span>
				</nav><!-- #nav-below -->
				<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>
</section>

<?php get_footer(); ?>
