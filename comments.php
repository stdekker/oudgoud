<?php
/**
 * The template for displaying Comments.
 */
?>

<?php if ( post_password_required() ) : ?>
				<p><?php esc_html_e( 'Dit bericht is beschermd met een wachtwoord.', 'oudgoud' ); ?></p>
<?php
		return;
	endif;
?>

<?php if ( have_comments() ) : ?>
<section id="comments">
			<h3 id="comments-title"><?php
				printf( _n( 'Een reactie op %2$s', '%1$s reacties op %2$s', get_comments_number(), 'oudgoud' ),
				number_format_i18n( get_comments_number() ), '' . get_the_title() . '' );
			?></h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<?php previous_comments_link( __( '&larr; Older Comments', 'oudgoud' ) ); ?>
				<?php next_comments_link( __( 'Newer Comments &rarr;', 'oudgoud' ) ); ?>
<?php endif; // check for comment navigation ?>

			<ol class="comment-list">
				<?php wp_list_comments( array( 'callback' => 'haven_comment' ) );	?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<?php previous_comments_link( __( '&larr; Older Comments', 'oudgoud' ) ); ?>
				<?php next_comments_link( __( 'Newer Comments &rarr;', 'oudgoud' ) ); ?>
<?php endif; // check for comment navigation ?>
 </section>
<?php endif; // end have_comments() ?>

<?php comment_form(); ?>