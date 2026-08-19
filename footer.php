<?php
/**
 * The template for displaying the footer.
 */
?>
	</main>

<footer id="site-footer">
	<nav aria-label="<?php esc_attr_e( 'Voettekstnavigatie', 'oudgoud' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'container'       => 'div',
				'container_class' => 'menu-footer',
				'theme_location'  => 'footer',
			)
		);
		?>
	</nav>
	<div class="copyright">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Terug naar de homepagina" rel="home" class="site-name">Scouting Jan van Hoof Groep <span class="rights">&copy; 1945 - <?php echo esc_html( date_i18n( 'Y' ) ); ?></span></a>
		<address>Gouderaksedijk 30-A, 2808 NG - Gouda</address>
	</div>	
</footer>
	
<?php wp_footer(); ?>
	</body>
</html>
