<?php
/**
 * Single: author box
 *
 * Args (get_template_part):
 * - author_id (int): post author ID.
 *
 * @package elemenane
 * @since   1.1.9
 */

defined( 'ABSPATH' ) || exit;

$elemenane_author_id = ! empty( $args['author_id'] ) ? absint( $args['author_id'] ) : 0;
if ( ! $elemenane_author_id ) {
	return;
}

$elemenane_name = get_the_author_meta( 'display_name', $elemenane_author_id );
if ( '' === (string) $elemenane_name ) {
	$elemenane_name = get_the_author_meta( 'nickname', $elemenane_author_id );
}
$elemenane_bio     = trim( (string) get_the_author_meta( 'user_description', $elemenane_author_id ) );
$elemenane_website = get_the_author_meta( 'user_url', $elemenane_author_id );
$elemenane_posts   = get_author_posts_url( $elemenane_author_id );
$elemenane_count   = (int) count_user_posts( $elemenane_author_id, 'post', true );

if ( '' === $elemenane_bio ) {
	return;
}
?>
<aside class="ane-author-box" aria-label="<?php esc_attr_e( 'About the author', 'elemenane' ); ?>">
	<a class="ane-author-box__avatar" href="<?php echo esc_url( $elemenane_posts ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo get_avatar( $elemenane_author_id, 88, '', '' ); ?>
	</a>

	<div class="ane-author-box__body">
		<span class="ane-author-box__kicker"><?php esc_html_e( 'Written by', 'elemenane' ); ?></span>
		<div class="ane-author-box__name">
			<a href="<?php echo esc_url( $elemenane_posts ); ?>"><?php echo esc_html( $elemenane_name ); ?></a>
		</div>
		<div class="ane-author-box__bio"><?php echo wp_kses_post( nl2br( $elemenane_bio ) ); ?></div>

		<div class="ane-author-box__links">
			<a class="ane-author-box__btn" href="<?php echo esc_url( $elemenane_posts ); ?>">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: number of articles */
						1 === $elemenane_count ? __( 'View %s article', 'elemenane' ) : __( 'View all %s articles', 'elemenane' ),
						number_format_i18n( $elemenane_count )
					)
				);
				?>
				<?php elemenane_icon( 'chevron-right' ); ?>
			</a>
			<?php if ( '' !== (string) $elemenane_website ) : ?>
				<a class="ane-author-box__site" href="<?php echo esc_url( $elemenane_website ); ?>" target="_blank" rel="nofollow noopener external">
					<?php elemenane_icon( 'link' ); ?>
					<span><?php esc_html_e( 'Website', 'elemenane' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</aside>
