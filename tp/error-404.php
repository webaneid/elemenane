<?php
/**
 * 404 page: large code, message, search, shortcuts from the main menu and the latest articles.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

// Top-level items of the main menu as shortcuts.
$elemenane_links = array();
$elemenane_items = has_nav_menu( 'menuutama' ) ? wp_get_nav_menu_items( (int) get_nav_menu_locations()['menuutama'] ) : array();
foreach ( (array) $elemenane_items as $elemenane_item ) {
	if ( 0 === (int) $elemenane_item->menu_item_parent ) {
		$elemenane_links[] = $elemenane_item;
	}
	if ( count( $elemenane_links ) >= 6 ) {
		break;
	}
}

$elemenane_recent = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<main id="main" class="site-main ane-archive ane-404" role="main">
	<div class="container">
		<section class="ane-404__hero">
			<p class="ane-404__code" aria-hidden="true">404</p>
			<h1 class="ane-404__title"><?php esc_html_e( 'Oops! Page Not Found', 'elemenane' ); ?></h1>
			<p class="ane-404__text"><?php esc_html_e( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'elemenane' ); ?></p>

			<div class="ane-404__search"><?php get_search_form(); ?></div>

			<div class="ane-404__actions">
				<a class="ane-404__btn ane-404__btn--solid" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to Homepage', 'elemenane' ); ?></a>
			</div>

			<?php if ( ! empty( $elemenane_links ) ) : ?>
				<p class="ane-404__label"><?php esc_html_e( 'Or try these helpful links:', 'elemenane' ); ?></p>
				<ul class="ane-404__chips">
					<?php foreach ( $elemenane_links as $elemenane_link ) : ?>
						<li><a href="<?php echo esc_url( $elemenane_link->url ); ?>"><?php echo esc_html( $elemenane_link->title ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>

		<?php if ( $elemenane_recent->have_posts() ) : ?>
			<section class="ane-404__recent" aria-labelledby="ane-404-recent-title">
				<h2 id="ane-404-recent-title" class="ane-section-title"><?php esc_html_e( 'Recent Articles', 'elemenane' ); ?></h2>
				<div class="ane-grid">
					<?php
					while ( $elemenane_recent->have_posts() ) :
						$elemenane_recent->the_post();
						get_template_part( 'tp/archive', 'card', array( 'heading' => 'h3' ) );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>
	</div>
</main>
