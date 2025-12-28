<?php
/**
 * Author Archive Template
 *
 * Displays all posts by a specific author.
 * Supports Elementor Theme Builder for author archive templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();

// Check if Elementor has a custom author archive template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
	// No Elementor author template, use native theme template
	?>
	<main id="main" class="site-main" role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<div class="ane-col-64">
				<?php if ( have_posts() ) : ?>
					<div class="ane-kiri">
						<header class="page-header ane-judul-arsip">
							<div class="author_bio_section">
								<?php
								// Get author information
								$author_id = get_queried_object_id();
								$display_name = get_the_author_meta( 'display_name', $author_id );

								if ( empty( $display_name ) ) {
									$display_name = get_the_author_meta( 'nickname', $author_id );
								}

								$user_description = get_the_author_meta( 'user_description', $author_id );
								$user_email = get_the_author_meta( 'user_email', $author_id );
								?>

								<h1 class="author_name">
									<?php
									printf(
										esc_html__( 'All Posts by %s', 'elemenane' ),
										'<span>' . esc_html( $display_name ) . '</span>'
									);
									?>
								</h1>

								<div class="author_details">
									<?php echo get_avatar( $user_email, 90, '', esc_attr( $display_name ) ); ?>
									<?php if ( ! empty( $user_description ) ) : ?>
										<div class="deskripsi">
											<?php echo wp_kses_post( wpautop( $user_description ) ); ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
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
// If elementor_theme_do_location() returns true, Elementor already rendered the author archive

get_footer();
?>
