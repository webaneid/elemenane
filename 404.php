<?php
/**
 * 404 Error Template
 *
 * Supports Elementor Theme Builder; otherwise renders tp/error-404.php.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	get_template_part( 'tp/error', '404' );
}

get_footer();
