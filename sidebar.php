<?php
/**
 * The Sidebar containing the primary and secondary widget areas.
 */
?>
<section class="widgets" id="secondary">
	<?php dynamic_sidebar( 'primary-widget-area' ); ?>
</section>

<section class="widgets" id="tertiary">
	<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
</section>

