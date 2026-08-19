<?php
/**
 * The template for displaying Tag Archive pages.
 *
 * @package WordPress
 * @subpackage Oudgoud
 */

get_header(); ?>

<div id="primary">
					<h1 class="archive-title"><?php
					printf( __( 'Tag Archives: %s', 'oudgoud' ), single_tag_title( '', false ) );
				?></h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
	 get_template_part( 'loop', 'tag' );
?>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
