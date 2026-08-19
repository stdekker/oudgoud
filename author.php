<?php
/**
 * The template for displaying Author Archive pages.
 *
 * @package WordPress
 * @subpackage Oudgoud
 */

get_header(); ?>

<div id="primary">
<?php
	/* Queue the first post, that way we know who
	 * the author is when we try to get their name,
	 * URL, description, avatar, etc.
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	if ( have_posts() )
		the_post();
?>

					<h1 class="archive-title"><?php printf( __( 'Author Archives: %s', 'oudgoud' ), "<a class='url fn n' href='" . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . esc_html( get_the_author() ) . "</a>" ); ?></h1>

<?php
// If a user has filled out their description, show a bio on their entries.
if ( get_the_author_meta( 'description' ) ) : ?>
	<section class="author-info" aria-labelledby="author-info-title">
								<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'oudgoud_author_bio_avatar_size', 60 ) ); ?>
								<h2 id="author-info-title"><?php printf( esc_html__( 'About %s', 'oudgoud' ), esc_html( get_the_author() ) ); ?></h2>
								<?php the_author_meta( 'description' ); ?>
	</section>
<?php endif; ?>

<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	/* Run the loop for the author archive page to output the authors posts
	 * If you want to overload this in a child theme then include a file
	 * called loop-author.php and that will be used instead.
	 */
		 get_template_part( 'loop', 'author' );
?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
