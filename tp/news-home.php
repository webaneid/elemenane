<?php
/**
 * News Home Section Template
 *
 * Displays latest posts with pagination and sidebar.
 * Used at the bottom of homepage.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get ACF custom title
$custom_title  = get_field( 'ane_title' );
$section_title = ! empty( $custom_title ) ? $custom_title : __( 'Latest Content', 'elemenane' );
?>
<section class="home-news">
	<div class="container">
		<div class="section-title">
			<div class="section-title-item">
				<h2><?php echo esc_html( $section_title ); ?></h2>
			</div>
		</div>

		<div class="ane-col-73">
			<div class="ane-kiri">
				<?php
				// Get current page number
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = absint( get_query_var( 'paged' ) );
				} elseif ( get_query_var( 'page' ) ) {
					$paged = absint( get_query_var( 'page' ) );
				}

				$home_query = new WP_Query(
					array(
						'post_type'              => 'post',
						'posts_per_page'         => 10,
						'paged'                  => $paged,
						'post_status'            => 'publish',
						'ignore_sticky_posts'    => true,
						'update_post_term_cache' => false,
					)
				);

				if ( $home_query->have_posts() ) :
					$counter = -1;
					while ( $home_query->have_posts() ) :
						$home_query->the_post();
						$counter++;

						// First post - overlay
						if ( 0 === $counter ) {
							get_template_part( 'tp/content', 'overlay' );
						}
						// 6th post - overlay
						elseif ( 5 === $counter ) {
							get_template_part( 'tp/content', 'overlay' );
						}
						// Other posts - list or post format
						else {
							if ( wp_is_mobile() ) {
								get_template_part( 'tp/content', 'list' );
							} else {
								get_template_part( 'tp/content', get_post_format() );
							}
						}
					endwhile;
					wp_reset_postdata();

					// Pagination
					if ( $home_query->max_num_pages > 1 ) :
						global $wp_query;
						$orig_query = $wp_query;
						$wp_query   = $home_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						// echo ane_post_pagination();
						$wp_query = $orig_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					endif;
				else :
					?>
					<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'elemenane' ); ?></p>
					<?php
				endif;
				?>
			</div>

			<div class="ane-kanan sticky-top">
				<aside id="sticky-sidebar">
					<div class="right-sidebar">
						<?php dynamic_sidebar( 'home-sidebar' ); ?>
					</div>
				</aside>
			</div>
		</div>
	</div>
</section>
