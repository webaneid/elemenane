<?php
/**
 * Linktree Analytics Admin Page
 *
 * @package elemenane
 * @since 1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Linktree Analytics submenu.
 * Uses late priority (999) to ensure parent menu exists.
 */
function ane_register_linktree_analytics_menu() {
	add_submenu_page(
		'ane-setup',
		__( 'Linktree Analytics', 'newsane' ),
		__( 'Linktree Analytics', 'newsane' ),
		'manage_options',
		'ane-linktree-analytics',
		'ane_render_linktree_analytics_page'
	);
}
add_action( 'admin_menu', 'ane_register_linktree_analytics_menu', 999 );

/**
 * Enqueue analytics dashboard assets.
 */
function ane_linktree_analytics_assets( $hook ) {
	if ( 'elemen-ane_page_ane-linktree-analytics' !== $hook ) {
		return;
	}

	// Enqueue Chart.js
	wp_enqueue_script(
		'chart-js',
		'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
		array(),
		'4.4.0',
		true
	);

	// Enqueue admin dashboard styles (reuse existing)
	wp_enqueue_style(
		'ane-admin-dashboard',
		get_template_directory_uri() . '/css/admin.min.css',
		array(),
		'1.0.6'
	);

	// Enqueue analytics script
	wp_enqueue_script(
		'ane-linktree-analytics',
		get_template_directory_uri() . '/js/admin-linktree-analytics.js',
		array( 'jquery', 'chart-js' ),
		'1.0.6',
		true
	);

	// Localize data
	$analytics_data = ane_get_linktree_analytics_data();
	$device_stats   = ane_get_device_breakdown();

	// Prepare device data for chart
	$device_data = array(
		'labels' => array(),
		'values' => array(),
	);

	foreach ( $device_stats as $device ) {
		$device_data['labels'][] = ucfirst( $device->device_type );
		$device_data['values'][] = (int) $device->clicks;
	}

	wp_localize_script(
		'ane-linktree-analytics',
		'aneLinktreeData',
		array(
			'clicksData'  => $analytics_data,
			'deviceData'  => $device_data,
			'colors'      => array(
				'primary'   => '#dc3545',
				'secondary' => '#ff6b35',
				'accent'    => '#e0ddcf',
				'body'      => '#3c434a',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ane_linktree_analytics_assets' );

/**
 * Render Linktree Analytics dashboard page.
 */
function ane_render_linktree_analytics_page() {
	$stats           = ane_get_linktree_stats();
	$top_links       = ane_get_top_linktree_links( 5 );
	$top_referrers   = ane_get_top_referrers( 5 );
	$device_stats    = ane_get_device_breakdown();
	?>
	<div class="wrap ane-custom-dashboard">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Linktree Analytics', 'newsane' ); ?></h1>

		<div class="ane-dashboard-grid">
			<!-- Stats Cards -->
			<div class="ane-dashboard-section ane-dashboard-section--full">
				<div class="ane-dashboard">
					<div class="ane-stats-grid">

				<!-- Total Clicks -->
				<div class="ane-stat-card">
					<div class="ane-stat-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
							<path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
						</svg>
					</div>
					<div class="ane-stat-card__content">
						<div class="ane-stat-card__label"><?php esc_html_e( 'Total Clicks', 'newsane' ); ?></div>
						<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['total_clicks'] ) ); ?></div>
					</div>
				</div>

				<!-- Clicks This Month -->
				<div class="ane-stat-card">
					<div class="ane-stat-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
							<line x1="16" y1="2" x2="16" y2="6"></line>
							<line x1="8" y1="2" x2="8" y2="6"></line>
							<line x1="3" y1="10" x2="21" y2="10"></line>
						</svg>
					</div>
					<div class="ane-stat-card__content">
						<div class="ane-stat-card__label"><?php esc_html_e( 'Clicks This Month', 'newsane' ); ?></div>
						<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['clicks_this_month'] ) ); ?></div>
					</div>
				</div>

				<!-- Unique Links Clicked -->
				<div class="ane-stat-card">
					<div class="ane-stat-card__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
						</svg>
					</div>
					<div class="ane-stat-card__content">
						<div class="ane-stat-card__label"><?php esc_html_e( 'Unique Links', 'newsane' ); ?></div>
						<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['unique_links'] ) ); ?></div>
					</div>
				</div>

					</div>
				</div>
			</div>

			<!-- Clicks Chart - Full Width -->
			<div class="ane-dashboard-section ane-dashboard-section--full">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Clicks Last 12 Months', 'newsane' ); ?></h2>
					<div class="inside">
						<div style="position: relative; height: 300px;">
							<canvas id="ane-linktree-clicks-chart"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="ane-dashboard-section">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Device Breakdown', 'newsane' ); ?></h2>
					<div class="inside">
						<div style="position: relative; height: 300px;">
							<canvas id="ane-device-chart"></canvas>
						</div>
					</div>
				</div>
			</div>

			<!-- 3 Column Grid: Device Breakdown, Top Links, Top Traffic Sources -->
			<div class="ane-dashboard-section">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Top Links', 'newsane' ); ?></h2>
					<div class="inside">
					<?php if ( ! empty( $top_links ) ) : ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Link', 'newsane' ); ?></th>
									<th><?php esc_html_e( 'Type', 'newsane' ); ?></th>
									<th><?php esc_html_e( 'Clicks', 'newsane' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $top_links as $link ) : ?>
									<tr>
										<td><strong><?php echo esc_html( $link->link_label ); ?></strong></td>
										<td><span class="ane-badge"><?php echo esc_html( ucfirst( $link->link_type ) ); ?></span></td>
										<td><?php echo esc_html( number_format_i18n( $link->clicks ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p><?php esc_html_e( 'No data yet.', 'newsane' ); ?></p>
					<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Top Referrers -->
			<div class="ane-dashboard-section">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Top Traffic Sources', 'newsane' ); ?></h2>
					<div class="inside">
					<?php if ( ! empty( $top_referrers ) ) : ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Platform', 'newsane' ); ?></th>
									<th><?php esc_html_e( 'Clicks', 'newsane' ); ?></th>
									<th><?php esc_html_e( 'Percentage', 'newsane' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $top_referrers as $referrer ) : ?>
									<tr>
										<td><strong><?php echo esc_html( $referrer->platform ); ?></strong></td>
										<td><?php echo esc_html( number_format_i18n( $referrer->clicks ) ); ?></td>
										<td><?php echo esc_html( number_format( $referrer->percentage, 1 ) ); ?>%</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p><?php esc_html_e( 'No data yet.', 'newsane' ); ?></p>
					<?php endif; ?>
					</div>
				</div>
			</div>

		</div>
	</div>
	<?php
}

/**
 * Get linktree statistics.
 *
 * @return array Statistics data.
 */
function ane_get_linktree_stats() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	// Total clicks
	$total_clicks = (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM $table_name"
	);

	// Clicks this month
	$clicks_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name
			WHERE MONTH(clicked_at) = %d
			AND YEAR(clicked_at) = %d",
			gmdate( 'n' ),
			gmdate( 'Y' )
		)
	);

	// Unique links clicked
	$unique_links = (int) $wpdb->get_var(
		"SELECT COUNT(DISTINCT link_url) FROM $table_name"
	);

	return array(
		'total_clicks'      => $total_clicks,
		'clicks_this_month' => $clicks_this_month,
		'unique_links'      => $unique_links,
	);
}

/**
 * Get top clicked links.
 *
 * @param int $limit Number of links to return.
 * @return array Top links data.
 */
function ane_get_top_linktree_links( $limit = 5 ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT link_label, link_type, COUNT(*) as clicks
			FROM $table_name
			GROUP BY link_url, link_label, link_type
			ORDER BY clicks DESC
			LIMIT %d",
			$limit
		)
	);

	return $results;
}

/**
 * Get top referrers/traffic sources.
 *
 * @param int $limit Number of referrers to return.
 * @return array Top referrers data.
 */
function ane_get_top_referrers( $limit = 5 ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	// Get total clicks for percentage calculation
	$total_clicks = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT referrer_platform as platform, COUNT(*) as clicks
			FROM $table_name
			WHERE referrer_platform IS NOT NULL
			GROUP BY referrer_platform
			ORDER BY clicks DESC
			LIMIT %d",
			$limit
		)
	);

	// Calculate percentage
	if ( $total_clicks > 0 ) {
		foreach ( $results as $result ) {
			$result->percentage = ( $result->clicks / $total_clicks ) * 100;
		}
	}

	return $results;
}

/**
 * Get device breakdown statistics.
 *
 * @return array Device stats.
 */
function ane_get_device_breakdown() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	$results = $wpdb->get_results(
		"SELECT device_type, COUNT(*) as clicks
		FROM $table_name
		WHERE device_type IS NOT NULL
		GROUP BY device_type
		ORDER BY clicks DESC"
	);

	return $results;
}

/**
 * Get clicks per month data for chart (last 12 months).
 *
 * @return array Chart data with labels and values.
 */
function ane_get_linktree_analytics_data() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ane_linktree_clicks';

	$data = array(
		'labels' => array(),
		'clicks' => array(),
	);

	// Get last 12 months
	for ( $i = 11; $i >= 0; $i-- ) {
		$date = strtotime( "-$i months" );
		$year = gmdate( 'Y', $date );
		$month = gmdate( 'n', $date );
		$label = gmdate( 'M Y', $date );

		$clicks = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $table_name
				WHERE MONTH(clicked_at) = %d
				AND YEAR(clicked_at) = %d",
				$month,
				$year
			)
		);

		$data['labels'][] = $label;
		$data['clicks'][] = $clicks;
	}

	return $data;
}
