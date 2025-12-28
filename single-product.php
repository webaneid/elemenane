<?php
/**
 * Single Product Template
 *
 * Displays individual product with gallery, pricing, purchase options, and related products.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$product_id = get_the_ID();

	// Get product data.
	$regular_price = get_field( '_regular_price', $product_id );
	$sale_price    = get_field( '_sale_price', $product_id );
	$sku           = get_field( '_sku', $product_id );
	$stock_status  = ane_get_product_stock_status( $product_id );
	$gallery       = get_field( 'ane_product_gallery', $product_id );
	$specs         = get_field( 'ane_product_specs', $product_id );
	$features      = get_field( 'ane_product_features', $product_id );
	$marketplaces  = ane_get_product_marketplaces( $product_id );
	$branches      = ane_get_product_branches();
	$whatsapp_url  = ane_get_product_whatsapp_url( $product_id, 'admin' );
	$categories    = get_the_terms( $product_id, 'product_cat' );

	// Calculate discount percentage.
	$discount_percent = 0;
	if ( ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) {
		$discount_percent = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
	}

	// Prepare gallery images.
	$thumbnail = get_post_thumbnail_id( $product_id );
	$images    = array();

	// Add featured image first.
	if ( $thumbnail ) {
		$images[] = $thumbnail;
	}

	// Add gallery images (ACF returns array of IDs with return_format='id').
	if ( ! empty( $gallery ) && is_array( $gallery ) ) {
		foreach ( $gallery as $image_id ) {
			if ( $image_id != $thumbnail ) {
				$images[] = $image_id;
			}
		}
	}
	?>

	<div class="ane-product-single">
		<div class="ane-container">

			<!-- Breadcrumb -->
			<div class="ane-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<span class="separator">›</span>
				<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
					<a href="<?php echo esc_url( get_term_link( $categories[0] ) ); ?>">
						<?php echo esc_html( $categories[0]->name ); ?>
					</a>
					<span class="separator">›</span>
				<?php endif; ?>
				<span class="current"><?php the_title(); ?></span>
			</div>

			<!-- Product Main Section -->
			<div class="ane-product-main">

				<!-- Gallery Section -->
				<div class="ane-product-gallery">
					<?php if ( ! empty( $images ) ) : ?>
						<!-- Main Image -->
						<div class="ane-gallery-main">
							<a href="<?php echo esc_url( wp_get_attachment_image_url( $images[0], 'full' ) ); ?>"
							   class="ane-gallery-popup">
								<?php echo wp_get_attachment_image( $images[0], 'large', false, array( 'class' => 'ane-gallery-main-img' ) ); ?>
							</a>
						</div>

						<!-- Thumbnails -->
						<?php if ( count( $images ) > 1 ) : ?>
							<div class="ane-gallery-thumbs">
								<?php foreach ( $images as $index => $image_id ) : ?>
									<div class="ane-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
									     data-full="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ); ?>"
									     data-large="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'large' ) ); ?>">
										<?php echo wp_get_attachment_image( $image_id, 'kotak' ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<div class="ane-gallery-placeholder">
							<img src="<?php echo esc_url( get_template_directory_uri() . '/img/placeholder.jpg' ); ?>" alt="No image">
						</div>
					<?php endif; ?>
				</div>

				<!-- Product Info Section -->
				<div class="ane-product-info">

					<!-- Title -->
					<h1 class="ane-product-title"><?php the_title(); ?></h1>

					<!-- Rating & Reviews (placeholder) -->
					<div class="ane-product-meta">
						<div class="ane-rating">
							<span class="stars">★★★★☆</span>
							<span class="rating-text">84 Reviews</span>
						</div>
						<?php if ( $sku ) : ?>
							<span class="ane-sku">SKU: <?php echo esc_html( $sku ); ?></span>
						<?php endif; ?>
					</div>

					<!-- Price -->
					<div class="ane-product-price-wrap">
						<?php if ( ! empty( $sale_price ) && $sale_price > 0 && $sale_price < $regular_price ) : ?>
							<div class="ane-price-main">
								<span class="ane-price-sale">Rp <?php echo number_format( $sale_price, 0, ',', '.' ); ?></span>
								<span class="ane-price-regular-strike">Rp <?php echo number_format( $regular_price, 0, ',', '.' ); ?></span>
							</div>
							<?php if ( $discount_percent > 0 ) : ?>
								<span class="ane-price-badge">-<?php echo $discount_percent; ?>%</span>
							<?php endif; ?>
						<?php else : ?>
							<div class="ane-price-main">
								<span class="ane-price-current">Rp <?php echo number_format( $regular_price, 0, ',', '.' ); ?></span>
							</div>
						<?php endif; ?>
						<p class="ane-price-note">(MRP inclusive of all taxes)</p>
					</div>

					<!-- Short Description -->
					<?php if ( has_excerpt() ) : ?>
						<div class="ane-product-excerpt">
							<?php the_excerpt(); ?>
						</div>
					<?php endif; ?>

					<!-- Stock Status -->
					<div class="ane-stock-status <?php echo $stock_status === 'instock' ? 'in-stock' : 'out-of-stock'; ?>">
						<?php if ( $stock_status === 'instock' ) : ?>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
								<circle cx="8" cy="8" r="8" fill="#10b981"/>
								<path d="M11 6L7 10L5 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<span><?php esc_html_e( 'In Stock', 'elemenane' ); ?></span>
						<?php else : ?>
							<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
								<circle cx="8" cy="8" r="8" fill="#ef4444"/>
								<path d="M10 6L6 10M6 6L10 10" stroke="white" stroke-width="2" stroke-linecap="round"/>
							</svg>
							<span><?php esc_html_e( 'Out of Stock', 'elemenane' ); ?></span>
						<?php endif; ?>
					</div>

					<!-- Purchase Options -->
					<div class="ane-purchase-options">

						<!-- WhatsApp Admin -->
						<?php if ( $whatsapp_url ) : ?>
							<a href="<?php echo esc_url( $whatsapp_url ); ?>"
							   class="ane-btn ane-btn-whatsapp"
							   target="_blank"
							   rel="noopener">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
									<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
								</svg>
								<?php esc_html_e( 'Chat WhatsApp', 'elemenane' ); ?>
							</a>
						<?php endif; ?>

						<!-- Buy at Branch (Dropdown Trigger) -->
						<?php if ( ! empty( $branches ) ) : ?>
							<button class="ane-btn ane-btn-branch" id="ane-branch-trigger">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
									<circle cx="12" cy="10" r="3"/>
								</svg>
								<?php esc_html_e( 'Buy at Branch', 'elemenane' ); ?>
							</button>
						<?php endif; ?>

						<!-- Marketplace Links -->
						<?php if ( ! empty( $marketplaces ) ) : ?>
							<div class="ane-marketplace-section">
								<p class="ane-marketplace-label"><?php esc_html_e( 'Or buy on marketplace:', 'elemenane' ); ?></p>
								<div class="ane-marketplace-links">
									<?php foreach ( $marketplaces as $key => $marketplace ) : ?>
										<a href="<?php echo esc_url( $marketplace['url'] ); ?>"
										   class="ane-marketplace-btn ane-marketplace-<?php echo esc_attr( $key ); ?>"
										   target="_blank"
										   rel="noopener">
											<?php echo esc_html( $marketplace['name'] ); ?>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>

					</div>

				</div>

			</div>

			<!-- Product Tabs -->
			<div class="ane-product-tabs">
				<div class="ane-tabs-nav">
					<button class="ane-tab-btn active" data-tab="description"><?php esc_html_e( 'Description', 'elemenane' ); ?></button>
					<?php if ( ! empty( $specs ) ) : ?>
						<button class="ane-tab-btn" data-tab="specs"><?php esc_html_e( 'Specifications', 'elemenane' ); ?></button>
					<?php endif; ?>
					<?php if ( ! empty( $features ) ) : ?>
						<button class="ane-tab-btn" data-tab="features"><?php esc_html_e( 'Features', 'elemenane' ); ?></button>
					<?php endif; ?>
				</div>

				<div class="ane-tabs-content">
					<!-- Description Tab -->
					<div class="ane-tab-pane active" id="tab-description">
						<?php the_content(); ?>
					</div>

					<!-- Specifications Tab -->
					<?php if ( ! empty( $specs ) ) : ?>
						<div class="ane-tab-pane" id="tab-specs">
							<table class="ane-specs-table">
								<tbody>
									<?php foreach ( $specs as $spec ) : ?>
										<tr>
											<th><?php echo esc_html( $spec['label'] ); ?></th>
											<td><?php echo esc_html( $spec['value'] ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endif; ?>

					<!-- Features Tab -->
					<?php if ( ! empty( $features ) ) : ?>
						<div class="ane-tab-pane" id="tab-features">
							<ul class="ane-features-list">
								<?php foreach ( $features as $feature ) : ?>
									<li><?php echo esc_html( $feature['feature'] ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Related Products -->
			<?php
			$related_ids = ane_get_related_products( $product_id, 4 );
			if ( ! empty( $related_ids ) ) :
				?>
				<div class="ane-related-products">
					<h2>Produk Terkait</h2>
					<div class="ane-products-grid">
						<?php
						foreach ( $related_ids as $related_id ) :
							$related_price     = get_field( '_regular_price', $related_id );
							$related_sale      = get_field( '_sale_price', $related_id );
							$related_thumbnail = get_post_thumbnail_id( $related_id );
							?>
							<article class="ane-product-card">
								<a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" class="ane-product-card-link">
									<?php if ( $related_thumbnail ) : ?>
										<div class="ane-product-card-image">
											<?php echo wp_get_attachment_image( $related_thumbnail, 'medium' ); ?>
										</div>
									<?php endif; ?>
									<div class="ane-product-card-content">
										<h3 class="ane-product-card-title"><?php echo esc_html( get_the_title( $related_id ) ); ?></h3>
										<div class="ane-product-card-price">
											<?php if ( ! empty( $related_sale ) && $related_sale > 0 && $related_sale < $related_price ) : ?>
												<span class="price-sale">Rp <?php echo number_format( $related_sale, 0, ',', '.' ); ?></span>
												<span class="price-regular">Rp <?php echo number_format( $related_price, 0, ',', '.' ); ?></span>
											<?php else : ?>
												<span class="price-current">Rp <?php echo number_format( $related_price, 0, ',', '.' ); ?></span>
											<?php endif; ?>
										</div>
									</div>
								</a>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</div>

	<!-- Branch Popup Modal -->
	<?php if ( ! empty( $branches ) ) : ?>
		<!-- Debug: <?php echo count( $branches ); ?> branches found -->
		<div class="ane-branch-modal" id="ane-branch-modal">
			<div class="ane-branch-modal-overlay"></div>
			<div class="ane-branch-modal-content">
				<button class="ane-branch-modal-close" id="ane-branch-modal-close">×</button>
				<h3><?php esc_html_e( 'Select Branch', 'elemenane' ); ?></h3>

				<!-- Search Input -->
				<div class="ane-branch-search-wrap">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="11" cy="11" r="8"/>
						<path d="m21 21-4.35-4.35"/>
					</svg>
					<input type="text"
					       id="ane-branch-search"
					       class="ane-branch-search"
					       placeholder="<?php esc_attr_e( 'Search by city or branch name...', 'elemenane' ); ?>"
					       autocomplete="off">
				</div>

				<div class="ane-branch-list">
					<?php foreach ( $branches as $branch ) : ?>
						<div class="ane-branch-item" data-branch='<?php echo esc_attr( json_encode( $branch ) ); ?>'>
							<div class="ane-branch-item-header">
								<h4><?php echo esc_html( $branch['title'] ); ?></h4>
								<?php if ( ! empty( $branch['city'] ) ) : ?>
									<span class="ane-branch-city"><?php echo esc_html( $branch['city'] ); ?></span>
								<?php endif; ?>
							</div>
							<div class="ane-branch-item-body">
								<?php if ( ! empty( $branch['address'] ) ) : ?>
									<p class="ane-branch-address">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor">
											<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
											<circle cx="12" cy="10" r="3"/>
										</svg>
										<?php echo esc_html( $branch['address'] ); ?>
									</p>
								<?php endif; ?>
								<div class="ane-branch-actions">
									<?php if ( ! empty( $branch['whatsapp'] ) ) : ?>
										<a href="#" class="ane-btn ane-btn-sm ane-btn-whatsapp ane-branch-whatsapp"
										   data-whatsapp="<?php echo esc_attr( $branch['whatsapp'] ); ?>">
											<?php esc_html_e( 'WhatsApp', 'elemenane' ); ?>
										</a>
									<?php endif; ?>
									<button class="ane-btn ane-btn-sm ane-btn-outline ane-branch-detail">
										<?php esc_html_e( 'Detail', 'elemenane' ); ?>
									</button>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
