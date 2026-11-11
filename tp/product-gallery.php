<?php
/**
 * Single product: gallery (main image, thumbnails, lightbox). js/single-product.js
 *
 * Args: product_id (int). Works for both data sources: featured image + `_product_image_gallery` IDs.
 * With WooCommerce variable products the script swaps the main image when a variation is chosen.
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_id    = ! empty( $args['product_id'] ) ? (int) $args['product_id'] : (int) get_the_ID();
$elemenane_thumb = (int) get_post_thumbnail_id( $elemenane_id );
$elemenane_ids   = array();
if ( $elemenane_thumb ) {
	$elemenane_ids[] = $elemenane_thumb;
}
foreach ( ane_get_product_gallery_ids( $elemenane_id ) as $elemenane_gid ) {
	if ( (int) $elemenane_gid !== $elemenane_thumb ) {
		$elemenane_ids[] = (int) $elemenane_gid;
	}
}
$elemenane_card = elemenane_get_product_card_data( $elemenane_id );
?>
<div class="ane-sp__gallery ane-product-gallery">
	<div class="ane-gallery-main">
		<?php if ( $elemenane_card['on_sale'] || ( $elemenane_card['woocommerce'] && ! $elemenane_card['simple'] && 'outofstock' !== $elemenane_card['stock'] && elemenane_get_wc_product( $elemenane_id )->is_on_sale() ) ) : ?>
			<span class="ane-sp__badge">
				<?php echo $elemenane_card['on_sale'] ? '-' . esc_html( (string) $elemenane_card['discount'] ) . '%' : esc_html__( 'Sale', 'elemenane' ); ?>
			</span>
		<?php endif; ?>

		<?php if ( ! empty( $elemenane_ids ) ) : ?>
			<a href="<?php echo esc_url( (string) wp_get_attachment_image_url( $elemenane_ids[0], 'full' ) ); ?>" class="ane-gallery-popup" aria-label="<?php esc_attr_e( 'Enlarge image', 'elemenane' ); ?>">
				<?php
				echo wp_get_attachment_image(
					$elemenane_ids[0],
					'kotak',
					false,
					array(
						'class'         => 'ane-gallery-main-img',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '(max-width: 992px) 100vw, 520px',
					)
				);
				?>
			</a>
		<?php else : ?>
			<div class="ane-gallery-placeholder">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/img/placeholder.jpg' ); ?>" alt="" width="300" height="300">
			</div>
		<?php endif; ?>
	</div>

	<?php if ( count( $elemenane_ids ) > 1 ) : ?>
		<ul class="ane-gallery-thumbs" aria-label="<?php esc_attr_e( 'Product images', 'elemenane' ); ?>">
			<?php foreach ( $elemenane_ids as $elemenane_index => $elemenane_image_id ) : ?>
				<li>
					<button type="button" class="ane-gallery-thumb<?php echo 0 === $elemenane_index ? ' active' : ''; ?>" data-full="<?php echo esc_url( (string) wp_get_attachment_image_url( $elemenane_image_id, 'full' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: image number */ __( 'Show image %d', 'elemenane' ), $elemenane_index + 1 ) ); ?>">
						<?php echo wp_get_attachment_image( $elemenane_image_id, 'kotak', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated, escaped image markup. ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
