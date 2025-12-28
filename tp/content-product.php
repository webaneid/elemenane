<?php
/**
 * Product Loop Item Template
 *
 * Template part for displaying products in archive/taxonomy.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product_id = get_the_ID();

// Get product data.
$regular_price = get_field( '_regular_price', $product_id );
$sale_price    = get_field( '_sale_price', $product_id );
$stock_status  = function_exists( 'ane_get_product_stock_status' )
	? ane_get_product_stock_status( $product_id )
	: 'instock';

// Calculate active price and discount.
$active_price = $regular_price;
$discount_percent = 0;

if ( ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) {
	$active_price = $sale_price;
	$discount_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
}

// Get thumbnail.
$thumbnail_id = get_post_thumbnail_id( $product_id );
?>

<div class="col-6 col-md-4 col-lg-3">
	<article id="product-<?php echo esc_attr( $product_id ); ?>" <?php post_class( 'ane-product-card' ); ?>>

		<!-- Product Image -->
		<div class="ane-product-card-image">
			<a href="<?php the_permalink(); ?>" class="ane-product-card-link">
				<?php if ( $thumbnail_id ) : ?>
					<?php echo wp_get_attachment_image( $thumbnail_id, 'kotak', false, array( 'class' => 'product-image' ) ); ?>
				<?php else : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/img/placeholder.jpg' ); ?>"
					     alt="<?php the_title_attribute(); ?>"
					     class="product-image placeholder">
				<?php endif; ?>

				<!-- Badges -->
				<?php if ( $discount_percent > 0 ) : ?>
					<span class="ane-product-badge ane-badge-sale">
						-<?php echo esc_html( $discount_percent ); ?>%
					</span>
				<?php endif; ?>

				<?php if ( $stock_status !== 'instock' ) : ?>
					<span class="ane-product-badge ane-badge-out">
						<?php esc_html_e( 'Out of Stock', 'elemenane' ); ?>
					</span>
				<?php endif; ?>

				<!-- Check if product is new (less than 7 days old) -->
				<?php
				$post_date = get_the_date( 'U' );
				$current_date = current_time( 'timestamp' );
				$days_diff = ( $current_date - $post_date ) / DAY_IN_SECONDS;

				if ( $days_diff <= 7 ) :
				?>
					<span class="ane-product-badge ane-badge-new">
						<?php esc_html_e( 'New', 'elemenane' ); ?>
					</span>
				<?php endif; ?>
			</a>
		</div>

		<!-- Product Info -->
		<div class="ane-product-card-content">

			<!-- Category -->
			<?php
			$categories = get_the_terms( $product_id, 'product_cat' );
			if ( $categories && ! is_wp_error( $categories ) ) :
			?>
				<div class="ane-product-category">
					<a href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
						<?php echo esc_html( $categories[0]->name ); ?>
					</a>
				</div>
			<?php endif; ?>

			<!-- Title -->
			<h3 class="ane-product-card-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h3>

			<!-- Price -->
			<div class="ane-product-card-price">
				<?php if ( ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) : ?>
					<span class="price-sale">Rp <?php echo number_format( $sale_price, 0, ',', '.' ); ?></span>
					<span class="price-regular">Rp <?php echo number_format( $regular_price, 0, ',', '.' ); ?></span>
				<?php else : ?>
					<span class="price-current">Rp <?php echo number_format( $regular_price, 0, ',', '.' ); ?></span>
				<?php endif; ?>
			</div>

			<!-- Stock Status -->
			<?php if ( $stock_status === 'instock' ) : ?>
				<div class="ane-product-stock in-stock">
					<svg width="12" height="12" viewBox="0 0 16 16" fill="none">
						<circle cx="8" cy="8" r="8" fill="#10b981"/>
						<path d="M11 6L7 10L5 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<span><?php esc_html_e( 'In Stock', 'elemenane' ); ?></span>
				</div>
			<?php else : ?>
				<div class="ane-product-stock out-of-stock">
					<svg width="12" height="12" viewBox="0 0 16 16" fill="none">
						<circle cx="8" cy="8" r="8" fill="#ef4444"/>
						<path d="M10 6L6 10M6 6L10 10" stroke="white" stroke-width="2" stroke-linecap="round"/>
					</svg>
					<span><?php esc_html_e( 'Out of Stock', 'elemenane' ); ?></span>
				</div>
			<?php endif; ?>

		</div>

	</article>
</div>
