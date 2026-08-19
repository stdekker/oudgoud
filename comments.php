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

<?php if ( have_comments() || comments_open() ) : ?>
<section id="comments">
	<?php if ( have_comments() ) : ?>
				<h2 id="comments-title"><?php
				printf( _n( 'Een reactie op %2$s', '%1$s reacties op %2$s', get_comments_number(), 'oudgoud' ),
				number_format_i18n( get_comments_number() ), '' . get_the_title() . '' );
				?></h2>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<nav class="comment-navigation" aria-label="<?php esc_attr_e( 'Reactienavigatie boven', 'oudgoud' ); ?>">
					<?php previous_comments_link( __( '&larr; Older Comments', 'oudgoud' ) ); ?>
					<?php next_comments_link( __( 'Newer Comments &rarr;', 'oudgoud' ) ); ?>
				</nav>
<?php endif; // check for comment navigation ?>

			<ol class="comment-list">
				<?php wp_list_comments( array( 'callback' => 'haven_comment' ) );	?>
			</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
				<nav class="comment-navigation" aria-label="<?php esc_attr_e( 'Reactienavigatie onder', 'oudgoud' ); ?>">
					<?php previous_comments_link( __( '&larr; Older Comments', 'oudgoud' ) ); ?>
					<?php next_comments_link( __( 'Newer Comments &rarr;', 'oudgoud' ) ); ?>
				</nav>
<?php endif; // check for comment navigation ?>
	<?php endif; // end have_comments() ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
<?php endif; ?>
