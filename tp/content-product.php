<?php
/**
 * Product card (archive, taxonomy, related products, WooCommerce loops).
 *
 * Works with the theme's own products and with WooCommerce products: data comes from
 * elemenane_get_product_card_data(). Args (get_template_part):
 * - product_id (int):   product to render (defaults to the current post).
 * - heading (string):   title tag, h3 (default) or h2.
 * - priority (bool):    eager-load the image (above the fold).
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_card = elemenane_get_product_card_data( ! empty( $args['product_id'] ) ? (int) $args['product_id'] : null );
$elemenane_tag  = ( ! empty( $args['heading'] ) && 'h2' === $args['heading'] ) ? 'h2' : 'h3';
$elemenane_out  = 'outofstock' === $elemenane_card['stock'];
$elemenane_img  = array(
	'class'    => 'ane-product-card__img',
	'alt'      => '',
	'sizes'    => '(max-width: 639px) 50vw, (max-width: 992px) 33vw, 270px',
	'decoding' => 'async',
	'loading'  => ! empty( $args['priority'] ) ? 'eager' : 'lazy',
);
$elemenane_classes = array_merge( array( 'ane-product-card' ), $elemenane_out ? array( 'is-out' ) : array(), $elemenane_card['woocommerce'] ? array( 'has-woocommerce' ) : array() );
?>
<article id="product-<?php echo esc_attr( (string) $elemenane_card['id'] ); ?>" class="<?php echo esc_attr( implode( ' ', array_merge( get_post_class( $elemenane_classes, $elemenane_card['id'] ) ) ) ); ?>">
	<a class="ane-product-card__media" href="<?php echo esc_url( $elemenane_card['url'] ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( $elemenane_card['thumb_id'] ) {
			echo wp_get_attachment_image( $elemenane_card['thumb_id'], 'kotak', false, $elemenane_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated, escaped image markup.
		} else {
			printf(
				'<img class="ane-product-card__img is-placeholder" src="%s" alt="" loading="lazy" decoding="async">',
				esc_url( get_template_directory_uri() . '/img/placeholder.jpg' )
			);
		}
		?>
	</a>

	<?php if ( $elemenane_card['on_sale'] || $elemenane_card['is_new'] || $elemenane_out ) : ?>
		<div class="ane-product-card__badges">
			<?php if ( $elemenane_out ) : ?>
				<span class="ane-product-card__badge ane-product-card__badge--out"><?php esc_html_e( 'Out of Stock', 'elemenane' ); ?></span>
			<?php endif; ?>
			<?php if ( $elemenane_card['on_sale'] && ! $elemenane_out ) : ?>
				<span class="ane-product-card__badge ane-product-card__badge--sale">-<?php echo esc_html( (string) $elemenane_card['discount'] ); ?>%</span>
			<?php endif; ?>
			<?php if ( $elemenane_card['is_new'] && ! $elemenane_out ) : ?>
				<span class="ane-product-card__badge ane-product-card__badge--new"><?php esc_html_e( 'New', 'elemenane' ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="ane-product-card__body">
		<?php if ( $elemenane_card['category'] ) : ?>
			<a class="ane-product-card__cat" href="<?php echo esc_url( (string) get_term_link( $elemenane_card['category'] ) ); ?>"><?php echo esc_html( $elemenane_card['category']->name ); ?></a>
		<?php endif; ?>

		<<?php echo esc_attr( $elemenane_tag ); ?> class="ane-product-card__title">
			<a href="<?php echo esc_url( $elemenane_card['url'] ); ?>"><?php echo esc_html( $elemenane_card['title'] ); ?></a>
		</<?php echo esc_attr( $elemenane_tag ); ?>>

		<?php if ( $elemenane_card['rating_count'] > 0 ) : ?>
			<div class="ane-product-card__rating" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: average rating out of 5 */ __( 'Rated %s out of 5', 'elemenane' ), number_format_i18n( $elemenane_card['rating'], 1 ) ) ); ?>">
				<span class="ane-stars" style="--ane-rating: <?php echo esc_attr( (string) $elemenane_card['rating'] ); ?>"></span>
				<span class="ane-product-card__reviews">(<?php echo esc_html( number_format_i18n( $elemenane_card['rating_count'] ) ); ?>)</span>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $elemenane_card['price_html'] ) : ?>
			<div class="ane-product-card__price"><?php echo wp_kses_post( $elemenane_card['price_html'] ); ?></div>
		<?php elseif ( $elemenane_card['price'] > 0 ) : ?>
			<div class="ane-product-card__price">
				<?php if ( $elemenane_card['on_sale'] ) : ?>
					<span class="ane-product-card__price-now"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['sale'] ) ); ?></span>
					<del class="ane-product-card__price-was"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['regular'] ) ); ?></del>
				<?php else : ?>
					<span class="ane-product-card__price-now"><?php echo wp_kses_post( elemenane_format_price( $elemenane_card['price'] ) ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! $elemenane_out ) : ?>
			<p class="ane-product-card__stock is-in"><?php esc_html_e( 'In Stock', 'elemenane' ); ?></p>
		<?php endif; ?>

		<div class="ane-product-card__cta">
			<?php elemenane_product_card_cta( $elemenane_card ); ?>
		</div>
	</div>
</article>
