<?php
/**
 * Desktop Homepage Template Part
 *
 * Displays news homepage layout optimized for desktop devices.
 * Uses ACF Flexible Content (ane_home_box) for modular content sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-landing-page' ); ?>>
	<?php
	// Featured news section
	get_template_part( 'tp/news', 'featured' );

	// ACF Flexible Content - Homepage Boxes
	if ( have_rows( 'ane_home_box' ) ) :
		while ( have_rows( 'ane_home_box' ) ) :
			the_row();

			$layout = get_row_layout();

			// Load template based on ACF layout type
			switch ( $layout ) {
				case 'ane_box_default':
					get_template_part( 'tp/news', 'default' );
					break;

				case 'ane_box_sliding':
					get_template_part( 'tp/news', 'sliding' );
					break;

				case 'ane_box_column':
					get_template_part( 'tp/news', 'column' );
					break;

				case 'ane_home_banner':
					get_template_part( 'tp/banner/home' );
					break;

				case 'ane_box_classic':
					get_template_part( 'tp/news', 'classic' );
					break;

				case 'ane_box_populer':
					get_template_part( 'tp/news', 'populer' );
					break;
			}

		endwhile;
	endif;

	// Latest news section
	get_template_part( 'tp/news', 'home' );
	?>
</article>
