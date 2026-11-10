<?php
/**
 * PHPStan bootstrap constants & third-party plugin stubs for Elemen Ane theme.
 *
 * @package ElemenAne
 */

namespace Elementor {
	if ( ! class_exists( '\Elementor\Experiments_Manager' ) ) {
		class Experiments_Manager {
			/**
			 * @param string $feature_name
			 * @return bool
			 */
			public function is_feature_active( $feature_name = '' ) {
				return false;
			}
		}
	}

	if ( ! class_exists( '\Elementor\Files_Manager' ) ) {
		class Files_Manager {
			/**
			 * @return void
			 */
			public function clear_cache() {}
		}
	}

	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		class Plugin {
			/**
			 * @var self|null
			 */
			public static $instance;

			/**
			 * @var Experiments_Manager|null
			 */
			public $experiments;

			/**
			 * @var Files_Manager|null
			 */
			public $files_manager;
		}
	}
}

namespace {
	if ( ! defined( 'ELEMENANE_VERSION' ) ) {
		define( 'ELEMENANE_VERSION', '1.1.8' );
	}

	if ( ! defined( 'ELEMENANE_DIR' ) ) {
		define( 'ELEMENANE_DIR', __DIR__ );
	}

	if ( ! defined( 'ELEMENANE_URI' ) ) {
		define( 'ELEMENANE_URI', 'https://example.com/wp-content/themes/elemenane' );
	}

	if ( ! defined( 'ELEMENANE_GMAPS_API_KEY' ) ) {
		define( 'ELEMENANE_GMAPS_API_KEY', '' );
	}

	if ( ! defined( 'ELEMENANE_UPDATER_TOKEN' ) ) {
		define( 'ELEMENANE_UPDATER_TOKEN', '' );
	}

	if ( ! defined( 'ELEMENANE_WEBP_QUALITY' ) ) {
		define( 'ELEMENANE_WEBP_QUALITY', 78 );
	}

	// Stubs for optional plugins (Breadcrumb NavXT, Post Views Counter, WP-PageNavi, Hello Elementor).
	if ( ! function_exists( 'hello_elementor_get_setting' ) ) {
		/**
		 * @param string $setting_id
		 * @return mixed
		 */
		function hello_elementor_get_setting( $setting_id = '' ) {
			return '';
		}
	}

	if ( ! function_exists( 'bcn_display' ) ) {
		/**
		 * @param bool $return
		 * @param bool $linked
		 * @param bool $reverse
		 * @param bool $force
		 * @return string|void
		 */
		function bcn_display( $return = false, $linked = true, $reverse = false, $force = false ) {}
	}

	if ( ! function_exists( 'pvc_get_post_views' ) ) {
		/**
		 * @param int $post_id
		 * @return int
		 */
		function pvc_get_post_views( $post_id = 0 ) {
			return 0;
		}
	}

	if ( ! function_exists( 'wp_pagenavi' ) ) {
		/**
		 * @param array<string, mixed> $args
		 * @return void
		 */
		function wp_pagenavi( $args = array() ) {}
	}
}
