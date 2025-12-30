<?php
/**
 * User profile page customization.
 *
 * @package elemenane
 */

/**
 * Enqueue user profile styles and scripts.
 */
function ane_user_profile_enqueue_scripts() : void {
	$screen = get_current_screen();

	if ( ! $screen || ( 'profile' !== $screen->id && 'user-edit' !== $screen->id ) ) {
		return;
	}

	// Enqueue admin CSS (contains user profile styles).
	wp_enqueue_style(
		'ane-admin',
		get_base_url_assets() . '/css/admin.min.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Enqueue Chart.js for performance chart.
	wp_enqueue_script(
		'ane-chartjs',
		'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
		array(),
		'4.4.0',
		true
	);

	// Enqueue user profile script.
	wp_enqueue_script(
		'ane-user-profile',
		get_base_url_assets() . '/js/admin-user.js',
		array( 'ane-chartjs' ),
		wp_get_theme()->get( 'Version' ),
		true
	);

	// Get user ID from page.
	$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : get_current_user_id();

	// Get user data.
	$chart_data = ane_get_user_posts_data( $user_id );

	// Localize script.
	wp_localize_script(
		'ane-user-profile',
		'aneUserProfile',
		array(
			'postsData' => $chart_data,
			'colors'    => array(
				'primary'   => '#2d232e',
				'secondary' => '#474448',
				'accent'    => '#73ab01',
				'light'     => '#f1f0ea',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ane_user_profile_enqueue_scripts' );

/**
 * Display user profile header card.
 *
 * @param WP_User $user User object.
 */
function ane_user_profile_header( WP_User $user ) : void {
	// Get user data.
	$avatar         = get_avatar( $user->ID, 120 );
	$display_name   = $user->display_name;
	$username       = $user->user_login;
	$email          = $user->user_email;
	$roles          = $user->roles;
	$role_name      = ! empty( $roles ) ? translate_user_role( ucfirst( $roles[0] ) ) : __( 'Subscriber', 'newsane' );
	$registered     = date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) );
	$bio            = get_user_meta( $user->ID, 'description', true );

	?>
	<div class="ane-user-profile__header">
		<div class="ane-user-profile__avatar">
			<?php echo wp_kses_post( $avatar ); ?>
		</div>
		<div class="ane-user-profile__info">
			<h2 class="ane-user-profile__name"><?php echo esc_html( $display_name ); ?></h2>
			<p class="ane-user-profile__username">@<?php echo esc_html( $username ); ?></p>
			<div class="ane-user-profile__meta">
				<span class="ane-user-profile__role">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
						<circle cx="12" cy="7" r="4"></circle>
					</svg>
					<?php echo esc_html( $role_name ); ?>
				</span>
				<span class="ane-user-profile__email">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
					<?php echo esc_html( $email ); ?>
				</span>
				<span class="ane-user-profile__registered">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<line x1="16" y1="2" x2="16" y2="6"></line>
						<line x1="8" y1="2" x2="8" y2="6"></line>
						<line x1="3" y1="10" x2="21" y2="10"></line>
					</svg>
					<?php
					/* translators: %s: Registration date */
					printf( esc_html__( 'Joined %s', 'newsane' ), esc_html( $registered ) );
					?>
				</span>
			</div>
			<?php if ( ! empty( $bio ) ) : ?>
				<p class="ane-user-profile__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Display user performance stats card.
 *
 * @param WP_User $user User object.
 */
function ane_user_profile_performance( WP_User $user ) : void {
	// Get user stats.
	$stats = ane_get_user_stats( $user->ID );

	?>
	<div class="ane-user-profile__performance">
		<h3><?php esc_html_e( 'Performance', 'newsane' ); ?></h3>

		<div class="ane-user-profile__stats-grid">
			<div class="ane-user-profile__stat">
				<div class="ane-user-profile__stat-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
						<polyline points="14,2 14,8 20,8"></polyline>
						<line x1="16" y1="13" x2="8" y2="13"></line>
						<line x1="16" y1="17" x2="8" y2="17"></line>
						<polyline points="10,9 9,9 8,9"></polyline>
					</svg>
				</div>
				<div class="ane-user-profile__stat-content">
					<div class="ane-user-profile__stat-value"><?php echo esc_html( number_format_i18n( $stats['total_posts'] ) ); ?></div>
					<div class="ane-user-profile__stat-label"><?php esc_html_e( 'Total Posts', 'newsane' ); ?></div>
				</div>
			</div>

			<div class="ane-user-profile__stat">
				<div class="ane-user-profile__stat-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
						<circle cx="12" cy="12" r="3"></circle>
					</svg>
				</div>
				<div class="ane-user-profile__stat-content">
					<div class="ane-user-profile__stat-value"><?php echo esc_html( number_format_i18n( $stats['total_views'] ) ); ?></div>
					<div class="ane-user-profile__stat-label"><?php esc_html_e( 'Total Views', 'newsane' ); ?></div>
				</div>
			</div>

			<div class="ane-user-profile__stat">
				<div class="ane-user-profile__stat-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
					</svg>
				</div>
				<div class="ane-user-profile__stat-content">
					<div class="ane-user-profile__stat-value"><?php echo esc_html( number_format_i18n( $stats['total_comments'] ) ); ?></div>
					<div class="ane-user-profile__stat-label"><?php esc_html_e( 'Total Comments', 'newsane' ); ?></div>
				</div>
			</div>

			<div class="ane-user-profile__stat">
				<div class="ane-user-profile__stat-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
					</svg>
				</div>
				<div class="ane-user-profile__stat-content">
					<div class="ane-user-profile__stat-value"><?php echo esc_html( number_format_i18n( $stats['avg_views'] ) ); ?></div>
					<div class="ane-user-profile__stat-label"><?php esc_html_e( 'Avg Views/Post', 'newsane' ); ?></div>
				</div>
			</div>

			<div class="ane-user-profile__stat">
				<div class="ane-user-profile__stat-icon">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
						<line x1="16" y1="2" x2="16" y2="6"></line>
						<line x1="8" y1="2" x2="8" y2="6"></line>
						<line x1="3" y1="10" x2="21" y2="10"></line>
					</svg>
				</div>
				<div class="ane-user-profile__stat-content">
					<div class="ane-user-profile__stat-value"><?php echo esc_html( number_format_i18n( $stats['posts_this_month'] ) ); ?></div>
					<div class="ane-user-profile__stat-label"><?php esc_html_e( 'Posts This Month', 'newsane' ); ?></div>
				</div>
			</div>
		</div>

		<div class="ane-user-profile__chart-container">
			<h4><?php esc_html_e( 'Posts Per Month (Last 12 Months)', 'newsane' ); ?></h4>
			<canvas id="ane-user-posts-chart"></canvas>
		</div>
	</div>
	<?php
}

/**
 * Display recent activity.
 *
 * @param WP_User $user User object.
 */
function ane_user_profile_recent_activity( WP_User $user ) : void {
	// Get recent posts.
	$recent_posts = get_posts(
		array(
			'author'         => $user->ID,
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	// Get recent comments.
	$recent_comments = get_comments(
		array(
			'user_id' => $user->ID,
			'number'  => 5,
			'status'  => 'approve',
			'orderby' => 'comment_date',
			'order'   => 'DESC',
		)
	);

	?>
	<div class="ane-user-profile__activity">
		<div class="ane-user-profile__activity-section">
			<h3><?php esc_html_e( 'Recent Posts', 'newsane' ); ?></h3>
			<?php if ( ! empty( $recent_posts ) ) : ?>
				<ul class="ane-user-profile__posts-list">
					<?php foreach ( $recent_posts as $post ) : ?>
						<li class="ane-user-profile__post-item">
							<div class="ane-user-profile__post-content">
								<a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>" class="ane-user-profile__post-title">
									<?php echo esc_html( $post->post_title ); ?>
								</a>
								<div class="ane-user-profile__post-meta">
									<span class="ane-user-profile__post-date">
										<?php echo esc_html( human_time_diff( strtotime( $post->post_date ), current_time( 'timestamp' ) ) ); ?>
										<?php esc_html_e( 'ago', 'newsane' ); ?>
									</span>
									<span class="ane-user-profile__post-views">
										<?php echo esc_html( ane_get_views( $post->ID ) ); ?>
										<?php esc_html_e( 'views', 'newsane' ); ?>
									</span>
									<span class="ane-user-profile__post-comments">
										<?php echo esc_html( get_comments_number( $post->ID ) ); ?>
										<?php esc_html_e( 'comments', 'newsane' ); ?>
									</span>
								</div>
							</div>
							<span class="ane-user-profile__post-status ane-user-profile__post-status--<?php echo esc_attr( $post->post_status ); ?>">
								<?php echo esc_html( ucfirst( $post->post_status ) ); ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="ane-user-profile__empty"><?php esc_html_e( 'No posts yet.', 'newsane' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="ane-user-profile__activity-section">
			<h3><?php esc_html_e( 'Recent Comments', 'newsane' ); ?></h3>
			<?php if ( ! empty( $recent_comments ) ) : ?>
				<ul class="ane-user-profile__comments-list">
					<?php foreach ( $recent_comments as $comment ) : ?>
						<li class="ane-user-profile__comment-item">
							<div class="ane-user-profile__comment-content">
								<p class="ane-user-profile__comment-text">
									<?php echo wp_kses_post( wp_trim_words( $comment->comment_content, 20 ) ); ?>
								</p>
								<div class="ane-user-profile__comment-meta">
									<span class="ane-user-profile__comment-post">
										<?php esc_html_e( 'On:', 'newsane' ); ?>
										<a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>">
											<?php echo esc_html( get_the_title( $comment->comment_post_ID ) ); ?>
										</a>
									</span>
									<span class="ane-user-profile__comment-date">
										<?php echo esc_html( human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) ) ); ?>
										<?php esc_html_e( 'ago', 'newsane' ); ?>
									</span>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="ane-user-profile__empty"><?php esc_html_e( 'No comments yet.', 'newsane' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Get user stats.
 *
 * @param int $user_id User ID.
 * @return array User stats.
 */
function ane_get_user_stats( int $user_id ) : array {
	global $wpdb;

	// Total posts.
	$total_posts = (int) count_user_posts( $user_id, 'post', true );

	// Total views.
	$total_views = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT SUM(pm.meta_value)
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = 'musi_views'
			AND p.post_author = %d
			AND p.post_type = 'post'
			AND p.post_status = 'publish'",
			$user_id
		)
	);

	// Average views per post.
	$avg_views = $total_posts > 0 ? (int) round( $total_views / $total_posts ) : 0;

	// Total comments.
	$total_comments = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->comments} c
			INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
			WHERE p.post_author = %d
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
			AND c.comment_approved = '1'",
			$user_id
		)
	);

	// Posts this month.
	$posts_this_month = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->posts}
			WHERE post_author = %d
			AND post_type = 'post'
			AND post_status = 'publish'
			AND MONTH(post_date) = %d
			AND YEAR(post_date) = %d",
			$user_id,
			date( 'n' ),
			date( 'Y' )
		)
	);

	return array(
		'total_posts'      => $total_posts,
		'total_views'      => $total_views,
		'avg_views'        => $avg_views,
		'total_comments'   => $total_comments,
		'posts_this_month' => $posts_this_month,
	);
}

/**
 * Get user posts per month data (last 12 months).
 *
 * @param int $user_id User ID.
 * @return array Posts per month data.
 */
function ane_get_user_posts_data( int $user_id ) : array {
	global $wpdb;

	$data = array(
		'labels' => array(),
		'posts'  => array(),
		'views'  => array(),
	);

	// Get data for last 12 months.
	for ( $i = 11; $i >= 0; $i-- ) {
		$date  = strtotime( "-{$i} months" );
		$month = date( 'n', $date );
		$year  = date( 'Y', $date );

		// Month label.
		$data['labels'][] = date_i18n( 'M Y', $date );

		// Posts count.
		$posts_count = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM {$wpdb->posts}
				WHERE post_author = %d
				AND post_type = 'post'
				AND post_status = 'publish'
				AND MONTH(post_date) = %d
				AND YEAR(post_date) = %d",
				$user_id,
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
				AND p.post_author = %d
				AND p.post_type = 'post'
				AND p.post_status = 'publish'
				AND MONTH(p.post_date) = %d
				AND YEAR(p.post_date) = %d",
				$user_id,
				$month,
				$year
			)
		);
		$data['views'][] = $views_count ? $views_count : 0;
	}

	return $data;
}

/**
 * Render complete user profile enhancement.
 *
 * @param WP_User $user User object.
 */
function ane_render_user_profile( WP_User $user ) : void {
	?>
	<div class="ane-user-profile">
		<h2 class="ane-user-profile__title"><?php esc_html_e( 'User Overview', 'newsane' ); ?></h2>

		<?php
		// Header section.
		ane_user_profile_header( $user );

		// Performance section.
		ane_user_profile_performance( $user );

		// Activity section.
		ane_user_profile_recent_activity( $user );
		?>
	</div>

	<?php
	// Styles now loaded from scss/_admin-user.scss
	// No inline styles needed
}


// Use personal_options hook to inject BEFORE default form fields.
add_action( 'personal_options', 'ane_render_user_profile', 1 );

/**
 * Close ane-user-profile wrapper after personal options.
 *
 * WordPress personal_options hook doesn't allow full wrapping,
 * so we need to inject closing div + h2 for default form.
 */
function ane_user_profile_close_wrapper() : void {
	?>
	<!-- End Ane User Profile -->
	<h2 style="margin-top: 2rem;"><?php esc_html_e( 'Personal Options', 'newsane' ); ?></h2>
	<?php
}
add_action( 'personal_options', 'ane_user_profile_close_wrapper', 999 );
