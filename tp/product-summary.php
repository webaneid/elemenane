<?php
/**
 * Single product: summary (title, rating, price, purchase options, share).
 *
 * Args: product_id (int).
 * - WooCommerce active: price HTML, SKU and the add-to-cart form (simple, variable, grouped, external) come from
 *   WooCommerce, so variations, quantity and cart notices work. Plugins hooked to `woocommerce_single_product_summary`
 *   still run (WooCommerce's default callbacks are unhooked first to avoid duplicates).
 * - Without WooCommerce: Rupiah price, WhatsApp / branch / marketplace buttons.
 * The WhatsApp, branch and marketplace options (ACF) are shown in both modes.
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_id     = ! empty( $args['product_id'] ) ? (int) $args['product_id'] : (int) get_the_ID();
$elemenane_card   = elemenane_get_product_card_data( $elemenane_id );
$elemenane_wc     = elemenane_get_wc_product( $elemenane_id );
$elemenane_sku    = $elemenane_wc ? (string) $elemenane_wc->get_sku() : (string) get_post_meta( $elemenane_id, '_sku', true );
$elemenane_cats   = get_the_terms( $elemenane_id, 'product_cat' );
$elemenane_market = ane_get_product_marketplaces( $elemenane_id );
$elemenane_branch = ane_get_product_branches();
$elemenane_wa     = ane_get_product_whatsapp_url( $elemenane_id, 'admin' );
$elemenane_out    = 'outofstock' === $elemenane_card['stock'];
$elemenane_variable = $elemenane_wc && ! $elemenane_card['simple'];
?>
<div class="ane-sp__summary ane-product-info entry-summary">
	<?php if ( $elemenane_cats && ! is_wp_error( $elemenane_cats ) ) : ?>
		<div class="ane-sp__cats">
			<?php foreach ( $elemenane_cats as $elemenane_cat ) : ?>
				<a class="ane-sp__cat" href="<?php echo esc_url( (string) get_term_link( $elemenane_cat ) ); ?>"><?php echo esc_html( $elemenane_cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<h1 class="ane-sp__title product_title entry-title"><?php echo esc_html( get_the_title( $elemenane_id ) ); ?></h1>

	<div class="ane-sp__meta product_meta">
		<?php if ( $elemenane_card['rating_count'] > 0 ) : ?>
			<a class="ane-sp__rating woocommerce-review-link" href="#reviews" data-open-tab="reviews" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: average rating out of 5 */ __( 'Rated %s out of 5', 'elemenane' ), number_format_i18n( $elemenane_card['rating'], 1 ) ) ); ?>">
				<span class="ane-stars" style="--ane-rating: <?php echo esc_attr( (string) $elemenane_card['rating'] ); ?>"></span>
				<span>(<?php echo esc_html( number_format_i18n( $elemenane_card['rating_count'] ) ); ?>)</span>
			</a>
		<?php endif; ?>

		<?php if ( '' !== $elemenane_sku || $elemenane_variable ) : ?>
			<span class="ane-sp__sku sku_wrapper"><?php esc_html_e( 'SKU:', 'elemenane' ); ?> <span class="sku"><?php echo '' !== $elemenane_sku ? esc_html( $elemenane_sku ) : esc_html__( 'N/A', 'elemenane' ); ?></span></span>
		<?php endif; ?>

		<?php if ( ! $elemenane_variable ) : ?>
			<span class="ane-sp__stock <?php echo $elemenane_out ? 'is-out' : 'is-in'; ?>">
				<?php echo $elemenane_out ? esc_html__( 'Out of Stock', 'elemenane' ) : esc_html__( 'In Stock', 'elemenane' ); ?>
			</span>
		<?php endif; ?>
	</div>

	<?php if ( '' !== $elemenane_card['price_html'] || $elemenane_card['price'] > 0 || $elemenane_wc ) : ?>
		<div class="ane-sp__price price">
			<?php if ( $elemenane_wc ) : ?>
				<?php echo wp_kses_post( $elemenane_wc->get_price_html() ); ?>
			<?php elseif ( $elemenane_card['on_sale'] ) : ?>
				<span class="ane-sp__price-now"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['sale'] ) ); ?></span>
				<del class="ane-sp__price-was"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['regular'] ) ); ?></del>
				<span class="ane-sp__price-off">-<?php echo esc_html( (string) $elemenane_card['discount'] ); ?>%</span>
			<?php else : ?>
				<span class="ane-sp__price-now"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['price'] ) ); ?></span>
			<?php endif; ?>
			<?php if ( ! $elemenane_wc && $elemenane_card['price'] > 0 ) : ?>
				<p class="ane-sp__note"><?php esc_html_e( '(MRP inclusive of all taxes)', 'elemenane' ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( has_excerpt( $elemenane_id ) ) : ?>
		<div class="ane-sp__excerpt ane-product-excerpt woocommerce-product-details__short-description"><?php the_excerpt(); ?></div>
	<?php endif; ?>

	<div class="ane-sp__buy ane-purchase-options">
		<?php if ( $elemenane_wc && function_exists( 'woocommerce_template_single_add_to_cart' ) ) : ?>
			<?php woocommerce_template_single_add_to_cart(); ?>
		<?php endif; ?>

		<?php if ( $elemenane_wa || ! empty( $elemenane_branch ) ) : ?>
			<div class="ane-sp__actions">
				<?php if ( $elemenane_wa ) : ?>
					<a href="<?php echo esc_url( $elemenane_wa ); ?>" class="ane-btn ane-btn-whatsapp" target="_blank" rel="noopener">
						<?php elemenane_icon( 'whatsapp' ); ?>
						<?php esc_html_e( 'Chat WhatsApp', 'elemenane' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( ! empty( $elemenane_branch ) ) : ?>
					<button type="button" class="ane-btn ane-btn-branch" id="ane-branch-trigger" aria-haspopup="dialog" aria-controls="ane-branch-modal">
						<?php elemenane_icon( 'lokasi' ); ?>
						<?php esc_html_e( 'Buy at Branch', 'elemenane' ); ?>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $elemenane_market ) ) : ?>
			<div class="ane-marketplace-section">
				<p class="ane-marketplace-label"><?php esc_html_e( 'Or buy on marketplace:', 'elemenane' ); ?></p>
				<div class="ane-marketplace-links">
					<?php foreach ( $elemenane_market as $elemenane_key => $elemenane_marketplace ) : ?>
						<a href="<?php echo esc_url( $elemenane_marketplace['url'] ); ?>" class="ane-marketplace-btn ane-marketplace-<?php echo esc_attr( $elemenane_key ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html( $elemenane_marketplace['name'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php
	if ( $elemenane_wc ) {
		// Let plugins add to the summary without duplicating what the theme already printed.
		foreach ( array(
			'woocommerce_template_single_title'       => 5,
			'woocommerce_template_single_rating'      => 10,
			'woocommerce_template_single_price'       => 10,
			'woocommerce_template_single_excerpt'     => 20,
			'woocommerce_template_single_add_to_cart' => 30,
			'woocommerce_template_single_meta'        => 40,
			'woocommerce_template_single_sharing'     => 50,
		) as $elemenane_callback => $elemenane_priority ) {
			remove_action( 'woocommerce_single_product_summary', $elemenane_callback, $elemenane_priority );
		}
		do_action( 'woocommerce_single_product_summary' );
	}

	echo wp_kses( ane_share_this_post(), elemenane_kses_post_with_svg() );
	?>
</div>
