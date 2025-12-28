<?php
/**
 * News Popular Section Template
 *
 * ACF Flexible Content layout for homepage.
 * Displays most viewed posts from all categories.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get ACF fields
$custom_title  = get_sub_field( 'judul_custom' );
$section_title = ! empty( $custom_title ) ? $custom_title : __( 'Popular', 'elemenane' );
?>
<section class="news-populer">
	<div class="container">
		<div class="section-title">
			<div class="section-title-item">
				<h2><?php echo esc_html( $section_title ); ?></h2>
			</div>
		</div>

		<div class="news-populer-isi">
			<div class="kiri">
				<?php
				$popular_query = new WP_Query(
					array(
						'post_type'              => 'post',
						'posts_per_page'         => 5,
						'orderby'                => 'meta_value_num',
						'meta_key'               => 'musi_views',
						'post_status'            => 'publish',
						'ignore_sticky_posts'    => true,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					)
				);

				if ( $popular_query->have_posts() ) :
					$popular_query->the_post();
					get_template_part( 'tp/content', 'overlay' );
					?>
			</div>

			<div class="kanan">
				<?php
					while ( $popular_query->have_posts() ) :
						$popular_query->the_post();
						get_template_part( 'tp/content', 'list' );
					endwhile;
				endif;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
</section>
