<?php
/**
 * Single Product Template
 *
 * Works with the theme's own products and with WooCommerce products (simple, variable, grouped, external).
 * Parts: tp/product-gallery.php, tp/product-summary.php, tp/product-tabs.php, tp/product-branch-modal.php.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$elemenane_id      = (int) get_the_ID();
	$elemenane_wc      = elemenane_get_wc_product( $elemenane_id );
	$elemenane_args    = array( 'product_id' => $elemenane_id );
	$elemenane_related = elemenane_get_related_product_ids( $elemenane_id, 4 );
	$elemenane_upsell  = $elemenane_wc ? array_slice( array_map( 'intval', (array) $elemenane_wc->get_upsell_ids() ), 0, 4 ) : array();
	$elemenane_classes = $elemenane_wc
		? implode( ' ', array_map( 'sanitize_html_class', wc_get_product_class( 'ane-sp', $elemenane_wc ) ) )
		: implode( ' ', array_map( 'sanitize_html_class', get_post_class( 'ane-sp product', $elemenane_id ) ) );
	?>
	<main id="main" class="site-main ane-archive ane-archive--single-product" role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<?php
			if ( $elemenane_wc ) {
				// Notices ("added to cart"), password protection, plugins.
				do_action( 'woocommerce_before_single_product' );
			}
			?>

			<div id="product-<?php echo esc_attr( (string) $elemenane_id ); ?>" class="<?php echo esc_attr( $elemenane_classes ); ?>">
				<div class="ane-sp__top">
					<?php
					get_template_part( 'tp/product', 'gallery', $elemenane_args );
					get_template_part( 'tp/product', 'summary', $elemenane_args );
					?>
				</div>

				<?php get_template_part( 'tp/product', 'tabs', $elemenane_args ); ?>

				<?php if ( ! empty( $elemenane_upsell ) ) : ?>
					<section class="ane-sp__more" aria-labelledby="ane-upsell-title">
						<h2 id="ane-upsell-title" class="ane-section-title"><?php esc_html_e( 'You may also like', 'elemenane' ); ?></h2>
						<?php elemenane_render_product_cards( $elemenane_upsell ); ?>
					</section>
				<?php endif; ?>

				<?php if ( ! empty( $elemenane_related ) ) : ?>
					<section class="ane-sp__more ane-related-products" aria-labelledby="ane-related-title">
						<h2 id="ane-related-title" class="ane-section-title"><?php esc_html_e( 'Related Products', 'elemenane' ); ?></h2>
						<?php elemenane_render_product_cards( $elemenane_related ); ?>
					</section>
				<?php endif; ?>
			</div>

			<?php
			if ( $elemenane_wc ) {
				do_action( 'woocommerce_after_single_product' );
			}
			?>
		</div>
	</main>

	<?php get_template_part( 'tp/product', 'branch-modal', array( 'branches' => ane_get_product_branches() ) ); ?>

	<?php
endwhile;

get_footer();
