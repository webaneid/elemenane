<?php
/**
 * News Featured Mobile Section Template
 *
 * Displays featured posts carousel for mobile homepage.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

ane_enqueue_carousel();
?>
<section class="news-featured">
	<div class="container">
		<div class="row">
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

				$slide_index = 0;
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					get_template_part(
						'tp/content',
						'overlay',
						array(
							'priority' => 0 === $slide_index++,
							'sizes'    => '100vw',
						)
					);
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
</section>
