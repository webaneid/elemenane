<?php
/**
 * Site Footer.
 *
 * Uses Elementor Theme Builder footer if available,
 * otherwise falls back to native theme footer.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'tp/footer-default' );
}
?>

	<?php get_template_part( 'tp/floating-chat' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
