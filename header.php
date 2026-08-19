<?php
/**
 * The header for our theme.
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class( 'blogid-' . get_current_blog_id() ); ?>>
		<?php wp_body_open(); ?>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Snel naar de inhoud', 'oudgoud' ); ?></a>

		<header id="site-header">
			<div id="branding">
				<div id="site-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
					</a>
				</div>
				<?php $site_description = get_bloginfo( 'description' ); ?>
				<?php if ( $site_description ) : ?>
					<p class="site-description screen-reader-text"><?php echo esc_html( $site_description ); ?></p>
				<?php endif; ?>
			</div>

			<nav id="main-navigation" aria-label="<?php esc_attr_e( 'Hoofdnavigatie', 'oudgoud' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'container'       => 'div',
						'container_class' => 'menu-header',
						'theme_location'  => 'primary',
					)
				);
				?>
			</nav>
		</header>

		<main id="content" tabindex="-1">
