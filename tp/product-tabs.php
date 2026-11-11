<?php
/**
 * Single product: tabs (Description, Specifications, Features, Reviews).
 *
 * Args: product_id (int). Specifications merge the theme's ACF specs with WooCommerce attributes / weight /
 * dimensions when WooCommerce is active. Reviews appear only when WooCommerce reviews are enabled.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_id       = ! empty( $args['product_id'] ) ? (int) $args['product_id'] : (int) get_the_ID();
$elemenane_wc       = elemenane_get_wc_product( $elemenane_id );
$elemenane_specs    = get_field( 'ane_product_specs', $elemenane_id );
$elemenane_features = get_field( 'ane_product_features', $elemenane_id );
$elemenane_has_attr = $elemenane_wc && ( $elemenane_wc->has_attributes() || $elemenane_wc->has_weight() || $elemenane_wc->has_dimensions() );
$elemenane_reviews  = $elemenane_wc && function_exists( 'wc_reviews_enabled' ) && wc_reviews_enabled();
$elemenane_tags     = get_the_terms( $elemenane_id, 'product_tag' );

$elemenane_tabs = array(
	'description' => __( 'Description', 'elemenane' ),
);
if ( ! empty( $elemenane_specs ) || $elemenane_has_attr ) {
	$elemenane_tabs['specs'] = __( 'Specifications', 'elemenane' );
}
if ( ! empty( $elemenane_features ) ) {
	$elemenane_tabs['features'] = __( 'Features', 'elemenane' );
}
if ( $elemenane_reviews ) {
	$elemenane_tabs['reviews'] = sprintf(
		/* translators: %d: number of reviews */
		__( 'Reviews (%d)', 'elemenane' ),
		(int) $elemenane_wc->get_review_count()
	);
}
?>
<div class="ane-sp__tabs ane-product-tabs">
	<div class="ane-tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Product information', 'elemenane' ); ?>">
		<?php foreach ( $elemenane_tabs as $elemenane_key => $elemenane_label ) : ?>
			<button type="button" id="tab-btn-<?php echo esc_attr( $elemenane_key ); ?>" class="ane-tab-btn<?php echo 'description' === $elemenane_key ? ' active' : ''; ?>" role="tab" data-tab="<?php echo esc_attr( $elemenane_key ); ?>" aria-controls="tab-<?php echo esc_attr( $elemenane_key ); ?>" aria-selected="<?php echo 'description' === $elemenane_key ? 'true' : 'false'; ?>" tabindex="<?php echo 'description' === $elemenane_key ? '0' : '-1'; ?>"><?php echo esc_html( $elemenane_label ); ?></button>
		<?php endforeach; ?>
	</div>

	<div class="ane-tabs-content">
		<div class="ane-content ane-tab-pane active" id="tab-description" role="tabpanel" aria-labelledby="tab-btn-description">
			<?php the_content(); ?>

			<?php if ( $elemenane_tags && ! is_wp_error( $elemenane_tags ) ) : ?>
				<div class="ane-product-tags">
					<span class="tags-label"><?php esc_html_e( 'Tags:', 'elemenane' ); ?></span>
					<div class="tags-list">
						<?php foreach ( $elemenane_tags as $elemenane_tag ) : ?>
							<a href="<?php echo esc_url( (string) get_term_link( $elemenane_tag ) ); ?>" class="tag-item" rel="tag"><span aria-hidden="true">#</span><?php echo esc_html( $elemenane_tag->name ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( isset( $elemenane_tabs['specs'] ) ) : ?>
			<div class="ane-tab-pane" id="tab-specs" role="tabpanel" aria-labelledby="tab-btn-specs" hidden>
				<?php if ( ! empty( $elemenane_specs ) ) : ?>
					<table class="ane-specs-table">
						<tbody>
							<?php foreach ( $elemenane_specs as $elemenane_spec ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( $elemenane_spec['label'] ); ?></th>
									<td><?php echo esc_html( $elemenane_spec['value'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				<?php if ( $elemenane_has_attr && function_exists( 'wc_display_product_attributes' ) ) : ?>
					<?php wc_display_product_attributes( $elemenane_wc ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( isset( $elemenane_tabs['features'] ) ) : ?>
			<div class="ane-tab-pane" id="tab-features" role="tabpanel" aria-labelledby="tab-btn-features" hidden>
				<ul class="ane-features-list">
					<?php foreach ( $elemenane_features as $elemenane_feature ) : ?>
						<li><?php echo esc_html( $elemenane_feature['feature'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( isset( $elemenane_tabs['reviews'] ) ) : ?>
			<div class="ane-tab-pane" id="tab-reviews" role="tabpanel" aria-labelledby="tab-btn-reviews" hidden>
				<?php comments_template(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
