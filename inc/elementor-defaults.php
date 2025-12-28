<?php
/**
 * Elementor Default Configuration
 *
 * Auto-configures Elementor settings when theme is activated.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Setup Elementor defaults on theme activation.
 */
function ane_setup_elementor_defaults() {
	// Only run if Elementor is active
	if ( ! did_action( 'elementor/loaded' ) ) {
		return;
	}

	// Check if already configured
	if ( get_option( 'ane_elementor_configured' ) ) {
		return;
	}

	// Import Elementor Kit settings from JSON
	ane_import_elementor_kit();

	// Elementor Settings
	$elementor_settings = array(
		// Page Settings
		'page_title_selector'              => 'h1.post-title',
		'default_generic_fonts'            => 'Sans-serif',

		// Style Settings
		'container_width'                  => '1200',
		'space_between_widgets'            => '20',
		'stretched_section_container'      => '',
		'viewport_lg'                      => '1025',
		'viewport_md'                      => '768',

		// Lightbox
		'global_image_lightbox'            => 'yes',

		// Advanced
		'css_print_method'                 => 'internal',
		'exclude_user_roles'               => array(),
		'enable_unfiltered_files_upload'   => '',

		// Integrations
		'google_maps_api_key'              => get_field( 'ane_gmap_api_key', 'option' ) ?: '',

		// Performance
		'optimized_dom_output'             => 'enabled',
		'lazyload'                         => 'yes',
		'font_display'                     => 'swap',
	);

	foreach ( $elementor_settings as $key => $value ) {
		update_option( 'elementor_' . $key, $value );
	}

	// Enable Elementor experiments
	$experiments = array(
		'container'                         => 'active',
		'e_optimized_assets_loading'        => 'active',
		'e_optimized_css_loading'           => 'active',
		'additional_custom_breakpoints'     => 'active',
		'theme_builder_v2'                  => 'active',
		'landing-pages'                     => 'active',
		'nested-elements'                   => 'active',
		'e_font_icon_svg'                   => 'active',
		'e_optimized_markup'                => 'active',
		'import-export-customization'       => 'active',
		'mega-menu'                         => 'active',
	);

	update_option( 'elementor_experiment', $experiments );

	// Disable Elementor default colors and fonts
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );

	// Set CPT support
	$cpt_support = array( 'page', 'post' );
	update_option( 'elementor_cpt_support', $cpt_support );

	// Mark as configured
	update_option( 'ane_elementor_configured', true );
}
add_action( 'after_switch_theme', 'ane_setup_elementor_defaults' );

/**
 * Import Elementor Kit settings from JSON file.
 */
function ane_import_elementor_kit() {
	$json_file = get_template_directory() . '/inc/site-settings.json';

	if ( ! file_exists( $json_file ) ) {
		return;
	}

	// Get JSON content
	$json_content = file_get_contents( $json_file );
	$kit_data     = json_decode( $json_content, true );

	if ( ! $kit_data || ! isset( $kit_data['settings'] ) ) {
		return;
	}

	// Get or create active kit
	$active_kit_id = get_option( 'elementor_active_kit' );

	if ( ! $active_kit_id ) {
		// Create new kit post
		$kit_id = wp_insert_post(
			array(
				'post_title'  => __( 'Elemen Ane Kit', 'elemenane' ),
				'post_status' => 'publish',
				'post_type'   => 'elementor_library',
				'meta_input'  => array(
					'_elementor_template_type' => 'kit',
				),
			)
		);

		if ( ! is_wp_error( $kit_id ) ) {
			update_option( 'elementor_active_kit', $kit_id );
			$active_kit_id = $kit_id;
		}
	}

	if ( $active_kit_id ) {
		// Update kit settings
		update_post_meta( $active_kit_id, '_elementor_page_settings', $kit_data['settings'] );

		// Regenerate CSS
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}
}

/**
 * Reset configuration flag on theme deactivation.
 */
function ane_reset_elementor_config_flag() {
	delete_option( 'ane_elementor_configured' );
}
add_action( 'switch_theme', 'ane_reset_elementor_config_flag' );
