<?php
// non aktifkan seluruh css d depan
// disable acf css on front-end acf forms
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
 
function my_deregister_styles() {
  wp_deregister_style( 'acf' );
  wp_deregister_style( 'acf-field-group' );
  wp_deregister_style( 'acf-global' );
  wp_deregister_style( 'acf-input' );
  wp_deregister_style( 'acf-datepicker' );
}

/**
 * Google Maps API key.
 *
 * Order: per-site setting, legacy `google_api_key` option, then the shared
 * Webane key so existing sites keep working until they set their own.
 *
 * @return string API key.
 */
function ane_get_google_maps_api_key() {
	$key = get_field( 'ane_gmap_api_key', 'option' );

	if ( ! $key ) {
		$key = get_field( 'google_api_key', 'option' );
	}

	if ( ! $key ) {
		$key = 'AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM';
	}

	/**
	 * Filter the Google Maps API key.
	 *
	 * @param string $key API key.
	 */
	return (string) apply_filters( 'ane_google_maps_api_key', trim( (string) $key ) );
}

/**
 * Register the Google Maps API key setting.
 */
function ane_register_gmap_api_key_field() {
	acf_add_local_field_group(
		array(
			'key'        => 'group_ane_gmap_api_key',
			'title'      => __( 'Google Maps', 'elemenane' ),
			'fields'     => array(
				array(
					'key'          => 'field_ane_gmap_api_key',
					'label'        => __( 'Google Maps API Key', 'elemenane' ),
					'name'         => 'ane_gmap_api_key',
					'type'         => 'text',
					'instructions' => __( 'API key for this site, restricted to this domain in Google Cloud Console. Leave empty to use the default key.', 'elemenane' ),
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

// Google Map field in the admin.
add_filter( 'acf/fields/google_map/api', 'my_acf_google_map_api' );
function my_acf_google_map_api( $args ) {
	$args['key'] = ane_get_google_maps_api_key();
	return $args;
}

/**
 * Load Google Maps only on the contact page when it has a location.
 * Branch pages load their own map script (inc/branch-acf.php).
 */
function ane_load_gmap_script() {
	if ( ! is_page_template( 'page-kontak.php' ) || ! get_field( 'ane_gmap', 'option' ) ) {
		return;
	}

	wp_enqueue_script( 'googlemap', 'https://maps.googleapis.com/maps/api/js?key=' . rawurlencode( ane_get_google_maps_api_key() ) . '&callback=Function.prototype', array(), null, true );
	wp_enqueue_script( 'map', get_base_url_assets() . '/js/gmap.js', array( 'jquery', 'googlemap' ), '1.0.1', true );
}
add_action( 'wp_enqueue_scripts', 'ane_load_gmap_script' );

// ACF Options Pages registration moved to inc/admin.php
// See ane_register_admin_dashboard() function for all ACF sub-menu registration
//disable heartbeat

add_action( 'init', 'disable_heartbeat', 1 );

function disable_heartbeat() {

wp_deregister_script('heartbeat');

}

/**
 * Register ACF Field Group for Page Schema Type
 * Allows selecting schema.org type for each page
 */
add_action( 'acf/init', 'ane_register_page_schema_field' );

function ane_register_page_schema_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_page_schema',
			'title'    => 'SEO Schema Settings',
			'fields'   => array(
				array(
					'key'           => 'field_ane_page_schema_type',
					'label'         => 'Schema Type',
					'name'          => 'ane_page_schema_type',
					'type'          => 'select',
					'instructions'  => 'Pilih jenis schema yang sesuai dengan konten halaman. Schema ini akan muncul di hasil pencarian Google dan membantu SEO.',
					'required'      => 0,
					'default_value' => 'WebPage',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'ajax'          => 0,
					'return_format' => 'value',
					'placeholder'   => 'Pilih Schema Type',
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
			'location' => array(
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