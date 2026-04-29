<?php
/**
 * The template for displaying the footer.
 */
?>
	
<footer role="contentinfo" id="site-footer">		
	<?php wp_nav_menu( array( 'container_class' => 'menu-footer', 'theme_location' => 'footer' ) ); ?>
	<div class="copyright">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Terug naar de homepagina" rel="home" class="site-name">Scouting Jan van Hoof Groep <span class="rights">&copy; 1945 - <?php echo esc_html( date_i18n( 'Y' ) ); ?></span></a>
		<address>Gouderaksedijk 30-A, 2808 NG - Gouda</address>
	</div>	
</footer><!-- footer -->
	
<?php wp_footer(); ?>
	</body>
</html>
