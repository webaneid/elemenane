<?php
/**
 * Comment Disabling & Cleanup Handlers.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

/**
 * Disable comments support on posts and pages in wp-admin.
 *
 * @return void
 */
function elemenane_disable_comments_admin() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'page', 'comments' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_init', 'elemenane_disable_comments_admin' );

/**
 * Redirect only comment feed requests to home URL (never redirect actual posts/pages).
 *
 * @return void
 */
function elemenane_redirect_comment_feeds() {
	if ( is_comment_feed() ) {
		wp_safe_redirect( home_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'elemenane_redirect_comment_feeds' );

// Close comments and pings on the front-end.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
