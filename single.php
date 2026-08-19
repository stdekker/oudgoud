<?php
/**
 * The Template for displaying all single posts.
 */

get_header(); ?>

<div id="primary">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
			</div><!-- .entry-meta -->
		</header>
						
					<div class="entry-content">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
					
					</article><!-- #post-## -->
					<nav id="nav-below" class="articles" aria-label="<?php esc_attr_e( 'Vorige en volgende berichten', 'oudgoud' ); ?>">
					 <span class="ni prev-posts">
						<?php previous_post_link('%link', '%title'); ?>
					</span>
					<span class="ni next-posts">
						<?php next_post_link( '%link', '%title'); ?>
					</span>
				</nav><!-- #nav-below -->
				<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>
</div>

<?php get_footer(); ?>
