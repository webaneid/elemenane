<?php
/**
 * News Sliding Section Template
 *
 * ACF Flexible Content layout for homepage.
 * Displays category posts in owl carousel slider.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get ACF fields
$category      = get_sub_field( 'ane_category' );
$custom_title  = get_sub_field( 'ane_judul' );

if ( empty( $category ) ) {
	return;
}

$category_link = get_category_link( $category->term_id );
$section_title = ! empty( $custom_title ) ? $custom_title : $category->name;
?>
<section class="news-sliding">
	<div class="container">
		<div class="section-title">
			<div class="section-title-item">
				<h2>
					<a href="<?php echo esc_url( $category_link ); ?>" rel="bookmark">
						<?php echo esc_html( $section_title ); ?>
					</a>
				</h2>
			</div>
			<a class="lainnya" href="<?php echo esc_url( $category_link ); ?>">
				<?php esc_html_e( 'Display More', 'elemenane' ); ?> <i class="ane-chevron-right-alt-2"></i>
			</a>
		</div>

		<div class="news-sliding-isi">
			<div class="owl-carousel dot-style2" id="home-sliding">
				<?php
				$sliding_query = new WP_Query(
					array(
						'post_type'              => 'post',
						'cat'                    => $category->term_id,
						'posts_per_page'         => 8,
						'post_status'            => 'publish',
						'ignore_sticky_posts'    => true,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					)
				);

				while ( $sliding_query->have_posts() ) :
					$sliding_query->the_post();
					get_template_part( 'tp/content', 'overlay' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
</section>
