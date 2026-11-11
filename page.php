<?php
/**
 * Page Template
 *
 * Supports Elementor Theme Builder; otherwise renders tp/page-layout.php.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Track page views once.
if ( have_posts() ) {
	the_post();
	ane_set_views( get_the_ID() );
	rewind_posts();
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	get_template_part( 'tp/page', 'layout' );
}

get_footer();
