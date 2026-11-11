<?php
/**
 * Landing: ranked list of popular posts (numbered, like the sidebar Latest Posts).
 *
 * Args (get_template_part):
 * - cat (int):    optional category ID.
 * - count (int):  number of items (default 5).
 * - offset (int): skip this many posts.
 * - thumbs (bool): show a small thumbnail.
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_count  = ! empty( $args['count'] ) ? absint( $args['count'] ) : 5;
$elemenane_thumbs = ! empty( $args['thumbs'] );

$elemenane_query_args = array(
	'post_type'              => 'post',
	'posts_per_page'         => $elemenane_count,
	'post_status'            => 'publish',
	'ignore_sticky_posts'    => true,
	'no_found_rows'          => true,
	'update_post_term_cache' => false,
);
if ( ! empty( $args['cat'] ) ) {
	$elemenane_query_args['cat'] = absint( $args['cat'] );
}
if ( ! empty( $args['offset'] ) ) {
	$elemenane_query_args['offset'] = absint( $args['offset'] );
}

// Most viewed first; fall back to the latest posts when no view counts exist yet.
$elemenane_popular = new WP_Query(
	array_merge(
		$elemenane_query_args,
		array(
			'orderby'  => 'meta_value_num',
			'meta_key' => 'musi_views', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Small, limited lookup.
		)
	)
);
if ( ! $elemenane_popular->have_posts() ) {
	$elemenane_popular = new WP_Query( $elemenane_query_args );
}

if ( ! $elemenane_popular->have_posts() ) {
	return;
}
?>
<ol class="ane-rank<?php echo $elemenane_thumbs ? ' ane-rank--thumbs' : ''; ?>">
	<?php
	while ( $elemenane_popular->have_posts() ) :
		$elemenane_popular->the_post();
		?>
		<li class="ane-rank__item">
			<a class="ane-rank__link" href="<?php the_permalink(); ?>">
				<?php if ( $elemenane_thumbs ) : ?>
					<span class="ane-rank__thumb">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail(
								'kotak',
								array(
									'alt' => '',
									'loading' => 'lazy',
									'decoding' => 'async',
								)
							);
						}
						?>
					</span>
				<?php endif; ?>
				<span class="ane-rank__text">
					<span class="ane-rank__title"><?php the_title(); ?></span>
					<span class="ane-rank__meta"><?php echo esc_html( ane_times_ago( (int) get_the_time( 'U' ) ) ); ?></span>
				</span>
			</a>
		</li>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</ol>
