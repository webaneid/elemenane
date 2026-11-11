<?php
/**
 * Template Name: Landing Page
 *
 * News landing page. One responsive layout for every device (no server-side mobile detection):
 * hero slider, ACF Flexible Content sections (ane_home_box), then the latest articles with the sidebar.
 * Standalone template driven by ACF; independent of Elementor.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main" role="main">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-landing-page' ); ?>>
		<?php
		get_template_part( 'tp/news', 'featured' );

		if ( have_rows( 'ane_home_box' ) ) :
			while ( have_rows( 'ane_home_box' ) ) :
				the_row();

				switch ( get_row_layout() ) {
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

		get_template_part( 'tp/news', 'home' );
		?>
	</article>
</main>
<?php
get_footer();
