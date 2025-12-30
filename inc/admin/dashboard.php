<?php
/**
 * Custom Admin Dashboard for Elemen Ane Theme.
 *
 * Professional dashboard dengan statistik untuk news website:
 * - Total visitors (based on post views)
 * - Posts per month (curve untuk 1 tahun)
 * - Total posts, categories, tags
 * - Popular posts, recent comments
 * - Author performance
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom dashboard page to admin menu.
 */
function ane_add_dashboard_page() {
	add_menu_page(
		__( 'Dashboard', 'newsane' ),
		__( 'Dashboard', 'newsane' ),
		'read',
		'ane-dashboard',
		'ane_render_dashboard_page',
		'dashicons-dashboard',
		2
	);

	// Remove default dashboard menu.
	remove_menu_page( 'index.php' );
}
add_action( 'admin_menu', 'ane_add_dashboard_page' );

/**
 * Redirect default dashboard to custom dashboard.
 */
function ane_redirect_dashboard() {
	wp_safe_redirect( admin_url( 'admin.php?page=ane-dashboard' ) );
	exit;
}
add_action( 'load-index.php', 'ane_redirect_dashboard' );

/**
 * Enqueue dashboard assets.
 *
 * @param string $hook Current admin page hook.
 */
function ane_dashboard_assets( $hook ) {
	// Only load on our custom dashboard page.
	if ( 'toplevel_page_ane-dashboard' !== $hook ) {
		return;
	}

	// Admin CSS.
	wp_enqueue_style(
		'ane-admin',
		get_base_url_assets() . '/css/admin.min.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Chart.js for analytics.
	wp_enqueue_script(
		'ane-chartjs',
		'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
		array(),
		'4.4.0',
		true
	);

	// Dashboard JavaScript.
	wp_enqueue_script(
		'ane-admin-dashboard',
		get_base_url_assets() . '/js/admin-dashboard.js',
		array( 'ane-chartjs' ),
		wp_get_theme()->get( 'Version' ),
		true
	);

	// Localize data for Chart.js.
	$chart_data = ane_get_posts_per_month_data();
	wp_localize_script(
		'ane-admin-dashboard',
		'aneDashboard',
		array(
			'postsData' => $chart_data,
			'colors'    => array(
				'primary'   => '#dc3545',
				'secondary' => '#ff6b35',
				'accent'    => '#e0ddcf',
				'body'      => '#3c434a',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ane_dashboard_assets' );

/**
 * Render custom dashboard page.
 */
function ane_render_dashboard_page() {
	// No capability check needed here - already handled by add_menu_page()

	$stats = ane_get_dashboard_stats();
	?>
	<div class="wrap ane-custom-dashboard">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard', 'newsane' ); ?></h1>

		<div class="ane-dashboard-grid">
			<!-- Stats Cards -->
			<div class="ane-dashboard-section ane-dashboard-section--full">
				<?php ane_dashboard_stats_cards(); ?>
			</div>

			<!-- Chart -->
			<div class="ane-dashboard-section ane-dashboard-section--full">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Content Analytics', 'newsane' ); ?></h2>
					<div class="inside">
						<?php ane_dashboard_posts_chart(); ?>
					</div>
				</div>
			</div>

			<!-- 3 Column Grid -->
			<div class="ane-dashboard-section">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Popular Posts', 'newsane' ); ?></h2>
					<div class="inside">
						<?php ane_dashboard_popular_posts(); ?>
					</div>
				</div>
			</div>

			<div class="ane-dashboard-section">
				<div class="postbox">
					<div class="ane-section-header">
						<h2 class="hndle"><?php esc_html_e( 'Recent Posts', 'newsane' ); ?></h2>
						<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Create New Post', 'newsane' ); ?>
						</a>
					</div>
					<div class="inside">
						<?php ane_dashboard_recent_posts(); ?>
					</div>
				</div>
			</div>

			<div class="ane-dashboard-section">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Author Performance', 'newsane' ); ?></h2>
					<div class="inside">
						<?php ane_dashboard_top_authors(); ?>
					</div>
				</div>
			</div>

			<!-- Content Distribution (можно добавить или убрать) -->
			<div class="ane-dashboard-section ane-dashboard-section--full">
				<div class="postbox">
					<h2 class="hndle"><?php esc_html_e( 'Content Distribution', 'newsane' ); ?></h2>
					<div class="inside">
						<?php ane_dashboard_content_breakdown(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Stats cards widget.
 */
function ane_dashboard_stats_cards() {
	$stats = ane_get_dashboard_stats();
	?>
	<div class="ane-dashboard">
		<div class="ane-stats-grid">
			<!-- Total Visitors -->
			<div class="ane-stat-card ane-stat-card--primary">
				<div class="ane-stat-card__icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
						<circle cx="9" cy="7" r="4"></circle>
						<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
						<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
					</svg>
				</div>
				<div class="ane-stat-card__content">
					<div class="ane-stat-card__label"><?php esc_html_e( 'Total Visitors', 'newsane' ); ?></div>
					<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['total_views'] ) ); ?></div>
					<?php
					$growth_value = $stats['views_growth'];
					$growth_class = 'ane-stat-card__change';
					$growth_sign  = '';

					if ( $growth_value > 0 ) {
						$growth_class .= ' ane-stat-card__change--up';
						$growth_sign   = '+';
					} elseif ( $growth_value < 0 ) {
						$growth_class .= ' ane-stat-card__change--down';
						$growth_sign   = '';
					} else {
						$growth_class .= ' ane-stat-card__change--neutral';
						$growth_sign   = '+';
					}
					?>
					<div class="<?php echo esc_attr( $growth_class ); ?>">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: percentage with sign */
								__( '%s%s%% this month', 'newsane' ),
								$growth_sign,
								number_format_i18n( abs( $growth_value ), 1 )
							)
						);
						?>
					</div>
				</div>
			</div>

			<!-- Total Posts -->
			<div class="ane-stat-card ane-stat-card--secondary">
				<div class="ane-stat-card__icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
						<polyline points="14 2 14 8 20 8"></polyline>
						<line x1="16" y1="13" x2="8" y2="13"></line>
						<line x1="16" y1="17" x2="8" y2="17"></line>
						<polyline points="10 9 9 9 8 9"></polyline>
					</svg>
				</div>
				<div class="ane-stat-card__content">
					<div class="ane-stat-card__label"><?php esc_html_e( 'Total Articles', 'newsane' ); ?></div>
					<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['total_posts'] ) ); ?></div>
					<div class="ane-stat-card__change ane-stat-card__change--up">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of posts */
								__( '+%s this month', 'newsane' ),
								number_format_i18n( $stats['posts_this_month'] )
							)
						);
						?>
					</div>
				</div>
			</div>

			<!-- Total Comments -->
			<div class="ane-stat-card ane-stat-card--accent">
				<div class="ane-stat-card__icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
					</svg>
				</div>
				<div class="ane-stat-card__content">
					<div class="ane-stat-card__label"><?php esc_html_e( 'Total Comments', 'newsane' ); ?></div>
					<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['total_comments'] ) ); ?></div>
					<div class="ane-stat-card__change ane-stat-card__change--up">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of comments */
								__( '+%s this month', 'newsane' ),
								number_format_i18n( $stats['comments_this_month'] )
							)
						);
						?>
					</div>
				</div>
			</div>

			<!-- Active Authors -->
			<div class="ane-stat-card ane-stat-card--light">
				<div class="ane-stat-card__icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
						<circle cx="9" cy="7" r="4"></circle>
						<path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
						<path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
					</svg>
				</div>
				<div class="ane-stat-card__content">
					<div class="ane-stat-card__label"><?php esc_html_e( 'Active Authors', 'newsane' ); ?></div>
					<div class="ane-stat-card__value"><?php echo esc_html( number_format_i18n( $stats['active_authors'] ) ); ?></div>
					<div class="ane-stat-card__change">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of authors */
								__( '%s authors this month', 'newsane' ),
								number_format_i18n( $stats['authors_this_month'] )
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Posts per month chart widget.
 */
function ane_dashboard_posts_chart() {
	?>
	<div class="ane-dashboard">
		<div class="ane-chart-container">
			<canvas id="ane-posts-chart"></canvas>
		</div>
		<div class="ane-chart-legend">
			<div class="ane-chart-legend__item">
				<span class="ane-chart-legend__dot ane-chart-legend__dot--posts"></span>
				<span><?php esc_html_e( 'Published Posts', 'newsane' ); ?></span>
			</div>
			<div class="ane-chart-legend__item">
				<span class="ane-chart-legend__dot ane-chart-legend__dot--views"></span>
				<span><?php esc_html_e( 'Total Views', 'newsane' ); ?></span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Popular posts widget.
 */
function ane_dashboard_popular_posts() {
	$popular = ane_get_popular_posts_dashboard( 5 );

	if ( empty( $popular ) ) {
		echo '<p>' . esc_html__( 'No posts found.', 'newsane' ) . '</p>';
		return;
	}
	?>
	<div class="ane-dashboard">
		<ul class="ane-post-list">
			<?php foreach ( $popular as $post ) : ?>
				<li class="ane-post-list__item">
					<div class="ane-post-list__rank"><?php echo esc_html( $post['rank'] ); ?></div>
					<div class="ane-post-list__content">
						<a href="<?php echo esc_url( get_edit_post_link( $post['id'] ) ); ?>" class="ane-post-list__title">
							<?php echo esc_html( $post['title'] ); ?>
						</a>
						<div class="ane-post-list__meta">
							<span class="ane-post-list__views">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
									<circle cx="12" cy="12" r="3"></circle>
								</svg>
								<?php echo esc_html( number_format_i18n( $post['views'] ) ); ?>
							</span>
							<span class="ane-post-list__date">
								<?php echo esc_html( $post['date'] ); ?>
							</span>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Recent posts widget.
 */
function ane_dashboard_recent_posts() {
	$recent_posts = get_posts(
		array(
			'numberposts' => 5,
			'post_type'   => 'post',
			'post_status' => 'publish',
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);

	if ( empty( $recent_posts ) ) {
		echo '<p>' . esc_html__( 'No posts found.', 'newsane' ) . '</p>';
		return;
	}
	?>
	<div class="ane-dashboard">
		<ul class="ane-post-list">
			<?php
			$rank = 1;
			foreach ( $recent_posts as $post ) :
				$views = (int) get_post_meta( $post->ID, 'musi_views', true );
				?>
				<li class="ane-post-list__item">
					<div class="ane-post-list__rank"><?php echo esc_html( $rank++ ); ?></div>
					<div class="ane-post-list__content">
						<a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>" class="ane-post-list__title">
							<?php echo esc_html( $post->post_title ); ?>
						</a>
						<div class="ane-post-list__meta">
							<span class="ane-post-list__views">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
									<circle cx="12" cy="12" r="3"></circle>
								</svg>
								<?php echo esc_html( number_format_i18n( $views ) ); ?>
							</span>
							<span class="ane-post-list__date">
								<?php echo esc_html( human_time_diff( strtotime( $post->post_date ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'newsane' ); ?>
							</span>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Top authors widget.
 */
function ane_dashboard_top_authors() {
	$authors = ane_get_top_authors_dashboard( 5 );

	if ( empty( $authors ) ) {
		echo '<p>' . esc_html__( 'No authors found.', 'newsane' ) . '</p>';
		return;
	}
	?>
	<div class="ane-dashboard">
		<ul class="ane-author-list">
			<?php foreach ( $authors as $author ) : ?>
				<li class="ane-author-list__item">
					<div class="ane-author-list__avatar">
						<?php echo get_avatar( $author['id'], 40 ); ?>
					</div>
					<div class="ane-author-list__content">
						<a href="<?php echo esc_url( get_edit_user_link( $author['id'] ) ); ?>" class="ane-author-list__name">
							<?php echo esc_html( $author['name'] ); ?>
						</a>
						<div class="ane-author-list__stats">
							<span><?php echo esc_html( number_format_i18n( $author['posts'] ) ); ?> <?php esc_html_e( 'posts', 'newsane' ); ?></span>
							<span>•</span>
							<span><?php echo esc_html( number_format_i18n( $author['views'] ) ); ?> <?php esc_html_e( 'views', 'newsane' ); ?></span>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Content breakdown widget.
 */
function ane_dashboard_content_breakdown() {
	$breakdown = ane_get_content_breakdown();
	?>
	<div class="ane-dashboard">
		<div class="ane-breakdown-grid">
			<div class="ane-breakdown-item">
				<div class="ane-breakdown-item__label"><?php esc_html_e( 'Categories', 'newsane' ); ?></div>
				<div class="ane-breakdown-item__value"><?php echo esc_html( number_format_i18n( $breakdown['categories'] ) ); ?></div>
			</div>
			<div class="ane-breakdown-item">
				<div class="ane-breakdown-item__label"><?php esc_html_e( 'Tags', 'newsane' ); ?></div>
				<div class="ane-breakdown-item__value"><?php echo esc_html( number_format_i18n( $breakdown['tags'] ) ); ?></div>
			</div>
			<div class="ane-breakdown-item">
				<div class="ane-breakdown-item__label"><?php esc_html_e( 'Draft Posts', 'newsane' ); ?></div>
				<div class="ane-breakdown-item__value"><?php echo esc_html( number_format_i18n( $breakdown['drafts'] ) ); ?></div>
			</div>
			<div class="ane-breakdown-item">
				<div class="ane-breakdown-item__label"><?php esc_html_e( 'Scheduled', 'newsane' ); ?></div>
				<div class="ane-breakdown-item__value"><?php echo esc_html( number_format_i18n( $breakdown['scheduled'] ) ); ?></div>
			</div>
		</div>

		<div class="ane-breakdown-progress">
			<div class="ane-breakdown-progress__label">
				<?php esc_html_e( 'Content Goal This Month', 'newsane' ); ?>
				<span><?php echo esc_html( $breakdown['posts_this_month'] ); ?> / <?php echo esc_html( $breakdown['goal'] ); ?></span>
			</div>
			<div class="ane-breakdown-progress__bar">
				<div class="ane-breakdown-progress__fill" style="width: <?php echo esc_attr( $breakdown['goal_percentage'] ); ?>%;"></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get dashboard statistics.
 */
function ane_get_dashboard_stats() {
	global $wpdb;

	// Total views from all posts.
	$total_views = (int) $wpdb->get_var(
		"SELECT SUM(meta_value)
		FROM {$wpdb->postmeta}
		WHERE meta_key = 'musi_views'"
	);

	// Views this month.
	$views_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(pm.meta_value)
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = 'musi_views'
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
			AND MONTH(p.post_date) = %d
			AND YEAR(p.post_date) = %d",
			date( 'n' ),
			date( 'Y' )
		)
	);

	// Views last month.
	$views_last_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(pm.meta_value)
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = 'musi_views'
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
			AND MONTH(p.post_date) = %d
			AND YEAR(p.post_date) = %d",
			date( 'n', strtotime( '-1 month' ) ),
			date( 'Y', strtotime( '-1 month' ) )
		)
	);

	// Calculate growth percentage.
	$views_growth = 0;
	if ( $views_last_month > 0 ) {
		$views_growth = ( ( $views_this_month - $views_last_month ) / $views_last_month ) * 100;
	}

	// Total posts.
	$total_posts = (int) wp_count_posts()->publish;

	// Posts this month.
	$posts_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->posts}
			WHERE post_type = 'post'
			AND post_status = 'publish'
			AND MONTH(post_date) = %d
			AND YEAR(post_date) = %d",
			date( 'n' ),
			date( 'Y' )
		)
	);

	// Total comments.
	$total_comments = (int) wp_count_comments()->approved;

	// Comments this month.
	$comments_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->comments}
			WHERE comment_approved = '1'
			AND MONTH(comment_date) = %d
			AND YEAR(comment_date) = %d",
			date( 'n' ),
			date( 'Y' )
		)
	);

	// Active authors (published at least 1 post).
	$active_authors = (int) $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_author)
		FROM {$wpdb->posts}
		WHERE post_type = 'post'
		AND post_status = 'publish'"
	);

	// Authors this month.
	$authors_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT post_author)
			FROM {$wpdb->posts}
			WHERE post_type = 'post'
			AND post_status = 'publish'
			AND MONTH(post_date) = %d
			AND YEAR(post_date) = %d",
			date( 'n' ),
			date( 'Y' )
		)
	);

	return array(
		'total_views'         => $total_views,
		'views_growth'        => $views_growth,
		'total_posts'         => $total_posts,
		'posts_this_month'    => $posts_this_month,
		'total_comments'      => $total_comments,
		'comments_this_month' => $comments_this_month,
		'active_authors'      => $active_authors,
		'authors_this_month'  => $authors_this_month,
	);
}

/**
 * Get posts per month data (last 12 months).
 */
function ane_get_posts_per_month_data() {
	global $wpdb;

	$data = array(
		'labels' => array(),
		'posts'  => array(),
		'views'  => array(),
	);

	// Get data for last 12 months.
	for ( $i = 11; $i >= 0; $i-- ) {
		$date = strtotime( "-{$i} months" );
		$month = date( 'n', $date );
		$year = date( 'Y', $date );

		// Month label.
		$data['labels'][] = date_i18n( 'M Y', $date );

		// Posts count.
		$posts_count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->posts}
				WHERE post_type = 'post'
				AND post_status = 'publish'
				AND MONTH(post_date) = %d
				AND YEAR(post_date) = %d",
				$month,
				$year
			)
		);
		$data['posts'][] = $posts_count;

		// Views count.
		$views_count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT SUM(pm.meta_value)
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
				WHERE pm.meta_key = 'musi_views'
				AND p.post_type = 'post'
				AND p.post_status = 'publish'
				AND MONTH(p.post_date) = %d
				AND YEAR(p.post_date) = %d",
				$month,
				$year
			)
		);
		$data['views'][] = $views_count ? $views_count : 0;
	}

	return $data;
}

/**
 * Get popular posts for dashboard.
 */
function ane_get_popular_posts_dashboard( $limit = 5 ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_key'       => 'musi_views',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		)
	);

	$posts = array();
	$rank = 1;

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$posts[] = array(
				'id'    => get_the_ID(),
				'rank'  => $rank++,
				'title' => get_the_title(),
				'views' => (int) get_post_meta( get_the_ID(), 'musi_views', true ),
				'date'  => human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'newsane' ),
			);
		}
		wp_reset_postdata();
	}

	return $posts;
}

/**
 * Get top authors for dashboard.
 */
function ane_get_top_authors_dashboard( $limit = 5 ) {
	global $wpdb;

	// Get authors with post count and total views.
	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT
				p.post_author,
				COUNT(DISTINCT p.ID) as post_count,
				SUM(CAST(pm.meta_value AS UNSIGNED)) as total_views
			FROM {$wpdb->posts} p
			LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'musi_views'
			WHERE p.post_type = 'post'
			AND p.post_status = 'publish'
			GROUP BY p.post_author
			ORDER BY total_views DESC
			LIMIT %d",
			$limit
		)
	);

	$authors = array();

	foreach ( $results as $result ) {
		$user = get_userdata( $result->post_author );
		if ( $user ) {
			$authors[] = array(
				'id'    => $result->post_author,
				'name'  => $user->display_name,
				'posts' => (int) $result->post_count,
				'views' => (int) $result->total_views,
			);
		}
	}

	return $authors;
}

/**
 * Get content breakdown.
 */
function ane_get_content_breakdown() {
	global $wpdb;

	$categories = wp_count_terms( 'category' );
	$tags = wp_count_terms( 'post_tag' );

	$drafts = (int) wp_count_posts()->draft;
	$scheduled = (int) wp_count_posts()->future;

	// Posts this month.
	$posts_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->posts}
			WHERE post_type = 'post'
			AND post_status = 'publish'
			AND MONTH(post_date) = %d
			AND YEAR(post_date) = %d",
			date( 'n' ),
			date( 'Y' )
		)
	);

	// Goal: 30 posts per month (customize as needed).
	$goal = 30;
	$goal_percentage = min( 100, ( $posts_this_month / $goal ) * 100 );

	return array(
		'categories'       => $categories,
		'tags'             => $tags,
		'drafts'           => $drafts,
		'scheduled'        => $scheduled,
		'posts_this_month' => $posts_this_month,
		'goal'             => $goal,
		'goal_percentage'  => $goal_percentage,
	);
}

/**
 * Custom admin footer text.
 *
 * @return string Footer text with proper escaping.
 */
function ane_remove_footer_admin() {
	return sprintf(
		/* translators: %s: link to Webane Indonesia website */
		esc_html__( 'Designed with love by %s. Powered by WordPress.', 'newsane' ),
		'<a href="' . esc_url( 'https://webane.com/' ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Webane Indonesia', 'newsane' ) . '</a>'
	);
}
add_filter( 'admin_footer_text', 'ane_remove_footer_admin' );

/**
 * Get "Designed by" text for frontend footer.
 *
 * @return string HTML string with proper escaping for footer credit.
 */
function ane_load_designed_by() {
	return sprintf(
		/* translators: %s: link to Webane Indonesia website */
		esc_html__( 'Designed with love by %s', 'newsane' ),
		'<a href="' . esc_url( 'https://webane.com/' ) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr__( 'Web Design Webane Indonesia', 'newsane' ) . '">' . esc_html__( 'Webane Indonesia', 'newsane' ) . '</a>'
	);
}