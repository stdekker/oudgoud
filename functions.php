<?php
/**
 * Oudgoud theme functions
 * Theme setup and helper functions.
 */ 
 

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */ 
  
if ( ! isset( $content_width ) )
	$content_width = 678;

/** Tell WordPress to run haven_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'haven_setup' );

if ( ! function_exists( 'haven_setup' ) ):

/* Specific settings for this theme */

function haven_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	load_theme_textdomain( 'oudgoud', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation','oudgoud' ),
                'footer' => __( 'Footer Navigation','oudgoud' ),
	));
	
	add_image_size( 'featured-full', 678, 9999 );
	add_image_size( 'featured-short', 311, 311, true);
	
}
endif;

function haven_enqueue_assets() {
	$font_stylesheet_path = get_template_directory() . '/css/fonts.css';
	$theme_stylesheet_path = get_stylesheet_directory() . '/style.css';

	wp_enqueue_style(
		'haven-fonts',
		get_template_directory_uri() . '/css/fonts.css',
		array(),
		filemtime( $font_stylesheet_path )
	);

	wp_enqueue_style(
		'haven-style',
		get_stylesheet_uri(),
		array( 'haven-fonts' ),
		filemtime( $theme_stylesheet_path )
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'haven_enqueue_assets' );

// Puts link in excerpts more tag
function haven_excerpt_more($more) {
		global $post;
		return '<a class="more-link" href="' . esc_url( get_permalink( $post->ID ) ) . '">&rsaquo; Meer</a>';
}

add_filter('excerpt_more', 'haven_excerpt_more');

/**
 * Sets the post excerpt length to 40 characters.
 */
function haven_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'haven_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
 
function haven_continue_reading_link() {
	return ' <a href="' . esc_url( get_permalink() ) . '" class="more-link">' . __( '&rsaquo; Meer', 'oudgoud' ) . '</a>';
}

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 */
 
function haven_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= haven_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'haven_custom_excerpt_more' );

if ( ! function_exists( 'haven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 */
 
function haven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'comment' :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>">
			<header class="comment-meta">
					<span class="comment-author"><?php comment_author_link(); ?></span>
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php
						/* translators: 1: date, 2: time */
						printf( __( '%1$s om %2$s', 'oudgoud' ), get_comment_date(), get_comment_time() );
						?>
					</time>
				</a>
				<?php edit_comment_link( __( 'Edit', 'oudgoud' ), '<span class="edit-link">', '</span>' ); ?>
				<span class="reply-link">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</span>
			</header>
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation"><?php esc_html_e( 'Je bericht moet nog worden goedgekeurd.', 'oudgoud' ); ?></em>
			<?php endif; ?>
			<div class="comment-body"><?php comment_text(); ?></div>
		</article><!-- #comment-##  -->
	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'oudgoud' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'oudgoud'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;

/**
 * Register the two sidebar widget areas used by sidebar.php.
 */
 
function haven_widgets_init() {
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'oudgoud' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'oudgoud' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	));

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Secondary Widget Area', 'oudgoud' ),
		'id' => 'secondary-widget-area',
		'description' => __( 'The secondary widget area', 'oudgoud' ),
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	));
}
/* Register sidebars by running haven_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'haven_widgets_init' );

if ( ! function_exists( 'haven_posted_on' ) ) :
/**
 * Prints HTML with date archive meta links for the current post.
 */
 
function haven_posted_on() {
	// BP: slight modification to Twenty Ten function, converting single permalink to multi-archival link
	// Y = 2012
	// F = September
	// m = 01–12
	// j = 1–31
	// d = 01–31
	printf( __( '<time datetime="%5$s">%3$s %2$s %4$s</time>', 'oudgoud' ),
		// %1$s = container class
		'meta-prep meta-prep-author',
		// %2$s = month: /yyyy/mm/
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			esc_url( get_month_link( get_the_date( 'Y' ), get_the_date( 'm' ) ) ),
			esc_attr( 'View Archives for ' . get_the_date( 'F' ) . ' ' . get_the_date( 'Y' ) ),
			get_the_date( 'F' )
		),
		// %3$s = day: /yyyy/mm/dd/
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			esc_url( get_day_link( get_the_date( 'Y' ), get_the_date( 'm' ), get_the_date( 'd' ) ) ),
			esc_attr( 'View Archives for ' . get_the_date( 'F' ) . ' ' . get_the_date( 'j' ) . ' ' . get_the_date( 'Y' ) ),
			get_the_date( 'j' )
		),
		// %4$s = year: /yyyy/
			sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
				esc_url( get_year_link( get_the_date( 'Y' ) ) ),
				esc_attr( 'View Archives for ' . get_the_date( 'Y' ) ),
				get_the_date( 'Y' )
			),
			esc_attr( get_the_date( 'c' ) )
		);
}
endif;

if ( ! function_exists( 'haven_posted_in' ) ) :
/**
 * Prints HTML with meta information for the current post (category, tags and permalink).
 */
 
function haven_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'oudgoud' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'oudgoud' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark">permalink</a>.', 'oudgoud' );
	}
	
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		esc_url( get_permalink() ),
		the_title_attribute( array( 'echo' => false ) )
	);
}
endif;

// change Search Form input type from "text" to "search" and add placeholder text
	function haven_search_form ( $form ) {
		$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/' ) ) . '" >
		<div><label class="screen-reader-text" for="s">' . esc_html__( 'Search for:', 'oudgoud' ) . '</label>
		<input type="search" placeholder="' . esc_attr__( 'Search for...', 'oudgoud' ) . '" value="' . esc_attr( get_search_query() ) . '" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', 'oudgoud' ) .'" />
		</div>
		</form>';
		return $form;
	}
	add_filter( 'get_search_form', 'haven_search_form' );


function haven_page_nav( $id = null ) {
?>
			<nav <?php if ( $id ) { printf( 'id="%s"', esc_attr( $id ) ); } ?> class="page-navigation" aria-label="<?php esc_attr_e( 'Nieuwspaginering', 'oudgoud' ); ?>">
		<div class="next-posts ni"><?php next_posts_link('&laquo; Ouder nieuws'); ?></div>
		<div class="previous-posts ni"><?php previous_posts_link('Nieuwer nieuws &raquo;'); ?></div>
		</nav>
	
<?php

}

function haven_render_news_article( $post, $format = 'full' ) { ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'news-' . $format ); ?>>
		<header>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'oudgoud' ), the_title_attribute( array( 'echo' => false ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<div class="entry-meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php the_time( "j F `y" ); ?></time></div>
		</header>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="featured">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'featured-' . $format ); ?>
				</a>
			</figure>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			switch ( $format ) {
				case 'full':
					the_content( '&raquo; Lees meer &hellip;' );
					break;

				case 'short':
					the_excerpt();
					break;
			}
			?>
		</div>
	</article>
<?php }

?>
