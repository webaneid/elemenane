<?php
/**
 * Branch ACF Field Groups
 *
 * Registers ACF field groups for Branch custom post type.
 * Includes Google Maps integration and contact information fields.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register ACF Field Group for Branch Details.
 *
 * Creates custom fields for branch information:
 * - Alamat Lengkap (Full address)
 * - Google Maps (Map picker)
 * - Nomor Telepon (Phone number)
 * - Email
 * - WhatsApp
 * - Jam Operasional (Operating hours)
 * - Foto Cabang (Branch photos gallery)
 *
 * @since 1.0.0
 */
function ane_register_branch_fields() {
	// Check if ACF function exists.
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_ane_branch_details',
			'title'    => __( 'Branch Details', 'elemenane' ),
			'fields'   => array(
				// Provinsi.
				array(
					'key'           => 'field_ane_branch_province',
					'label'         => __( 'Provinsi', 'elemenane' ),
					'name'          => 'ane_branch_province',
					'type'          => 'select',
					'instructions'  => __( 'Pilih provinsi.', 'elemenane' ),
					'required'      => 1,
					'choices'       => array(),
					'allow_null'    => 1,
					'ui'            => 1,
					'ajax'          => 0,
					'placeholder'   => __( 'Pilih Provinsi', 'elemenane' ),
				),
				// Kabupaten/Kota.
				array(
					'key'               => 'field_ane_branch_city',
					'label'             => __( 'Kabupaten/Kota', 'elemenane' ),
					'name'              => 'ane_branch_city',
					'type'              => 'select',
					'instructions'      => __( 'Pilih kabupaten/kota.', 'elemenane' ),
					'required'          => 1,
					'choices'           => array(),
					'allow_null'        => 1,
					'ui'                => 1,
					'ajax'              => 0,
					'placeholder'       => __( 'Pilih Kabupaten/Kota', 'elemenane' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_branch_province',
								'operator' => '!=empty',
							),
						),
					),
				),
				// Kecamatan.
				array(
					'key'               => 'field_ane_branch_district',
					'label'             => __( 'Kecamatan', 'elemenane' ),
					'name'              => 'ane_branch_district',
					'type'              => 'text',
					'instructions'      => __( 'Masukkan nama kecamatan.', 'elemenane' ),
					'required'          => 0,
					'placeholder'       => __( 'Contoh: Kebayoran Baru', 'elemenane' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_ane_branch_city',
								'operator' => '!=empty',
							),
						),
					),
				),
				// Alamat Lengkap.
				array(
					'key'           => 'field_ane_branch_address',
					'label'         => __( 'Alamat Lengkap', 'elemenane' ),
					'name'          => 'ane_branch_address',
					'type'          => 'textarea',
					'instructions'  => __( 'Masukkan alamat lengkap cabang.', 'elemenane' ),
					'required'      => 1,
					'rows'          => 3,
					'new_lines'     => 'br',
				),
				// Google Maps.
				array(
					'key'           => 'field_ane_branch_map',
					'label'         => __( 'Google Maps', 'elemenane' ),
					'name'          => 'ane_branch_map',
					'type'          => 'google_map',
					'instructions'  => __( 'Pilih lokasi cabang pada peta.', 'elemenane' ),
					'required'      => 1,
					'center_lat'    => '-6.200000',
					'center_lng'    => '106.816666',
					'zoom'          => 12,
					'height'        => 400,
				),
				// Nomor Telepon.
				array(
					'key'           => 'field_ane_branch_phone',
					'label'         => __( 'Nomor Telepon', 'elemenane' ),
					'name'          => 'ane_branch_phone',
					'type'          => 'text',
					'instructions'  => __( 'Contoh: (021) 12345678', 'elemenane' ),
					'required'      => 0,
					'placeholder'   => '(021) 12345678',
				),
				// Email.
				array(
					'key'           => 'field_ane_branch_email',
					'label'         => __( 'Email', 'elemenane' ),
					'name'          => 'ane_branch_email',
					'type'          => 'email',
					'instructions'  => __( 'Email cabang.', 'elemenane' ),
					'required'      => 0,
					'placeholder'   => 'cabang@example.com',
				),
				// WhatsApp.
				array(
					'key'           => 'field_ane_branch_whatsapp',
					'label'         => __( 'WhatsApp', 'elemenane' ),
					'name'          => 'ane_branch_whatsapp',
					'type'          => 'text',
					'instructions'  => __( 'Nomor WhatsApp dengan kode negara. Contoh: 628123456789', 'elemenane' ),
					'required'      => 0,
					'placeholder'   => '628123456789',
				),
				// Jam Operasional.
				array(
					'key'           => 'field_ane_branch_hours',
					'label'         => __( 'Jam Operasional', 'elemenane' ),
					'name'          => 'ane_branch_hours',
					'type'          => 'textarea',
					'instructions'  => __( 'Contoh: Senin - Jumat: 08:00 - 17:00, Sabtu: 08:00 - 12:00', 'elemenane' ),
					'required'      => 0,
					'rows'          => 3,
					'placeholder'   => 'Senin - Jumat: 08:00 - 17:00',
				),
				// Foto Cabang (Gallery).
				array(
					'key'           => 'field_ane_branch_photos',
					'label'         => __( 'Foto Cabang', 'elemenane' ),
					'name'          => 'ane_branch_photos',
					'type'          => 'gallery',
					'instructions'  => __( 'Upload foto-foto cabang.', 'elemenane' ),
					'required'      => 0,
					'min'           => 0,
					'max'           => 10,
					'insert'        => 'append',
					'library'       => 'all',
					'min_width'     => 800,
					'min_height'    => 600,
					'preview_size'  => 'medium',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'branch',
					),
				),
			),
			'position' => 'normal',
		)
	);
}
add_action( 'acf/init', 'ane_register_branch_fields' );

/**
 * Load province choices for ACF select field.
 *
 * @param array $field ACF field array.
 * @return array Modified field array with choices.
 * @since 1.0.0
 */
function ane_load_province_choices( $field ) {
	// Reset choices.
	$field['choices'] = array();

	// Load JSON file.
	$json_file = get_template_directory() . '/inc/indonesia-locations.json';

	if ( ! file_exists( $json_file ) ) {
		return $field;
	}

	$json_data = file_get_contents( $json_file );
	$data      = json_decode( $json_data, true );

	if ( ! $data || ! isset( $data['provinces'] ) ) {
		return $field;
	}

	// Build choices array.
	foreach ( $data['provinces'] as $province ) {
		$field['choices'][ $province['id'] ] = $province['name'];
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_ane_branch_province', 'ane_load_province_choices' );

/**
 * Load city choices based on selected province.
 *
 * @param array $field ACF field array.
 * @return array Modified field array with choices.
 * @since 1.0.0
 */
function ane_load_city_choices( $field ) {
	// Reset choices.
	$field['choices'] = array();

	// Get selected province from POST or from saved value.
	$province_id = '';

	// Check if we're editing existing post.
	if ( isset( $_GET['post'] ) ) {
		$post_id     = absint( $_GET['post'] );
		$province_id = get_field( 'ane_branch_province', $post_id );
	}

	// Check AJAX request.
	if ( isset( $_POST['province_id'] ) ) {
		$province_id = sanitize_text_field( $_POST['province_id'] );
	}

	if ( empty( $province_id ) ) {
		return $field;
	}

	// Load JSON file.
	$json_file = get_template_directory() . '/inc/indonesia-locations.json';

	if ( ! file_exists( $json_file ) ) {
		return $field;
	}

	$json_data = file_get_contents( $json_file );
	$data      = json_decode( $json_data, true );

	if ( ! $data || ! isset( $data['provinces'] ) ) {
		return $field;
	}

	// Find the selected province and load its cities.
	foreach ( $data['provinces'] as $province ) {
		if ( $province['id'] === $province_id ) {
			if ( isset( $province['cities'] ) && is_array( $province['cities'] ) ) {
				foreach ( $province['cities'] as $city ) {
					$field['choices'][ $city['id'] ] = $city['name'];
				}
			}
			break;
		}
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_ane_branch_city', 'ane_load_city_choices' );

/**
 * AJAX handler to load cities based on province selection.
 *
 * @since 1.0.0
 */
function ane_ajax_load_cities() {
	// Check nonce.
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ane_branch_locations' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
	}

	// Get province ID.
	$province_id = isset( $_POST['province_id'] ) ? sanitize_text_field( $_POST['province_id'] ) : '';

	if ( empty( $province_id ) ) {
		wp_send_json_error( array( 'message' => 'No province selected' ) );
	}

	// Load JSON file.
	$json_file = get_template_directory() . '/inc/indonesia-locations.json';

	if ( ! file_exists( $json_file ) ) {
		wp_send_json_error( array( 'message' => 'Location data not found' ) );
	}

	$json_data = file_get_contents( $json_file );
	$data      = json_decode( $json_data, true );

	if ( ! $data || ! isset( $data['provinces'] ) ) {
		wp_send_json_error( array( 'message' => 'Invalid location data' ) );
	}

	// Find cities for selected province.
	$cities = array();
	foreach ( $data['provinces'] as $province ) {
		if ( $province['id'] === $province_id ) {
			if ( isset( $province['cities'] ) && is_array( $province['cities'] ) ) {
				foreach ( $province['cities'] as $city ) {
					$cities[] = array(
						'id'   => $city['id'],
						'name' => $city['name'],
					);
				}
			}
			break;
		}
	}

	wp_send_json_success( array( 'cities' => $cities ) );
}
add_action( 'wp_ajax_ane_load_cities', 'ane_ajax_load_cities' );

/**
 * Enqueue admin scripts for branch location dropdown.
 *
 * @since 1.0.0
 */
function ane_enqueue_branch_admin_scripts( $hook ) {
	global $post_type;

	// Only load on branch edit pages.
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'branch' === $post_type ) {
		wp_enqueue_script(
			'ane-branch-locations',
			get_template_directory_uri() . '/js/branch-locations.js',
			array( 'jquery', 'acf-input' ),
			'1.0.5',
			true
		);

		wp_localize_script(
			'ane-branch-locations',
			'aneBranchLocations',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ane_branch_locations' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'ane_enqueue_branch_admin_scripts' );

/**
 * Enqueue frontend scripts for branch archive.
 *
 * @since 1.0.0
 */
function ane_enqueue_branch_frontend_scripts() {
	// Get Google Maps API key from ACF settings.
	$google_api_key = get_field( 'google_api_key', 'option' );

	// Archive page.
	if ( is_post_type_archive( 'branch' ) ) {
		if ( $google_api_key ) {
			// Enqueue Google Maps API.
			wp_enqueue_script(
				'google-maps',
				'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $google_api_key ) . '&callback=initAneBranchMap',
				array(),
				null,
				true
			);
		}

		// Enqueue branch archive script.
		wp_enqueue_script(
			'ane-branch-archive',
			get_template_directory_uri() . '/js/branch-archive.js',
			array( 'jquery' ),
			'1.0.1',
			true
		);

		// Pass data to JavaScript.
		wp_localize_script(
			'ane-branch-archive',
			'aneMapConfig',
			array(
				'location_json_url' => get_template_directory_uri() . '/inc/indonesia-locations.json',
			)
		);
	}

	// Single branch page.
	if ( is_singular( 'branch' ) ) {
		if ( $google_api_key ) {
			// Enqueue Google Maps API.
			wp_enqueue_script(
				'google-maps',
				'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $google_api_key ) . '&callback=initAneSingleBranchMap',
				array(),
				null,
				true
			);
		}

		// Enqueue single branch script.
		wp_enqueue_script(
			'ane-single-branch',
			get_template_directory_uri() . '/js/single-branch.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_branch_frontend_scripts' );
