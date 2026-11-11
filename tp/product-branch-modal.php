<?php
/**
 * Single product: "Buy at branch" dialog. Opened by #ane-branch-trigger (js/single-product.js).
 *
 * Args: branches (array) from ane_get_product_branches().
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_branches = ! empty( $args['branches'] ) ? (array) $args['branches'] : array();
if ( empty( $elemenane_branches ) ) {
	return;
}
?>
<div class="ane-branch-modal" id="ane-branch-modal" role="dialog" aria-modal="true" aria-labelledby="ane-branch-modal-title">
	<div class="ane-branch-modal-overlay"></div>
	<div class="ane-branch-modal-content">
		<button type="button" class="ane-branch-modal-close" id="ane-branch-modal-close" aria-label="<?php esc_attr_e( 'Close', 'elemenane' ); ?>">&times;</button>
		<h3 id="ane-branch-modal-title"><?php esc_html_e( 'Select Branch', 'elemenane' ); ?></h3>

		<div class="ane-branch-search-wrap">
			<?php elemenane_icon( 'search' ); ?>
			<label class="screen-reader-text" for="ane-branch-search"><?php esc_html_e( 'Search by city or branch name...', 'elemenane' ); ?></label>
			<input type="text" id="ane-branch-search" class="ane-branch-search" placeholder="<?php esc_attr_e( 'Search by city or branch name...', 'elemenane' ); ?>" autocomplete="off">
		</div>

		<div class="ane-branch-list">
			<?php foreach ( $elemenane_branches as $elemenane_branch ) : ?>
				<div class="ane-branch-item" data-branch="<?php echo esc_attr( (string) wp_json_encode( $elemenane_branch ) ); ?>">
					<div class="ane-branch-item-header">
						<h4><?php echo esc_html( $elemenane_branch['title'] ); ?></h4>
						<?php if ( ! empty( $elemenane_branch['city'] ) ) : ?>
							<span class="ane-branch-city"><?php echo esc_html( $elemenane_branch['city'] ); ?></span>
						<?php endif; ?>
					</div>
					<div class="ane-branch-item-body">
						<?php if ( ! empty( $elemenane_branch['address'] ) ) : ?>
							<p class="ane-branch-address">
								<?php elemenane_icon( 'lokasi' ); ?>
								<?php echo esc_html( $elemenane_branch['address'] ); ?>
							</p>
						<?php endif; ?>
						<div class="ane-branch-actions">
							<?php if ( ! empty( $elemenane_branch['whatsapp'] ) ) : ?>
								<a href="#" class="ane-btn ane-btn-sm ane-btn-whatsapp ane-branch-whatsapp" data-whatsapp="<?php echo esc_attr( $elemenane_branch['whatsapp'] ); ?>"><?php esc_html_e( 'WhatsApp', 'elemenane' ); ?></a>
							<?php endif; ?>
							<button type="button" class="ane-btn ane-btn-sm ane-btn-outline ane-branch-detail"><?php esc_html_e( 'Detail', 'elemenane' ); ?></button>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
