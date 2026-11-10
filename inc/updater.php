<?php
/**
 * Theme Auto-Updater from GitHub Releases.
 *
 * Checks for theme updates from GitHub releases (supports public and private
 * repositories via ELEMENANE_UPDATER_TOKEN or ACF/option `ane_updater_token`).
 *
 * @package elemenane
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elemen Ane Theme Updater Class.
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
	 * Transient cache key.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'elemenane_update_check';

	/**
	 * Last error transient key.
	 *
	 * @var string
	 */
	const ERROR_KEY = 'elemenane_updater_last_error';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$theme                 = wp_get_theme( $this->theme_slug );
		$this->current_version = (string) $theme->get( 'Version' );
		$this->github_api_url  = "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases/latest";

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
		add_filter( 'themes_api', array( $this, 'theme_info_modal' ), 10, 3 );
		add_filter( 'http_request_args', array( $this, 'authenticate_github_download' ), 10, 2 );
		add_filter( 'upgrader_source_selection', array( $this, 'fix_source_folder' ), 10, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_theme_cache_after_upgrade' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'add_update_checker_notice' ) );
		add_filter( 'theme_action_links_' . $this->theme_slug, array( $this, 'add_update_action_link' ), 10, 2 );
	}

	/**
	 * Retrieve GitHub Personal Access Token if configured.
	 *
	 * Priority:
	 * 1. ELEMENANE_UPDATER_TOKEN constant in wp-config.php
	 * 2. ACF field / option `ane_updater_token`
	 *
	 * @return string
	 */
	private function get_github_token() {
		if ( defined( 'ELEMENANE_UPDATER_TOKEN' ) && is_string( ELEMENANE_UPDATER_TOKEN ) && '' !== trim( ELEMENANE_UPDATER_TOKEN ) ) {
			return trim( ELEMENANE_UPDATER_TOKEN );
		}

		if ( function_exists( 'get_field' ) ) {
			$acf_token = get_field( 'ane_updater_token', 'option' );
			if ( is_string( $acf_token ) && '' !== trim( $acf_token ) ) {
				return trim( $acf_token );
			}
		}

		$opt_token = get_option( 'ane_updater_token', '' );
		return is_string( $opt_token ) ? trim( $opt_token ) : '';
	}

	/**
	 * Check for theme updates.
	 *
	 * @param object $transient Update transient.
	 * @return object Modified transient.
	 */
	public function check_for_update( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		$remote_version = $this->get_remote_version();
		if ( ! $remote_version ) {
			return $transient;
		}

		if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme'        => $this->theme_slug,
				'new_version'  => $remote_version['version'],
				'url'          => $remote_version['url'],
				'package'      => $remote_version['package'],
				'requires'     => '6.4',
				'requires_php' => '8.0',
			);
		}

		return $transient;
	}

	/**
	 * Provide release changelog details for the WordPress theme details modal.
	 *
	 * @param false|object|array $result The result object or array.
	 * @param string             $action The type of information being requested.
	 * @param object             $args   Theme API arguments.
	 * @return false|object|array
	 */
	public function theme_info_modal( $result, $action, $args ) {
		if ( 'theme_information' !== $action || ! isset( $args->slug ) || $this->theme_slug !== $args->slug ) {
			return $result;
		}

		$remote = $this->get_remote_version();
		if ( ! $remote ) {
			return $result;
		}

		$theme = wp_get_theme( $this->theme_slug );

		return (object) array(
			'name'          => $theme->get( 'Name' ),
			'slug'          => $this->theme_slug,
			'version'       => $remote['version'],
			'author'        => $theme->get( 'Author' ),
			'homepage'      => $theme->get( 'ThemeURI' ),
			'requires'      => '6.4',
			'requires_php'  => '8.0',
			'last_updated'  => isset( $remote['published_at'] ) ? $remote['published_at'] : '',
			'sections'      => array(
				'description' => wp_kses_post( $theme->get( 'Description' ) ),
				'changelog'   => ! empty( $remote['notes'] ) ? wp_kses_post( wpautop( $remote['notes'] ) ) : esc_html__( 'See GitHub release notes for full changelog.', 'elemenane' ),
			),
			'download_link' => $remote['package'],
		);
	}

	/**
	 * Inject Authorization header when downloading release package from GitHub.
	 *
	 * @param array<string, mixed> $args HTTP request arguments.
	 * @param string               $url  Request URL.
	 * @return array<string, mixed>
	 */
	public function authenticate_github_download( $args, $url ) {
		if ( ! is_string( $url ) ) {
			return $args;
		}

		$is_github_repo = false !== strpos( $url, "api.github.com/repos/{$this->github_owner}/{$this->github_repo}/" )
			|| false !== strpos( $url, "github.com/{$this->github_owner}/{$this->github_repo}/releases/download/" );

		if ( ! $is_github_repo ) {
			return $args;
		}

		$token = $this->get_github_token();
		if ( '' !== $token ) {
			if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
				$args['headers'] = array();
			}
			$args['headers']['Authorization'] = 'Bearer ' . $token;
		}

		return $args;
	}

	/**
	 * Get remote version info from GitHub.
	 *
	 * @return array<string, string>|false Version info or false on failure.
	 */
	private function get_remote_version() {
		$cached = get_transient( self::CACHE_KEY );
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$headers = array(
			'Accept'     => 'application/vnd.github+json',
			'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
		);

		$token = $this->get_github_token();
		if ( '' !== $token ) {
			$headers['Authorization'] = 'Bearer ' . $token;
		}

		$response = wp_remote_get(
			$this->github_api_url,
			array(
				'timeout' => 10,
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			set_transient( self::ERROR_KEY, $response->get_error_message(), HOUR_IN_SECONDS );
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			set_transient(
				self::ERROR_KEY,
				sprintf( 'HTTP %d from GitHub API.', $code ),
				HOUR_IN_SECONDS
			);
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) || empty( $data['tag_name'] ) ) {
			return false;
		}

		$version     = ltrim( (string) $data['tag_name'], 'v' );
		$package_url = '';

		if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				if (
					isset( $asset['name'], $asset['browser_download_url'] )
					&& 0 === strpos( (string) $asset['name'], 'elemenane-' )
					&& false !== strpos( (string) $asset['name'], '.zip' )
				) {
					$package_url = (string) $asset['browser_download_url'];
					break;
				}
			}
		}

		if ( '' === $package_url && ! empty( $data['zipball_url'] ) ) {
			$package_url = (string) $data['zipball_url'];
		}

		if ( '' === $package_url ) {
			return false;
		}

		$version_info = array(
			'version'      => $version,
			'url'          => isset( $data['html_url'] ) ? (string) $data['html_url'] : '',
			'package'      => $package_url,
			'notes'        => isset( $data['body'] ) ? (string) $data['body'] : '',
			'published_at' => isset( $data['published_at'] ) ? (string) $data['published_at'] : '',
		);

		delete_transient( self::ERROR_KEY );
		set_transient( self::CACHE_KEY, $version_info, 6 * HOUR_IN_SECONDS );

		return $version_info;
	}

	/**
	 * Fix source folder name after update.
	 *
	 * @param string $source        Source folder.
	 * @param string $remote_source Remote source.
	 * @param object $upgrader      WP_Upgrader instance.
	 * @return string Fixed source folder.
	 */
	public function fix_source_folder( $source, $remote_source, $upgrader ) {
		global $wp_filesystem;

		if ( ! isset( $upgrader->skin->theme ) || $this->theme_slug !== $upgrader->skin->theme ) {
			return $source;
		}

		$corrected_source = trailingslashit( $remote_source ) . $this->theme_slug . '/';
		if ( untrailingslashit( $source ) === untrailingslashit( $corrected_source ) ) {
			return $source;
		}

		if ( $wp_filesystem && $wp_filesystem->move( $source, $corrected_source, true ) ) {
			return $corrected_source;
		}

		return $source;
	}

	/**
	 * Add update checker notice in admin.
	 *
	 * @return void
	 */
	public function add_update_checker_notice() {
		$screen = get_current_screen();
		if ( ! $screen || 'themes' !== $screen->base ) {
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
	 * @param array<string, string> $actions Theme action links.
	 * @param WP_Theme              $theme   Theme object.
	 * @return array<string, string> Modified action links.
	 */
	public function add_update_action_link( $actions, $theme ) {
		unset( $theme );
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
				esc_html__( 'Update Available', 'elemenane' )
			);
		}

		return $actions;
	}

	/**
	 * Clear theme cache after upgrade.
	 *
	 * @param object               $upgrader WP_Upgrader instance.
	 * @param array<string, mixed> $options  Upgrade options.
	 * @return void
	 */
	public function clear_theme_cache_after_upgrade( $upgrader, $options ) {
		unset( $upgrader );
		if ( ! isset( $options['type'] ) || 'theme' !== $options['type'] ) {
			return;
		}

		if ( isset( $options['themes'] ) && is_array( $options['themes'] ) && in_array( $this->theme_slug, $options['themes'], true ) ) {
			wp_cache_delete( $this->theme_slug, 'themes' );
			wp_clean_themes_cache();
			delete_transient( self::CACHE_KEY );
			delete_transient( self::ERROR_KEY );
			delete_site_transient( 'update_themes' );
		}
	}

	/**
	 * Force update check (for manual trigger).
	 *
	 * @return void
	 */
	public static function force_update_check() {
		delete_transient( self::CACHE_KEY );
		delete_transient( self::ERROR_KEY );
		delete_site_transient( 'update_themes' );
	}
}

new Elemenane_Updater();

/**
 * Handle authenticated manual update check request in wp-admin/themes.php.
 *
 * @return void
 */
function elemenane_handle_manual_update_check() {
	if ( ! is_admin() || ! current_user_can( 'update_themes' ) ) {
		return;
	}

	if ( ! isset( $_GET['ane_force_check'] ) && ! isset( $_GET['elemenane_force_check'] ) ) {
		return;
	}

	check_admin_referer( 'elemenane_force_check' );

	Elemenane_Updater::force_update_check();

	add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Update check completed!', 'elemenane' ); ?></p>
			</div>
			<?php
		}
	);
}
add_action( 'admin_init', 'elemenane_handle_manual_update_check' );
