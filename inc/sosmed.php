<?php
/**
 * Social Media Sharing Functions
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Generate social media share buttons for current post.
 *
 * Includes Facebook, Twitter, WhatsApp, and LinkedIn sharing options.
 * Uses proper URL encoding and escaping for security.
 *
 * @return string HTML markup for share buttons
 */
function ane_share_this_post() {
	global $post;

	if ( ! $post ) {
		return '';
	}

	$title     = rawurlencode( get_the_title() );
	$excerpt   = rawurlencode( wp_trim_words( get_the_excerpt(), 20, '...' ) );
	$permalink = get_permalink( $post->ID );

	// Twitter handler from options
	$twitter_handler = get_option( 'twitter_handler' ) ? '&via=' . esc_attr( get_option( 'twitter_handler' ) ) : '';

	// Build share URLs
	$share_urls = array(
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $permalink ),
		'twitter'  => 'https://twitter.com/intent/tweet?text=' . $title . '%0A%0A' . $excerpt . '&url=' . rawurlencode( $permalink ) . $twitter_handler,
		'whatsapp' => 'https://wa.me/?text=' . $title . '%0A%0A' . $excerpt . '%0A%0A' . rawurlencode( $permalink ),
		'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( $permalink ) . '&title=' . $title . '&summary=' . $excerpt,
	);

	// Build share buttons HTML
	$content  = '<div class="social-share-buttons">';
	$content .= '<ul>';

	// Facebook Share
	$content .= '<li>';
	$content .= '<a id="share-facebook" class="facebook" href="' . esc_url( $share_urls['facebook'] ) . '" target="_blank" rel="nofollow noopener" aria-label="' . esc_attr__( 'Share on Facebook', 'elemenane' ) . '">';
	$content .= '<i class="ane-facebook"></i>';
	$content .= '</a>';
	$content .= '</li>';

	// Twitter Share
	$content .= '<li>';
	$content .= '<a id="share-twitter" class="twitter" href="' . esc_url( $share_urls['twitter'] ) . '" target="_blank" rel="nofollow noopener" aria-label="' . esc_attr__( 'Share on Twitter', 'elemenane' ) . '">';
	$content .= '<i class="ane-twitter"></i>';
	$content .= '</a>';
	$content .= '</li>';

	// WhatsApp Share
	$content .= '<li>';
	$content .= '<a id="share-whatsapp" class="whatsapp" href="' . esc_url( $share_urls['whatsapp'] ) . '" target="_blank" rel="nofollow noopener" aria-label="' . esc_attr__( 'Share on WhatsApp', 'elemenane' ) . '">';
	$content .= '<i class="ane-whatsapp"></i>';
	$content .= '</a>';
	$content .= '</li>';

	// LinkedIn Share
	$content .= '<li>';
	$content .= '<a id="share-linkedin" class="linkedin" href="' . esc_url( $share_urls['linkedin'] ) . '" target="_blank" rel="nofollow noopener" aria-label="' . esc_attr__( 'Share on LinkedIn', 'elemenane' ) . '">';
	$content .= '<i class="ane-linkedin"></i>';
	$content .= '</a>';
	$content .= '</li>';

	$content .= '</ul>';
	$content .= '</div>';

	return $content;
}

/**
 * Generate Facebook Comments embed code.
 *
 * Note: Facebook Comments requires Facebook SDK to be loaded.
 * This is handled in inc/tracking-scripts.php via ACF settings.
 *
 * Reference: https://developers.facebook.com/docs/plugins/comments/
 *
 * @return string HTML markup for Facebook Comments plugin
 */
function ane_load_facebook_comment() {
	global $post;

	if ( ! $post ) {
		return '';
	}

	// Check if Facebook Comments is enabled.
	if ( ! function_exists( 'ane_is_facebook_comments_enabled' ) || ! ane_is_facebook_comments_enabled() ) {
		return '';
	}

	$permalink = esc_url( get_permalink( $post->ID ) );

	// Get number of comments from ACF settings.
	$num_posts = function_exists( 'ane_get_facebook_comments_num_posts' ) ? ane_get_facebook_comments_num_posts() : 10;

	$content  = '<div class="fb-comments-wrapper">';
	$content .= '<div class="fb-comments" ';
	$content .= 'data-href="' . $permalink . '" ';
	$content .= 'data-width="" ';
	$content .= 'data-numposts="' . absint( $num_posts ) . '" ';
	$content .= 'data-order-by="social" ';
	$content .= 'data-colorscheme="light" ';
	$content .= 'data-lazy="true">';
	$content .= '</div>';
	$content .= '</div>';

	return $content;
}