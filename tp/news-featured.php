<?php
/**
 * News Featured Section Template
 *
 * Displays featured posts with carousel slider.
 * Left side shows recent 4 posts, right side shows featured posts carousel.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="news-featured">
	<div class="container">
		<div class="ane-col-46">
			<div class="ane-kiri">
				<?php
				$recent_query = new WP_Query(
					array(
						'post_type'              => 'post',
						'posts_per_page'         => 4,
						'post_status'            => 'publish',
						'ignore_sticky_posts'    => true,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					)
				);

				while ( $recent_query->have_posts() ) :
					$recent_query->the_post();
					get_template_part( 'tp/content', 'list' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<div class="ane-kanan">
				<div class="one-item-carousel owl-carousel">
					<?php
					$featured_query = new WP_Query(
						array(
							'post_type'              => 'post',
							'posts_per_page'         => 5,
							'post_status'            => 'publish',
							'meta_query'             => array(
								array(
									'key'   => 'ane_news_utama',
									'value' => '1',
								),
							),
							'ignore_sticky_posts'    => true,
							'no_found_rows'          => true,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
						)
					);

					while ( $featured_query->have_posts() ) :
						$featured_query->the_post();
						get_template_part( 'tp/content', 'overlay' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
	</div>
</section>
