<?php
/**
 * Mobile Popular News Template
 *
 * Displays popular posts carousel for mobile view.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="mobile-populer">
	<div class="section-title">
		<div class="section-title-item">
			<h2><?php esc_html_e( 'Popular', 'elemenane' ); ?></h2>
		</div>
	</div>

	<div class="owl-carousel" id="home-sliding">
		<?php
		$popular_query = new WP_Query(
			array(
				'post_type'              => 'post',
				'posts_per_page'         => 5,
				'orderby'                => 'meta_value_num',
				'meta_key'               => 'musi_views',
				'offset'                 => 1,
				'post_status'            => 'publish',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			)
		);

		while ( $popular_query->have_posts() ) :
			$popular_query->the_post();
			get_template_part( 'tp/content', 'overlay' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</div>
