<?php
/**
 * Site Footer
 *
 * Uses Elementor Theme Builder footer if available,
 * otherwise falls back to native theme footer.
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	// No Elementor footer found, use native theme footer
	get_template_part( 'tp/footer-asli' );
}
// If elementor_theme_do_location() returns true, Elementor already rendered the footer
?>


</div> <!--- webane-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
