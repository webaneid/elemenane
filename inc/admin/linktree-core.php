<?php
/**
 * Linktree Analytics
 *
 * Track and analyze linktree link clicks with referrer detection.
 *
 * @package elemenane
 * @since 1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get current database version.
 */
function ane_get_linktree_db_version() {
	return get_option( 'ane_linktree_db_version', '0' );
}

/**
 * Update database version.
 *
 * @param string $version Version number.
 */
function ane_update_linktree_db_version( $version ) {
	update_option( 'ane_linktree_db_version', $version );
}

/**
 * Create custom table for linktree analytics.
 * Uses dbDelta for safe table creation/updates.
 */
function ane_create_linktree_analytics_table() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'ane_linktree_clicks';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		link_type varchar(50) NOT NULL,
		link_label varchar(255) NOT NULL,
		link_url varchar(500) NOT NULL,
		clicked_at datetime NOT NULL,
		referrer varchar(500) DEFAULT NULL,
		referrer_platform varchar(100) DEFAULT NULL,
		device_type varchar(20) DEFAULT NULL,
		user_agent text DEFAULT NULL,
		PRIMARY KEY  (id),
		KEY link_type (link_type),
		KEY clicked_at (clicked_at),
		KEY referrer_platform (referrer_platform),
		KEY device_type (device_type)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	// Update DB version after successful creation
	ane_update_linktree_db_version( '1.0' );
}

/**
 * Check and run database migrations if needed.
 * Runs on every admin page load (lightweight check).
 */
function ane_check_linktree_db_migration() {
	$current_version = ane_get_linktree_db_version();
	$target_version  = '1.0'; // Update this when you add new migrations

	// No migration needed
	if ( version_compare( $current_version, $target_version, '>=' ) ) {
		return;
	}

	// Run migrations
	if ( version_compare( $current_version, '1.0', '<' ) ) {
		ane_create_linktree_analytics_table();
	}

	// Future migrations example:
	// if ( version_compare( $current_version, '1.1', '<' ) ) {
	//     ane_migrate_to_1_1();
	// }
}
add_action( 'admin_init', 'ane_check_linktree_db_migration' );

// Also run on theme activation for first-time setup
add_action( 'after_switch_theme', 'ane_create_linktree_analytics_table' );

/**
 * Detect platform from referrer URL.
 *
 * @param string $referrer The HTTP referrer URL.
 * @return string Platform name or 'Direct' if no referrer.
 */
function ane_detect_referrer_platform( $referrer ) {
	if ( empty( $referrer ) ) {
		return 'Direct';
	}

	$referrer = strtolower( $referrer );

	// Social media platforms
	$platforms = array(
		'facebook.com'   => 'Facebook',
		'fb.com'         => 'Facebook',
		'instagram.com'  => 'Instagram',
		'tiktok.com'     => 'TikTok',
		'twitter.com'    => 'Twitter',
		'x.com'          => 'Twitter',
		't.co'           => 'Twitter',
		'youtube.com'    => 'YouTube',
		'youtu.be'       => 'YouTube',
		'linkedin.com'   => 'LinkedIn',
		'telegram.org'   => 'Telegram',
		't.me'           => 'Telegram',
		'threads.net'    => 'Threads',
		'whatsapp.com'   => 'WhatsApp',
		'wa.me'          => 'WhatsApp',
		'google.com'     => 'Google',
		'google.co.id'   => 'Google',
		'bing.com'       => 'Bing',
		'yahoo.com'      => 'Yahoo',
		'duckduckgo.com' => 'DuckDuckGo',
	);

	foreach ( $platforms as $domain => $platform ) {
		if ( strpos( $referrer, $domain ) !== false ) {
			return $platform;
		}
	}

	// Parse domain if not matched
	$parsed = wp_parse_url( $referrer );
	if ( isset( $parsed['host'] ) ) {
		return ucfirst( str_replace( 'www.', '', $parsed['host'] ) );
	}

	return 'Unknown';
}

/**
 * Detect device type from user agent.
 *
 * @param string $user_agent The user agent string.
 * @return string Device type: 'mobile', 'tablet', or 'desktop'.
 */
function ane_detect_device_type( $user_agent ) {
	if ( empty( $user_agent ) ) {
		return 'unknown';
	}

	// Use Mobile_Detect if available
	if ( class_exists( 'Mobile_Detect' ) ) {
		$detect = new Mobile_Detect();
		$detect->setUserAgent( $user_agent );

		if ( $detect->isTablet() ) {
			return 'tablet';
		} elseif ( $detect->isMobile() ) {
			return 'mobile';
		} else {
			return 'desktop';
		}
	}

	// Fallback detection
	$user_agent = strtolower( $user_agent );

	if ( preg_match( '/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $user_agent ) ) {
		return 'tablet';
	}

	if ( preg_match( '/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $user_agent ) ) {
		return 'mobile';
	}

	return 'desktop';
}

/**
 * Social media platforms shown on the linktree page, from ACF options.
 *
 * @return array Platform key => array( url, label, icon ).
 */
function ane_get_linktree_social_platforms() {
	$platforms = array(
		'whatsapp'  => 'WhatsApp',
		'facebook'  => 'Facebook',
		'twitter'   => 'Twitter',
		'youtube'   => 'Youtube',
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
		'telegram'  => 'Telegram',
		'threads'   => 'Threads',
		'linkedin'  => 'LinkedIn',
	);

	$social = array();
	foreach ( $platforms as $key => $label ) {
		$social[ $key ] = array(
			'url'   => get_field( 'ane_' . $key, 'option' ),
			'label' => $label,
			'icon'  => 'ane-' . $key,
		);
	}

	return $social;
}

/**
 * Links that can be tracked on a linktree page.
 *
 * The click endpoint is public, so only URLs actually rendered on the page are
 * accepted, and type/label come from here rather than from the request.
 *
 * @param int $page_id Linktree page ID.
 * @return array Normalized URL => array( type, label ). Empty if not a linktree page.
 */
function ane_get_linktree_trackable_links( $page_id ) {
	if ( ! $page_id || 'page-linktree.php' !== get_page_template_slug( $page_id ) || 'publish' !== get_post_status( $page_id ) ) {
		return array();
	}

	$links = array();

	// Custom links (same type mapping as js/linktree-tracking.js).
	$rows = get_field( 'ane_linktree', $page_id );
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( empty( $row['ane_url'] ) || empty( $row['ane_label'] ) ) {
				continue;
			}
			$type = isset( $row['ane_jenislink'] ) && in_array( $row['ane_jenislink'], array( 'whatsapp', 'website', 'tiktok', 'telegram' ), true ) ? $row['ane_jenislink'] : 'custom';

			$links[ esc_url_raw( $row['ane_url'] ) ] = array(
				'type'  => $type,
				'label' => sanitize_text_field( $row['ane_label'] ),
			);
		}
	}

	// Social links. Custom links take precedence when a URL appears in both.
	foreach ( ane_get_linktree_social_platforms() as $platform => $data ) {
		$url = esc_url_raw( (string) $data['url'] );
		if ( $url && ! isset( $links[ $url ] ) ) {
			$links[ $url ] = array(
				'type'  => $platform,
				'label' => $data['label'],
			);
		}
	}

	return $links;
}

/**
 * Basic per-IP rate limit for the public click endpoint.
 *
 * @return bool True if the request is within the limit.
 */
function ane_linktree_within_rate_limit() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( ! $ip ) {
		return true;
	}

	/**
	 * Filter the maximum tracked clicks per IP per 10 minutes.
	 *
	 * @param int $limit Default 60.
	 */
	$limit = (int) apply_filters( 'ane_linktree_rate_limit', 60 );
	$key   = 'ane_lt_rl_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= $limit ) {
		return false;
	}

	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	return true;
}

/**
 * AJAX handler to track linktree link clicks.
 */
function ane_track_linktree_click() {
	// Verify nonce
	check_ajax_referer( 'ane-linktree-track', 'nonce' );

	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	// Only accept links that exist on the linktree page.
	$page_id  = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;
	$link_url = isset( $_POST['link_url'] ) ? esc_url_raw( wp_unslash( $_POST['link_url'] ) ) : '';
	$links    = ane_get_linktree_trackable_links( $page_id );

	if ( ! $link_url || ! isset( $links[ $link_url ] ) ) {
		wp_send_json_error( array( 'message' => 'Unknown link' ), 400 );
	}

	if ( ! ane_linktree_within_rate_limit() ) {
		wp_send_json_error( array( 'message' => 'Too many requests' ), 429 );
	}

	$link_type  = $links[ $link_url ]['type'];
	$link_label = $links[ $link_url ]['label'];
	$referrer   = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	// Keep stored values within column sizes.
	$referrer   = substr( $referrer, 0, 500 );
	$user_agent = substr( $user_agent, 0, 500 );

	// Detect platform and device
	$referrer_platform = ane_detect_referrer_platform( $referrer );
	$device_type       = ane_detect_device_type( $user_agent );

	// Insert data
	$inserted = $wpdb->insert(
		$table_name,
		array(
			'link_type'         => $link_type,
			'link_label'        => $link_label,
			'link_url'          => $link_url,
			'clicked_at'        => current_time( 'mysql' ),
			'referrer'          => $referrer,
			'referrer_platform' => $referrer_platform,
			'device_type'       => $device_type,
			'user_agent'        => $user_agent,
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( $inserted ) {
		wp_send_json_success( array( 'message' => 'Click tracked successfully' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Failed to track click' ) );
	}
}
add_action( 'wp_ajax_ane_track_linktree_click', 'ane_track_linktree_click' );
add_action( 'wp_ajax_nopriv_ane_track_linktree_click', 'ane_track_linktree_click' );

/**
 * Enqueue linktree tracking script.
 */
function ane_enqueue_linktree_tracking_script() {
	// Only load on linktree pages
	if ( ! is_page_template( 'page-linktree.php' ) ) {
		return;
	}

	wp_enqueue_script(
		'ane-linktree-tracking',
		get_template_directory_uri() . '/js/linktree-tracking.js',
		array( 'jquery' ),
		'1.0.7',
		true
	);

	wp_localize_script(
		'ane-linktree-tracking',
		'aneLinktreeTracking',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'ane-linktree-track' ),
			'pageId'  => get_queried_object_id(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_linktree_tracking_script' );

/**
 * Delete old click records (data retention).
 *
 * Click rows store referrer and user agent, so they are not kept forever.
 */
function ane_cleanup_linktree_clicks() {
	global $wpdb;

	/**
	 * Filter how many months of linktree click data to keep.
	 *
	 * @param int $months Default 12. Return 0 to keep data forever.
	 */
	$months = (int) apply_filters( 'ane_linktree_retention_months', 12 );
	if ( $months <= 0 ) {
		return;
	}

	$table_name = $wpdb->prefix . 'ane_linktree_clicks';
	$cutoff     = gmdate( 'Y-m-d H:i:s', strtotime( "-{$months} months", current_time( 'timestamp' ) ) );

	$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE clicked_at < %s", $cutoff ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}
add_action( 'ane_linktree_cleanup', 'ane_cleanup_linktree_clicks' );

/**
 * Schedule the daily cleanup.
 */
function ane_schedule_linktree_cleanup() {
	if ( ! wp_next_scheduled( 'ane_linktree_cleanup' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'ane_linktree_cleanup' );
	}
}
add_action( 'init', 'ane_schedule_linktree_cleanup' );

/**
 * Unschedule the cleanup when switching away from the theme.
 */
function ane_unschedule_linktree_cleanup() {
	wp_clear_scheduled_hook( 'ane_linktree_cleanup' );
}
add_action( 'switch_theme', 'ane_unschedule_linktree_cleanup' );
