<?php
/**
 * Landing: section heading (same kicker style as the sidebar widget titles).
 *
 * Args (get_template_part):
 * - title (string): heading text.
 * - url (string):   optional link for the title and the "more" link.
 * - more (string):  label of the "more" link (default "Display More").
 * - rail (bool):    show previous / next buttons for a rail (js/home.js).
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_title = ! empty( $args['title'] ) ? (string) $args['title'] : '';
$elemenane_url   = ! empty( $args['url'] ) ? (string) $args['url'] : '';
$elemenane_more  = ! empty( $args['more'] ) ? (string) $args['more'] : __( 'Display More', 'elemenane' );
$elemenane_rail  = ! empty( $args['rail'] );

if ( '' === $elemenane_title ) {
	return;
}
?>
<header class="ane-sec-head">
	<h2 class="ane-section-title">
		<?php if ( '' !== $elemenane_url ) : ?>
			<a href="<?php echo esc_url( $elemenane_url ); ?>"><?php echo esc_html( $elemenane_title ); ?></a>
		<?php else : ?>
			<?php echo esc_html( $elemenane_title ); ?>
		<?php endif; ?>
	</h2>

	<?php if ( $elemenane_rail ) : ?>
		<div class="ane-sec-head__rail">
			<button type="button" class="ane-rail-btn" data-rail-prev aria-label="<?php esc_attr_e( 'Previous', 'elemenane' ); ?>"><?php elemenane_icon( 'chevron-left' ); ?></button>
			<button type="button" class="ane-rail-btn" data-rail-next aria-label="<?php esc_attr_e( 'Next', 'elemenane' ); ?>"><?php elemenane_icon( 'chevron-right' ); ?></button>
		</div>
	<?php endif; ?>

	<?php if ( '' !== $elemenane_url ) : ?>
		<a class="ane-sec-head__more" href="<?php echo esc_url( $elemenane_url ); ?>">
			<?php echo esc_html( $elemenane_more ); ?>
			<?php elemenane_icon( 'chevron-right' ); ?>
		</a>
	<?php endif; ?>
</header>
