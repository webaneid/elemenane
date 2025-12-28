<?php
/**
 * News Column Section Template
 *
 * ACF Flexible Content layout for homepage.
 * Displays multiple category columns side by side.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! have_rows( 'ane_box_column_isi' ) ) {
	return;
}
?>
<section class="news-column">
	<div class="container">
		<div class="ane-col-3">
			<?php
			while ( have_rows( 'ane_box_column_isi' ) ) :
				the_row();

				$category      = get_sub_field( 'ane_category' );
				$custom_title  = get_sub_field( 'judul_custom' );

				if ( empty( $category ) ) {
					continue;
				}

				$category_link = get_category_link( $category->term_id );
				$section_title = ! empty( $custom_title ) ? $custom_title : $category->name;
				?>
				<div class="ane-isi">
					<div class="section-title">
						<div class="section-title-item">
							<h2>
								<a href="<?php echo esc_url( $category_link ); ?>" rel="bookmark">
									<?php echo esc_html( $section_title ); ?>
								</a>
							</h2>
						</div>
						<a class="lainnya" href="<?php echo esc_url( $category_link ); ?>">
							<?php esc_html_e( 'More', 'elemenane' ); ?> <i class="ane-chevron-right-alt-2"></i>
						</a>
					</div>

					<div class="kepalanya">
						<?php
						$column_query = new WP_Query(
							array(
								'post_type'              => 'post',
								'cat'                    => $category->term_id,
								'posts_per_page'         => 5,
								'post_status'            => 'publish',
								'ignore_sticky_posts'    => true,
								'no_found_rows'          => true,
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
							)
						);

						if ( $column_query->have_posts() ) :
							$column_query->the_post();
							get_template_part( 'tp/content', 'overlay' );
							?>
					</div>

					<div class="badannya">
						<?php
							while ( $column_query->have_posts() ) :
								$column_query->the_post();
								get_template_part( 'tp/content', 'list' );
							endwhile;
						endif;
						wp_reset_postdata();
						?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>
