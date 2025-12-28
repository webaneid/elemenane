<?php
/**
 * News Featured New Section Template
 *
 * Displays recent 5 posts in list format.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="col-xl-5 col-lg-5 col-md-6 col-sm-6">
	<div class="home-featured-right prlm-0">
		<?php
		$recent_query = new WP_Query(
			array(
				'post_type'              => 'post',
				'posts_per_page'         => 5,
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
</div>
