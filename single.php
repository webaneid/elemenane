<?php
/**
 * Single Post Template
 *
 * Supports Elementor Theme Builder for single post templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Track post views
ane_set_views( get_the_ID() );

// Check if Elementor has a custom single post template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	// No Elementor single template, use native theme template
	?>
	<main id="main" class="site-main">
		<?php get_template_part( 'breadcrumbs' ); ?>
		<div class="container">
			<div class="ane-col-64">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						get_template_part( 'tp/single', get_post_format() );
					endwhile;
				else :
					get_template_part( 'tp/content', 'none' );
				endif;
				?>
			</div>
		</div>
	</main>
	<?php
}
// If elementor_theme_do_location() returns true, Elementor already rendered the single post

get_footer();
?>
