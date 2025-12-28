<?php
/**
 * Product Tag Taxonomy Template
 *
 * Displays products filtered by tag.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = get_queried_object();
?>

<div class="ane-product-archive ane-product-taxonomy">
	<div class="ane-container">

		<!-- Taxonomy Header -->
		<div class="ane-archive-header">
			<h1 class="ane-archive-title">
				<?php
				printf(
					/* translators: %s: tag name */
					esc_html__( 'Products tagged: %s', 'elemenane' ),
					'<span class="tag-name">' . esc_html( $term->name ) . '</span>'
				);
				?>
			</h1>

			<?php if ( ! empty( $term->description ) ) : ?>
				<div class="ane-archive-description">
					<?php echo wp_kses_post( wpautop( $term->description ) ); ?>
				</div>
			<?php endif; ?>

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
				<p>
					<?php
					printf(
						/* translators: %s: tag name */
						esc_html__( 'No products found with tag %s.', 'elemenane' ),
						'<strong>' . esc_html( $term->name ) . '</strong>'
					);
					?>
				</p>
			</div>
		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
