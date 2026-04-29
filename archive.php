<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Oudgoud
 */

get_header(); ?>
<section id="primary">
<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	if ( have_posts() )
		the_post();
?>
					<h1 class="page-title"><?php
						if ( is_day() ) :
							printf( __( 'Daily Archives: %s', 'oudgoud' ), get_the_date() );
						elseif ( is_month() ) :
							printf( __( 'Monthly Archives: %s', 'oudgoud' ), get_the_date('F Y') );
						elseif ( is_year() ) :
							printf( __( 'Yearly Archives: %s', 'oudgoud' ), get_the_date('Y') );
						else :
							_e( 'Blog Archives', 'oudgoud' );
						endif;
					?></h1>
<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	if ( is_year() || is_month() ) :
		$archive_query_args = array(
			'post_type'           => get_query_var( 'post_type' ) ? get_query_var( 'post_type' ) : 'post',
			'year'                => (int) get_query_var( 'year' ),
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => true,
		);

		if ( is_month() ) {
			$archive_query_args['monthnum'] = (int) get_query_var( 'monthnum' );
		}

		$date_archive_query = new WP_Query( $archive_query_args );
		?>
		<ul class="date-archive-list">
		<?php if ( $date_archive_query->have_posts() ) : ?>
			<?php while ( $date_archive_query->have_posts() ) : $date_archive_query->the_post(); ?>
				<li>
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
					&ndash;
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile; ?>
		<?php else : ?>
			<li><?php esc_html_e( 'No posts found for this date archive.', 'oudgoud' ); ?></li>
		<?php endif; ?>
		</ul>
		<?php
		wp_reset_postdata();
	else :
		/* Run the loop for the archives page to output the posts.
		 * If you want to overload this in a child theme then include a file
		 * called loop-archives.php and that will be used instead.
		 */
		get_template_part( 'loop', 'archive' );
	endif;
?>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>