<?php
/**
 * Title: News card
 * Slug: oudgoud/news-card
 * Inserter: no
 */

if ( ! defined( 'ABSPATH' ) ) {
	http_response_code( 403 );
	exit;
}
?>

<!-- wp:group {"tagName":"article","className":"news-card","layout":{"type":"default"}} -->
<article class="wp-block-group news-card">
	<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","sizeSlug":"large","className":"news-card__image"} /-->
	<!-- wp:group {"className":"news-card__body","layout":{"type":"default"}} -->
	<div class="wp-block-group news-card__body">
		<!-- wp:post-title {"isLink":true,"level":2,"className":"news-card__title"} /-->
		<!-- wp:group {"className":"news-card__meta","layout":{"type":"default"}} -->
		<div class="wp-block-group news-card__meta">
			<!-- wp:post-date {"format":"j F Y","isLink":true,"className":"news-card__date"} /-->
			<!-- wp:post-terms {"term":"category","className":"news-card__categories"} /-->
		</div>
		<!-- /wp:group -->
		<!-- wp:post-excerpt {"moreText":"Lees verder","excerptLength":34,"className":"news-card__excerpt"} /-->
	</div>
	<!-- /wp:group -->
</article>
<!-- /wp:group -->
