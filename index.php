<?php
/**
 * Index Template (Fallback)
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * Supports Elementor Theme Builder templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Check if Elementor has a custom template for this view
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	// No Elementor template, use native theme template
	?>
	<main id="main" class="site-main" role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<div class="ane-col-64">
				<?php if ( have_posts() ) : ?>
					<div class="ane-kiri">
						<?php if ( is_home() && ! is_front_page() ) : ?>
							<header class="page-header ane-judul-arsip">
								<h1><?php single_post_title(); ?></h1>
							</header>
						<?php endif; ?>

						<?php get_template_part( 'tp/content', 'archive' ); ?>
					</div>
				<?php else : ?>
					<div class="ane-kiri">
						<?php get_template_part( 'tp/content', 'none' ); ?>
					</div>
				<?php endif; ?>
				<?php get_sidebar(); ?>
			</div>
		</div>
	</main>
	<?php
}
// If elementor_theme_do_location() returns true, Elementor already rendered the template

get_footer();
?>
