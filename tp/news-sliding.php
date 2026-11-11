<?php
/**
 * Landing section: horizontal rail of portrait story cards for a category.
 * ACF Flexible Content layout `ane_box_sliding`. Scroll-snap, arrows handled by js/home.js.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_category = get_sub_field( 'ane_category' );
$elemenane_custom   = get_sub_field( 'ane_judul' );

if ( empty( $elemenane_category ) ) {
	return;
}

$elemenane_url   = get_category_link( $elemenane_category->term_id );
$elemenane_title = ! empty( $elemenane_custom ) ? $elemenane_custom : $elemenane_category->name;

$elemenane_q = new WP_Query(
	array(
		'post_type'              => 'post',
		'cat'                    => $elemenane_category->term_id,
		'posts_per_page'         => 8,
		'post_status'            => 'publish',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
	)
);

if ( ! $elemenane_q->have_posts() ) {
	return;
}
?>
<section class="ane-block ane-block--sliding" data-ane-rail>
	<div class="container">
		<?php
		get_template_part(
			'tp/home-head',
			null,
			array(
				'title' => $elemenane_title,
				'url' => $elemenane_url,
				'rail' => true,
			)
		);
		?>

		<div class="ane-rail" tabindex="0" role="region" aria-label="<?php echo esc_attr( $elemenane_title ); ?>">
			<?php
			while ( $elemenane_q->have_posts() ) :
				$elemenane_q->the_post();
				get_template_part(
					'tp/archive',
					'card',
					array(
						'variant' => 'featured',
						'heading' => 'h3',
						'sizes' => '(max-width: 639px) 72vw, 280px',
					)
				);
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
