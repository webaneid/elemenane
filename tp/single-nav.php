<?php
/**
 * Single: previous / next post navigation
 *
 * @package elemenane
 * @since   1.1.9
 */

defined( 'ABSPATH' ) || exit;

$elemenane_items = array(
	'prev' => get_previous_post(),
	'next' => get_next_post(),
);

if ( ! $elemenane_items['prev'] && ! $elemenane_items['next'] ) {
	return;
}
?>
<nav class="ane-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'elemenane' ); ?>">
	<?php
	foreach ( $elemenane_items as $elemenane_dir => $elemenane_nav_post ) :
		if ( ! $elemenane_nav_post instanceof WP_Post ) {
			continue;
		}
		$elemenane_nav_id    = (int) $elemenane_nav_post->ID;
		$elemenane_is_next   = 'next' === $elemenane_dir;
		$elemenane_thumb_url = has_post_thumbnail( $elemenane_nav_id )
			? (string) get_the_post_thumbnail_url( $elemenane_nav_id, 'kotak' )
			: '';
		?>
		<a class="ane-post-nav__item ane-post-nav__item--<?php echo esc_attr( $elemenane_dir ); ?>" href="<?php echo esc_url( get_permalink( $elemenane_nav_id ) ); ?>" rel="<?php echo esc_attr( $elemenane_dir ); ?>">
			<span class="ane-post-nav__label">
				<?php if ( ! $elemenane_is_next ) : ?>
					<?php elemenane_icon( 'chevron-left' ); ?>
				<?php endif; ?>
				<?php echo $elemenane_is_next ? esc_html__( 'Next article', 'elemenane' ) : esc_html__( 'Previous article', 'elemenane' ); ?>
				<?php if ( $elemenane_is_next ) : ?>
					<?php elemenane_icon( 'chevron-right' ); ?>
				<?php endif; ?>
			</span>
			<span class="ane-post-nav__row">
				<?php if ( '' !== $elemenane_thumb_url ) : ?>
					<img class="ane-post-nav__thumb" src="<?php echo esc_url( $elemenane_thumb_url ); ?>" alt="" width="72" height="72" loading="lazy" decoding="async">
				<?php endif; ?>
				<span class="ane-post-nav__title"><?php echo esc_html( get_the_title( $elemenane_nav_id ) ); ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</nav>
