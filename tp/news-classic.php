<?php
/**
 * Landing section: category with a wide lead story, card grid and a "Popular" panel.
 * ACF Flexible Content layout `ane_box_classic`.
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
		'posts_per_page'         => 5,
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
<section class="ane-block ane-block--classic">
	<div class="container">
		<?php
		get_template_part(
			'tp/home-head',
			null,
			array(
				'title' => $elemenane_title,
				'url' => $elemenane_url,
			)
		);
		?>

		<div class="ane-split">
			<div class="ane-split__main">
				<div class="ane-grid">
					<?php
					$elemenane_i = 0;
					while ( $elemenane_q->have_posts() ) :
						$elemenane_q->the_post();
						++$elemenane_i;
						get_template_part( 'tp/archive', 'card', array( 'variant' => 1 === $elemenane_i ? 'wide' : 'standard' ) );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>

			<aside class="ane-split__aside">
				<div class="ane-panel">
					<h3 class="ane-section-title"><?php esc_html_e( 'Popular', 'elemenane' ); ?></h3>
					<?php get_template_part( 'tp/home-rank', null, array( 'cat' => $elemenane_category->term_id ) ); ?>
				</div>
			</aside>
		</div>

		<?php get_template_part( 'tp/banner/header' ); ?>
	</div>
</section>
