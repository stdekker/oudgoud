<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 */
?>
<aside class="widgets" id="sidebar" role="complementary" aria-label="Sidebar">
	<?php dynamic_sidebar( 'primary-widget-area' ); ?>
	<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
</aside>

