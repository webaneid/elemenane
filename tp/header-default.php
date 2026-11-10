<?php
/**
 * Default Site Header.
 *
 * One <nav> serves every screen size: a horizontal bar on desktop and an
 * off-canvas drawer below 992px (behaviour in js/header.js, styles in
 * scss/_header.scss, classes .ane-header__*).
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

// Logo: custom logo, then the legacy `logo` theme mod, then the bundled image.
$elemenane_logo_url = (string) get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-elemenane.png' );

// Latest posts feed both the utility bar (first one) and the search overlay.
$elemenane_latest = get_posts(
	array(
		'numberposts'         => 4,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	)
);

// Call to action: ACF link field, falling back to the old default.
$elemenane_cta       = get_field( 'ane_header_cta_link', 'option' );
$elemenane_cta_url   = '#contact';
$elemenane_cta_text  = __( 'Get In Touch', 'elemenane' );
$elemenane_cta_blank = false;
if ( is_array( $elemenane_cta ) && ! empty( $elemenane_cta['url'] ) ) {
	$elemenane_cta_url   = (string) $elemenane_cta['url'];
	$elemenane_cta_text  = ! empty( $elemenane_cta['title'] ) ? (string) $elemenane_cta['title'] : $elemenane_cta_text;
	$elemenane_cta_blank = ! empty( $elemenane_cta['target'] ) && '_blank' === $elemenane_cta['target'];
}

$elemenane_phone       = (string) get_field( 'ane_telepon', 'option' );
$elemenane_phone_label = (string) get_field( 'ane_telepon_label', 'option' );
if ( '' === $elemenane_phone_label ) {
	$elemenane_phone_label = __( 'Sales Team', 'elemenane' );
}
$elemenane_phone_href = 'tel:' . preg_replace( '/[^0-9+]/', '', $elemenane_phone );

$elemenane_product_archive = get_field( 'ane_header_show_product', 'option' ) ? get_post_type_archive_link( 'product' ) : '';

$elemenane_has_utility = ! empty( $elemenane_latest ) || '' !== $elemenane_phone;
?>
<header class="site-header ane-header<?php echo $elemenane_has_utility ? ' ane-header--utility' : ''; ?>" id="ane-header" data-ane-header>

	<?php if ( $elemenane_has_utility ) : ?>
		<div class="ane-header__utility">
			<div class="ane-header__inner ane-header__utility-inner">
				<?php if ( ! empty( $elemenane_latest ) ) : ?>
					<p class="ane-header__latest">
						<span class="ane-header__latest-badge"><?php esc_html_e( 'Latest', 'elemenane' ); ?></span>
						<a href="<?php echo esc_url( get_permalink( $elemenane_latest[0] ) ); ?>"><?php echo esc_html( get_the_title( $elemenane_latest[0] ) ); ?></a>
					</p>
				<?php endif; ?>
				<?php if ( '' !== $elemenane_phone ) : ?>
					<a class="ane-header__tel" href="<?php echo esc_url( $elemenane_phone_href, array( 'tel' ) ); ?>">
						<?php elemenane_icon( 'telepon' ); ?>
						<span><?php echo esc_html( $elemenane_phone_label ); ?>:</span>
						<strong><?php echo esc_html( $elemenane_phone ); ?></strong>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="ane-header__main">
		<div class="ane-header__inner ane-header__bar">

			<div class="ane-header__brand">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					printf(
						'<a class="ane-header__logo" href="%1$s" rel="home"><img src="%2$s" alt="%3$s"></a>',
						esc_url( home_url( '/' ) ),
						esc_url( $elemenane_logo_url ),
						esc_attr( get_bloginfo( 'name' ) )
					);
				}
				?>
			</div>

			<div class="ane-header__panel" id="ane-header-panel">
				<div class="ane-header__panel-head">
					<span class="ane-header__panel-title"><?php esc_html_e( 'Menu', 'elemenane' ); ?></span>
					<button type="button" class="ane-header__close" data-ane-menu-close aria-label="<?php esc_attr_e( 'Close menu', 'elemenane' ); ?>">
						<?php elemenane_icon( 'tutup' ); ?>
					</button>
				</div>

				<form role="search" method="get" class="ane-header__drawer-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="ane-drawer-search"><?php esc_html_e( 'Search', 'elemenane' ); ?></label>
					<input type="search" id="ane-drawer-search" name="s" placeholder="<?php esc_attr_e( 'Search…', 'elemenane' ); ?>" autocomplete="off">
					<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'elemenane' ); ?>"><?php elemenane_icon( 'search' ); ?></button>
				</form>

				<nav class="ane-header__nav" aria-label="<?php esc_attr_e( 'Primary menu', 'elemenane' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menuutama',
							'container'      => '',
							'items_wrap'     => '<ul class="ane-header__menu">%3$s</ul>',
							'fallback_cb'    => 'elemenane_fallback_menu',
							'depth'          => 3,
						)
					);
					?>
				</nav>

				<?php if ( has_nav_menu( 'mobilemenu' ) ) : ?>
					<nav class="ane-header__quick" aria-label="<?php esc_attr_e( 'Quick links', 'elemenane' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'mobilemenu',
								'container'      => '',
								'items_wrap'     => '<ul class="ane-header__quick-menu">%3$s</ul>',
								'fallback_cb'    => false,
								'depth'          => 1,
								'ane_no_ids'     => true,
							)
						);
						?>
					</nav>
				<?php endif; ?>

				<div class="ane-header__panel-foot">
					<?php if ( '' !== $elemenane_phone ) : ?>
						<a class="ane-header__panel-tel" href="<?php echo esc_url( $elemenane_phone_href, array( 'tel' ) ); ?>">
							<?php elemenane_icon( 'telepon' ); ?>
							<span><?php echo esc_html( $elemenane_phone_label ); ?></span>
							<strong><?php echo esc_html( $elemenane_phone ); ?></strong>
						</a>
					<?php endif; ?>
					<a class="ane-header__cta" href="<?php echo esc_url( $elemenane_cta_url ); ?>"<?php echo $elemenane_cta_blank ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( $elemenane_cta_text ); ?>
					</a>
				</div>
			</div>

			<div class="ane-header__actions">
				<button type="button" class="ane-header__icon-btn" data-ane-search-open aria-haspopup="dialog" aria-controls="ane-search" aria-label="<?php esc_attr_e( 'Search', 'elemenane' ); ?>">
					<?php elemenane_icon( 'search' ); ?>
				</button>
				<?php if ( $elemenane_product_archive ) : ?>
					<a class="ane-header__icon-btn" href="<?php echo esc_url( $elemenane_product_archive ); ?>" aria-label="<?php esc_attr_e( 'Products', 'elemenane' ); ?>">
						<?php elemenane_icon( 'cart' ); ?>
					</a>
				<?php endif; ?>
				<button type="button" class="ane-header__toggle" data-ane-menu-toggle aria-expanded="false" aria-controls="ane-header-panel" aria-label="<?php esc_attr_e( 'Menu', 'elemenane' ); ?>">
					<span class="ane-header__burger" aria-hidden="true"></span>
				</button>
			</div>

		</div>
	</div>

	<div class="ane-header__backdrop" data-ane-backdrop></div>
</header>

<dialog class="ane-search" id="ane-search" aria-label="<?php esc_attr_e( 'Search', 'elemenane' ); ?>">
	<div class="ane-search__inner">
		<button type="button" class="ane-search__close" data-ane-search-close aria-label="<?php esc_attr_e( 'Close search', 'elemenane' ); ?>">
			<?php elemenane_icon( 'tutup' ); ?>
		</button>

		<form role="search" method="get" class="ane-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="ane-search-input"><?php esc_html_e( 'Search for:', 'elemenane' ); ?></label>
			<input type="search" id="ane-search-input" class="ane-search__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr_x( 'Looking for something…', 'placeholder', 'elemenane' ); ?>" autocomplete="off">
			<button type="submit" class="ane-search__submit" aria-label="<?php esc_attr_e( 'Search', 'elemenane' ); ?>"><?php elemenane_icon( 'search' ); ?></button>
		</form>

		<?php if ( ! empty( $elemenane_latest ) ) : ?>
			<div class="ane-search__suggest">
				<p class="ane-search__suggest-title"><?php esc_html_e( 'Latest articles', 'elemenane' ); ?></p>
				<ul>
					<?php foreach ( $elemenane_latest as $elemenane_post ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $elemenane_post ) ); ?>"><?php echo esc_html( get_the_title( $elemenane_post ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="ane-search__banner">
			<?php get_template_part( 'tp/banner/header' ); ?>
		</div>
	</div>
</dialog>
<?php
if ( function_exists( 'ane_output_navigation_schema' ) ) {
	ane_output_navigation_schema();
}
