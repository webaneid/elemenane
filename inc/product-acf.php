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
 * - _manage_stock: Whether to manage stock
 * - _stock: Stock quantity
 *
 * @since 1.0.0
 */
function ane_register_product_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Product Pricing & Basic Info.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_product_pricing',
			'title'                 => __( 'Product Information', 'elemenane' ),
			'fields'                => array(
				// SKU.
				array(
					'key'               => 'field_product_sku',
					'label'             => __( 'SKU', 'elemenane' ),
					'name'              => '_sku',
					'type'              => 'text',
					'instructions'      => __( 'Product SKU (Stock Keeping Unit)', 'elemenane' ),
					'required'          => 0,
					'wrapper'           => array(
						'width' => '50',
					),
				),
				// Regular Price.
				array(
					'key'               => 'field_product_regular_price',
					'label'             => __( 'Regular Price', 'elemenane' ),
					'name'              => '_regular_price',
					'type'              => 'number',
					'instructions'      => __( 'Regular price in rupiah (without Rp and thousand separator)', 'elemenane' ),
					'required'          => 1,
					'min'               => 0,
					'step'              => 1,
					'wrapper'           => array(
						'width' => '50',
					),
				),
				// Sale Price.
				array(
					'key'               => 'field_product_sale_price',
					'label'             => __( 'Sale Price', 'elemenane' ),
					'name'              => '_sale_price',
					'type'              => 'number',
					'instructions'      => __( 'Sale/discount price in rupiah (leave empty if no discount)', 'elemenane' ),
					'required'          => 0,
					'min'               => 0,
					'step'              => 1,
					'wrapper'           => array(
						'width' => '50',
					),
				),
				// Stock Status.
				array(
					'key'               => 'field_product_stock_status',
					'label'             => __( 'Stock Status', 'elemenane' ),
					'name'              => '_stock_status',
					'type'              => 'select',
					'instructions'      => __( 'Product stock availability', 'elemenane' ),
					'required'          => 1,
					'choices'           => array(
						'instock'    => __( 'In Stock', 'elemenane' ),
						'outofstock' => __( 'Out of Stock', 'elemenane' ),
					),
					'default_value'     => 'instock',
					'wrapper'           => array(
						'width' => '50',
					),
				),
				// Manage Stock.
				array(
					'key'               => 'field_product_manage_stock',
					'label'             => __( 'Manage Stock', 'elemenane' ),
					'name'              => '_manage_stock',
					'type'              => 'true_false',
					'instructions'      => __( 'Enable stock quantity management', 'elemenane' ),
					'required'          => 0,
					'default_value'     => 0,
					'ui'                => 1,
					'wrapper'           => array(
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

	// Product Gallery.
	acf_add_local_field_group(
		array(
			'key'                   => 'group_product_gallery',
			'title'                 => __( 'Product Gallery', 'elemenane' ),
			'fields'                => array(
				array(
					'key'           => 'field_product_gallery',
					'label'         => __( 'Product Images', 'elemenane' ),
					'name'          => 'ane_product_gallery',
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
 * Sets _price to the lower value between _regular_price and _sale_price.
 * This matches WooCommerce behavior.
 *
 * @param int $post_id Post ID being saved.
 * @since 1.0.0
 */
function ane_product_save_active_price( $post_id ) {
	// Check if this is a product post type.
	if ( get_post_type( $post_id ) !== 'product' ) {
		return;
	}

	// Get prices.
	$regular_price = get_field( '_regular_price', $post_id );
	$sale_price    = get_field( '_sale_price', $post_id );

	// Calculate active price.
	if ( ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) {
		$active_price = $sale_price;
	} else {
		$active_price = $regular_price;
	}

	// Save active price.
	update_post_meta( $post_id, '_price', $active_price );
}
add_action( 'acf/save_post', 'ane_product_save_active_price', 20 );

/**
 * Get formatted product price with currency.
 *
 * @param int  $product_id Product post ID.
 * @param bool $show_sale  Whether to show sale price separately.
 * @return string Formatted price HTML.
 * @since 1.0.0
 */
function ane_get_product_price( $product_id, $show_sale = true ) {
	$regular_price = get_field( '_regular_price', $product_id );
	$sale_price    = get_field( '_sale_price', $product_id );

	if ( empty( $regular_price ) ) {
		return '';
	}

	$html = '<div class="ane-product-price">';

	if ( $show_sale && ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) {
		$html .= '<span class="ane-price-sale">Rp ' . number_format( $sale_price, 0, ',', '.' ) . '</span>';
		$html .= '<span class="ane-price-regular ane-price-strikethrough">Rp ' . number_format( $regular_price, 0, ',', '.' ) . '</span>';

		// Calculate discount percentage.
		$discount = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
		$html    .= '<span class="ane-price-discount">-' . $discount . '%</span>';
	} else {
		$html .= '<span class="ane-price-regular">Rp ' . number_format( $regular_price, 0, ',', '.' ) . '</span>';
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
	$stock_status = get_field( '_stock_status', $product_id );
	$manage_stock = get_field( '_manage_stock', $product_id );

	// If managing stock, check quantity.
	if ( $manage_stock ) {
		$stock_qty = get_field( '_stock', $product_id );
		if ( $stock_qty <= 0 ) {
			return 'outofstock';
		}
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
		function( $marketplace ) {
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
		// Enqueue single product JavaScript.
		wp_enqueue_script(
			'ane-single-product',
			get_template_directory_uri() . '/js/single-product.js',
			array( 'jquery', 'magnific' ),
			'1.0.0',
			true
		);

		// Localize script with translated strings.
		wp_localize_script(
			'ane-single-product',
			'aneProductStrings',
			array(
				'noBranchFound'   => __( 'No branches found for search', 'elemenane' ),
				'whatsappMessage' => __( 'Hi, I am interested in the product *%s*. Can you help me with more information? %s', 'elemenane' ),
				'address'         => __( 'Address', 'elemenane' ),
				'phone'           => __( 'Phone', 'elemenane' ),
				'email'           => __( 'Email', 'elemenane' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ane_product_single_enqueue_scripts' );
