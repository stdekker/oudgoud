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
	$editor_stylesheet = haven_get_theme_asset( 'style.css' );

	load_theme_textdomain( 'oudgoud', get_template_directory() . '/languages' );

	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'wp-block-styles' );
	remove_theme_support( 'core-block-patterns' );

	add_editor_style(
		add_query_arg( 'ver', $editor_stylesheet['version'], $editor_stylesheet['uri'] )
	);

	add_image_size( 'featured-full', 720, 9999 );
	add_image_size( 'featured-short', 360, 240, true );
}
add_action( 'after_setup_theme', 'haven_setup' );

/**
 * Resolve a theme asset's path, URI, and cache version.
 *
 * Development environments use file modification times. Production and
 * staging use the version declared in style.css, so every release that changes
 * theme assets must update the theme Version header.
 *
 * @param string $relative_path Theme-relative asset path.
 * @return array Asset path, URI, and version.
 */
function haven_get_theme_asset( $relative_path ) {
	static $theme_version = null;

	$relative_path = ltrim( $relative_path, '/' );
	$path          = get_theme_file_path( $relative_path );

	if ( null === $theme_version ) {
		$theme_version = (string) wp_get_theme()->get( 'Version' );
	}

	$version = $theme_version;

	if ( in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) && file_exists( $path ) ) {
		$modified_time = filemtime( $path );

		if ( false !== $modified_time ) {
			$version = (string) $modified_time;
		}
	}

	return array(
		'path'    => $path,
		'uri'     => get_theme_file_uri( $relative_path ),
		'version' => $version,
	);
}

/**
 * Preload fonts used by the above-the-fold header and page title.
 */
function haven_preload_primary_fonts() {
	$font_urls = array(
		get_theme_file_uri( 'css/fonts/arvo-latin-700.woff2' ),
		get_theme_file_uri( 'css/fonts/bevan-latin-400.woff2' ),
	);

	foreach ( $font_urls as $font_url ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $font_url )
		);
	}
}
add_action( 'wp_head', 'haven_preload_primary_fonts', 1 );

/**
 * Register Oudgoud block styles.
 */
function haven_register_block_styles() {
	$block_styles = array(
		'torn-paper'    => array(
			'blocks' => array( 'core/image', 'core/cover' ),
			'label'  => __( 'Gescheurd papier', 'oudgoud' ),
		),
		'small-panorama' => array(
			'blocks' => array( 'core/cover' ),
			'label'  => __( 'Klein panorama', 'oudgoud' ),
		),
		'mobile-scroll'  => array(
			'blocks' => array( 'core/query' ),
			'label'  => __( 'Mobiel scrollen', 'oudgoud' ),
		),
	);

	foreach ( $block_styles as $style_name => $block_style ) {
		foreach ( $block_style['blocks'] as $block_name ) {
			register_block_style(
				$block_name,
				array(
					'name'  => $style_name,
					'label' => $block_style['label'],
				)
			);
		}
	}
}
add_action( 'init', 'haven_register_block_styles' );

/**
 * Load the theme stylesheet on top of Global Styles.
 */
function haven_enqueue_assets() {
	$stylesheet        = haven_get_theme_asset( 'style.css' );
	$header_navigation = haven_get_theme_asset( 'js/header-navigation.js' );

	wp_enqueue_style(
		'oudgoud-style',
		$stylesheet['uri'],
		array(),
		$stylesheet['version']
	);

	// Prevent server-rendered submenu lists from painting before Navigation CSS.
	wp_add_inline_style(
		'oudgoud-style',
		'.site-navigation .wp-block-navigation__submenu-container{display:none}'
	);

	wp_style_add_data( 'oudgoud-style', 'path', $stylesheet['path'] );

	wp_enqueue_script(
		'oudgoud-header-navigation',
		$header_navigation['uri'],
		array(),
		$header_navigation['version'],
		true
	);
	wp_script_add_data( 'oudgoud-header-navigation', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'haven_enqueue_assets' );

/**
 * Load block-editor features required by the current editing context.
 */
function haven_enqueue_block_editor_assets() {
	$news_cards = haven_get_theme_asset( 'js/news-cards-query-variation.js' );
	$wide_image = haven_get_theme_asset( 'js/wide-image-compatibility.js' );

	wp_enqueue_script(
		'oudgoud-news-cards-query-variation',
		$news_cards['uri'],
		array( 'wp-blocks', 'wp-i18n' ),
		$news_cards['version'],
		true
	);

	wp_enqueue_script(
		'oudgoud-wide-image-compatibility',
		$wide_image['uri'],
		array( 'wp-element', 'wp-hooks' ),
		$wide_image['version'],
		true
	);

	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->base || 'page' !== $screen->post_type ) {
		return;
	}

	$landing_page = haven_get_theme_asset( 'js/landing-page-editor.js' );

	wp_enqueue_script(
		'oudgoud-landing-page-editor',
		$landing_page['uri'],
		array( 'wp-blocks', 'wp-data', 'wp-dom-ready' ),
		$landing_page['version'],
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
