<?php
/**
 * Mobile Homepage Banner Template Part
 *
 * Displays banner from ACF flexible content for mobile homepage.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( have_rows( 'ane_home_box' ) ) :
	while ( have_rows( 'ane_home_box' ) ) :
		the_row();
		if ( 'ane_home_banner' === get_row_layout() ) :
			get_template_part( 'tp/banner/home' );
		endif;
	endwhile;
endif;
