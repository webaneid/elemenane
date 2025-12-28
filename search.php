<?php
/**
 * Search Results Template
 *
 * Supports Elementor Theme Builder for search results templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Check if Elementor has a custom search results template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	// No Elementor search template, use native theme template
	?>
	<main id="main" class="site-main" role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<div class="ane-col-64">
				<?php if ( have_posts() ) : ?>
					<div class="ane-kiri">
						<header class="page-header ane-judul-arsip">
							<h1 style="margin-bottom: 20px;">
								<?php
								printf(
									esc_html__( 'Search Results for: %s', 'elemenane' ),
									'<span>' . esc_html( get_search_query() ) . '</span>'
								);
								?>
							</h1>
							<?php get_search_form(); ?>
						</header>

						<?php get_template_part( 'tp/content', 'archive' ); ?>
					</div>
				<?php else : ?>
					<?php get_template_part( 'tp/content', 'none' ); ?>
				<?php endif; ?>
				<?php get_sidebar(); ?>
			</div>
		</div>
	</main>
	<?php
}
// If elementor_theme_do_location() returns true, Elementor already rendered search results

get_footer();
?>
