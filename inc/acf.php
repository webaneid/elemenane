<?php
/**
 * ACF Configuration, Google Maps API Key, Admin-Only Field Guards, and Page Schema.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

/**
 * Deregister ACF front-end styles.
 *
 * @return void
 */
function my_deregister_styles() {
	wp_deregister_style( 'acf' );
	wp_deregister_style( 'acf-field-group' );
	wp_deregister_style( 'acf-global' );
	wp_deregister_style( 'acf-input' );
	wp_deregister_style( 'acf-datepicker' );
}
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );

/**
 * Decode default Webane Google Maps API key stored in XOR-Hex format.
 *
 * @return string
 */
function elemenane_get_default_gmaps_key() {
	$cipher_hex = '2f2c0d123217264c420c03365b135b2a35531447781a0518264f4b385827452e2332543726592a';
	$mask       = 'newsane-webane-gmaps-v1';
	$mask_len   = strlen( $mask );
	$hex_len    = strlen( $cipher_hex );
	$decoded    = '';

	for ( $i = 0; $i < $hex_len; $i += 2 ) {
		$byte     = (int) hexdec( substr( $cipher_hex, $i, 2 ) );
		$mask_chr = ord( $mask[ (int) ( $i / 2 ) % $mask_len ] );
		$decoded .= chr( $byte ^ $mask_chr );
	}

	return $decoded;
}

/**
 * Retrieve Google Maps API key.
 *
 * Priority:
 * 1. ELEMENANE_GMAPS_API_KEY constant in wp-config.php
 * 2. ACF `ane_gmap_api_key` option
 * 3. Legacy ACF `google_api_key` option
 * 4. XOR-Hex default key
 *
 * @return string API key.
 */
function elemenane_get_google_maps_api_key() {
	if ( defined( 'ELEMENANE_GMAPS_API_KEY' ) && is_string( ELEMENANE_GMAPS_API_KEY ) && '' !== trim( ELEMENANE_GMAPS_API_KEY ) ) {
		return (string) apply_filters( 'ane_google_maps_api_key', trim( ELEMENANE_GMAPS_API_KEY ) );
	}

	$key = get_field( 'ane_gmap_api_key', 'option' );
	if ( ! $key ) {
		$key = get_field( 'google_api_key', 'option' );
	}
	if ( ! $key ) {
		$key = elemenane_get_default_gmaps_key();
	}

	return (string) apply_filters( 'ane_google_maps_api_key', trim( (string) $key ) );
}

/**
 * Backward-compatible alias for Google Maps API key.
 *
 * @return string
 */
function ane_get_google_maps_api_key() {
	return elemenane_get_google_maps_api_key();
}

/**
 * Register the Google Maps API key setting.
 *
 * @return void
 */
function ane_register_gmap_api_key_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'        => 'group_ane_gmap_api_key',
			'title'      => __( 'Google Maps', 'elemenane' ),
			'fields'     => array(
				array(
					'key'          => 'field_ane_gmap_api_key',
					'label'        => __( 'Google Maps API Key', 'elemenane' ),
					'name'         => 'ane_gmap_api_key',
					'type'         => 'password',
					'instructions' => __( 'API key for this site, restricted to this domain in Google Cloud Console. Leave empty to use the default key or define ELEMENANE_GMAPS_API_KEY in wp-config.php.', 'elemenane' ),
				),
			),
			'location'   => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'ane-general-setting',
					),
				),
			),
			'menu_order' => 90,
		)
	);
}
add_action( 'acf/init', 'ane_register_gmap_api_key_field' );

/**
 * Filter Google Map field API key in wp-admin.
 *
 * @param array<string, mixed> $args Google Map field arguments.
 * @return array<string, mixed>
 */
function my_acf_google_map_api( $args ) {
	$args['key'] = elemenane_get_google_maps_api_key();
	return $args;
}
add_filter( 'acf/fields/google_map/api', 'my_acf_google_map_api' );

/**
 * Load Google Maps only on the contact page when it has a location.
 *
 * @return void
 */
function ane_load_gmap_script() {
	if ( ! is_page_template( 'page-kontak.php' ) || ! get_field( 'ane_gmap', 'option' ) ) {
		return;
	}

	wp_enqueue_script(
		'googlemap',
		'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( elemenane_get_google_maps_api_key() ) . '&callback=Function.prototype',
		array(),
		null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		true
	);
	wp_enqueue_script( 'map', get_base_url_assets() . '/js/gmap.js', array( 'jquery', 'googlemap' ), '1.0.1', true );
}
add_action( 'wp_enqueue_scripts', 'ane_load_gmap_script' );

/**
 * Check if an ACF field is restricted to Administrators (`manage_options`).
 *
 * Protects raw HTML/script tracking fields and API keys from non-admin users.
 *
 * @param mixed $field ACF field array.
 * @return bool
 */
function elemenane_is_admin_only_field( $field ) {
	if ( ! is_array( $field ) ) {
		return false;
	}

	$restricted_names = array(
		'ane_ga_header',
		'ane_sc_header',
		'ane_metapixel_header',
		'ane_metasdk_body',
		'ane_ga_footer',
		'ane_gmap_api_key',
		'google_api_key',
		'ane_updater_token',
	);

	$name = isset( $field['name'] ) ? (string) $field['name'] : '';
	return '' !== $name && in_array( $name, $restricted_names, true );
}

/**
 * Hide administrator-only ACF fields from non-admin users in wp-admin.
 *
 * @param mixed $field ACF field array.
 * @return mixed
 */
function elemenane_acf_hide_admin_only_field( $field ) {
	if ( is_admin() && elemenane_is_admin_only_field( $field ) && ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	return $field;
}
add_filter( 'acf/prepare_field', 'elemenane_acf_hide_admin_only_field' );

/**
 * Prevent non-admin users from modifying administrator-only ACF fields.
 *
 * @param mixed      $value   Submitted value.
 * @param int|string $post_id Target post/option ID.
 * @param mixed      $field   ACF field definition.
 * @return mixed
 */
function elemenane_acf_guard_admin_only_value( $value, $post_id, $field ) {
	if ( is_user_logged_in() && elemenane_is_admin_only_field( $field ) && ! current_user_can( 'manage_options' ) ) {
		return get_field( (string) $field['name'], $post_id, false );
	}
	return $value;
}
add_filter( 'acf/update_value', 'elemenane_acf_guard_admin_only_value', 10, 3 );

/**
 * Deregister WordPress Heartbeat script.
 *
 * @return void
 */
function disable_heartbeat() {
	wp_deregister_script( 'heartbeat' );
}
add_action( 'init', 'disable_heartbeat', 1 );

/**
 * Register ACF Field Group for Page Schema Type.
 *
 * @return void
 */
function ane_register_page_schema_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_page_schema',
			'title'                 => __( 'SEO Schema Settings', 'elemenane' ),
			'fields'                => array(
				array(
					'key'           => 'field_ane_page_schema_type',
					'label'         => __( 'Schema Type', 'elemenane' ),
					'name'          => 'ane_page_schema_type',
					'type'          => 'select',
					'instructions'  => __( 'Select the Schema.org type matching this page content.', 'elemenane' ),
					'required'      => 0,
					'default_value' => 'WebPage',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'ajax'          => 0,
					'return_format' => 'value',
					'placeholder'   => __( 'Select Schema Type', 'elemenane' ),
					'choices'       => array(
						'WebPage'           => 'WebPage (Default) - Halaman standar',
						'AboutPage'         => 'AboutPage - Halaman tentang kami / profil',
						'ContactPage'       => 'ContactPage - Halaman kontak',
						'FAQPage'           => 'FAQPage - Halaman FAQ / tanya jawab',
						'ProfilePage'       => 'ProfilePage - Halaman profil organisasi/perusahaan',
						'CollectionPage'    => 'CollectionPage - Halaman koleksi/kumpulan konten',
						'ItemPage'          => 'ItemPage - Halaman item individual',
						'SearchResultsPage' => 'SearchResultsPage - Halaman hasil pencarian',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
			'menu_order'            => 20,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => 'Schema.org structured data untuk halaman',
		)
	);
}
add_action( 'acf/init', 'ane_register_page_schema_field' );
