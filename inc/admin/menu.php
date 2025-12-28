<?php
/**
 * Admin Menu Styling & Customization.
 *
 * Modern sidebar menu dengan dynamic brand colors dan smooth animations.
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu styles now loaded from scss/_admin-menu.scss
 * Compiled into css/admin.css
 *
 * All inline styles (467 lines with 109 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue admin menu JavaScript.
 */
function ane_admin_menu_scripts() {
	wp_enqueue_script(
		'ane-admin-menu',
		get_base_url_assets() . '/js/admin-menu.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ane_admin_menu_scripts' );

/**
 * Customize admin menu order.
 *
 * Reorder menu items untuk better UX.
 */
function ane_custom_menu_order( $menu_order ) {
	if ( ! $menu_order ) {
		return true;
	}

	return array(
		'index.php',                   // Dashboard.
		'ane-dashboard',           // Custom Dashboard (if using).
		'separator1',                  // First separator.
		'edit.php',                    // Posts.
		'upload.php',                  // Media.
		'edit.php?post_type=page',     // Pages.
		'edit-comments.php',           // Comments.
		'separator2',                  // Second separator.
		'themes.php',                  // Appearance.
		'plugins.php',                 // Plugins.
		'users.php',                   // Users.
		'tools.php',                   // Tools.
		'options-general.php',         // Settings.
		'separator-last',              // Last separator.
	);
}
// Uncomment to enable custom menu order:
// add_filter( 'custom_menu_order', '__return_true' );
// add_filter( 'menu_order', 'ane_custom_menu_order' );

/**
 * Remove unnecessary admin menu items for clients.
 *
 * Simplify admin interface untuk non-admin users.
 * Uncomment items you want to hide.
 */
function ane_remove_admin_menus() {
	// Only for non-admin users.
	if ( ! current_user_can( 'manage_options' ) ) {
		// remove_menu_page( 'tools.php' );           // Tools.
		// remove_menu_page( 'options-general.php' ); // Settings.
		// remove_menu_page( 'edit-comments.php' );   // Comments.
	}
}
add_action( 'admin_menu', 'ane_remove_admin_menus', 999 );

/**
 * Add custom CSS classes to admin body.
 *
 * Allows targeting specific conditions dengan CSS.
 *
 * @param string $classes Current body classes.
 * @return string Modified body classes.
 */
function ane_admin_body_class( $classes ) {
	// Add custom admin class.
	$classes .= ' sarika-admin';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$classes .= ' user-role-' . $user->roles[0];
	}

	// Add mobile class.
	if ( wp_is_mobile() ) {
		$classes .= ' sarika-mobile-admin';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'ane_admin_body_class' );

/**
 * Customize admin menu icon for custom post type.
 *
 * Example: Change dashicon untuk specific menu item.
 *
 * @param array $icon_data Icon data array.
 * @param string $icon_file Icon file path.
 * @param int $menu_id Menu ID.
 * @return array Modified icon data.
 */
function ane_custom_menu_icon( $icon_data, $icon_file, $menu_id ) {
	// Example: Change icon untuk specific menu.
	// if ( $menu_id === 'edit.php' ) {
	// $icon_data = 'dashicons-admin-post';
	// }

	return $icon_data;
}
// add_filter( 'wp_menu_image', 'ane_custom_menu_icon', 10, 3 );
