<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register ACF Fields for Header Settings
 *
 * @package elemenane
 * @since 1.0.1
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_header_fields',
			'title'                 => __( 'Header Fields', 'elemenane' ),
			'fields'                => array(
				array(
					'key'               => 'field_header_cta_link',
					'label'             => __( 'Get In Touch Button Link', 'elemenane' ),
					'name'              => 'ane_header_cta_link',
					'type'              => 'link',
					'instructions'      => __( 'Select a page or enter a custom URL for the "Get In Touch" button', 'elemenane' ),
					'required'          => 0,
					'conditional_logic' => 0,
					'return_format'     => 'array',
				),
				array(
					'key'               => 'field_header_show_product_link',
					'label'             => __( 'Show Product/Cart Link', 'elemenane' ),
					'name'              => 'ane_header_show_product',
					'type'              => 'true_false',
					'instructions'      => __( 'Enable to show product/cart icon in header', 'elemenane' ),
					'required'          => 0,
					'conditional_logic' => 0,
					'message'           => __( 'Show product cart icon', 'elemenane' ),
					'default_value'     => 1,
					'ui'                => 1,
					'ui_on_text'        => __( 'Yes', 'elemenane' ),
					'ui_off_text'       => __( 'No', 'elemenane' ),
				),
				array(
					'key'               => 'field_header_phone_label',
					'label'             => __( 'Phone Label', 'elemenane' ),
					'name'              => 'ane_telepon_label',
					'type'              => 'text',
					'instructions'      => __( 'Label text for phone number (e.g., Sales Team, Customer Service)', 'elemenane' ),
					'required'          => 0,
					'conditional_logic' => 0,
					'placeholder'       => __( 'Sales Team', 'elemenane' ),
					'default_value'     => __( 'Sales Team', 'elemenane' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ane-general-setting',
					),
				),
			),
			'menu_order'            => 10,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

endif;
