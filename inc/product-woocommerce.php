<?php
/**
 * WooCommerce data compatibility for the product CPT.
 *
 * The theme's products use WooCommerce's post type, taxonomies and meta keys,
 * so installing WooCommerce later keeps the catalog. This module keeps the
 * stored formats identical to WooCommerce's and hands over to WooCommerce
 * when it is active:
 *
 * - Gallery:      `_product_image_gallery`, comma-separated attachment IDs.
 * - Manage stock: `_manage_stock`, "yes" / "no".
 * - With WooCommerce active, the theme does not register the post type
 *   (inc/custompost.php), hides its own price/stock/gallery fields (edited in
 *   WooCommerce's Product data box) and declares theme support so WooCommerce
 *   renders products with the theme's templates.
 *
 * @package elemenane
 * @since 1.1.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function ane_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Declare WooCommerce support so WooCommerce loads the theme's product
 * templates (single-product.php, archive-product.php, taxonomy-product_*.php)
 * instead of injecting its own product page into the_content().
 */
function ane_woocommerce_theme_support() {
	if ( ane_is_woocommerce_active() ) {
		add_theme_support( 'woocommerce' );
	}
}
add_action( 'after_setup_theme', 'ane_woocommerce_theme_support' );

/**
 * Read a WooCommerce-style boolean ("yes"/"no", also "1"/"0" and true/false).
 * Same rules as wc_string_to_bool().
 *
 * @param mixed $value Stored value.
 * @return bool
 */
function ane_wc_bool( $value ) {
	if ( is_bool( $value ) ) {
		return $value;
	}

	$value = strtolower( (string) $value );

	return 'yes' === $value || '1' === $value || 'true' === $value;
}

/**
 * Gallery image IDs for a product (featured image not included), from
 * WooCommerce's `_product_image_gallery`, falling back to the theme's former
 * `ane_product_gallery` meta for products not yet migrated.
 *
 * @param int $product_id Product ID.
 * @return int[]
 */
function ane_get_product_gallery_ids( $product_id ) {
	if ( metadata_exists( 'post', $product_id, '_product_image_gallery' ) ) {
		$ids = get_post_meta( $product_id, '_product_image_gallery', true );
	} else {
		$ids = (array) get_post_meta( $product_id, 'ane_product_gallery', true );
	}

	// wp_parse_id_list() turns an empty value into ID 0.
	return array_values( array_filter( wp_parse_id_list( $ids ) ) );
}

/**
 * Store the ACF gallery field as WooCommerce does: "12,34,56".
 *
 * @param mixed $value Attachment IDs from ACF.
 * @return string
 */
function ane_acf_gallery_to_wc( $value ) {
	return implode( ',', array_filter( wp_parse_id_list( (array) $value ) ) );
}
add_filter( 'acf/update_value/key=field_product_gallery', 'ane_acf_gallery_to_wc' );

/**
 * Give ACF the gallery as an array of IDs, reading the old meta for products
 * not yet migrated.
 *
 * @param mixed      $value   Stored value.
 * @param int|string $post_id Post ID.
 * @return int[]
 */
function ane_acf_gallery_from_wc( $value, $post_id ) {
	if ( is_numeric( $post_id ) ) {
		return ane_get_product_gallery_ids( (int) $post_id );
	}

	return array_values( array_filter( wp_parse_id_list( (array) $value ) ) );
}
add_filter( 'acf/load_value/key=field_product_gallery', 'ane_acf_gallery_from_wc', 10, 2 );

/**
 * Store the "Manage stock" switch as "yes"/"no" like WooCommerce.
 *
 * @param mixed $value ACF true/false value.
 * @return string
 */
function ane_acf_manage_stock_to_wc( $value ) {
	return ane_wc_bool( $value ) ? 'yes' : 'no';
}
add_filter( 'acf/update_value/key=field_product_manage_stock', 'ane_acf_manage_stock_to_wc' );

/**
 * Give ACF's true/false field 1 or 0 for a stored "yes"/"no".
 *
 * @param mixed $value Stored value.
 * @return int
 */
function ane_acf_manage_stock_from_wc( $value ) {
	return ane_wc_bool( $value ) ? 1 : 0;
}
add_filter( 'acf/load_value/key=field_product_manage_stock', 'ane_acf_manage_stock_from_wc' );

/**
 * One-time migration of existing products to WooCommerce's formats:
 * gallery to `_product_image_gallery` and `_manage_stock` to "yes"/"no".
 * The old `ane_product_gallery` meta is kept, so nothing is lost.
 */
function ane_migrate_products_to_wc_format() {
	if ( (int) get_option( 'ane_product_wc_format' ) >= 1 ) {
		return;
	}

	global $wpdb;

	// Gallery: copy to the WooCommerce key where it isn't set yet.
	$rows = $wpdb->get_results(
		"SELECT m.post_id, m.meta_value FROM {$wpdb->postmeta} m
		INNER JOIN {$wpdb->posts} p ON p.ID = m.post_id AND p.post_type = 'product'
		WHERE m.meta_key = 'ane_product_gallery' AND m.meta_value <> ''"
	);
	foreach ( $rows as $row ) {
		if ( ! metadata_exists( 'post', $row->post_id, '_product_image_gallery' ) ) {
			$ids = array_filter( wp_parse_id_list( (array) maybe_unserialize( $row->meta_value ) ) );
			update_post_meta( $row->post_id, '_product_image_gallery', implode( ',', $ids ) );
		}
	}

	// Manage stock: "1"/"0" (ACF true/false) to "yes"/"no", products only.
	$wpdb->query(
		"UPDATE {$wpdb->postmeta} m INNER JOIN {$wpdb->posts} p ON p.ID = m.post_id AND p.post_type = 'product'
		SET m.meta_value = CASE WHEN m.meta_value = '1' THEN 'yes' ELSE 'no' END
		WHERE m.meta_key = '_manage_stock' AND m.meta_value IN ( '1', '0', '' )"
	);

	if ( function_exists( 'wp_cache_flush_group' ) && wp_cache_supports( 'flush_group' ) ) {
		wp_cache_flush_group( 'post_meta' );
	} else {
		wp_cache_flush();
	}

	update_option( 'ane_product_wc_format', 1, false );
}
add_action( 'admin_init', 'ane_migrate_products_to_wc_format' );

/**
 * WooCommerce moves the product archive from /product/ to its Shop page
 * (/shop/). Redirect the theme's old archive URLs there so existing links and
 * search results keep working, including pagination and filter parameters.
 */
function ane_redirect_old_product_archive() {
	if ( ! ane_is_woocommerce_active() || ! is_404() || ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}

	$shop = wc_get_page_permalink( 'shop' );
	if ( ! $shop || untrailingslashit( $shop ) === untrailingslashit( home_url() ) ) {
		return;
	}

	$path      = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$home_path = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = substr( $path, strlen( $home_path ) + 1 );
	}

	// /product/ and /product/page/2/ (the product permalink base).
	$base = function_exists( 'wc_get_permalink_structure' ) ? wc_get_permalink_structure()['product_rewrite_slug'] : 'product';
	if ( ! preg_match( '#^' . preg_quote( trim( $base, '/' ), '#' ) . '(?:/page/(\d+))?$#', $path, $match ) ) {
		return;
	}

	$url = trailingslashit( $shop );
	if ( ! empty( $match[1] ) && (int) $match[1] > 1 ) {
		$url .= 'page/' . (int) $match[1] . '/';
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- carrying over read-only filter parameters.
	$url = add_query_arg( urlencode_deep( wp_unslash( $_GET ) ), $url );

	wp_safe_redirect( $url, 301 );
	exit;
}
add_action( 'template_redirect', 'ane_redirect_old_product_archive' );
