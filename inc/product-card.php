<?php
/**
 * Product card data + helpers (tp/content-product.php).
 *
 * One card for both data sources: the theme's own product post type (ACF fields stored in WooCommerce's meta keys)
 * and WooCommerce when it is active. Both produce the same array, so the card looks and behaves the same; with
 * WooCommerce active the card additionally shows ratings and WooCommerce's own add-to-cart / select-options button.
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * The WooCommerce product object for an ID, or null when WooCommerce is not active.
 *
 * @param int $product_id Product post ID.
 * @return object|null WC_Product (typed loosely: WooCommerce may not be installed).
 */
function elemenane_get_wc_product( $product_id ) {
	if ( ! ane_is_woocommerce_active() || ! function_exists( 'wc_get_product' ) ) {
		return null;
	}

	$product = wc_get_product( $product_id );

	return $product ? $product : null;
}

/**
 * Format an amount: WooCommerce's formatter (currency settings) when active, otherwise Rupiah.
 *
 * @param float $amount Amount.
 * @return string HTML-safe string (run through wp_kses_post() when printing).
 */
function elemenane_format_price( $amount ) {
	if ( ane_is_woocommerce_active() && function_exists( 'wc_price' ) ) {
		return (string) wc_price( (float) $amount );
	}

	return esc_html( ane_format_rupiah( $amount ) );
}

/**
 * Normalised data for one product card.
 *
 * @param int|null $product_id Product post ID (defaults to the current post).
 * @return array{
 *     id:int, title:string, url:string, thumb_id:int, category:?WP_Term, sku:string,
 *     regular:float, sale:float, price:float, on_sale:bool, discount:int, price_html:string,
 *     stock:string, is_new:bool, rating:float, rating_count:int, woocommerce:bool,
 *     simple:bool
 * }
 */
function elemenane_get_product_card_data( $product_id = null ) {
	$product_id = $product_id ? absint( $product_id ) : (int) get_the_ID();
	$wc         = elemenane_get_wc_product( $product_id );
	$prices     = ane_get_product_price_data( $product_id );

	$categories = get_the_terms( $product_id, 'product_cat' );
	$category   = ( $categories && ! is_wp_error( $categories ) ) ? reset( $categories ) : null;

	$simple     = true;
	$price_html = '';
	if ( $wc ) {
		$simple = $wc->is_type( 'simple' );
		// Variable / grouped products show WooCommerce's own range ("Rp 20.000 – Rp 40.000").
		if ( ! $simple ) {
			$price_html = (string) $wc->get_price_html();
		}
	}

	$rating       = 0.0;
	$rating_count = 0;
	if ( $wc && function_exists( 'wc_review_ratings_enabled' ) && wc_review_ratings_enabled() ) {
		$rating       = (float) $wc->get_average_rating();
		$rating_count = (int) $wc->get_rating_count();
	}

	$post_time = (int) get_post_time( 'U', true, $product_id );

	return array(
		'id'           => $product_id,
		'title'        => get_the_title( $product_id ),
		'url'          => (string) get_permalink( $product_id ),
		'thumb_id'     => (int) get_post_thumbnail_id( $product_id ),
		'category'     => $category instanceof WP_Term ? $category : null,
		'sku'          => (string) get_post_meta( $product_id, '_sku', true ),
		'regular'      => $prices['regular'],
		'sale'         => $prices['sale'],
		'price'        => $prices['price'],
		'on_sale'      => $prices['on_sale'],
		'discount'     => $prices['discount'],
		'price_html'   => $price_html,
		'stock'        => ane_get_product_stock_status( $product_id ),
		'is_new'       => $post_time > 0 && ( time() - $post_time ) <= 7 * DAY_IN_SECONDS,
		'rating'       => $rating,
		'rating_count' => $rating_count,
		'woocommerce'  => null !== $wc,
		'simple'       => $simple,
	);
}

/**
 * Add the theme's button class to WooCommerce's loop add-to-cart button (keeps WooCommerce's own classes so its
 * AJAX add-to-cart script still works).
 *
 * @param array $args Loop add-to-cart arguments.
 * @return array
 */
function elemenane_loop_add_to_cart_args( $args ) {
	$args['class'] = trim( ( isset( $args['class'] ) ? $args['class'] : '' ) . ' ane-product-card__btn' );

	return $args;
}

/**
 * Print the card call-to-action.
 *
 * - WooCommerce active: WooCommerce's own loop button (Add to cart / Select options / Read more).
 * - Otherwise: a "View details" link.
 *
 * @param array $data Card data from elemenane_get_product_card_data().
 */
function elemenane_product_card_cta( $data ) {
	if ( $data['woocommerce'] && function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {
		global $product;
		$previous = $product;
		$product  = elemenane_get_wc_product( $data['id'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- WooCommerce's loop button reads the global.

		add_filter( 'woocommerce_loop_add_to_cart_args', 'elemenane_loop_add_to_cart_args' );
		woocommerce_template_loop_add_to_cart();
		remove_filter( 'woocommerce_loop_add_to_cart_args', 'elemenane_loop_add_to_cart_args' );

		$product = $previous; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		return;
	}

	printf(
		'<a class="ane-product-card__btn ane-product-card__btn--view" href="%s">%s%s</a>',
		esc_url( $data['url'] ),
		esc_html__( 'View details', 'elemenane' ),
		elemenane_get_icon_svg( 'chevron-right' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG from the theme.
	);
}
