<?php
/**
 * Theme Auto-Updater
 *
 * Checks for theme updates from GitHub releases and provides
 * automatic update functionality for premium theme distribution.
 *
 * @package elemenane
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elemen Ane Theme Updater Class
 */
class Elemenane_Updater {

	/**
	 * GitHub repository owner.
	 *
	 * @var string
	 */
	private $github_owner = 'webaneid';

	/**
	 * GitHub repository name.
	 *
	 * @var string
	 */
	private $github_repo = 'elemenane';

	/**
	 * Current theme version.
	 *
	 * @var string
	 */
	private $current_version;

	/**
	 * Theme slug.
	 *
	 * @var string
	 */
	private $theme_slug = 'elemenane';

	/**
	 * GitHub API URL for releases.
	 *
	 * @var string
	 */
	private $github_api_url;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$theme = wp_get_theme();
		$this->current_version = $theme->get( 'Version' );
		$this->github_api_url = "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases/latest";

		// Hook into WordPress update system.
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_source_folder' ), 10, 3 );

		// Add update checker to admin footer.
		add_action( 'admin_footer', array( $this, 'add_update_checker_notice' ) );

		// Add custom update link to theme actions.
		add_filter( 'theme_action_links_' . $this->theme_slug, array( $this, 'add_update_action_link' ), 10, 2 );
	}

	/**
	 * Check for theme updates.
	 *
	 * @param object $transient Update transient.
	 * @return object Modified transient.
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get latest release from GitHub.
		$remote_version = $this->get_remote_version();

		if ( ! $remote_version ) {
			return $transient;
		}

		// Compare versions.
		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			$theme_data = wp_get_theme( $this->theme_slug );

			$transient->response[ $this->theme_slug ] = array(
				'theme'        => $this->theme_slug,
				'new_version'  => $remote_version['version'],
				'url'          => $remote_version['url'],
				'package'      => $remote_version['package'],
				'requires'     => '5.0',
				'requires_php' => '7.4',
			);
		}

		return $transient;
	}

	/**
	 * Get remote version info from GitHub.
	 *
	 * @return array|false Version info or false on failure.
	 */
	private function get_remote_version() {
		// Check cache first (24 hours).
		$cache_key = 'elemenane_update_check';
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		// Fetch from GitHub API.
		$response = wp_remote_get(
			$this->github_api_url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/vnd.github.v3+json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data['tag_name'] ) ) {
			return false;
		}

		// Remove 'v' prefix from version.
		$version = ltrim( $data['tag_name'], 'v' );

		// Get download URL from assets (elemenane-x.x.x.zip).
		$package_url = '';
		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				if ( isset( $asset['name'] ) && strpos( $asset['name'], 'elemenane-' ) === 0 && strpos( $asset['name'], '.zip' ) !== false ) {
					$package_url = $asset['browser_download_url'];
					break;
				}
			}
		}

		// If no asset found, return false (don't use zipball as it creates wrong folder names).
		if ( empty( $package_url ) ) {
			return false;
		}

		$version_info = array(
			'version' => $version,
			'url'     => $data['html_url'],
			'package' => $package_url,
		);

		// Cache for 24 hours.
		set_transient( $cache_key, $version_info, DAY_IN_SECONDS );

		return $version_info;
	}

	/**
	 * Fix source folder name after update.
	 *
	 * GitHub downloads come with weird folder names, we need to rename to theme slug.
	 *
	 * @param string $source        Source folder.
	 * @param string $remote_source Remote source.
	 * @param object $upgrader      WP_Upgrader instance.
	 * @return string Fixed source folder.
	 */
	public function fix_source_folder( $source, $remote_source, $upgrader ) {
		global $wp_filesystem;

		// Only for theme updates.
		if ( ! isset( $upgrader->skin->theme ) || $upgrader->skin->theme !== $this->theme_slug ) {
			return $source;
		}

		// Fix the folder name.
		$corrected_source = trailingslashit( $remote_source ) . $this->theme_slug . '/';

		if ( $wp_filesystem->move( $source, $corrected_source, true ) ) {
			return $corrected_source;
		}

		return $source;
	}

	/**
	 * Add update checker notice in admin.
	 */
	public function add_update_checker_notice() {
		$screen = get_current_screen();

		// Only show on themes page.
		if ( 'themes' !== $screen->base ) {
			return;
		}

		$remote_version = $this->get_remote_version();

		if ( ! $remote_version ) {
			return;
		}

		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			$update_url = wp_nonce_url(
				admin_url( 'update.php?action=upgrade-theme&theme=' . $this->theme_slug ),
				'upgrade-theme_' . $this->theme_slug
			);
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<strong><?php esc_html_e( 'Elemen Ane Theme Update Available!', 'elemenane' ); ?></strong><br>
					<?php
					printf(
						/* translators: 1: new version, 2: current version, 3: update URL */
						esc_html__( 'Version %1$s is available. You have version %2$s.', 'elemenane' ) . ' <a href="%3$s" class="update-link">' . esc_html__( 'Update now', 'elemenane' ) . '</a>',
						esc_html( $remote_version['version'] ),
						esc_html( $this->current_version ),
						esc_url( $update_url )
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Add update link to theme action links.
	 *
	 * @param array  $actions Theme action links.
	 * @param object $theme   Theme object.
	 * @return array Modified action links.
	 */
	public function add_update_action_link( $actions, $theme ) {
		$remote_version = $this->get_remote_version();

		if ( ! $remote_version ) {
			return $actions;
		}

		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			$update_url = wp_nonce_url(
				admin_url( 'update.php?action=upgrade-theme&theme=' . $this->theme_slug ),
				'upgrade-theme_' . $this->theme_slug
			);

			$actions['update'] = sprintf(
				'<a href="%s" class="update-link" aria-label="%s">%s</a>',
				esc_url( $update_url ),
				/* translators: %s: theme name */
				esc_attr( sprintf( __( 'Update %s now', 'elemenane' ), 'Elemen Ane' ) ),
				__( 'Update Available', 'elemenane' )
			);
		}

		return $actions;
	}

	/**
	 * Force update check (for manual trigger).
	 */
	public static function force_update_check() {
		delete_transient( 'elemenane_update_check' );
		delete_site_transient( 'update_themes' );
	}
}

// Initialize updater.
new Elemenane_Updater();

/**
 * Add manual update check button to admin.
 */
function elemenane_add_manual_update_check() {
	add_action( 'admin_notices', function() {
		$screen = get_current_screen();
		if ( 'themes' === $screen->base && isset( $_GET['elemenane_force_check'] ) ) {
			Elemenane_Updater::force_update_check();
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Update check completed!', 'elemenane' ); ?></p>
			</div>
			<?php
		}

		// Debug: Show update transient data (remove after debugging).
		if ( 'themes' === $screen->base && isset( $_GET['elemenane_debug'] ) ) {
			$updates = get_site_transient( 'update_themes' );
			echo '<div class="notice notice-info"><pre>';
			echo 'Current version: ' . wp_get_theme()->get( 'Version' ) . "\n";
			echo 'Update data: ';
			print_r( isset( $updates->response['elemenane'] ) ? $updates->response['elemenane'] : 'No update data' );
			echo '</pre></div>';
		}
	});
}
add_action( 'admin_init', 'elemenane_add_manual_update_check' );
