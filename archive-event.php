<?php
/**
 * The template for displaying lists of events
 */
get_header(); ?>

<?php if ( ! function_exists( 'eo_is_all_day' ) || ! function_exists( 'eo_the_start' ) || ! function_exists( 'eo_get_the_start' ) || ! function_exists( 'eo_get_venue_name' ) || ! function_exists( 'eo_venue_link' ) || ! function_exists( 'eo_venue_name' ) ) : ?>
	<div id="primary" class="agenda">
		<article id="post-0" class="post no-results not-found">
			<h1 class="entry-title"><?php esc_html_e( 'Agenda', 'oudgoud' ); ?></h1>
			<div class="entry-content">
				<p><?php esc_html_e( 'De agenda is tijdelijk niet beschikbaar.', 'oudgoud' ); ?></p>
			</div>
		</article>
	</div>
	<?php get_footer(); ?>
	<?php return; ?>
<?php endif; ?>

	<div id="primary" class="agenda">

				<header class="page-header">
					<h1 class="entry-title">
						<?php _e('Agenda','eventorganiser'); ?>
					</h1>
				</header>
		
			<?php if ( have_posts() ) : ?>
				<?php 
				global $wp_query;
				if ( $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-above" aria-label="<?php esc_attr_e( 'Evenementpaginering boven', 'oudgoud' ); ?>">
						<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&rarr;</span>' , 'eventorganiser' ) ); ?></div>
						<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&larr;</span> Newer events', 'eventorganiser' ) ); ?></div>
					</nav><!-- #nav-above -->
				<?php endif; ?>

				<?php /* Start the Loop */ ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<header class="entry-header">
							<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							<div class="entry-meta">
								<!-- Output the date of the occurrence-->
								<?php if ( eo_is_all_day() ) : ?>
									<!-- Event is an all day event -->
									<time datetime="<?php echo esc_attr( eo_get_the_start( 'c' ) ); ?>"><?php eo_the_start( 'd F Y' ); ?></time>
								<?php else: ?>
									<!-- Event is not an all day event - display time -->
									<time datetime="<?php echo esc_attr( eo_get_the_start( 'c' ) ); ?>"><?php eo_the_start( 'd F Y g:ia' ); ?></time>
								<?php endif; ?>

								<!-- If the event has a venue saved, display this-->
								<?php if ( eo_get_venue_name() ) : ?>
									&rarr; <a href="<?php eo_venue_link();?>"><?php eo_venue_name();?></a>
								<?php endif; ?>
							</div><!-- .entry-meta -->

						</header><!-- .entry-header -->

					</article><!-- #post-<?php the_ID(); ?> -->

    				<?php endwhile; ?><!----The Loop ends-->

				<!---- Navigate between pages-->
				<?php 
				if ( $wp_query->max_num_pages > 1 ) : ?>
					<nav id="nav-below" aria-label="<?php esc_attr_e( 'Evenementpaginering onder', 'oudgoud' ); ?>">
						<div class="nav-next events-nav-newer"><?php next_posts_link( __( 'Later events <span class="meta-nav">&larr;</span>' , 'eventorganiser' ) ); ?></div>
						<div class="nav-previous events-nav-newer"><?php previous_posts_link( __( ' <span class="meta-nav">&rarr;</span> Newer events', 'eventorganiser' ) ); ?></div>
					</nav><!-- #nav-below -->
				<?php endif; ?>

			<?php else : ?>
				<!---- If there are no events -->
				<article id="post-0" class="post no-results not-found">

					<div class="entry-content">
						<p>Er staat nu niets op deze agenda, maar er is bij ons altijd wel wat te doen.</p>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>
		</div><!-- #primary -->

<!-- Call template sidebar and footer -->
<?php get_footer(); ?>
