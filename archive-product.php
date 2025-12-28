<?php
/**
 * Product Archive Template
 *
 * Displays product archive with grid layout and filters.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="ane-product-archive">
	<div class="ane-container">

		<!-- Archive Header -->
		<div class="ane-archive-header">
			<h1 class="ane-archive-title"><?php esc_html_e( 'Products', 'elemenane' ); ?></h1>

			<?php if ( have_posts() ) : ?>
				<div class="ane-archive-meta">
					<?php
					global $wp_query;
					$total = $wp_query->found_posts;
					printf(
						/* translators: %s: number of products */
						esc_html( _n( '%s Product', '%s Products', $total, 'elemenane' ) ),
						'<span class="count">' . number_format_i18n( $total ) . '</span>'
					);
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<!-- Product Grid -->
			<div class="ane-products-wrap">
				<div class="row">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'tp/content', 'product' );
					endwhile;
					?>
				</div>
			</div>

			<!-- Pagination -->
			<?php
			if ( function_exists( 'ane_post_pagination' ) ) {
				ane_post_pagination( $wp_query );
			}
			?>

		<?php else : ?>
			<!-- No Products Found -->
			<div class="ane-no-products">
				<p><?php esc_html_e( 'No products found.', 'elemenane' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
