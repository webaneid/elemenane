<?php
/**
 * Page Template
 *
 * Supports Elementor Theme Builder for page templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Track page views
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		ane_set_views( get_the_ID() );
		break; // Only need to set views once
	}
	rewind_posts(); // Reset query for template rendering
}

// Check if Elementor has a custom page template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	// No Elementor page template, use native theme template
	?>
	<main id="main" class="site-main">
		<?php get_template_part( 'breadcrumbs' ); ?>
		<div class="container">
			<div class="ane-col-64">
				<div class="ane-kiri">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							get_template_part( 'tp/content', 'page' );
						endwhile;
					else :
						get_template_part( 'tp/content', 'none' );
					endif;
					?>
				</div>
				<?php get_sidebar(); ?>
			</div>
		</div>
	</main>
	<?php
}
// If elementor_theme_do_location() returns true, Elementor already rendered the page

get_footer();
?>
