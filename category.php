<?php
/**
 * Category Archive Template
 *
 * Displays all posts in a specific category.
 * Supports Elementor Theme Builder for category archive templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Check if Elementor has a custom category archive template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	// No Elementor category template, use native theme template
	?>
	<main id="main" class="site-main" role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<div class="ane-col-64">
				<?php if ( have_posts() ) : ?>
					<div class="ane-kiri">
						<header class="page-header ane-judul-arsip">
							<?php single_cat_title( '<h1>', '</h1>' ); ?>
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
// If elementor_theme_do_location() returns true, Elementor already rendered the category archive

get_footer();
