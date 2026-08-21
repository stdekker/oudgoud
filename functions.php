<?php
/**
 * Oudgoud block theme setup.
 */

if ( ! isset( $content_width ) ) {
	$content_width = 720;
}

/**
 * Register theme features shared by the front end and block editors.
 */
function haven_setup() {
	load_theme_textdomain( 'oudgoud', get_template_directory() . '/languages' );

	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'wp-block-styles' );
	remove_theme_support( 'core-block-patterns' );

	add_editor_style( 'style.css' );

	add_image_size( 'featured-full', 720, 9999 );
	add_image_size( 'featured-short', 360, 240, true );
}
add_action( 'after_setup_theme', 'haven_setup' );

/**
 * Register Oudgoud block styles.
 */
function haven_register_block_styles() {
	$torn_paper_style = array(
		'name'  => 'torn-paper',
		'label' => __( 'Gescheurd papier', 'oudgoud' ),
	);
	$small_panorama_style = array(
		'name'  => 'small-panorama',
		'label' => __( 'Klein panorama', 'oudgoud' ),
	);

	register_block_style( 'core/image', $torn_paper_style );
	register_block_style( 'core/cover', $torn_paper_style );
	register_block_style( 'core/cover', $small_panorama_style );
}
add_action( 'init', 'haven_register_block_styles' );

/**
 * Load the theme stylesheet on top of Global Styles.
 */
function haven_enqueue_assets() {
	$stylesheet_path          = get_stylesheet_directory() . '/style.css';
	$stylesheet_version       = file_exists( $stylesheet_path ) ? (string) filemtime( $stylesheet_path ) : wp_get_theme()->get( 'Version' );
	$header_navigation_path   = get_template_directory() . '/js/header-navigation.js';
	$header_navigation_version = file_exists( $header_navigation_path ) ? (string) filemtime( $header_navigation_path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'oudgoud-style',
		get_stylesheet_uri(),
		array(),
		$stylesheet_version
	);

	wp_style_add_data( 'oudgoud-style', 'path', $stylesheet_path );

	wp_enqueue_script(
		'oudgoud-header-navigation',
		get_template_directory_uri() . '/js/header-navigation.js',
		array(),
		$header_navigation_version,
		true
	);
	wp_script_add_data( 'oudgoud-header-navigation', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'haven_enqueue_assets' );

/**
 * Load editor-only compatibility fixes.
 */
function haven_enqueue_block_editor_assets() {
	$script_path = get_template_directory() . '/js/block-editor.js';
	$version     = file_exists( $script_path ) ? (string) filemtime( $script_path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_script(
		'oudgoud-block-editor',
		get_template_directory_uri() . '/js/block-editor.js',
		array( 'wp-blocks', 'wp-data', 'wp-dom-ready', 'wp-element', 'wp-hooks' ),
		$version,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'haven_enqueue_block_editor_assets' );

/**
 * Allow only locally registered Oudgoud block patterns.
 *
 * Synced patterns and reusable blocks are user content and are not part of the
 * block pattern registry, so they remain available.
 */
function haven_restrict_block_patterns() {
	$registry = WP_Block_Patterns_Registry::get_instance();

	foreach ( $registry->get_all_registered() as $pattern ) {
		$pattern_name = isset( $pattern['name'] ) ? $pattern['name'] : '';

		if ( 0 !== strpos( $pattern_name, 'oudgoud/' ) ) {
			unregister_block_pattern( $pattern_name );
		}
	}
}
add_action( 'init', 'haven_restrict_block_patterns', PHP_INT_MAX );

// Prevent patterns from the WordPress.org Pattern Directory being registered.
add_filter( 'should_load_remote_block_patterns', '__return_false' );

/**
 * Remove non-Oudgoud patterns added directly to block editor settings.
 *
 * @param array $settings Editor settings.
 * @return array
 */
function haven_restrict_editor_patterns( $settings ) {
	$pattern_setting_keys = array(
		'__experimentalBlockPatterns',
		'__experimentalAdditionalBlockPatterns',
	);

	foreach ( $pattern_setting_keys as $setting_key ) {
		if ( empty( $settings[ $setting_key ] ) || ! is_array( $settings[ $setting_key ] ) ) {
			continue;
		}

		$settings[ $setting_key ] = array_values(
			array_filter(
				$settings[ $setting_key ],
				static function ( $pattern ) {
					return isset( $pattern['name'] ) && 0 === strpos( $pattern['name'], 'oudgoud/' );
				}
			)
		);
	}

	return $settings;
}
add_filter( 'block_editor_settings_all', 'haven_restrict_editor_patterns', 100 );

/**
 * Keep file-defined template structure locked in the Site Editor.
 * Navigation menu content remains editable through the Navigation screen.
 *
 * @param array                   $settings Editor settings.
 * @param WP_Block_Editor_Context $context  Current editor context.
 * @return array
 */
function haven_lock_site_editor_templates( $settings, $context ) {
	if ( isset( $context->name ) && 'core/edit-site' === $context->name ) {
		$settings['canLockBlocks'] = false;
	}

	return $settings;
}
add_filter( 'block_editor_settings_all', 'haven_lock_site_editor_templates', 10, 2 );
