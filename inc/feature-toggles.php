<?php
/**
 * Feature Toggles System
 *
 * Allows users to enable/disable theme features via ACF Options.
 * Provides centralized control for all optional theme functionality.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register ACF Field Group for Feature Toggles.
 *
 * Creates toggle switches in General Settings to enable/disable:
 * - Custom Post Types (Product, Service)
 * - Post Views Tracking
 * - Related Posts
 * - Social Media Sharing
 * - And more...
 *
 * @since 1.0.0
 */
function ane_register_feature_toggles_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_ane_feature_toggles',
			'title'    => __( 'Custom Post Types', 'elemenane' ),
			'fields'   => array(
				// Header.
				array(
					'key'     => 'field_ane_cpt_header',
					'label'   => __( 'Enable or Disable Custom Post Types', 'elemenane' ),
					'type'    => 'message',
					'message' => __( 'Control which custom post types are active. Note: Disabling will hide the post type from admin menu but will not delete existing data.', 'elemenane' ),
				),
				// Product CPT Toggle.
				array(
					'key'           => 'field_ane_enable_product_cpt',
					'label'         => __( 'Enable Product Post Type', 'elemenane' ),
					'name'          => 'ane_enable_product_cpt',
					'type'          => 'true_false',
					'instructions'  => __( 'Enable custom post type for products/portfolio items. Slug: /product/', 'elemenane' ),
					'ui'            => 1,
					'default_value' => 1,
				),
				// Service CPT Toggle.
				array(
					'key'           => 'field_ane_enable_service_cpt',
					'label'         => __( 'Enable Service Post Type', 'elemenane' ),
					'name'          => 'ane_enable_service_cpt',
					'type'          => 'true_false',
					'instructions'  => __( 'Enable custom post type for services/offerings. Slug: /service/', 'elemenane' ),
					'ui'            => 1,
					'default_value' => 0,
				),
				// Branch CPT Toggle.
				array(
					'key'           => 'field_ane_enable_branch_cpt',
					'label'         => __( 'Enable Branch Post Type', 'elemenane' ),
					'name'          => 'ane_enable_branch_cpt',
					'type'          => 'true_false',
					'instructions'  => __( 'Enable custom post type for branches/locations. Slug: /branch/', 'elemenane' ),
					'ui'            => 1,
					'default_value' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ane-general-setting',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'ane_register_feature_toggles_fields' );

/**
 * ============================================================================
 * HELPER FUNCTIONS - Check Feature Status
 * ============================================================================
 */

/**
 * Check if Product CPT is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_product_cpt_enabled() {
	$enabled = get_field( 'ane_enable_product_cpt', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Service CPT is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_service_cpt_enabled() {
	$enabled = get_field( 'ane_enable_service_cpt', 'option' );
	return (bool) $enabled; // Default: disabled
}

/**
 * Check if Post Views Tracking is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_post_views_enabled() {
	$enabled = get_field( 'ane_enable_post_views', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Related Posts is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_related_posts_enabled() {
	$enabled = get_field( 'ane_enable_related_posts', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Social Sharing is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_social_sharing_enabled() {
	$enabled = get_field( 'ane_enable_social_sharing', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Breadcrumbs is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_breadcrumbs_enabled() {
	$enabled = get_field( 'ane_enable_breadcrumbs', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Lazy Load is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_lazy_load_enabled() {
	$enabled = get_field( 'ane_enable_lazy_load', 'option' );
	return $enabled !== false ? (bool) $enabled : true; // Default: enabled
}

/**
 * Check if Emojis are disabled.
 *
 * @return bool True if disabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_emojis_disabled() {
	$disabled = get_field( 'ane_disable_emojis', 'option' );
	return $disabled !== false ? (bool) $disabled : true; // Default: disabled
}

/**
 * ============================================================================
 * FEATURE IMPLEMENTATIONS
 * ============================================================================
 */

/**
 * Disable WordPress emojis if toggle is enabled.
 *
 * @since 1.0.0
 */
function ane_maybe_disable_emojis() {
	if ( ! ane_is_emojis_disabled() ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove from TinyMCE.
	add_filter( 'tiny_mce_plugins', 'ane_disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'ane_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'ane_maybe_disable_emojis' );

/**
 * Filter function to remove emoji TinyMCE plugin.
 *
 * @param array $plugins TinyMCE plugins.
 * @return array Modified plugins array.
 * @since 1.0.0
 */
function ane_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}

	return array();
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference between the two arrays.
 * @since 1.0.0
 */
function ane_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls          = array_diff( $urls, array( $emoji_svg_url ) );
	}

	return $urls;
}

/**
 * Add loading="lazy" attribute to images if enabled.
 *
 * @param string $content Post content.
 * @return string Modified content.
 * @since 1.0.0
 */
function ane_add_lazy_load_to_images( $content ) {
	if ( ! ane_is_lazy_load_enabled() || is_admin() ) {
		return $content;
	}

	// Add loading="lazy" to img tags that don't already have it.
	$content = preg_replace( '/<img((?![^>]*loading=)[^>]*)>/i', '<img$1 loading="lazy">', $content );

	return $content;
}
add_filter( 'the_content', 'ane_add_lazy_load_to_images', 20 );
add_filter( 'post_thumbnail_html', 'ane_add_lazy_load_to_images', 20 );
