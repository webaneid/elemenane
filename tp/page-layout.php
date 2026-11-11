<?php
/**
 * Standard page layout (page.php and the "No Sidebar" template).
 *
 * Args (get_template_part): sidebar (bool) - show the default sidebar next to the page (default true).
 * Same frame as the archives: breadcrumb, header panel, container, bottom spacing; without a sidebar the page is
 * a centred reading column.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_sidebar = ! isset( $args['sidebar'] ) || ! empty( $args['sidebar'] );
$elemenane_sidebar = $elemenane_sidebar && is_active_sidebar( 'default-sidebar' );

// WooCommerce cart / checkout / account pages: full container width, no sidebar, no share bar.
$elemenane_shop_page = ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_account_page' ) && is_account_page() );
if ( $elemenane_shop_page ) {
	$elemenane_sidebar = false;
}
?>
<main id="main" class="site-main ane-archive ane-archive--page<?php echo $elemenane_sidebar || $elemenane_shop_page ? '' : ' ane-archive--narrow'; ?>" role="main">
	<?php get_template_part( 'breadcrumbs' ); ?>

	<div class="container">
		<div class="ane-archive__layout<?php echo $elemenane_sidebar ? ' has-sidebar' : ( $elemenane_shop_page ? '' : ' is-narrow' ); ?>">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();

					$elemenane_parent = wp_get_post_parent_id( get_the_ID() );
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-page' ); ?>>
						<header class="page-header ane-archive-head">
							<?php if ( $elemenane_parent ) : ?>
								<span class="ane-archive-head__kicker"><?php echo esc_html( get_the_title( $elemenane_parent ) ); ?></span>
							<?php endif; ?>
							<h1 class="ane-archive-head__title"><?php the_title(); ?></h1>
						</header>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="ane-entry__figure ane-page__figure">
								<?php
								the_post_thumbnail(
									'large',
									array(
										'alt'           => get_the_title(),
										'loading'       => 'eager',
										'fetchpriority' => 'high',
									)
								);
								$elemenane_caption = (string) get_post( get_post_thumbnail_id() )->post_excerpt;
								if ( '' !== $elemenane_caption ) {
									echo '<figcaption class="featured-image-caption">' . wp_kses_post( $elemenane_caption ) . '</figcaption>';
								}
								?>
							</figure>
						<?php endif; ?>

						<div class="ane-content"><?php the_content(); ?></div>

						<?php
						wp_link_pages(
							array(
								'before' => '<nav class="ane-page__pages" aria-label="' . esc_attr__( 'Page', 'elemenane' ) . '">',
								'after'  => '</nav>',
							)
						);

						if ( ! $elemenane_shop_page ) {
							echo wp_kses( ane_share_this_post( __( 'Share this page', 'elemenane' ) ), elemenane_kses_post_with_svg() );
						}
						?>

						<div class="comments-form"><?php echo wp_kses_post( ane_load_facebook_comment() ); ?></div>
					</article>
					<?php
				endwhile;
			else :
				get_template_part( 'tp/content', 'none' );
			endif;

			if ( $elemenane_sidebar ) {
				get_sidebar();
			}
			?>
		</div>
	</div>
</main>
