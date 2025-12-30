<?php
/**
 * Admin pages & styling for Elemen Ane options.
 *
 * @package elemenane
 */

/**
 * Return all Ane admin sections.
 */
function ane_get_admin_sections() : array {
	$sections = array(
		'ane-setup'          => array(
			'title'      => __( 'Elemen Ane', 'elemenane' ),
			'menu_title' => __( 'Elemen Ane', 'elemenane' ),
			'badge'      => __( 'Control Center', 'elemenane' ),
			'tagline'    => __( 'Main panel to configure identity, colors, and Elemen Ane theme utilities.', 'elemenane' ),
			'location'   => __( 'Use this page as a summary and shortcut to ACF sub-menus.', 'elemenane' ),
			'cards'      => array(
				array(
					'label'       => __( 'Workflow', 'elemenane' ),
					'title'       => __( 'Customer Care', 'elemenane' ),
					'description' => __( 'Manage editorial contact data, CS, WhatsApp, and help CTA.', 'elemenane' ),
					'link'        => admin_url( 'admin.php?page=ane-customer-care' ),
					'link_label'  => __( 'Open Customer Care', 'elemenane' ),
				),
				array(
					'label'       => __( 'SEO & News', 'elemenane' ),
					'title'       => __( 'SEO & News Setup', 'elemenane' ),
					'description' => __( 'Google News sitemap, AI crawler optimization, and news website SEO guide.', 'elemenane' ),
					'link'        => admin_url( 'admin.php?page=ane-seo-news' ),
					'link_label'  => __( 'Open SEO & News', 'elemenane' ),
				),
				array(
					'label'       => __( 'Theme Updates', 'elemenane' ),
					'title'       => __( 'Check for Updates', 'elemenane' ),
					'description' => __( 'Check GitHub for latest Elemen Ane theme version and update automatically.', 'elemenane' ),
					'link'        => add_query_arg( 'ane_force_check', '1', admin_url( 'themes.php' ) ),
					'link_label'  => __( 'Check Updates Now', 'elemenane' ),
				),
				array(
					'label'        => __( 'Elementor', 'elemenane' ),
					'title'        => __( 'Regenerate Elementor Kit', 'elemenane' ),
					'description'  => __( 'Import colors, typography, and experiments from site-settings.json to Elementor Kit.', 'elemenane' ),
					'link'         => wp_nonce_url( admin_url( 'admin.php?page=ane-setup&action=regenerate_elementor_kit' ), 'ane_regenerate_elementor_kit' ),
					'link_label'   => __( 'Regenerate Kit Now', 'elemenane' ),
					'link_2'       => admin_url( 'admin.php?page=elementor#tab-style' ),
					'link_2_label' => __( 'Elementor Setting', 'elemenane' ),
				),
			),
		),
		'ane-seo-news'       => array(
			'title'      => __( 'SEO & News Setup', 'elemenane' ),
			'menu_title' => __( 'SEO & News', 'elemenane' ),
			'badge'      => __( 'Google News Ready', 'elemenane' ),
			'tagline'    => __( 'Complete guide for Google News submission, AI crawler optimization, and news website SEO.', 'elemenane' ),
			'location'   => __( 'Enhance Yoast SEO Free with NewsArticle schema, Google News sitemap, and AI-friendly metadata.', 'elemenane' ),
		),
		'ane-general-setting' => array(
			'title'      => __( 'General Setting', 'elemenane' ),
			'menu_title' => __( 'General Setting', 'elemenane' ),
			'badge'      => __( 'Brand Identity', 'elemenane' ),
			'tagline'    => __( 'Configure brand identity, logo, tagline, and fallback content for hero blocks.', 'elemenane' ),
		),
		'ane-customer-care'    => array(
			'title'      => __( 'Customer Care', 'elemenane' ),
			'menu_title' => __( 'Customer Care', 'elemenane' ),
			'badge'      => __( 'Support Channel', 'elemenane' ),
			'tagline'    => __( 'All communication channels: editorial email, hotline, WhatsApp, and operating hours.', 'elemenane' ),
		),
	);

	return apply_filters( 'ane/admin/sections', $sections );
}

/**
 * Register options pages via ACF.
 */
function ane_register_acf_options_pages() : void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	$sections = ane_get_admin_sections();

	acf_add_options_page(
		array(
			'page_title' => $sections['ane-setup']['title'],
			'menu_title' => $sections['ane-setup']['menu_title'],
			'menu_slug'  => 'ane-setup',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 59,
			'redirect'   => false,
		)
	);

	$subpages = array(
		'ane-general-setting',
		'ane-customer-care',
	);

	foreach ( $subpages as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		acf_add_options_sub_page(
			array(
				'page_title'  => $sections[ $slug ]['title'],
				'menu_title'  => $sections[ $slug ]['menu_title'],
				'menu_slug'   => $slug,
				'parent_slug' => 'ane-setup',
				'capability'  => 'manage_options',
			)
		);
	}
}
add_action( 'acf/init', 'ane_register_acf_options_pages' );

/**
 * Register SEO & News submenu separately (uses custom render, not ACF).
 * Uses admin_menu with late priority to ensure parent exists.
 */
function ane_register_seo_news_page() {
	$sections = ane_get_admin_sections();

	if ( ! empty( $sections['ane-seo-news'] ) ) {
		add_submenu_page(
			'ane-setup',
			$sections['ane-seo-news']['title'],
			$sections['ane-seo-news']['menu_title'],
			'manage_options',
			'ane-seo-news',
			'ane_render_seo_news_page'
		);
	}
}
add_action( 'admin_menu', 'ane_register_seo_news_page', 999 );

/**
 * Register fallback menu when ACF Options is not available.
 */
function ane_register_admin_menu_fallback() : void {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	add_menu_page(
		__( 'Elemen Ane', 'elemenane' ),
		__( 'Elemen Ane', 'elemenane' ),
		'manage_options',
		'ane-setup',
		'ane_render_acf_missing_notice',
		'dashicons-admin-customizer',
		59
	);

	$sections = ane_get_admin_sections();
	// Note: ane-seo-news is registered separately via ane_register_seo_news_page().
	$slugs    = array( 'ane-general-setting', 'ane-customer-care' );

	foreach ( $slugs as $slug ) {
		if ( empty( $sections[ $slug ] ) ) {
			continue;
		}

		add_submenu_page(
			'ane-setup',
			$sections[ $slug ]['title'],
			$sections[ $slug ]['menu_title'],
			'manage_options',
			$slug,
			'ane_render_acf_missing_notice'
		);
	}
}
add_action( 'admin_menu', 'ane_register_admin_menu_fallback' );

/**
 * Render fallback notice.
 */
function ane_render_acf_missing_notice() : void {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Elemen Ane', 'elemenane' ) . '</h1>';
	echo '<p>' . esc_html__( 'Activate Advanced Custom Fields Pro to start using this options page.', 'elemenane' ) . '</p>';
	echo '</div>';
}

/**
 * Determine current Ane admin slug.
 */
function ane_get_current_admin_page_slug() : ?string {
	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return null;
	}

	$slug     = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sections = ane_get_admin_sections();

	return isset( $sections[ $slug ] ) ? $slug : null;
}

/**
 * Enqueue admin styles for ALL admin pages.
 *
 * Loads admin.min.css globally to ensure consistent styling across
 * all WordPress admin pages (dashboard, posts, media, users, etc).
 */
function ane_enqueue_admin_assets( string $hook ) : void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'ane-admin',
		get_base_url_assets() . '/css/admin.min.css',
		array(),
		$theme_version
	);
}
add_action( 'admin_enqueue_scripts', 'ane_enqueue_admin_assets' );

/**
 * Register custom meta boxes for Ane admin pages.
 */
function ane_register_admin_meta_boxes() : void {
	$sections = ane_get_admin_sections();

	foreach ( array_keys( $sections ) as $slug ) {
		$hook = ( 'ane-setup' === $slug ) ? 'toplevel_page_ane-setup' : 'ane_page_' . $slug;
		add_action( 'load-' . $hook, 'ane_prepare_admin_metaboxes' );
	}
}
add_action( 'admin_menu', 'ane_register_admin_meta_boxes', 20 );

/**
 * Prepare metaboxes for the current screen.
 */
function ane_prepare_admin_metaboxes() : void {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$slug = ane_get_current_admin_page_slug();

	/**
	 * Allow developers to register additional metaboxes.
	 *
	 * @param WP_Screen $screen Screen object.
	 * @param string    $slug   Current Ane admin slug.
	 */
	do_action( 'ane/options_page/register_metaboxes', $screen, $slug );
}

/**
 * Render hero + cards before ACF form.
 */
function ane_render_admin_intro() : void {
	$slug = ane_get_current_admin_page_slug();

	if ( ! $slug ) {
		return;
	}

	$sections = ane_get_admin_sections();
	$data     = $sections[ $slug ] ?? null;

	if ( ! $data ) {
		return;
	}

	echo '<div class="ane-admin wrap" id="ane-admin-' . esc_attr( $slug ) . '">';
	echo '<div class="ane-admin__hero">';
	echo '<div class="ane-admin__hero-content">';
	if ( ! empty( $data['badge'] ) ) {
		echo '<span class="ane-admin__badge">' . esc_html( $data['badge'] ) . '</span>';
	}
	echo '<h1>' . esc_html( $data['title'] ) . '</h1>';
	if ( ! empty( $data['tagline'] ) ) {
		echo '<p>' . esc_html( $data['tagline'] ) . '</p>';
	}
	echo '</div>';
	echo '</div>';

	if ( ! empty( $data['cards'] ) && is_array( $data['cards'] ) ) {
		echo '<div class="ane-admin__cards">';
		foreach ( $data['cards'] as $card ) {
			echo '<div class="ane-admin__card">';
			if ( ! empty( $card['label'] ) ) {
				echo '<span class="ane-admin__card-label">' . esc_html( $card['label'] ) . '</span>';
			}
			if ( ! empty( $card['title'] ) ) {
				echo '<h3>' . esc_html( $card['title'] ) . '</h3>';
			}
			if ( ! empty( $card['description'] ) ) {
				echo '<p>' . esc_html( $card['description'] ) . '</p>';
			}
			if ( ! empty( $card['items'] ) && is_array( $card['items'] ) ) {
				echo '<ul>';
				foreach ( $card['items'] as $item ) {
					echo '<li>' . esc_html( $item ) . '</li>';
				}
				echo '</ul>';
			}
			if ( ! empty( $card['link'] ) || ! empty( $card['link_2'] ) ) {
				echo '<div class="ane-admin__card-buttons">';

				if ( ! empty( $card['link'] ) ) {
					echo '<a class="ane-admin__cta" href="' . esc_url( $card['link'] ) . '">';
					echo '<span class="ane-admin__cta-text">' . esc_html( $card['link_label'] ?? __( 'Open page', 'elemenane' ) ) . '</span>';
					echo '<span class="ane-admin__cta-mobile">Open</span>';
					echo '<span class="ane-admin__cta-arrow" aria-hidden="true">→</span>';
					echo '</a>';
				}

				if ( ! empty( $card['link_2'] ) ) {
					echo '<a class="ane-admin__cta ane-admin__cta--secondary" href="' . esc_url( $card['link_2'] ) . '">';
					echo '<span class="ane-admin__cta-text">' . esc_html( $card['link_2_label'] ?? __( 'Open page', 'elemenane' ) ) . '</span>';
					echo '<span class="ane-admin__cta-mobile">Open</span>';
					echo '<span class="ane-admin__cta-arrow" aria-hidden="true">→</span>';
					echo '</a>';
				}

				echo '</div>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	// Render meta boxes registered via 'ane/options_page/register_metaboxes' filter.
	$screen = get_current_screen();
	if ( $screen ) {
		ob_start();
		echo '<div class="ane-admin__metaboxes">';
		do_meta_boxes( $screen, 'ane-admin', null );
		echo '</div>';
		$metabox_markup = trim( ob_get_clean() );
		if ( $metabox_markup ) {
			echo $metabox_markup;
		}
	}

	do_action( 'ane/options_page/after_intro', $slug );
	echo '</div>';
}
add_action( 'admin_notices', 'ane_render_admin_intro' );

/**
 * Get Google News Sitemap URL.
 *
 * @return string News sitemap URL.
 */
function ane_get_news_sitemap_url() : string {
	// Check if Yoast SEO is active and has news sitemap
	if ( defined( 'WPSEO_VERSION' ) && class_exists( 'WPSEO_News_Sitemap' ) ) {
		return home_url( '/news-sitemap.xml' );
	}

	// Fallback to standard WordPress sitemap for posts
	return home_url( '/wp-sitemap-posts-post-1.xml' );
}

/**
 * Render SEO & News Setup page content.
 */
function ane_render_seo_news_page() {

	$news_sitemap_url = ane_get_news_sitemap_url();
	$home_url         = home_url();
	$rss_feed_url     = get_feed_link();

	?>
	<style>
		.ane-seo-panel {
			background: white;
			border: 1px solid #ddd;
			border-radius: 4px;
			padding: 20px;
			margin: 20px 0;
			box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		}
		.ane-seo-panel h3 {
			margin-top: 0;
			border-bottom: 2px solid #2271b1;
			padding-bottom: 10px;
			color: #1d2327;
		}
		.ane-seo-checklist {
			list-style: none;
			padding-left: 0;
		}
		.ane-seo-checklist li {
			padding: 8px 0;
			padding-left: 30px;
			position: relative;
		}
		.ane-seo-checklist li:before {
			content: '✓';
			position: absolute;
			left: 0;
			color: #46b450;
			font-weight: bold;
			font-size: 18px;
		}
		.ane-seo-url-box {
			background: #f0f0f1;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			padding: 12px;
			font-family: monospace;
			font-size: 14px;
			word-break: break-all;
			margin: 10px 0;
		}
		.ane-seo-url-box code {
			color: #2271b1;
			font-weight: 600;
		}
		.ane-seo-warning {
			background: #fcf9e8;
			border-left: 4px solid #dba617;
			padding: 12px;
			margin: 15px 0;
		}
		.ane-seo-success {
			background: #e7f7e7;
			border-left: 4px solid #46b450;
			padding: 12px;
			margin: 15px 0;
		}
		.ane-seo-steps {
			counter-reset: step-counter;
			list-style: none;
			padding-left: 0;
		}
		.ane-seo-steps li {
			counter-increment: step-counter;
			padding: 15px 0;
			padding-left: 45px;
			position: relative;
			border-bottom: 1px solid #f0f0f1;
		}
		.ane-seo-steps li:before {
			content: counter(step-counter);
			position: absolute;
			left: 0;
			top: 15px;
			width: 30px;
			height: 30px;
			background: #2271b1;
			color: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
		}
		.ane-seo-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 20px;
			margin: 20px 0;
		}
	</style>

	<div class="wrap">
		<!-- Google News Sitemap -->
		<div class="ane-seo-panel">
			<h3>📰 Google News Sitemap</h3>
			<p><?php esc_html_e( 'Your Google News sitemap is general and includes posts from the last 2 days.', 'elemenane' ); ?></p>

			<div class="ane-seo-url-box">
				<strong><?php esc_html_e( 'News Sitemap URL:', 'elemenane' ); ?></strong><br>
				<code><?php echo esc_html( $news_sitemap_url ); ?></code>
			</div>

			<p>
				<a href="<?php echo esc_url( $news_sitemap_url ); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e( 'View News Sitemap', 'elemenane' ); ?>
				</a>
			</p>

			<div class="ane-seo-warning">
				<strong><?php esc_html_e( '⚠️ Important:', 'elemenane' ); ?></strong>
				<?php esc_html_e( 'Add this URL to Google Search Console under Sitemaps section.', 'elemenane' ); ?>
			</div>
		</div>

		<!-- Google News Publisher Center -->
		<div class="ane-seo-panel">
			<h3>🚀 Google News Publisher Center Submission</h3>
			<p><?php esc_html_e( 'Follow these steps to submit your news website to Google News:', 'elemenane' ); ?></p>

			<ol class="ane-seo-steps">
				<li>
					<strong><?php esc_html_e( 'Go to Google News Publisher Center', 'elemenane' ); ?></strong><br>
					<a href="https://publishercenter.google.com/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Publisher Center', 'elemenane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add Your Publication', 'elemenane' ); ?></strong><br>
					<?php esc_html_e( 'Click "Add publication" and enter your website URL.', 'elemenane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Verify Ownership', 'elemenane' ); ?></strong><br>
					<?php esc_html_e( 'Verify via Google Search Console (recommended) or HTML tag.', 'elemenane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Add News Sitemap', 'elemenane' ); ?></strong><br>
					<?php esc_html_e( 'In Google Search Console, go to Sitemaps and add:', 'elemenane' ); ?><br>
					<code><?php echo esc_html( $news_sitemap_url ); ?></code>
				</li>
				<li>
					<strong><?php esc_html_e( 'Complete Publication Details', 'elemenane' ); ?></strong><br>
					<?php esc_html_e( 'Fill in publication name, logo, contact info, and editorial team.', 'elemenane' ); ?>
				</li>
				<li>
					<strong><?php esc_html_e( 'Submit for Review', 'elemenane' ); ?></strong><br>
					<?php esc_html_e( 'Google will review your application (typically 1-2 weeks).', 'elemenane' ); ?>
				</li>
			</ol>

			<div class="ane-seo-success">
				<strong><?php esc_html_e( '✅ Requirements Met:', 'elemenane' ); ?></strong><br>
				<?php esc_html_e( 'Your theme includes NewsArticle schema, proper meta tags, and author attribution.', 'elemenane' ); ?>
			</div>
		</div>

		<!-- SEO Features Enabled -->
		<div class="ane-seo-panel">
			<h3>🎯 SEO Features Enabled</h3>
			<p><?php esc_html_e( 'Elemen Ane theme automatically includes these SEO enhancements:', 'elemenane' ); ?></p>

			<div class="ane-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Schema.org Markup', 'elemenane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'NewsArticle schema', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Breadcrumb schema', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Publisher & Author schema', 'elemenane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'AI-Friendly Metadata', 'elemenane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Dublin Core metadata', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Citation metadata', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Enhanced RSS feed', 'elemenane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'Open Graph & Twitter', 'elemenane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Facebook Open Graph', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Twitter Card tags', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Social sharing optimized', 'elemenane' ); ?></li>
					</ul>
				</div>

				<div>
					<h4><?php esc_html_e( 'News Optimization', 'elemenane' ); ?></h4>
					<ul class="ane-seo-checklist">
						<li><?php esc_html_e( 'Google News sitemap', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Freshness signals', 'elemenane' ); ?></li>
						<li><?php esc_html_e( 'Robots meta enhanced', 'elemenane' ); ?></li>
					</ul>
				</div>
			</div>
		</div>

		<!-- AI Crawler Optimization -->
		<div class="ane-seo-panel">
			<h3>🤖 AI Crawler Optimization</h3>
			<p><?php esc_html_e( 'Your content is optimized for AI models like ChatGPT, Claude, and Perplexity:', 'elemenane' ); ?></p>

			<ul class="ane-seo-checklist">
				<li><?php esc_html_e( 'Dublin Core metadata for academic/news citations', 'elemenane' ); ?></li>
				<li><?php esc_html_e( 'Citation metadata for proper attribution', 'elemenane' ); ?></li>
				<li><?php esc_html_e( 'Structured NewsArticle schema', 'elemenane' ); ?></li>
				<li><?php esc_html_e( 'Enhanced RSS feed with full content', 'elemenane' ); ?></li>
				<li><?php esc_html_e( 'Clear author attribution and bylines', 'elemenane' ); ?></li>
				<li><?php esc_html_e( 'Semantic HTML5 markup', 'elemenane' ); ?></li>
			</ul>

			<div class="ane-seo-url-box">
				<strong><?php esc_html_e( 'RSS Feed URL:', 'elemenane' ); ?></strong><br>
				<code><?php echo esc_html( $rss_feed_url ); ?></code>
			</div>
		</div>

		<!-- Testing & Validation -->
		<div class="ane-seo-panel">
			<h3>🧪 Testing & Validation Tools</h3>
			<p><?php esc_html_e( 'Use these tools to validate your SEO implementation:', 'elemenane' ); ?></p>

			<div class="ane-seo-grid">
				<div>
					<h4><?php esc_html_e( 'Facebook Debugger', 'elemenane' ); ?></h4>
					<p><?php esc_html_e( 'Test Open Graph tags', 'elemenane' ); ?></p>
					<a href="https://developers.facebook.com/tools/debug/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'elemenane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Twitter Card Validator', 'elemenane' ); ?></h4>
					<p><?php esc_html_e( 'Test Twitter Card meta', 'elemenane' ); ?></p>
					<a href="https://cards-dev.twitter.com/validator" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'elemenane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Schema Markup Validator', 'elemenane' ); ?></h4>
					<p><?php esc_html_e( 'Test structured data', 'elemenane' ); ?></p>
					<a href="https://validator.schema.org/" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'elemenane' ); ?>
					</a>
				</div>

				<div>
					<h4><?php esc_html_e( 'Google Rich Results Test', 'elemenane' ); ?></h4>
					<p><?php esc_html_e( 'Test rich snippets', 'elemenane' ); ?></p>
					<a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">
						<?php esc_html_e( 'Open Tool', 'elemenane' ); ?>
					</a>
				</div>
			</div>
		</div>

		<!-- Additional Resources -->
		<div class="ane-seo-panel">
			<h3>📚 Additional Resources</h3>
			<ul>
				<li>
					<strong><?php esc_html_e( 'Google News Guidelines:', 'elemenane' ); ?></strong>
					<a href="https://support.google.com/news/publisher-center/answer/9606710" target="_blank">
						<?php esc_html_e( 'View Guidelines', 'elemenane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Google Search Console:', 'elemenane' ); ?></strong>
					<a href="https://search.google.com/search-console" target="_blank">
						<?php esc_html_e( 'Open Console', 'elemenane' ); ?>
					</a>
				</li>
				<li>
					<strong><?php esc_html_e( 'Schema.org Documentation:', 'elemenane' ); ?></strong>
					<a href="https://schema.org/NewsArticle" target="_blank">
						<?php esc_html_e( 'NewsArticle Docs', 'elemenane' ); ?>
					</a>
				</li>
			</ul>
		</div>

		<!-- SEO Action Plan for Google Enhanced Results -->
		<div class="ane-seo-panel">
			<h3>🎯 SEO Action Plan - Optimasi untuk Hasil Pencarian Google yang Lebih Baik</h3>

			<div class="ane-seo-success">
				<strong>✅ Fitur SEO Premium Sudah Aktif!</strong>
				<p>Theme Anda sudah dilengkapi dengan structured data lengkap untuk meningkatkan visibilitas di Google.</p>
			</div>

			<h4>📊 Apa yang Sudah Otomatis Berjalan:</h4>
			<ul class="ane-seo-checklist">
				<li><strong>NewsArticle Schema</strong> - Setiap artikel memiliki rich snippets untuk Google News</li>
				<li><strong>Breadcrumb Schema</strong> - Google memahami struktur hierarki website Anda</li>
				<li><strong>WebSite Schema + SearchAction</strong> - Search box bisa muncul di hasil Google</li>
				<li><strong>Navigation Schema</strong> - Menu utama ditandai untuk Google Sitelinks</li>
				<li><strong>Dublin Core & Citation</strong> - Optimasi untuk AI crawlers (ChatGPT, Claude, Perplexity)</li>
				<li><strong>Open Graph & Twitter Cards</strong> - Preview cantik saat share di sosial media</li>
			</ul>

			<h4 style="margin-top: 30px;">📝 Action Plan Anda (Checklist 30 Hari):</h4>

			<ol class="ane-seo-steps">
				<li>
					<strong>Hari 1-3: Setup Google Search Console</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Daftar website di <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
						<li>Verifikasi kepemilikan website (gunakan meta tag atau DNS)</li>
						<li>Submit sitemap.xml: <code><?php echo esc_url( $news_sitemap_url ); ?></code></li>
						<li>Request indexing untuk 5-10 halaman penting</li>
					</ul>
				</li>

				<li>
					<strong>Hari 4-7: Validasi Structured Data</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Buka <a href="https://search.google.com/test/rich-results" target="_blank">Rich Results Test</a></li>
						<li>Test homepage Anda - pastikan WebSite schema terdeteksi</li>
						<li>Test 2-3 artikel - pastikan NewsArticle schema terdeteksi</li>
						<li>Test halaman category - pastikan CollectionPage schema terdeteksi</li>
						<li>Screenshot hasil test untuk dokumentasi</li>
					</ul>
				</li>

				<li>
					<strong>Hari 8-14: Optimasi Struktur Website</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Buat 5-8 kategori utama yang fokus (jangan terlalu banyak)</li>
						<li>Setiap kategori minimal punya 10-15 artikel berkualitas</li>
						<li>Setup menu utama (<strong>Appearance → Menus</strong>) dengan kategori penting</li>
						<li>Pastikan menu di-assign ke location <strong>"menuutama"</strong></li>
						<li>Buat halaman penting: About, Contact, Privacy Policy</li>
					</ul>
				</li>

				<li>
					<strong>Hari 15-21: Internal Linking Strategy</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Di setiap artikel baru, link ke 2-3 artikel terkait yang sudah ada</li>
						<li>Update artikel lama: tambahkan link ke artikel baru yang relevan</li>
						<li>Link dari homepage ke kategori/halaman penting</li>
						<li>Gunakan anchor text yang natural dan deskriptif</li>
						<li>Hindari link berlebihan (3-5 internal links per artikel sudah cukup)</li>
					</ul>
				</li>

				<li>
					<strong>Hari 22-28: Content Quality & Consistency</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Publish artikel minimal 2-3x per minggu (consistency is key!)</li>
						<li>Setiap artikel minimal 300-500 kata (lebih panjang lebih baik)</li>
						<li>Gunakan heading (H2, H3) untuk struktur artikel</li>
						<li>Selalu upload featured image berkualitas (min 1200x675px)</li>
						<li>Tulis excerpt/ringkasan untuk setiap artikel</li>
						<li>Pilih 1 kategori utama per artikel (jangan multi-kategori)</li>
					</ul>
				</li>

				<li>
					<strong>Hari 29-30: Monitor & Optimize</strong>
					<ul style="margin-top: 8px; margin-left: 20px;">
						<li>Cek Google Search Console → Performance → lihat impressions & clicks</li>
						<li>Identifikasi artikel dengan impressions tinggi tapi clicks rendah</li>
						<li>Improve title & meta description artikel tersebut (make it clickable!)</li>
						<li>Cek Coverage → fix halaman yang error atau excluded</li>
						<li>Monitor Core Web Vitals → pastikan website loading cepat</li>
					</ul>
				</li>
			</ol>

			<h4 style="margin-top: 30px;">🎁 Bonus Tips - Mempercepat Google Sitelinks:</h4>
			<div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 15px 0;">
				<p><strong>Google Sitelinks</strong> adalah sub-link yang muncul di bawah hasil pencarian website Anda (seperti menu mini). Sitelinks muncul otomatis ketika:</p>
				<ul style="margin-left: 20px;">
					<li>Website punya traffic organik yang stabil</li>
					<li>Struktur navigasi jelas dan konsisten</li>
					<li>User sering search brand name Anda di Google</li>
					<li>Website punya authority (backlinks, umur domain, trust)</li>
				</ul>
				<p><strong>Timeline realistis:</strong> 2-6 bulan setelah optimasi di atas (tergantung kompetisi niche Anda)</p>
				<p><strong>Cara mempercepat:</strong></p>
				<ul style="margin-left: 20px;">
					<li>Brand building (social media, PR, backlinks berkualitas)</li>
					<li>Konsisten publish konten berkualitas</li>
					<li>Encourage user search brand name Anda (bukan generic keywords)</li>
					<li>Pastikan bounce rate rendah (konten engaging, loading cepat)</li>
				</ul>
			</div>

			<h4 style="margin-top: 30px;">🔍 Tools untuk Monitor SEO:</h4>
			<div class="ane-seo-grid">
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>📊 Google Search Console</strong>
					<p>Monitor traffic, indexing, dan performance</p>
					<a href="https://search.google.com/search-console" target="_blank" class="button button-secondary">Open Console →</a>
				</div>
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>✅ Rich Results Test</strong>
					<p>Validasi structured data Anda</p>
					<a href="https://search.google.com/test/rich-results" target="_blank" class="button button-secondary">Test Now →</a>
				</div>
				<div style="background: white; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
					<strong>⚡ PageSpeed Insights</strong>
					<p>Cek kecepatan loading website</p>
					<a href="https://pagespeed.web.dev/" target="_blank" class="button button-secondary">Check Speed →</a>
				</div>
			</div>

			<div class="ane-seo-warning" style="margin-top: 20px;">
				<strong>⚠️ Penting untuk Diingat:</strong>
				<ul style="margin: 10px 0 0 20px;">
					<li>SEO adalah marathon, bukan sprint (butuh waktu 2-6 bulan untuk hasil signifikan)</li>
					<li>Google Sitelinks <strong>TIDAK bisa dipaksa</strong> - Google yang memutuskan berdasarkan site authority</li>
					<li>Focus on quality content & user experience - ranking akan follow naturally</li>
					<li>Jangan gunakan black-hat SEO (keyword stuffing, paid links, cloaking) - bisa kena penalty!</li>
					<li>Monitor terus Google Search Console untuk early detection masalah indexing</li>
				</ul>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Inject admin bar logo dynamically for mobile.
 *
 * Uses WordPress custom logo if set, otherwise fallback to theme logo.
 * Injects inline CSS to replace hardcoded "elemenane" text with logo image.
 *
 * @since 1.0.0
 */
function ane_admin_bar_logo() : void {
	// Get custom logo ID from theme customizer.
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		// Use WordPress custom logo if set.
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	} else {
		// Fallback to theme logo.
		$logo_url = get_template_directory_uri() . '/img/logo-elemenane.svg';
	}

	?>
	<style>
		@media screen and (max-width: 782px) {
			#wpadminbar #wp-admin-bar-root-default::after {
				background-image: url('<?php echo esc_url( $logo_url ); ?>') !important;
			}
		}
	</style>
	<?php
}
add_action( 'admin_head', 'ane_admin_bar_logo' );
add_action( 'wp_head', 'ane_admin_bar_logo' );

/**
 * Handle Elementor Kit regeneration from admin button.
 *
 * @since 1.0.0
 */
function ane_handle_regenerate_elementor_kit() : void {
	// Check if action is triggered.
	if ( empty( $_GET['action'] ) || 'regenerate_elementor_kit' !== $_GET['action'] ) {
		return;
	}

	// Check if we're on the right page.
	if ( empty( $_GET['page'] ) || 'ane-setup' !== $_GET['page'] ) {
		return;
	}

	// Verify nonce.
	if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ane_regenerate_elementor_kit' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'elemenane' ) );
	}

	// Check user capability.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'elemenane' ) );
	}

	// Check if Elementor is active.
	if ( ! did_action( 'elementor/loaded' ) ) {
		wp_die( esc_html__( 'Elementor is not active. Please activate Elementor first.', 'elemenane' ) );
	}

	// Import Elementor Kit from JSON.
	if ( function_exists( 'ane_import_elementor_kit' ) ) {
		ane_import_elementor_kit();

		// Redirect with success message.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                   => 'ane-setup',
					'elementor_kit_imported' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	wp_die( esc_html__( 'Elementor Kit import function not found.', 'elemenane' ) );
}
add_action( 'admin_init', 'ane_handle_regenerate_elementor_kit' );

/**
 * Display success notice after Elementor Kit regeneration.
 *
 * @since 1.0.0
 */
function ane_elementor_kit_success_notice() : void {
	if ( empty( $_GET['elementor_kit_imported'] ) || '1' !== $_GET['elementor_kit_imported'] ) {
		return;
	}

	if ( empty( $_GET['page'] ) || 'ane-setup' !== $_GET['page'] ) {
		return;
	}

	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Success!', 'elemenane' ); ?></strong>
			<?php esc_html_e( 'Elementor Kit has been regenerated from site-settings.json. All colors, typography, and experiments have been imported.', 'elemenane' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ane_elementor_kit_success_notice' );
