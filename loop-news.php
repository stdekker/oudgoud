<?php

/* Custom news-page loop */
global $wp_query, $paged;

if (  $wp_query->max_num_pages > 1 && $paged) { haven_page_nav('nav-above'); }

		$count = 0;	
		$format = 'full';
		while(have_posts()) : 		
		$count++;	
		if($count > 2) { 
		  $format = 'short'; // shorten 
		}	
		the_post();
		haven_render_news_article($post, $format);					
		endwhile; 

		if (  $wp_query->max_num_pages > 1 ) { haven_page_nav('nav-below'); } ?>
