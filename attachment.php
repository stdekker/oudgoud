<?php
/**
 * The template for displaying attachments.
 *
 * @package WordPress
 * @subpackage Oudgoud
 */

get_header(); ?>
<div id="primary">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php if ( ! empty( $post->post_parent ) ) : ?>
					<p class="page-title"><a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'Return to %s', 'oudgoud' ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery"><?php
						/* translators: %s - title of parent post */
						printf( __( '<span class="meta-nav">&larr;</span> %s', 'oudgoud' ), get_the_title( $post->post_parent ) );
					?></a></p>
					<?php endif; ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						<div class="entry-meta">
							<?php
								printf(
									__( 'Published %s', 'oudgoud' ),
									sprintf(
										'<time datetime="%1$s">%2$s</time>',
										esc_attr( get_the_date( 'c' ) ),
										esc_html( get_the_date() )
									)
								);
							if ( wp_attachment_is_image() ) {
								echo ' | ';
								$metadata = wp_get_attachment_metadata();
								printf( __( 'Full size is %s pixels', 'oudgoud'),
									sprintf( '<a href="%1$s" title="%2$s">%3$s &times; %4$s</a>',
										esc_url( wp_get_attachment_url() ),
										esc_attr( __('Link to full-size image', 'oudgoud') ),
										$metadata['width'],
										$metadata['height']
									)
								);
							}
						?>
							<?php edit_post_link( __( 'Edit', 'oudgoud' ), '', '' ); ?>
						</div><!-- .entry-meta -->
						</header>
						<div class="entry-content">
						<div class="entry-attachment">
<?php if ( wp_attachment_is_image() ) :
	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID )
			break;
	}
	$k++;
	// If there is more than 1 image attachment in a gallery
	if ( count( $attachments ) > 1 ) {
		if ( isset( $attachments[ $k ] ) )
			// get the URL of the next image attachment
			$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
		else
			// or get the URL of the first image attachment
			$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
	} else {
		// or, if there's only 1 image attachment, get the URL of the image
		$next_attachment_url = wp_get_attachment_url();
	}
?>
							<p><a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
								$attachment_size = apply_filters( 'oudgoud_attachment_size', 900 );
								echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
							?></a></p>
								<nav id="nav-below" class="navigation" aria-label="<?php esc_attr_e( 'Afbeeldingsnavigatie', 'oudgoud' ); ?>">
								<div class="nav-previous"><?php previous_image_link( false ); ?></div>
								<div class="nav-next"><?php next_image_link( false ); ?></div>
							</nav><!-- #nav-below -->
<?php else : ?>
							<a href="<?php echo esc_url( wp_get_attachment_url() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo esc_html( basename( get_permalink() ) ); ?></a>
<?php endif; ?>
						</div><!-- .entry-attachment -->
							<?php if ( ! empty( $post->post_excerpt ) ) : ?>
								<div class="entry-caption"><?php the_excerpt(); ?></div>
							<?php endif; ?>
<?php the_content( __( 'Continue reading &rarr;', 'oudgoud' ) ); ?>
<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'oudgoud' ), 'after' => '' ) ); ?>
						</div><!-- .entry-content -->
							<footer class="entry-utility">
							<?php haven_posted_in(); ?>
							<?php edit_post_link( __( 'Edit', 'oudgoud' ), ' <span class="edit-link">', '</span>' ); ?>
							</footer><!-- .entry-utility -->
					</article>
					<?php comments_template(); ?>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
