<?php
/**
 * Landing page (news home) helpers.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * IDs of the posts shown in the landing hero slider.
 *
 * Posts flagged with the ACF field `ane_news_utama` come first; if none are flagged the latest posts are used
 * so the hero is never empty. Cached per request (also used to preload the LCP image).
 *
 * @param int $limit Maximum number of slides.
 * @return int[]
 */
function elemenane_home_hero_ids( $limit = 5 ) {
	static $cache = array();

	$limit = max( 1, absint( $limit ) );
	if ( isset( $cache[ $limit ] ) ) {
		return $cache[ $limit ];
	}

	$base = array(
		'post_type'              => 'post',
		'posts_per_page'         => $limit,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);

	$ids = get_posts(
		array_merge(
			$base,
			array(
				'meta_key'   => 'ane_news_utama', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Small, limited lookup.
				'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		)
	);

	if ( empty( $ids ) ) {
		$ids = get_posts( $base );
	}

	$cache[ $limit ] = array_map( 'intval', $ids );

	return $cache[ $limit ];
}
