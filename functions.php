<?php
/**
 * Elemen Ane Theme Functions
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check WordPress version compatibility
if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

/**
 * Theme setup
 *
 * @since 1.0.0
 */
function elemenane_theme_setup() {
	/**
	 * Load theme translations.
	 * Using load_textdomain() instead of load_theme_textdomain() to bypass WordPress
	 * translation caching mechanism that can prevent new translations from loading.
	 * determine_locale() gets fresh locale without cache interference.
	 */
	$locale = determine_locale();
	$mofile = get_template_directory() . "/languages/elemenane-{$locale}.mo";

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'elemenane', $mofile );
	}

	// Core theme supports
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	// Custom image sizes
	add_image_size( 'kotak', 394, 394, true );

	// Set default image sizes (runs once on theme activation)
	if ( get_option( 'elemenane_image_sizes_set' ) !== 'yes' ) {
		update_option( 'medium_size_w', 700 );
		update_option( 'medium_size_h', 394 );
		update_option( 'medium_crop', 1 );
		update_option( 'large_size_w', 1000 );
		update_option( 'large_size_h', 563 );
		update_option( 'large_crop', 1 );
		update_option( 'thumbnail_size_w', 400 );
		update_option( 'thumbnail_size_h', 225 );
		update_option( 'thumbnail_crop', 1 );
		update_option( 'elemenane_image_sizes_set', 'yes' );
	}

	// Navigation menus
	register_nav_menus(
		array(
			'menuutama'  => __( 'Menu Utama', 'elemenane' ),
			'mobilemenu' => __( 'Mobile Menu', 'elemenane' ),
			'menufooter' => __( 'Menu Footer', 'elemenane' ),
		)
	);

	// Post formats
	add_theme_support( 'post-formats', array( 'video', 'gallery' ) );

	// HTML5 support
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Custom logo
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 98,
			'width'       => 255,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Yoast SEO breadcrumbs support
	add_theme_support( 'yoast-seo-breadcrumbs' );
}
add_action( 'after_setup_theme', 'elemenane_theme_setup' );

/**
 * Register widget areas
 *
 * @since 1.0.0
 */
function elemenane_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Default Sidebar', 'elemenane' ),
			'id'            => 'default-sidebar',
			'description'   => __( 'Sidebar widget area for posts and pages', 'elemenane' ),
			'before_widget' => '<div class="ane-sidebar">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title general-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Home Sidebar', 'elemenane' ),
			'id'            => 'home-sidebar',
			'description'   => __( 'Sidebar widget area for homepage', 'elemenane' ),
			'before_widget' => '<div class="ane-sidebar">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title general-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'elemenane_widgets_init' );

/**
 * Remove WordPress version from script and style URLs for security
 *
 * @param string $src The URL of the script or style.
 * @return string Modified URL without version query string.
 * @since 1.0.0
 */
function elemenane_remove_wp_version_strings( $src ) {
	if ( ! is_string( $src ) ) {
		return $src;
	}

	if ( strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return esc_url( $src );
}
add_filter( 'script_loader_src', 'elemenane_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'elemenane_remove_wp_version_strings' );

/**
 * Remove WordPress version from meta generator tag for security
 *
 * @return string Empty string to remove generator tag.
 * @since 1.0.0
 */
function elemenane_remove_meta_version() {
	return '';
}
add_filter( 'the_generator', 'elemenane_remove_meta_version' );

/**
 * Auto-load all PHP files from /inc directory and subdirectories
 *
 * @since 1.0.0
 */
$root_files = glob( get_template_directory() . '/inc/*.php' );
$subdirectory_files = glob( get_template_directory() . '/inc/**/*.php' );
$all_files = array_merge( $root_files ?: array(), $subdirectory_files ?: array() );

foreach ( $all_files as $file ) {
	if ( is_file( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) === 'php' ) {
		include_once $file;
	}
}
