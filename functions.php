<?php
/**
 * Elemen Ane Theme Functions
 *
 * @package elemenane
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ELEMENANE_VERSION' ) ) {
	define( 'ELEMENANE_VERSION', (string) wp_get_theme()->get( 'Version' ) );
}

if ( ! defined( 'ELEMENANE_DIR' ) ) {
	define( 'ELEMENANE_DIR', get_template_directory() );
}

if ( ! defined( 'ELEMENANE_URI' ) ) {
	define( 'ELEMENANE_URI', get_template_directory_uri() );
}

/**
 * Configure default WordPress image sizes once per theme version or activation.
 *
 * @since 1.1.9
 * @return void
 */
function elemenane_configure_image_sizes() {
	if ( ELEMENANE_VERSION === get_option( 'elemenane_image_sizes_version' ) ) {
		return;
	}

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
	update_option( 'elemenane_image_sizes_version', ELEMENANE_VERSION );
}
add_action( 'after_switch_theme', 'elemenane_configure_image_sizes' );
add_action( 'admin_init', 'elemenane_configure_image_sizes' );

/**
 * Theme setup.
 *
 * @since 1.0.0
 * @return void
 */
function elemenane_theme_setup() {
	$locale = determine_locale();
	$mofile = ELEMENANE_DIR . "/languages/elemenane-{$locale}.mo";

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'elemenane', $mofile );
	}

	// Core theme supports.
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	// Custom image sizes.
	add_image_size( 'kotak', 394, 394, true );

	// Navigation menus.
	register_nav_menus(
		array(
			'menuutama'  => __( 'Menu Utama', 'elemenane' ),
			'mobilemenu' => __( 'Mobile Menu', 'elemenane' ),
			'menufooter' => __( 'Menu Footer', 'elemenane' ),
		)
	);

	// Post formats.
	add_theme_support( 'post-formats', array( 'video', 'gallery' ) );

	// HTML5 support.
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

	// Custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 98,
			'width'       => 255,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Yoast SEO breadcrumbs support.
	add_theme_support( 'yoast-seo-breadcrumbs' );
}
add_action( 'after_setup_theme', 'elemenane_theme_setup' );

/**
 * Register widget areas.
 *
 * @since 1.0.0
 * @return void
 */
function elemenane_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Default Sidebar', 'elemenane' ),
			'id'            => 'default-sidebar',
			'description'   => __( 'Sidebar widget area for posts and pages', 'elemenane' ),
			'before_widget' => '<section id="%1$s" class="ane-sidebar ane-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title general-title"><span>',
			'after_title'   => '</span></h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Home Sidebar', 'elemenane' ),
			'id'            => 'home-sidebar',
			'description'   => __( 'Sidebar widget area for homepage', 'elemenane' ),
			'before_widget' => '<section id="%1$s" class="ane-sidebar ane-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title general-title"><span>',
			'after_title'   => '</span></h2>',
		)
	);
}
add_action( 'widgets_init', 'elemenane_widgets_init' );

/**
 * Remove WordPress version from script and style URLs for security.
 *
 * @param string $src The URL of the script or style.
 * @return string Modified URL without version query string.
 * @since 1.0.0
 */
function elemenane_remove_wp_version_strings( $src ) {
	if ( ! is_string( $src ) ) {
		return $src;
	}

	// Only strip the WordPress version. Theme/plugin asset versions are kept so
	// updated files (including security fixes) bypass browser and CDN caches.
	$query = wp_parse_url( $src, PHP_URL_QUERY );
	if ( $query ) {
		parse_str( $query, $params );
		if ( isset( $params['ver'] ) && $GLOBALS['wp_version'] === $params['ver'] ) {
			$src = remove_query_arg( 'ver', $src );
		}
	}

	return esc_url( $src );
}
add_filter( 'script_loader_src', 'elemenane_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'elemenane_remove_wp_version_strings' );

/**
 * Remove WordPress version from meta generator tag for security.
 *
 * @return string Empty string to remove generator tag.
 * @since 1.0.0
 */
function elemenane_remove_meta_version() {
	return '';
}
add_filter( 'the_generator', 'elemenane_remove_meta_version' );

/**
 * Admin notice shown when ACF is not active.
 *
 * @since 1.1.4
 * @return void
 */
function elemenane_acf_missing_notice() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>',
		esc_html__( 'Elemen Ane:', 'elemenane' ),
		esc_html__( 'this theme requires the Advanced Custom Fields Pro plugin. Theme features are disabled until it is installed and activated.', 'elemenane' ),
		esc_url( admin_url( 'plugin-install.php?tab=upload' ) ),
		esc_html__( 'Upload ACF Pro', 'elemenane' )
	);
}

/**
 * Frontend placeholder shown when ACF is not active.
 *
 * @since 1.1.4
 * @return void
 */
function elemenane_acf_missing_frontend() {
	wp_die(
		esc_html__( 'This site is being set up. Please check back soon.', 'elemenane' ),
		esc_html( get_bloginfo( 'name' ) ),
		array( 'response' => 503 )
	);
}

/**
 * ACF Pro is required: modules and templates call get_field() directly.
 * Without it, skip loading the theme modules so the site does not fatal
 * and an admin can still reach wp-admin to install ACF.
 *
 * @since 1.1.4
 */
if ( ! class_exists( 'ACF' ) ) {
	// Defer after_switch_theme until ACF is active so theme setup (Elementor defaults) still runs.
	remove_action( 'init', 'check_theme_switched', 99 );
	add_action( 'admin_notices', 'elemenane_acf_missing_notice' );
	add_action( 'template_redirect', 'elemenane_acf_missing_frontend' );
	return;
}

/**
 * Load all theme modules explicitly (no glob()) for security and deterministic load order.
 *
 * @since 1.1.9
 */
$elemenane_includes = array(
	'/inc/acf.php',
	'/inc/admin.php',
	'/inc/archive.php',
	'/inc/branch-acf.php',
	'/inc/custompost.php',
	'/inc/elemenane.php',
	'/inc/elementor-defaults.php',
	'/inc/elementor.php',
	'/inc/feature-toggles.php',
	'/inc/handle.php',
	'/inc/header-acf.php',
	'/inc/icons.php',
	'/inc/landing.php',
	'/inc/product-acf.php',
	'/inc/product-filter.php',
	'/inc/product-woocommerce.php',
	'/inc/seo.php',
	'/inc/single-iklan.php',
	'/inc/sosmed.php',
	'/inc/tracking-scripts.php',
	'/inc/updater.php',
	'/inc/webp-converter.php',
	'/inc/widget.php',
	'/inc/admin/content.php',
	'/inc/admin/customizer.php',
	'/inc/admin/dashboard.php',
	'/inc/admin/design-tokens.php',
	'/inc/admin/footer-mobile-menu.php',
	'/inc/admin/header.php',
	'/inc/admin/linktree-analytics.php',
	'/inc/admin/linktree-core.php',
	'/inc/admin/menu.php',
	'/inc/admin/navigation.php',
	'/inc/admin/user.php',
);

foreach ( $elemenane_includes as $elemenane_include ) {
	require_once ELEMENANE_DIR . $elemenane_include;
}
