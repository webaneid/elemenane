<?php
/**
 * Custom Post Types
 *
 * Registers Product and Service custom post types.
 * Can be enabled/disabled via ACF Options in General Settings.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register Product Custom Post Type.
 *
 * @since 1.0.0
 */
function ane_register_product_cpt() {
	// Check if Product CPT is enabled.
	if ( function_exists( 'ane_is_product_cpt_enabled' ) && ! ane_is_product_cpt_enabled() ) {
		return;
	}

	register_post_type(
		'product',
		array(
			'labels'        => array(
				'name'          => __( 'Products', 'elemenane' ),
				'singular_name' => __( 'Product', 'elemenane' ),
				'add_new'       => __( 'Add New', 'elemenane' ),
				'add_new_item'  => __( 'Add New Product', 'elemenane' ),
				'edit_item'     => __( 'Edit Product', 'elemenane' ),
				'view_item'     => __( 'View Product', 'elemenane' ),
				'all_items'     => __( 'All Products', 'elemenane' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'menu_position' => 30,
			'supports'      => array( 'title', 'thumbnail', 'editor', 'excerpt' ),
			'menu_icon'     => 'dashicons-cart',
			'rewrite'       => array( 'slug' => 'product' ),
		)
	);

	// Register Product Category taxonomy (WooCommerce compatible).
	register_taxonomy(
		'product_cat',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Product Categories', 'elemenane' ),
				'singular_name' => __( 'Product Category', 'elemenane' ),
				'menu_name'     => __( 'Categories', 'elemenane' ),
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'product-category' ),
		)
	);

	// Register Product Tag taxonomy (WooCommerce compatible).
	register_taxonomy(
		'product_tag',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Product Tags', 'elemenane' ),
				'singular_name' => __( 'Product Tag', 'elemenane' ),
				'menu_name'     => __( 'Tags', 'elemenane' ),
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'product-tag' ),
		)
	);
}
add_action( 'init', 'ane_register_product_cpt' );

/**
 * Register Service Custom Post Type.
 *
 * @since 1.0.0
 */
function ane_register_service_cpt() {
	// Check if Service CPT is enabled.
	if ( function_exists( 'ane_is_service_cpt_enabled' ) && ! ane_is_service_cpt_enabled() ) {
		return;
	}

	register_post_type(
		'service',
		array(
			'labels'        => array(
				'name'          => __( 'Services', 'elemenane' ),
				'singular_name' => __( 'Service', 'elemenane' ),
				'add_new'       => __( 'Add New', 'elemenane' ),
				'add_new_item'  => __( 'Add New Service', 'elemenane' ),
				'edit_item'     => __( 'Edit Service', 'elemenane' ),
				'view_item'     => __( 'View Service', 'elemenane' ),
				'all_items'     => __( 'All Services', 'elemenane' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'menu_position' => 31,
			'supports'      => array( 'title' ),
			'menu_icon'     => 'dashicons-admin-tools',
			'rewrite'       => array( 'slug' => 'service' ),
		)
	);
}
add_action( 'init', 'ane_register_service_cpt' );

/**
 * Register Branch Custom Post Type.
 *
 * @since 1.0.0
 */
function ane_register_branch_cpt() {
	// Check if Branch CPT is enabled.
	if ( function_exists( 'ane_is_branch_cpt_enabled' ) && ! ane_is_branch_cpt_enabled() ) {
		return;
	}

	register_post_type(
		'branch',
		array(
			'labels'        => array(
				'name'          => __( 'Branches', 'elemenane' ),
				'singular_name' => __( 'Branch', 'elemenane' ),
				'add_new'       => __( 'Add New', 'elemenane' ),
				'add_new_item'  => __( 'Add New Branch', 'elemenane' ),
				'edit_item'     => __( 'Edit Branch', 'elemenane' ),
				'view_item'     => __( 'View Branch', 'elemenane' ),
				'all_items'     => __( 'All Branches', 'elemenane' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'show_in_rest'  => true,
			'menu_position' => 32,
			'supports'      => array( 'title', 'thumbnail' ),
			'menu_icon'     => 'dashicons-location',
			'rewrite'       => array( 'slug' => 'branch' ),
		)
	);

}
add_action( 'init', 'ane_register_branch_cpt' );

/**
 * Check if Branch CPT is enabled.
 *
 * @return bool True if enabled, false otherwise.
 * @since 1.0.0
 */
function ane_is_branch_cpt_enabled() {
	$enabled = get_field( 'ane_enable_branch_cpt', 'option' );
	return (bool) $enabled; // Default: disabled
}
