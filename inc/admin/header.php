<?php
/**
 * Admin Header/Topbar Styling.
 *
 * Custom styling untuk WordPress admin bar (top black bar).
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin bar styles now loaded from scss/_admin-header.scss
 * Compiled into css/admin.css
 *
 * All inline styles (395 lines with 142 !important declarations)
 * have been moved to external SCSS for better caching and performance.
 */

/**
 * Enqueue mobile header JavaScript.
 */
function ane_admin_header_scripts( $hook ) {
	// Only load on admin pages
	if ( ! is_admin() ) {
		return;
	}

	$base_url = get_base_url_assets();
	$version = wp_get_theme()->get( 'Version' );

	if ( file_exists( get_template_directory() . '/js/admin-header.js' ) ) {
		wp_enqueue_script(
			'ane-admin-header',
			$base_url . '/js/admin-header.js',
			array(),
			$version,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'ane_admin_header_scripts' );

/**
 * Customize admin bar menu items.
 *
 * Add custom New Post & New Page buttons, remove unnecessary items.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function ane_customize_admin_bar( $wp_admin_bar ) {
	// Remove WordPress logo.
	$wp_admin_bar->remove_node( 'wp-logo' );

	// Remove comments.
	$wp_admin_bar->remove_node( 'comments' );

	// Remove new content menu.
	$wp_admin_bar->remove_node( 'new-content' );

	// Remove updates.
	$wp_admin_bar->remove_node( 'updates' );

	// Add New Post button (solid) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-new-post',
			'title'  => '<span class="ane-btn-icon ane-btn-icon-post"></span>' . __( 'New Post', 'newsane' ),
			'href'   => admin_url( 'post-new.php' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'ane-action-btn',
			),
		)
	);

	// Add New Page button (outline) - direct to top-secondary.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-new-page',
			'title'  => '<span class="ane-btn-icon ane-btn-icon-page"></span>' . __( 'New Page', 'newsane' ),
			'href'   => admin_url( 'post-new.php?post_type=page' ),
			'parent' => 'top-secondary',
			'meta'   => array(
				'class' => 'ane-action-btn',
			),
		)
	);

	// Add Dashboard link to site-name submenu.
	$wp_admin_bar->add_node(
		array(
			'id'     => 'ane-dashboard',
			'title'  => __( 'Dashboard', 'newsane' ),
			'href'   => admin_url( 'admin.php?page=ane-setup' ),
			'parent' => 'site-name',
			'meta'   => array(
				'class' => 'ane-dashboard-link',
			),
		)
	);
}
add_action( 'admin_bar_menu', 'ane_customize_admin_bar', 999 );

/**
 * Hide admin bar for non-admin users on frontend.
 *
 * Clean frontend experience untuk subscribers/customers.
 */
function ane_hide_admin_bar_frontend() {
	if ( ! current_user_can( 'manage_options' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'ane_hide_admin_bar_frontend' );

/**
 * Add custom CSS class to admin bar.
 *
 * @param string $class Current class.
 * @return string Modified class.
 */
function ane_admin_bar_class( $class ) {
	$class .= ' ane-admin-bar';

	// Add role-based class.
	$user = wp_get_current_user();
	if ( ! empty( $user->roles ) ) {
		$class .= ' user-role-' . $user->roles[0];
	}

	return $class;
}
add_filter( 'admin_bar_class', 'ane_admin_bar_class' );

/**
 * Customize "Howdy" text in admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 */
function ane_replace_howdy( $wp_admin_bar ) {
	$account = $wp_admin_bar->get_node( 'my-account' );

	if ( ! $account ) {
		return;
	}

	// Replace "Howdy" with "Selamat datang".
	$account->title = str_replace( 'Howdy,', __( 'Selamat datang,', 'newsane' ), $account->title );

	$wp_admin_bar->add_node( $account );
}
add_action( 'admin_bar_menu', 'ane_replace_howdy', 25 );
