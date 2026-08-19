<?php
/**
 * The template for displaying Category Archive pages.
 */

get_header(); ?>
<div id="primary">

				<h1 class="archive-title"><?php
					printf( __( '%s archief', 'oudgoud' ), '' . single_cat_title( '', false ) . '' );
				?></h1>
	<div id="archive" class="post-grid">
					<?php
						$category_description = category_description();
						if ( ! empty( $category_description ) )
							echo '' . $category_description . '';

					/* Run the loop for the category page to output the posts. */
					
					get_template_part( 'loop', 'category' );
					?>
	</div>
</div>
<?php get_footer(); ?>
