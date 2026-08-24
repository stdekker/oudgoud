<?php
/**
 * Title: Horizontal tiles
 * Slug: oudgoud/horizontal-tiles
 * Categories: featured, call-to-action
 * Viewport Width: 1200
 * Description: Drie gekleurde, horizontaal geplaatste tegels met links naar andere pagina's.
 */

if ( ! defined( 'ABSPATH' ) ) {
	http_response_code( 403 );
	exit;
}
?>

<!-- wp:columns {"align":"wide","isStackedOnMobile":false,"className":"horizontal-tiles"} -->
<div class="wp-block-columns alignwide is-not-stacked-on-mobile horizontal-tiles">
	<!-- wp:column {"backgroundColor":"brand","textColor":"white","className":"horizontal-tile"} -->
	<div class="wp-block-column horizontal-tile has-white-color has-brand-background-color has-text-color has-background">
		<!-- wp:heading {"level":3,"className":"horizontal-tile__title"} -->
		<h3 class="wp-block-heading horizontal-tile__title"><a href="#"><?php esc_html_e( 'Eerste pagina', 'oudgoud' ); ?></a></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"horizontal-tile__description","fontSize":"small"} -->
		<p class="horizontal-tile__description has-small-font-size"><?php esc_html_e( 'Korte toelichting bij de eerste pagina.', 'oudgoud' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"backgroundColor":"gold","textColor":"brown","className":"horizontal-tile"} -->
	<div class="wp-block-column horizontal-tile has-brown-color has-gold-background-color has-text-color has-background">
		<!-- wp:heading {"level":3,"className":"horizontal-tile__title"} -->
		<h3 class="wp-block-heading horizontal-tile__title"><a href="#"><?php esc_html_e( 'Tweede pagina', 'oudgoud' ); ?></a></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"horizontal-tile__description","fontSize":"small"} -->
		<p class="horizontal-tile__description has-small-font-size"><?php esc_html_e( 'Korte toelichting bij de tweede pagina.', 'oudgoud' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"backgroundColor":"brown","textColor":"white","className":"horizontal-tile"} -->
	<div class="wp-block-column horizontal-tile has-white-color has-brown-background-color has-text-color has-background">
		<!-- wp:heading {"level":3,"className":"horizontal-tile__title"} -->
		<h3 class="wp-block-heading horizontal-tile__title"><a href="#"><?php esc_html_e( 'Derde pagina', 'oudgoud' ); ?></a></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"horizontal-tile__description","fontSize":"small"} -->
		<p class="horizontal-tile__description has-small-font-size"><?php esc_html_e( 'Korte toelichting bij de derde pagina.', 'oudgoud' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
