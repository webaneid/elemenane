<?php
/**
 * Archive Card
 *
 * Args (get_template_part):
 * - variant (string): featured | standard | wide.
 * - priority (bool):  first card, treat image as LCP candidate.
 *
 * @package elemenane
 * @since   1.1.9
 */

defined( 'ABSPATH' ) || exit;

$elemenane_variant  = ! empty( $args['variant'] ) ? (string) $args['variant'] : 'standard';
$elemenane_priority = ! empty( $args['priority'] );
$elemenane_featured = 'featured' === $elemenane_variant;

$elemenane_img_size = $elemenane_featured ? 'large' : 'medium_large';
$elemenane_sizes    = $elemenane_featured
	? '(max-width: 992px) 100vw, 760px'
	: '(max-width: 639px) 112px, (max-width: 992px) 50vw, 370px';

$elemenane_img_attrs = array(
	'alt'      => '',
	'sizes'    => $elemenane_sizes,
	'decoding' => 'async',
	'loading'  => ( $elemenane_priority && $elemenane_featured ) ? 'eager' : 'lazy',
);
if ( $elemenane_priority && $elemenane_featured ) {
	$elemenane_img_attrs['fetchpriority'] = 'high';
}

$elemenane_cats   = get_the_category();
$elemenane_format = get_post_format();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-card ane-card--' . $elemenane_variant ); ?>>
	<a class="ane-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			echo get_the_post_thumbnail( get_the_ID(), $elemenane_img_size, $elemenane_img_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated, escaped image markup.
		} else {
			$elemenane_fallback = ane_dummy_thumbnail();
			if ( 'video' === $elemenane_format ) {
				$elemenane_video = ane_get_embedded_media( array( 'video', 'iframe' ) );
				if ( $elemenane_video ) {
					$elemenane_fallback = ane_get_youtube_thumbnail( $elemenane_video, 'mq' );
				}
			}
			printf(
				'<img src="%s" alt="" loading="%s" decoding="async">',
				esc_url( $elemenane_fallback ),
				esc_attr( $elemenane_img_attrs['loading'] )
			);
		}

		if ( 'video' === $elemenane_format || 'gallery' === $elemenane_format ) :
			?>
			<span class="ane-card__badge">
				<?php elemenane_icon( 'video' === $elemenane_format ? 'play' : 'gallery' ); ?>
			</span>
		<?php endif; ?>
	</a>

	<div class="ane-card__body">
		<?php if ( ! empty( $elemenane_cats ) ) : ?>
			<a class="ane-card__cat" href="<?php echo esc_url( get_category_link( $elemenane_cats[0]->term_id ) ); ?>">
				<?php echo esc_html( $elemenane_cats[0]->name ); ?>
			</a>
		<?php endif; ?>

		<h2 class="ane-card__title">
			<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() || ! $elemenane_featured ) : ?>
			<div class="ane-card__excerpt"><?php the_excerpt(); ?></div>
		<?php endif; ?>

		<div class="ane-card__meta">
			<a class="ane-card__author" href="<?php echo esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ); ?>">
				<?php echo esc_html( get_the_author() ); ?>
			</a>
			<?php echo wp_kses( ane_load_times_ago(), elemenane_kses_post_with_svg() ); ?>
		</div>
	</div>
</article>
