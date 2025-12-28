<?php
/**
 * News Default Section Template
 *
 * ACF Flexible Content layout for homepage.
 * Displays category posts in default layout with popular sidebar.
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
<section class="news-default">
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

		<div class="ane-col-73">
			<div class="ane-kiri">
				<!-- First featured post -->
				<div class="ane-kiri-1">
					<?php
					$main_query = new WP_Query(
						array(
							'post_type'              => 'post',
							'cat'                    => $category->term_id,
							'posts_per_page'         => 6,
							'post_status'            => 'publish',
							'ignore_sticky_posts'    => true,
							'no_found_rows'          => true,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
						)
					);

					if ( $main_query->have_posts() ) :
						$main_query->the_post();
						get_template_part( 'tp/content', 'overlay' );
						?>
				</div>

				<!-- Next 5 posts -->
				<div class="ane-kiri-2">
					<?php
						while ( $main_query->have_posts() ) :
							$main_query->the_post();
							get_template_part( 'tp/content', 'list' );
						endwhile;
					endif;
					wp_reset_postdata();
					?>
				</div>
			</div>

			<!-- Popular posts sidebar -->
			<div class="ane-kanan">
				<div class="konten-populer">
					<h3><?php esc_html_e( 'Popular', 'elemenane' ); ?></h3>
					<div class="isinya">
						<ol>
							<?php
							$popular_query = new WP_Query(
								array(
									'post_type'              => 'post',
									'cat'                    => $category->term_id,
									'posts_per_page'         => 5,
									'orderby'                => 'meta_value_num',
									'meta_key'               => 'musi_views',
									'post_status'            => 'publish',
									'ignore_sticky_posts'    => true,
									'no_found_rows'          => true,
									'update_post_term_cache' => false,
								)
							);

							while ( $popular_query->have_posts() ) :
								$popular_query->the_post();
								echo '<li>';
								get_template_part( 'tp/content', 'list-title' );
								echo '</li>';
							endwhile;
							wp_reset_postdata();
							?>
						</ol>
					</div>
				</div>
				<?php get_template_part( 'tp/banner/post' ); ?>
			</div>
		</div>
	</div>
</section>
