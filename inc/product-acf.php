<?php
/**
 * Product ACF Configuration
 *
 * ACF field groups and functionality for Product CPT.
 * Uses WooCommerce-compatible meta keys for seamless integration.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Product ACF Field Groups.
 *
 * Uses WooCommerce meta key naming convention for compatibility:
 * - _regular_price: Regular price
 * - _sale_price: Sale/discount price
 * - _price: Active price (automatically set to lowest)
 * - _sku: Product SKU
 * - _stock_status: Stock status (instock/outofstock)
 * - _manage_stock: Whether to manage stock ("yes"/"no")
 * - _stock: Stock quantity
 * - _product_image_gallery: Gallery image IDs ("12,34,56")
 *
 * These are WooCommerce's meta keys and formats, so the data carries over when
 * WooCommerce is installed (see inc/product-woocommerce.php).
 *
 * @since 1.0.0
 */
function ane_register_product_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// With WooCommerce active, price, stock and gallery are edited in its
	// Product data and Product gallery boxes (same meta keys).
	$woocommerce = ane_is_woocommerce_active();

	// Product Pricing & Basic Info.
	if ( ! $woocommerce ) {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_product_pricing',
				'title'                 => __( 'Product Information', 'elemenane' ),
				'fields'                => array(
					// SKU.
					array(
						'key'          => 'field_product_sku',
						'label'        => __( 'SKU', 'elemenane' ),
						'name'         => '_sku',
						'type'         => 'text',
						'instructions' => __( 'Product SKU (Stock Keeping Unit)', 'elemenane' ),
						'required'     => 0,
						'wrapper'      => array(
							'width' => '50',
						),
					),
					// Regular Price.
					array(
						'key'          => 'field_product_regular_price',
						'label'        => __( 'Regular Price', 'elemenane' ),
						'name'         => '_regular_price',
						'type'         => 'number',
						'instructions' => __( 'Regular price in rupiah (without Rp and thousand separator)', 'elemenane' ),
						'required'     => 1,
						'min'          => 0,
						'step'         => 1,
						'wrapper'      => array(
							'width' => '50',
						),
					),
					// Sale Price.
					array(
						'key'          => 'field_product_sale_price',
						'label'        => __( 'Sale Price', 'elemenane' ),
						'name'         => '_sale_price',
						'type'         => 'number',
						'instructions' => __( 'Sale/discount price in rupiah (leave empty if no discount)', 'elemenane' ),
						'required'     => 0,
						'min'          => 0,
						'step'         => 1,
						'wrapper'      => array(
							'width' => '50',
						),
					),
					// Stock Status.
					array(
						'key'           => 'field_product_stock_status',
						'label'         => __( 'Stock Status', 'elemenane' ),
						'name'          => '_stock_status',
						'type'          => 'select',
						'instructions'  => __( 'Product stock availability', 'elemenane' ),
						'required'      => 1,
						'choices'       => array(
							'instock'    => __( 'In Stock', 'elemenane' ),
							'outofstock' => __( 'Out of Stock', 'elemenane' ),
						),
						'default_value' => 'instock',
						'wrapper'       => array(
							'width' => '50',
						),
					),
					// Manage Stock.
					array(
						'key'           => 'field_product_manage_stock',
						'label'         => __( 'Manage Stock', 'elemenane' ),
						'name'          => '_manage_stock',
						'type'          => 'true_false',
						'instructions'  => __( 'Enable stock quantity management', 'elemenane' ),
						'required'      => 0,
						'default_value' => 0,
						'ui'            => 1,
						'wrapper'       => array(
							'width' => '50',
						),
					),
					// Stock Quantity.
					array(
						'key'               => 'field_product_stock',
						'label'             => __( 'Stock Quantity', 'elemenane' ),
						'name'              => '_stock',
						'type'              => 'number',
						'instructions'      => __( 'Stock quantity (only if manage stock is enabled)', 'elemenane' ),
						'required'          => 0,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_product_manage_stock',
									'operator' => '==',
									'value'    => '1',
								),
							),
						),
						'min'               => 0,
						'step'              => 1,
						'wrapper'           => array(
							'width' => '50',
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'product',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'seamless',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
			)
		);
	}

	// Product Gallery.
	if ( ! $woocommerce ) {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_product_gallery',
				'title'                 => __( 'Product Gallery', 'elemenane' ),
				'fields'                => array(
					array(
						'key'           => 'field_product_gallery',
						'label'         => __( 'Product Images', 'elemenane' ),
						'name'          => '_product_image_gallery', // WooCommerce's key; stored as "12,34" (inc/product-woocommerce.php).
						'type'          => 'gallery',
						'instructions'  => __( 'Upload product images (recommended: 1000x1000px)', 'elemenane' ),
						'required'      => 0,
						'return_format' => 'id',
						'min'           => 0,
						'max'           => 20,
						'insert'        => 'append',
						'library'       => 'all',
						'min_width'     => 500,
						'min_height'    => 500,
						'mime_types'    => 'jpg,jpeg,png,webp',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'product',
						),
					),
				),
				'menu_order'            => 1,
				'position'              => 'normal',
				'style'                 => 'seamless',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
			)
		);
	}

	// Product Specifications.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_product_specs',
			'title'                 => __( 'Product Specifications', 'elemenane' ),
			'fields'                => array(
				array(
					'key'          => 'field_product_specs',
					'label'        => __( 'Specifications', 'elemenane' ),
					'name'         => 'ane_product_specs',
					'type'         => 'repeater',
					'instructions' => __( 'Add product specifications (size, weight, material, etc.)', 'elemenane' ),
					'required'     => 0,
					'collapsed'    => '',
					'layout'       => 'row',
					'button_label' => __( 'Add Specification', 'elemenane' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_spec_label',
							'label' => __( 'Label', 'elemenane' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_spec_value',
							'label' => __( 'Value', 'elemenane' ),
							'name'  => 'value',
							'type'  => 'text',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);

	// Product Features.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_product_features',
			'title'                 => __( 'Product Features', 'elemenane' ),
			'fields'                => array(
				array(
					'key'          => 'field_product_features',
					'label'        => __( 'Features', 'elemenane' ),
					'name'         => 'ane_product_features',
					'type'         => 'repeater',
					'instructions' => __( 'Add product key features', 'elemenane' ),
					'required'     => 0,
					'collapsed'    => '',
					'layout'       => 'row',
					'button_label' => __( 'Add Feature', 'elemenane' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_feature_text',
							'label' => __( 'Feature', 'elemenane' ),
							'name'  => 'feature',
							'type'  => 'text',
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'menu_order'            => 3,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);

	// Product Marketplace Links.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_product_marketplace',
			'title'                 => __( 'Marketplace Links', 'elemenane' ),
			'fields'                => array(
				array(
					'key'          => 'field_product_tokopedia',
					'label'        => __( 'Tokopedia', 'elemenane' ),
					'name'         => 'ane_product_tokopedia',
					'type'         => 'url',
					'instructions' => __( 'Product URL on Tokopedia', 'elemenane' ),
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_product_shopee',
					'label'        => __( 'Shopee', 'elemenane' ),
					'name'         => 'ane_product_shopee',
					'type'         => 'url',
					'instructions' => __( 'Product URL on Shopee', 'elemenane' ),
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_product_lazada',
					'label'        => __( 'Lazada', 'elemenane' ),
					'name'         => 'ane_product_lazada',
					'type'         => 'url',
					'instructions' => __( 'Product URL on Lazada', 'elemenane' ),
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_product_blibli',
					'label'        => __( 'Blibli', 'elemenane' ),
					'name'         => 'ane_product_blibli',
					'type'         => 'url',
					'instructions' => __( 'Product URL on Blibli', 'elemenane' ),
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_product_tiktok',
					'label'        => __( 'TikTok Shop', 'elemenane' ),
					'name'         => 'ane_product_tiktok',
					'type'         => 'url',
					'instructions' => __( 'Product URL on TikTok Shop', 'elemenane' ),
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'menu_order'            => 4,
			'position'              => 'normal',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'ane_register_product_acf_fields' );

/**
 * Auto-calculate and save active price (_price).
 *
 * Sets _price to the price shown on the site (see ane_get_product_price_data()):
 * the sale price when on sale, otherwise the regular price.
 *
 * @param int $post_id Post ID being saved.
 * @since 1.0.0
 */
function ane_product_save_active_price( $post_id ) {
	// Check if this is a product post type. WooCommerce maintains _price
	// itself (sale schedules, several rows for variable products).
	if ( get_post_type( $post_id ) !== 'product' || ane_is_woocommerce_active() ) {
		return;
	}

	// Same price the templates show; 0 when the product has no price.
	$prices = ane_get_product_price_data( $post_id );

	update_post_meta( $post_id, '_price', $prices['price'] );
}
add_action( 'acf/save_post', 'ane_product_save_active_price', 20 );

/**
 * One-time backfill of _price for products saved before it existed or
 * imported without it. The archive price filter and sorting read _price.
 *
 * @since 1.1.7
 */
function ane_product_backfill_active_price() {
	if ( get_option( 'ane_product_price_backfilled' ) || ane_is_woocommerce_active() ) {
		return;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	foreach ( $ids as $id ) {
		$price = ane_get_product_price_data( $id )['price'];
		if ( (float) get_post_meta( $id, '_price', true ) !== (float) $price ) {
			update_post_meta( $id, '_price', $price );
		}
	}

	update_option( 'ane_product_price_backfilled', 1, false );
}
add_action( 'admin_init', 'ane_product_backfill_active_price' );

/**
 * Product price data for display.
 *
 * Prices are read as numbers, so empty values and "0"/"0.00" all count as no
 * price. Templates show nothing when `price` is 0.
 *
 * @param int $product_id Product post ID.
 * @return array {
 *     @type float $regular  Regular price (0 if not set).
 *     @type float $sale     Sale price (0 if not set).
 *     @type float $price    Price to show: the sale price when on sale, else the
 *                           regular price, else a sale price set without a
 *                           regular price. 0 when there is nothing to show.
 *     @type bool  $on_sale  True when a valid sale price is below the regular price.
 *     @type int   $discount Discount percentage when on sale, else 0.
 * }
 * @since 1.1.7
 */
function ane_get_product_price_data( $product_id ) {
	$product = ane_is_woocommerce_active() && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;

	if ( $product ) {
		// WooCommerce knows about sale schedules and variable products (where
		// these are the lowest variation prices).
		$regular = max( 0, (float) $product->get_regular_price() );
		$sale    = $product->is_on_sale() ? max( 0, (float) $product->get_sale_price() ) : 0;
	} else {
		// Same meta keys and format as WooCommerce.
		$regular = max( 0, (float) get_post_meta( $product_id, '_regular_price', true ) );
		$sale    = max( 0, (float) get_post_meta( $product_id, '_sale_price', true ) );
	}

	$on_sale = $regular > 0 && $sale > 0 && $sale < $regular;

	if ( $on_sale ) {
		$price = $sale;
	} elseif ( $regular > 0 ) {
		$price = $regular;
	} else {
		$price = $sale;
	}

	return array(
		'regular'  => $regular,
		'sale'     => $sale,
		'price'    => $price,
		'on_sale'  => $on_sale,
		'discount' => $on_sale ? (int) round( ( ( $regular - $sale ) / $regular ) * 100 ) : 0,
	);
}

/**
 * Format an amount as Rupiah, e.g. "Rp 230.000".
 *
 * @param float $amount Amount.
 * @return string
 * @since 1.1.7
 */
function ane_format_rupiah( $amount ) {
	return 'Rp ' . number_format( (float) $amount, 0, ',', '.' );
}

/**
 * Get formatted product price with currency.
 *
 * @param int  $product_id Product post ID.
 * @param bool $show_sale  Whether to show sale price separately.
 * @return string Formatted price HTML, or '' when the product has no price.
 * @since 1.0.0
 */
function ane_get_product_price( $product_id, $show_sale = true ) {
	$prices = ane_get_product_price_data( $product_id );

	if ( $prices['price'] <= 0 ) {
		return '';
	}

	$html = '<div class="ane-product-price">';

	if ( $show_sale && $prices['on_sale'] ) {
		$html .= '<span class="ane-price-sale">' . esc_html( ane_format_rupiah( $prices['sale'] ) ) . '</span>';
		$html .= '<span class="ane-price-regular ane-price-strikethrough">' . esc_html( ane_format_rupiah( $prices['regular'] ) ) . '</span>';
		$html .= '<span class="ane-price-discount">-' . esc_html( $prices['discount'] ) . '%</span>';
	} else {
		$html .= '<span class="ane-price-regular">' . esc_html( ane_format_rupiah( $show_sale ? $prices['price'] : ( $prices['regular'] ?: $prices['price'] ) ) ) . '</span>';
	}

	$html .= '</div>';

	return $html;
}

/**
 * Get product stock status.
 *
 * @param int $product_id Product post ID.
 * @return string 'instock' or 'outofstock'.
 * @since 1.0.0
 */
function ane_get_product_stock_status( $product_id ) {
	$product = ane_is_woocommerce_active() && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;

	if ( $product ) {
		$stock_status = $product->get_stock_status();
	} else {
		$stock_status = get_post_meta( $product_id, '_stock_status', true );

		// If managing stock, check quantity. "_manage_stock" is "yes"/"no" like
		// WooCommerce ("1"/"0" before 1.1.8).
		if ( ane_wc_bool( get_post_meta( $product_id, '_manage_stock', true ) ) && (float) get_post_meta( $product_id, '_stock', true ) <= 0 ) {
			return 'outofstock';
		}
	}

	// Backorders can still be ordered, so the theme shows them as available.
	if ( 'onbackorder' === $stock_status ) {
		return 'instock';
	}

	return $stock_status ? $stock_status : 'instock';
}

/**
 * Get related products by category.
 *
 * @param int $product_id Product post ID.
 * @param int $limit      Number of products to return.
 * @return array Array of product post IDs.
 * @since 1.0.0
 */
function ane_get_related_products( $product_id, $limit = 4 ) {
	// Get products from same category.
	$categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

	if ( empty( $categories ) ) {
		return array();
	}

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => $limit,
		'post__not_in'   => array( $product_id ),
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $categories,
			),
		),
		'orderby'        => 'rand',
	);

	$query = new WP_Query( $args );

	$related_ids = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$related_ids[] = get_the_ID();
		}
		wp_reset_postdata();
	}

	return $related_ids;
}

/**
 * Get product marketplace links.
 *
 * @param int $product_id Product post ID.
 * @return array Array of marketplace data with 'name', 'url', 'icon'.
 * @since 1.0.0
 */
function ane_get_product_marketplaces( $product_id ) {
	$marketplaces = array(
		'tokopedia' => array(
			'name' => 'Tokopedia',
			'url'  => get_field( 'ane_product_tokopedia', $product_id ),
			'icon' => 'tokopedia',
		),
		'shopee'    => array(
			'name' => 'Shopee',
			'url'  => get_field( 'ane_product_shopee', $product_id ),
			'icon' => 'shopee',
		),
		'lazada'    => array(
			'name' => 'Lazada',
			'url'  => get_field( 'ane_product_lazada', $product_id ),
			'icon' => 'lazada',
		),
		'blibli'    => array(
			'name' => 'Blibli',
			'url'  => get_field( 'ane_product_blibli', $product_id ),
			'icon' => 'blibli',
		),
		'tiktok'    => array(
			'name' => 'TikTok Shop',
			'url'  => get_field( 'ane_product_tiktok', $product_id ),
			'icon' => 'tiktok',
		),
	);

	// Filter out empty URLs.
	return array_filter(
		$marketplaces,
		function ( $marketplace ) {
			return ! empty( $marketplace['url'] );
		}
	);
}

/**
 * Get city name from city ID.
 *
 * @param string $city_id     City ID from ACF field.
 * @param string $province_id Province ID (optional).
 * @return string City name or empty string.
 * @since 1.0.0
 */
function ane_get_city_name( $city_id, $province_id = '' ) {
	if ( empty( $city_id ) ) {
		return '';
	}

	// Load JSON file.
	$json_file = get_template_directory() . '/inc/indonesia-locations.json';

	if ( ! file_exists( $json_file ) ) {
		return $city_id; // Return ID if JSON not found.
	}

	$json_data = file_get_contents( $json_file );
	$data      = json_decode( $json_data, true );

	if ( ! $data || ! isset( $data['provinces'] ) ) {
		return $city_id;
	}

	// If province ID provided, search only in that province.
	if ( ! empty( $province_id ) ) {
		foreach ( $data['provinces'] as $province ) {
			if ( $province['id'] === $province_id && isset( $province['cities'] ) ) {
				foreach ( $province['cities'] as $city ) {
					if ( $city['id'] === $city_id ) {
						return $city['name'];
					}
				}
			}
		}
	} else {
		// Search all provinces.
		foreach ( $data['provinces'] as $province ) {
			if ( isset( $province['cities'] ) ) {
				foreach ( $province['cities'] as $city ) {
					if ( $city['id'] === $city_id ) {
						return $city['name'];
					}
				}
			}
		}
	}

	return $city_id; // Return ID if not found.
}

/**
 * Get all available branches for product purchase.
 *
 * @return array Array of branch post objects.
 * @since 1.0.0
 */
function ane_get_product_branches() {
	// Check if Branch CPT is enabled.
	if ( ! function_exists( 'ane_is_branch_cpt_enabled' ) || ! ane_is_branch_cpt_enabled() ) {
		return array();
	}

	$args = array(
		'post_type'      => 'branch',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$query = new WP_Query( $args );

	$branches = array();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$city_id     = get_field( 'ane_branch_city' );
			$province_id = get_field( 'ane_branch_province' );
			$city_name   = ane_get_city_name( $city_id, $province_id );

			// Get Google Maps data (ACF google_map field returns array with lat/lng).
			$map_data = get_field( 'ane_branch_map' );

			$branches[] = array(
				'id'       => get_the_ID(),
				'title'    => get_the_title(),
				'address'  => get_field( 'ane_branch_address' ),
				'city'     => $city_name, // Use city name instead of ID
				'city_id'  => $city_id,
				'phone'    => get_field( 'ane_branch_phone' ),
				'whatsapp' => get_field( 'ane_branch_whatsapp' ),
				'email'    => get_field( 'ane_branch_email' ),
				'lat'      => isset( $map_data['lat'] ) ? $map_data['lat'] : '',
				'lng'      => isset( $map_data['lng'] ) ? $map_data['lng'] : '',
			);
		}
		wp_reset_postdata();
	}

	return $branches;
}

/**
 * Get WhatsApp admin URL for product purchase.
 *
 * @param int    $product_id Product post ID.
 * @param string $source     Purchase source (admin/branch).
 * @return string WhatsApp chat URL.
 * @since 1.0.0
 */
function ane_get_product_whatsapp_url( $product_id, $source = 'admin' ) {
	$product_title = get_the_title( $product_id );
	$product_url   = get_permalink( $product_id );

	// Default message.
	$message = sprintf(
		/* translators: 1: product title, 2: product URL */
		__( 'Hi, I am interested in the product *%1$s*. Can you help me with more information? %2$s', 'elemenane' ),
		$product_title,
		$product_url
	);

	if ( $source === 'admin' ) {
		$whatsapp_no = get_field( 'ane_whatsapp_no', 'option' );
	} else {
		// Will be replaced with branch whatsapp in JS.
		$whatsapp_no = '';
	}

	if ( empty( $whatsapp_no ) ) {
		return '';
	}

	// Clean phone number (remove spaces, dashes, +).
	$clean_number = preg_replace( '/[^0-9]/', '', $whatsapp_no );

	// Add 62 if starts with 0.
	if ( substr( $clean_number, 0, 1 ) === '0' ) {
		$clean_number = '62' . substr( $clean_number, 1 );
	}

	return 'https://wa.me/' . $clean_number . '?text=' . rawurlencode( $message );
}

/**
 * Enqueue product archive scripts and styles.
 *
 * @since 1.0.0
 */
function ane_product_archive_enqueue_scripts() {
	if ( is_post_type_archive( 'product' ) || is_tax( array( 'product_cat', 'product_tag' ) ) ) {
		// Enqueue scripts for archive page when ready.
	}
}
add_action( 'wp_enqueue_scripts', 'ane_product_archive_enqueue_scripts' );

/**
 * Enqueue product single scripts and styles.
 *
 * @since 1.0.0
 */
function ane_product_single_enqueue_scripts() {
	if ( is_singular( 'product' ) ) {
		// Gallery lightbox (the script is pulled in as a dependency below).
		wp_enqueue_style( 'magnific-popup' );

		// Enqueue single product JavaScript.
		wp_enqueue_script(
			'ane-single-product',
			get_template_directory_uri() . '/js/single-product.js',
			array( 'jquery', 'magnific' ),
			'1.2.0',
			true
		);

		// Localize script with translated strings.
		wp_localize_script(
			'ane-single-product',
			'aneProductStrings',
			array(
				'noBranchFound'   => __( 'No branches found for search', 'elemenane' ),
				'whatsappMessage' => __( 'Hi, I am interested in the product *%1$s*. Can you help me with more information? %2$s', 'elemenane' ),
				'address'         => __( 'Address', 'elemenane' ),
				'phone'           => __( 'Phone', 'elemenane' ),
				'email'           => __( 'Email', 'elemenane' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ane_product_single_enqueue_scripts' );
