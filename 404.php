<?php
/**
 * 404 Error Template
 *
 * Supports Elementor Theme Builder for 404 page templates.
 * Falls back to native theme template if no Elementor template is set.
 */
get_header();
?>
<style>
	.ane-404-page {
		min-height: 60vh;
	}
	.ane-404-number {
		animation: fadeIn 0.5s ease-in;
	}
	.ane-404-btn:hover {
		opacity: 0.9;
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
	}
	.ane-404-btn-secondary:hover {
		background: #e0e0e0;
		transform: translateY(-2px);
	}
	.ane-404-post-item {
		transition: transform 0.3s, box-shadow 0.3s;
	}
	.ane-404-post-item:hover {
		transform: translateY(-4px);
		box-shadow: 0 8px 20px rgba(0,0,0,0.1);
	}
	.ane-404-post-item h3 a:hover {
		color: var(--ane-warna-utama, #333);
	}
	@keyframes fadeIn {
		from { opacity: 0; transform: scale(0.8); }
		to { opacity: 1; transform: scale(1); }
	}
	@media (max-width: 768px) {
		.ane-404-number {
			font-size: 80px !important;
		}
		.ane-404-wrapper {
			padding: 40px 15px !important;
		}
		.page-header h1 {
			font-size: 24px !important;
		}
		.page-header p {
			font-size: 16px !important;
		}
	}
</style>
<?php
// Check if Elementor has a custom 404 template
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
	// No Elementor 404 template, use native theme template
	?>
	<main id="content" class="site-main ane-404-page">
		<div class="container">
			<div class="ane-404-wrapper" style="text-align: center; padding: 60px 20px;">

				<!-- 404 Number -->
				<div class="ane-404-number" style="font-size: 120px; font-weight: 700; line-height: 1; margin-bottom: 20px; color: var(--ane-warna-utama, #333);">
					404
				</div>

				<!-- Page Header -->
				<div class="page-header" style="margin-bottom: 30px;">
					<h1 class="entry-title" style="font-size: 32px; margin-bottom: 15px;">
						<?php echo esc_html__( 'Oops! Page Not Found', 'elemenane' ); ?>
					</h1>
					<p style="font-size: 18px; color: #666; max-width: 600px; margin: 0 auto;">
						<?php echo esc_html__( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'elemenane' ); ?>
					</p>
				</div>

				<!-- Search Box -->
				<div class="ane-404-search" style="max-width: 500px; margin: 40px auto;">
					<p style="font-size: 16px; margin-bottom: 15px; font-weight: 600;">
						<?php echo esc_html__( 'Try searching for what you need:', 'elemenane' ); ?>
					</p>
					<?php get_search_form(); ?>
				</div>

				<!-- Quick Links -->
				<div class="ane-404-links" style="margin-top: 50px;">
					<p style="font-size: 16px; margin-bottom: 20px; font-weight: 600;">
						<?php echo esc_html__( 'Or try these helpful links:', 'elemenane' ); ?>
					</p>
					<div class="ane-404-buttons" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ane-404-btn" style="display: inline-block; padding: 12px 30px; background: var(--ane-warna-utama, #333); color: #fff; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s;">
							<?php echo esc_html__( 'Go to Homepage', 'elemenane' ); ?>
						</a>
						<?php if ( has_nav_menu( 'menuutama' ) ) : ?>
						<a href="javascript:history.back()" class="ane-404-btn-secondary" style="display: inline-block; padding: 12px 30px; background: #f5f5f5; color: #333; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s;">
							<?php echo esc_html__( 'Go Back', 'elemenane' ); ?>
						</a>
						<?php endif; ?>
					</div>
				</div>

				<?php
				// Show recent posts if available
				$recent_posts = new WP_Query( array(
					'posts_per_page' => 3,
					'post_status'    => 'publish',
					'ignore_sticky_posts' => true,
				) );

				if ( $recent_posts->have_posts() ) :
					?>
					<div class="ane-404-recent" style="margin-top: 60px; text-align: left; max-width: 900px; margin-left: auto; margin-right: auto;">
						<h2 style="font-size: 24px; margin-bottom: 25px; text-align: center;">
							<?php echo esc_html__( 'Recent Articles', 'elemenane' ); ?>
						</h2>
						<div class="ane-404-posts" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
							<?php
							while ( $recent_posts->have_posts() ) :
								$recent_posts->the_post();
								?>
								<article class="ane-404-post-item" style="background: #f9f9f9; padding: 20px; border-radius: 4px;">
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="ane-404-post-thumb" style="margin-bottom: 15px; border-radius: 4px; overflow: hidden;">
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: auto; display: block;' ) ); ?>
											</a>
										</div>
									<?php endif; ?>
									<h3 style="font-size: 16px; line-height: 1.4; margin-bottom: 10px;">
										<a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
											<?php the_title(); ?>
										</a>
									</h3>
									<div class="ane-404-post-meta" style="font-size: 13px; color: #999;">
										<?php echo esc_html( get_the_date() ); ?>
									</div>
								</article>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
					<?php
				endif;
				?>

			</div>
		</div>
	</main>
	<?php
}
// If elementor_theme_do_location() returns true, Elementor already rendered the 404 page

get_footer();
?>