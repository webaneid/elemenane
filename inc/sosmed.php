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
 * Title, description and URL used when sharing a post.
 *
 * Description order: Yoast SEO meta description (with %%variables%% resolved) -> manual excerpt ->
 * first words of the content. `text` is "Title\n\nDescription\n\nURL", the same block WhatsApp receives
 * and the Copy button puts on the clipboard.
 *
 * @param WP_Post|int|null $post Post (defaults to the current post).
 * @return array{title:string,description:string,url:string,text:string,source:string}
 */
function elemenane_get_share_data( $post = null ) {
	$post = get_post( $post );
	if ( ! $post instanceof WP_Post ) {
		return array(
			'title'       => '',
			'description' => '',
			'url'         => '',
			'text'        => '',
			'source'      => '',
		);
	}

	$title       = html_entity_decode( wp_strip_all_tags( get_the_title( $post ) ), ENT_QUOTES, 'UTF-8' );
	$url         = get_permalink( $post );
	$description = '';
	$source      = '';

	$seo = trim( (string) get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true ) );
	if ( '' !== $seo && function_exists( 'wpseo_replace_vars' ) ) {
		$seo = trim( (string) wpseo_replace_vars( $seo, $post ) );
	}
	if ( '' !== $seo && false === strpos( $seo, '%%' ) ) {
		$description = $seo;
		$source      = 'seo';
	} elseif ( has_excerpt( $post ) ) {
		$description = (string) get_the_excerpt( $post );
		$source      = 'excerpt';
	} else {
		$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '...' );
		$source      = 'content';
	}
	$description = trim( html_entity_decode( wp_strip_all_tags( $description ), ENT_QUOTES, 'UTF-8' ) );

	$parts = array_filter(
		array( $title, $description, (string) $url ),
		static function ( $part ) {
			return '' !== $part;
		}
	);

	return array(
		'title'       => $title,
		'description' => $description,
		'url'         => (string) $url,
		'text'        => implode( "\n\n", $parts ),
		'source'      => $source,
	);
}

/**
 * Generate the share bar for the current post / page.
 *
 * Buttons: WhatsApp, Facebook, X, LinkedIn, email, copy (title + description + link) and, on devices that
 * support it, the native share sheet (js/share.js shows that button). Every channel that accepts free text
 * receives title, description and URL.
 *
 * @param string $label Optional heading text (defaults to "Share").
 * @return string HTML markup for the share bar
 */
function ane_share_this_post( $label = '' ) {
	$data = elemenane_get_share_data();
	if ( '' === $data['url'] ) {
		return '';
	}

	$label = '' !== $label ? $label : __( 'Share', 'elemenane' );
	$url   = rawurlencode( $data['url'] );

	// X has a character limit, so keep its text short: title + a trimmed description.
	$x_text   = $data['title'];
	$x_extra  = wp_trim_words( $data['description'], 18, '...' );
	$x_text  .= '' !== $x_extra ? "\n\n" . $x_extra : '';
	$twitter  = get_option( 'twitter_handler' ) ? '&via=' . rawurlencode( (string) get_option( 'twitter_handler' ) ) : '';
	$wa_title = '' !== $data['title'] ? '*' . $data['title'] . '*' : '';
	$wa_text  = implode(
		"\n\n",
		array_filter(
			array( $wa_title, $data['description'], $data['url'] ),
			static function ( $part ) {
				return '' !== $part;
			}
		)
	);

	$channels = array(
		'whatsapp' => array(
			'label' => 'WhatsApp',
			'name'  => __( 'Share on WhatsApp', 'elemenane' ),
			'href'  => 'https://wa.me/?text=' . rawurlencode( $wa_text ),
		),
		'facebook' => array(
			'label' => 'Facebook',
			'name'  => __( 'Share on Facebook', 'elemenane' ),
			'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		),
		'twitter'  => array(
			'label' => 'X',
			'name'  => __( 'Share on X', 'elemenane' ),
			'href'  => 'https://twitter.com/intent/tweet?text=' . rawurlencode( $x_text ) . '&url=' . $url . $twitter,
		),
		'linkedin' => array(
			'label' => 'LinkedIn',
			'name'  => __( 'Share on LinkedIn', 'elemenane' ),
			'href'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		),
		'email'    => array(
			'label' => __( 'Email', 'elemenane' ),
			'name'  => __( 'Share by email', 'elemenane' ),
			'href'  => 'mailto:?subject=' . rawurlencode( $data['title'] ) . '&body=' . rawurlencode( $data['text'] ),
		),
	);

	$html  = '<div class="ane-share" data-ane-share data-title="' . esc_attr( $data['title'] ) . '" data-text="' . esc_attr( $data['description'] ) . '" data-url="' . esc_url( $data['url'] ) . '" data-copy="' . esc_attr( $data['text'] ) . '">';
	$html .= '<span class="ane-share__label">' . esc_html( $label ) . '</span>';
	$html .= '<ul class="ane-share__list">';

	foreach ( $channels as $key => $channel ) {
		$is_mail = 'email' === $key;
		// Not esc_url(): it strips the %0A line breaks. Hrefs are built above from fixed schemes + rawurlencode()d values.
		$html   .= '<li><a class="ane-share__btn ane-share__btn--' . esc_attr( $key ) . '" href="' . esc_attr( $channel['href'] ) . '"' . ( $is_mail ? '' : ' target="_blank" rel="nofollow noopener"' ) . ' aria-label="' . esc_attr( $channel['name'] ) . '">';
		$html   .= elemenane_get_icon_svg( $key );
		$html   .= '<span class="ane-share__name">' . esc_html( $channel['label'] ) . '</span></a></li>';
	}

	$html .= '<li><button type="button" class="ane-share__btn ane-share__btn--copy" data-share-copy aria-label="' . esc_attr__( 'Copy title, description and link', 'elemenane' ) . '">';
	$html .= elemenane_get_icon_svg( 'copy' );
	$html .= '<span class="ane-share__name">' . esc_html__( 'Copy link', 'elemenane' ) . '</span></button></li>';

	$html .= '<li class="ane-share__native"><button type="button" class="ane-share__btn ane-share__btn--native" data-share-native aria-label="' . esc_attr__( 'Share via...', 'elemenane' ) . '">';
	$html .= elemenane_get_icon_svg( 'share' );
	$html .= '<span class="ane-share__name">' . esc_html__( 'More', 'elemenane' ) . '</span></button></li>';

	$html .= '</ul>';
	$html .= '<span class="ane-share__toast" role="status" aria-live="polite"></span>';
	$html .= '</div>';

	return $html;
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
