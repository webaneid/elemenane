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

//setup google map API
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
function my_acf_google_map_api( $args ) {
    $args['key'] = 'AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM';
    return $args;
}

function ane_load_gmap_script (){
    wp_enqueue_script( 'googlemap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM&callback=Function.prototype', array('jquery'), true );
    wp_enqueue_script( 'map', get_base_url_assets() . '/js/gmap.js', array('googlemap'), true );
}
add_action( 'wp_enqueue_scripts', 'ane_load_gmap_script' );

// ACF Options Pages registration moved to inc/admin.php
// See ane_register_admin_dashboard() function for all ACF sub-menu registration
//disable heartbeat

add_action( 'init', 'disable_heartbeat', 1 );

function disable_heartbeat() {

wp_deregister_script('heartbeat');

}

// SCRIPTs
// Disabled: Tracking scripts managed in inc/acf-layouts.php
/*
function ane_gtm_header_content($isi) {
    $isi = get_field('ane_ga_header','option');
    if(!empty($isi)) {
        echo $isi;
    }
}
add_action('wp_head','ane_gtm_header_content');

function ane_sc_header_content($isi) {
    $isi = get_field('ane_sc_header','option');
    if(!empty($isi)) {
        echo $isi;
    }
}
add_action('wp_head','ane_sc_header_content');

function ane_metapixel_header_content($isi) {
    $isi = get_field('ane_metapixel_header','option');
    if(!empty($isi)) {
        echo $isi;
    }
}
add_action('wp_head','ane_metapixel_header_content');

function ane_meta_sdk_script($isi) {
    $isi = get_field('ane_metasdk_body','option');
    if(!empty($isi)) {
        echo $isi;
    }
}
add_action( 'wp_body_open', 'ane_meta_sdk_script' );

function ane_gtm_footer_content($isi) {
    $isi = get_field('ane_ga_footer','option');
    if(!empty($isi)) {
        echo $isi;
    }
}
add_action('wp_footer','ane_gtm_footer_content');
*/

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