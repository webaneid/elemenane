<?php
/**
 * Mobile Footer Navigation Menu
 *
 * Bottom navigation bar for mobile admin (≤782px)
 * with 5 quick access menu items.
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render mobile footer navigation menu.
 *
 * Only visible on mobile devices (≤782px).
 * Provides quick access to Dashboard, Pages, Create Post, Posts, and Settings.
 */
function ane_admin_footer_mobile_menu() : void {
	// Only display in admin area.
	if ( ! is_admin() ) {
		return;
	}

	// Get current screen.
	$current_screen = get_current_screen();
	$current_page   = '';

	// Determine active menu based on current screen.
	if ( $current_screen ) {
		if ( 'dashboard' === $current_screen->id || 'ane-dashboard' === $current_screen->id ) {
			$current_page = 'dashboard';
		} elseif ( 'edit-page' === $current_screen->id || 'page' === $current_screen->id ) {
			$current_page = 'pages';
		} elseif ( 'post-new' === $current_screen->id ) {
			$current_page = 'create';
		} elseif ( 'edit-post' === $current_screen->id || 'post' === $current_screen->id ) {
			$current_page = 'posts';
		} elseif ( strpos( $current_screen->id, 'newsane' ) !== false ) {
			$current_page = 'settings';
		}
	}

	?>
	<div class="ane-footer-mobile-menu">
		<nav class="ane-footer-nav">
			<!-- Dashboard -->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ane-dashboard' ) ); ?>"
			   class="ane-footer-item <?php echo 'dashboard' === $current_page ? 'is-active' : ''; ?>">
				<svg class="ane-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g stroke="currentColor" stroke-width="1.5">
						<path stroke-linecap="round" d="M13.5 13L17 9m-3 6a2 2 0 1 1-4 0a2 2 0 0 1 4 0Zm-8-3a6 6 0 0 1 9-5.197"/>
						<path d="M2.5 12c0-4.478 0-6.717 1.391-8.109c1.392-1.39 3.63-1.39 8.11-1.39c4.477 0 6.717 0 8.108 1.39c1.391 1.392 1.391 3.63 1.391 8.11c0 4.477 0 6.717-1.391 8.108S16.479 21.5 12 21.5c-4.478 0-6.717 0-8.109-1.391c-1.39-1.391-1.39-3.63-1.39-8.109Z"/>
					</g>
				</svg>
				<span class="ane-footer-label"><?php esc_html_e( 'Dashboard', 'newsane' ); ?></span>
			</a>

			<!-- Pages -->
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"
			   class="ane-footer-item <?php echo 'pages' === $current_page ? 'is-active' : ''; ?>">
				<svg class="ane-footer-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill="currentColor" fill-rule="evenodd" d="M7.29 13.904L5.25 10.75L2.096 8.71a2.4 2.4 0 0 1 .5-4.278l9.273-3.296a2.346 2.346 0 0 1 2.996 2.995L13.45 3.63a.844.844 0 0 0-1.08-1.08L3.1 5.846a.9.9 0 0 0-.19 1.604l2.78 1.799l3.279-3.28a.75.75 0 1 1 1.06 1.061L6.75 10.31l1.799 2.779a.9.9 0 0 0 1.604-.188l3.297-9.272l1.413.502l-3.296 9.273a2.4 2.4 0 0 1-4.277.5" clip-rule="evenodd"/>
				</svg>
				<span class="ane-footer-label"><?php esc_html_e( 'Pages', 'newsane' ); ?></span>
			</a>

			<!-- Create Post (Center - Primary) -->
			<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"
			   class="ane-footer-item ane-footer-create <?php echo 'create' === $current_page ? 'is-active' : ''; ?>">
				<svg class="ane-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill="currentColor"/>
				</svg>
				<span class="ane-footer-label"><?php esc_html_e( 'Create', 'newsane' ); ?></span>
			</a>

			<!-- Posts -->
			<a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"
			   class="ane-footer-item <?php echo 'posts' === $current_page ? 'is-active' : ''; ?>">
				<svg class="ane-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill="currentColor" fill-rule="evenodd" d="M11.943 1.25H13.5a.75.75 0 0 1 0 1.5H12c-2.378 0-4.086.002-5.386.176c-1.279.172-2.05.5-2.62 1.069c-.569.57-.896 1.34-1.068 2.619c-.174 1.3-.176 3.008-.176 5.386s.002 4.086.176 5.386c.172 1.279.5 2.05 1.069 2.62c.57.569 1.34.896 2.619 1.068c1.3.174 3.008.176 5.386.176s4.086-.002 5.386-.176c1.279-.172 2.05-.5 2.62-1.069c.569-.57.896-1.34 1.068-2.619c.174-1.3.176-3.008.176-5.386v-1.5a.75.75 0 0 1 1.5 0v1.557c0 2.309 0 4.118-.19 5.53c-.194 1.444-.6 2.584-1.494 3.479c-.895.895-2.035 1.3-3.48 1.494c-1.411.19-3.22.19-5.529.19h-.114c-2.309 0-4.118 0-5.53-.19c-1.444-.194-2.584-.6-3.479-1.494c-.895-.895-1.3-2.035-1.494-3.48c-.19-1.411-.19-3.22-.19-5.529v-.114c0-2.309 0-4.118.19-5.53c.194-1.444.6-2.584 1.494-3.479c.895-.895 2.035-1.3 3.48-1.494c1.411-.19 3.22-.19 5.529-.19m4.827 1.026a3.503 3.503 0 0 1 4.954 4.953l-6.648 6.649c-.371.37-.604.604-.863.806a5.3 5.3 0 0 1-.987.61c-.297.141-.61.245-1.107.411l-2.905.968a1.492 1.492 0 0 1-1.887-1.887l.968-2.905c.166-.498.27-.81.411-1.107q.252-.526.61-.987c.202-.26.435-.492.806-.863zm3.893 1.06a2.003 2.003 0 0 0-2.832 0l-.376.377q.032.145.098.338c.143.413.415.957.927 1.469a3.9 3.9 0 0 0 1.807 1.025l.376-.376a2.003 2.003 0 0 0 0-2.832m-1.558 4.391a5.4 5.4 0 0 1-1.686-1.146a5.4 5.4 0 0 1-1.146-1.686L11.218 9.95c-.417.417-.58.582-.72.76a4 4 0 0 0-.437.71c-.098.203-.172.423-.359.982l-.431 1.295l1.032 1.033l1.295-.432c.56-.187.779-.261.983-.358q.378-.18.71-.439c.177-.139.342-.302.759-.718z" clip-rule="evenodd"/>
				</svg>
				<span class="ane-footer-label"><?php esc_html_e( 'Posts', 'newsane' ); ?></span>
			</a>

			<!-- Settings (Ane Setup) -->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ane-setup' ) ); ?>"
			   class="ane-footer-item <?php echo 'settings' === $current_page ? 'is-active' : ''; ?>">
				<svg class="ane-footer-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<g stroke="currentColor" stroke-linecap="round" stroke-width="1.5">
						<path d="M9 21H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6"/>
						<path stroke-linejoin="round" d="M12 21v-4.639c0-.51.1-.999.284-1.453M22 21v-3.185m-7.778-5.08A5.5 5.5 0 0 1 17 12c2.28 0 4.203 1.33 4.805 3.15M15 22v-2.177M19 22v-5.147C19 15.83 18.105 15 17 15s-2 .83-2 1.853v.794M2 7h20M5 5.01l.01-.011M8 5.01l.01-.011M11 5.01l.01-.011"/>
					</g>
				</svg>
				<span class="ane-footer-label"><?php esc_html_e( 'Settings', 'newsane' ); ?></span>
			</a>
		</nav>
	</div>
	<?php
}
add_action( 'admin_footer', 'ane_admin_footer_mobile_menu' );
