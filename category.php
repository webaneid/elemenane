<?php
/**
 * Category Archive Template
 *
 * Supports Elementor Theme Builder (archive location); otherwise renders the shared
 * archive layout (tp/archive-layout.php).
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	get_template_part( 'tp/archive', 'layout' );
}

get_footer();
