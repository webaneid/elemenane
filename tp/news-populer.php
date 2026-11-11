<?php
/**
 * Landing section: most viewed posts (lead card + ranked list with thumbnails).
 * ACF Flexible Content layout `ane_box_populer`.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_custom = get_sub_field( 'judul_custom' );
$elemenane_title  = ! empty( $elemenane_custom ) ? $elemenane_custom : __( 'Popular', 'elemenane' );

$elemenane_lead = new WP_Query(
	array(
		'post_type'              => 'post',
		'posts_per_page'         => 1,
		'post_status'            => 'publish',
		'orderby'                => 'meta_value_num',
		'meta_key'               => 'musi_views', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Small, limited lookup.
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
	)
);
if ( ! $elemenane_lead->have_posts() ) {
	$elemenane_lead = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}
if ( ! $elemenane_lead->have_posts() ) {
	return;
}
?>
<section class="ane-block ane-block--populer">
	<div class="container">
		<?php get_template_part( 'tp/home-head', null, array( 'title' => $elemenane_title ) ); ?>

		<div class="ane-duo">
			<div class="ane-duo__lead">
				<?php
				while ( $elemenane_lead->have_posts() ) :
					$elemenane_lead->the_post();
					get_template_part( 'tp/archive', 'card', array( 'variant' => 'featured' ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<div class="ane-duo__list">
				<?php
				get_template_part(
					'tp/home-rank',
					null,
					array(
						'count' => 5,
						'offset' => 1,
						'thumbs' => true,
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
