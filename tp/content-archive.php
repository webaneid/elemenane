<?php
/**
 * Archive Content Template
 *
 * Displays post loop for archive pages with alternating layouts.
 * Uses different content templates based on device type and position.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="ane-konten-arsip">
	<?php
	$counter = 0;

	while ( have_posts() ) :
		the_post();
		$counter++;

		// First post - overlay layout
		if ( 1 === $counter ) {
			get_template_part( 'tp/content', 'overlay' );
		}
		// 4th post - content + banner
		elseif ( 4 === $counter ) {
			if ( wp_is_mobile() ) {
				get_template_part( 'tp/content', 'list' );
			} else {
				get_template_part( 'tp/content', get_post_format() );
			}
			get_template_part( 'tp/banner/header' );
		}
		// 7th post - content + banner
		elseif ( 7 === $counter ) {
			if ( wp_is_mobile() ) {
				get_template_part( 'tp/content', 'list' );
			} else {
				get_template_part( 'tp/content', get_post_format() );
			}
			get_template_part( 'tp/banner/header' );
		}
		// All other posts - standard layout
		else {
			if ( wp_is_mobile() ) {
				get_template_part( 'tp/content', 'list' );
			} else {
				get_template_part( 'tp/content', get_post_format() );
			}
		}

	endwhile;

	// Pagination
	echo ane_post_pagination();
	?>
</div>
