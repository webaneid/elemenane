<?php
/**
 * Product archive layout (shop archive, product category, product tag).
 *
 * Same frame as the post archives (tp/archive-layout.php): breadcrumb, header panel, container, grid,
 * pagination, bottom spacing. Called from archive-product.php and taxonomy-product_*.php.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$elemenane_term  = is_tax() ? get_queried_object() : null;
$elemenane_found = (int) $wp_query->found_posts;

if ( is_tax( 'product_tag' ) ) {
	$elemenane_kicker = __( 'Product tag', 'elemenane' );
} elseif ( is_tax( 'product_cat' ) ) {
	$elemenane_kicker = __( 'Product category', 'elemenane' );
} else {
	$elemenane_kicker = __( 'Catalog', 'elemenane' );
}
$elemenane_title = $elemenane_term instanceof WP_Term ? $elemenane_term->name : __( 'Products', 'elemenane' );
$elemenane_desc  = $elemenane_term instanceof WP_Term ? $elemenane_term->description : '';
$elemenane_page  = max( 1, (int) get_query_var( 'paged' ) );
$elemenane_i     = 0;
?>
<main id="main" class="site-main ane-archive ane-archive--product" role="main">
	<?php get_template_part( 'breadcrumbs' ); ?>

	<div class="container">
		<?php
		if ( function_exists( 'wc_print_notices' ) ) {
			wc_print_notices();
		}
		?>

		<header class="page-header ane-archive-head">
			<span class="ane-archive-head__kicker"><?php echo esc_html( $elemenane_kicker ); ?></span>
			<h1 class="ane-archive-head__title">
				<?php if ( is_tax( 'product_tag' ) ) : ?>
					<span class="ane-archive-head__hash" aria-hidden="true">#</span>
				<?php endif; ?>
				<?php echo esc_html( $elemenane_title ); ?>
			</h1>

			<?php if ( '' !== trim( wp_strip_all_tags( (string) $elemenane_desc ) ) ) : ?>
				<div class="ane-archive-head__desc"><?php echo wp_kses_post( wpautop( $elemenane_desc ) ); ?></div>
			<?php endif; ?>

			<p class="ane-archive-head__count">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: number of products */
						1 === $elemenane_found ? __( '%s product', 'elemenane' ) : __( '%s products', 'elemenane' ),
						number_format_i18n( $elemenane_found )
					)
				);
				?>
			</p>
		</header>

		<?php ane_product_filters(); ?>

		<?php if ( have_posts() ) : ?>
			<div class="ane-pgrid">
				<?php
				while ( have_posts() ) :
					the_post();
					++$elemenane_i;
					get_template_part(
						'tp/content',
						'product',
						array(
							'priority' => 1 === $elemenane_page && $elemenane_i <= 4,
						)
					);
				endwhile;
				?>
			</div>

			<?php ane_post_pagination(); ?>
		<?php else : ?>
			<section class="ane-empty">
				<span class="ane-empty__icon" aria-hidden="true"><?php elemenane_icon( 'search' ); ?></span>
				<h2 class="ane-empty__title"><?php esc_html_e( 'No products found', 'elemenane' ); ?></h2>
				<p class="ane-empty__text">
					<?php
					echo ane_product_filters_active()
						? esc_html__( 'No product matches these filters. Try removing a filter or widening the price range.', 'elemenane' )
						: esc_html__( 'There are no products here yet. Please check back later.', 'elemenane' );
					?>
				</p>
			</section>
		<?php endif; ?>
	</div>
</main>
