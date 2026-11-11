<?php
/**
 * Single: post tags
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_tags = get_the_tags();
if ( empty( $elemenane_tags ) || is_wp_error( $elemenane_tags ) ) {
	return;
}
?>
<section class="ane-tags" aria-labelledby="ane-tags-title">
	<h2 id="ane-tags-title" class="ane-section-title"><?php esc_html_e( 'Tags', 'elemenane' ); ?></h2>
	<ul class="ane-tags__list">
		<?php foreach ( $elemenane_tags as $elemenane_tag ) : ?>
			<li>
				<a href="<?php echo esc_url( get_tag_link( $elemenane_tag->term_id ) ); ?>" rel="tag">
					<span aria-hidden="true">#</span><?php echo esc_html( $elemenane_tag->name ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
