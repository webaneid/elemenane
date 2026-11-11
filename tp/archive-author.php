<?php
/**
 * Author Archive Header (profile)
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_author_id = (int) get_queried_object_id();
$elemenane_name      = get_the_author_meta( 'display_name', $elemenane_author_id );
if ( '' === (string) $elemenane_name ) {
	$elemenane_name = get_the_author_meta( 'nickname', $elemenane_author_id );
}
$elemenane_bio     = get_the_author_meta( 'user_description', $elemenane_author_id );
$elemenane_website = get_the_author_meta( 'user_url', $elemenane_author_id );
$elemenane_total   = (int) count_user_posts( $elemenane_author_id, 'post', true );

$elemenane_latest = get_posts(
	array(
		'author'                 => $elemenane_author_id,
		'numberposts'            => 1,
		'post_type'              => 'post',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);
?>
<header class="page-header ane-author-hero">
	<div class="ane-author-hero__cover" aria-hidden="true"></div>

	<div class="ane-author-hero__inner">
		<div class="ane-author-hero__avatar">
			<?php
			echo get_avatar(
				$elemenane_author_id,
				128,
				'',
				$elemenane_name,
				array(
					'class'   => 'ane-author-hero__img',
					'loading' => 'eager',
				)
			);
			?>
		</div>

		<div class="ane-author-hero__info">
			<span class="ane-archive-head__kicker"><?php echo esc_html( elemenane_archive_kicker( 'author' ) ); ?></span>
			<h1 class="ane-author-hero__name"><?php echo esc_html( $elemenane_name ); ?></h1>

			<?php if ( '' !== trim( (string) $elemenane_bio ) ) : ?>
				<div class="ane-author-hero__bio"><?php echo wp_kses_post( wpautop( $elemenane_bio ) ); ?></div>
			<?php else : ?>
				<p class="ane-author-hero__bio">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: author name */
							__( 'All articles written by %s.', 'elemenane' ),
							$elemenane_name
						)
					);
					?>
				</p>
			<?php endif; ?>

			<ul class="ane-author-hero__stats">
				<li>
					<strong><?php echo esc_html( number_format_i18n( $elemenane_total ) ); ?></strong>
					<span><?php echo esc_html( 1 === $elemenane_total ? __( 'article', 'elemenane' ) : __( 'articles', 'elemenane' ) ); ?></span>
				</li>
				<?php if ( ! empty( $elemenane_latest ) ) : ?>
					<li>
						<strong><?php echo esc_html( get_the_date( get_option( 'date_format' ), $elemenane_latest[0] ) ); ?></strong>
						<span><?php esc_html_e( 'latest article', 'elemenane' ); ?></span>
					</li>
				<?php endif; ?>
			</ul>

			<?php if ( '' !== (string) $elemenane_website ) : ?>
				<a class="ane-author-hero__link" href="<?php echo esc_url( $elemenane_website ); ?>" target="_blank" rel="nofollow noopener external">
					<?php elemenane_icon( 'link' ); ?>
					<span><?php esc_html_e( 'Visit website', 'elemenane' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>
