<?php
/**
 * The Header for our theme.
 */
 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" />
<?php
		wp_head();
?>
	</head>
	<body <?php
	$bid = get_current_blog_id();
	body_class( 'blogid-' . $bid ); ?>>
	
	<header role="banner" id="site-header">
		<section id="branding">
			<figure id="site-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Terug naar de voorpagina" rel="home">
			</a></figure>
			<h1 class="site-name"></h1>
			<h2 class="site-description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></h2>
		</section>
			
		<section id="access" role="navigation">
			<?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
			<a id="skip" href="#content" title="Snel naar de inhoud">Snel naar de inhoud</a>
			<?php wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' ) ); ?>
		</section>
		
	</header>
	
	<div id="filler">
		<div id="positioning">
