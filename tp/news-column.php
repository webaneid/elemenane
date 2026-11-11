<?php
/**
 * Landing section: several category columns side by side (lead card + compact rows).
 * ACF Flexible Content layout `ane_box_column`.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_rows( 'ane_box_column_isi' ) ) {
	return;
}
?>
<section class="ane-block ane-block--column">
	<div class="container">
		<div class="ane-columns">
			<?php
			while ( have_rows( 'ane_box_column_isi' ) ) :
				the_row();

				$elemenane_category = get_sub_field( 'ane_category' );
				$elemenane_custom   = get_sub_field( 'judul_custom' );

				if ( empty( $elemenane_category ) ) {
					continue;
				}

				$elemenane_url   = get_category_link( $elemenane_category->term_id );
				$elemenane_title = ! empty( $elemenane_custom ) ? $elemenane_custom : $elemenane_category->name;

				$elemenane_q = new WP_Query(
					array(
						'post_type'              => 'post',
						'cat'                    => $elemenane_category->term_id,
						'posts_per_page'         => 5,
						'post_status'            => 'publish',
						'ignore_sticky_posts'    => true,
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
					)
				);

				if ( ! $elemenane_q->have_posts() ) {
					continue;
				}
				?>
				<div class="ane-columns__col">
					<?php
					get_template_part(
						'tp/home-head',
						null,
						array(
							'title' => $elemenane_title,
							'url' => $elemenane_url,
							'more' => __( 'More', 'elemenane' ),
						)
					);
					?>

					<div class="ane-columns__list">
						<?php
						$elemenane_i = 0;
						while ( $elemenane_q->have_posts() ) :
							$elemenane_q->the_post();
							++$elemenane_i;
							get_template_part(
								'tp/archive',
								'card',
								array(
									'variant' => 1 === $elemenane_i ? 'featured' : 'row',
									'heading' => 'h3',
									'sizes'   => '(max-width: 992px) 100vw, 370px',
								)
							);
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>
