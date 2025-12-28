<?php
/**
 * Admin Customizer - Custom Login & Admin Styling.
 *
 * Custom login page dengan glassmorphism effect dan Webane branding.
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue custom login styles.
 *
 * Loads external CSS file instead of inline styles for better performance and caching.
 */
function ane_login_enqueue_scripts() : void {
	wp_enqueue_style(
		'ane-login',
		get_base_url_assets() . '/css/admin.min.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'login_enqueue_scripts', 'ane_login_enqueue_scripts' );

/**
 * Add custom title and subtitle to login form.
 */
function ane_login_form_top() {
	?>
	<h1 class="login-title"><?php esc_html_e( 'Login', 'newsane' ); ?></h1>
	<p class="login-subtitle"><?php esc_html_e( 'Welcome Back! Please Login To Your Account', 'newsane' ); ?></p>
	<?php
}
add_action( 'login_form', 'ane_login_form_top', 1 );

/**
 * Add signup link below login form.
 */
function ane_login_form_bottom() {
	if ( get_option( 'users_can_register' ) ) {
		?>
		<p class="signup-link">
			<?php esc_html_e( "Don't have an account?", 'newsane' ); ?>
			<a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Signup', 'newsane' ); ?></a>
		</p>
		<?php
	}
}
add_action( 'login_form', 'ane_login_form_bottom', 100 );

/**
 * Change login logo URL to home.
 */
function ane_login_logo_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'ane_login_logo_url' );

/**
 * Change login logo title.
 */
function ane_login_logo_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'ane_login_logo_title' );

/**
 * Remove shake effect on login error.
 */
add_action( 'login_head', function() {
	remove_action( 'login_head', 'wp_shake_js', 12 );
});
