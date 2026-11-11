<?php
/**
 * Archive helpers (category, tag, date, search, author, blog index).
 *
 * Shared by tp/archive-layout.php so every post archive renders from one template.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Identify which kind of post archive is being displayed.
 *
 * @return string One of: search, author, category, tag, date, tax, home, archive.
 */
function elemenane_archive_type() {
	if ( is_search() ) {
		return 'search';
	}
	if ( is_author() ) {
		return 'author';
	}
	if ( is_category() ) {
		return 'category';
	}
	if ( is_tag() ) {
		return 'tag';
	}
	if ( is_date() ) {
		return 'date';
	}
	if ( is_tax() ) {
		return 'tax';
	}
	if ( is_home() ) {
		return 'home';
	}
	return 'archive';
}

/**
 * Small label shown above the archive title.
 *
 * @param string $type Archive type from elemenane_archive_type().
 * @return string
 */
function elemenane_archive_kicker( $type ) {
	switch ( $type ) {
		case 'search':
			return __( 'Search results', 'elemenane' );
		case 'author':
			return __( 'Author', 'elemenane' );
		case 'category':
			return __( 'Category', 'elemenane' );
		case 'tag':
			return __( 'Tag', 'elemenane' );
		case 'date':
			return __( 'Archive', 'elemenane' );
		case 'home':
			return __( 'Latest posts', 'elemenane' );
		default:
			return __( 'Archive', 'elemenane' );
	}
}

/**
 * Archive title without WordPress' "Category:" / "Tag:" prefix.
 *
 * @param string $type Archive type from elemenane_archive_type().
 * @return string Plain text (escape on output).
 */
function elemenane_archive_title( $type ) {
	if ( 'search' === $type ) {
		return get_search_query();
	}
	if ( 'home' === $type ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );
		return $page_for_posts ? get_the_title( $page_for_posts ) : get_bloginfo( 'name' );
	}

	add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
	$title = get_the_archive_title();
	remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

	return wp_strip_all_tags( $title );
}

/**
 * Number of posts matched by the main query (all pages).
 *
 * @return int
 */
function elemenane_archive_found_posts() {
	global $wp_query;

	return $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
}
