<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 */
?>
<aside class="widgets" id="sidebar" aria-label="<?php esc_attr_e( 'Zijbalk', 'oudgoud' ); ?>">
	<?php dynamic_sidebar( 'primary-widget-area' ); ?>
	<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
</aside>
