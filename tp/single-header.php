<?php
/**
 * Single: entry header (categories, title, lead, byline, share bar, featured image).
 *
 * Args (get_template_part):
 * - image (bool): show the featured image (default true; video / gallery posts show their media in the content).
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_with_image = ! isset( $args['image'] ) || ! empty( $args['image'] );
$elemenane_share      = elemenane_get_share_data();
$elemenane_cats       = get_the_category();
$elemenane_author_id  = (int) get_the_author_meta( 'ID' );
$elemenane_lead       = in_array( $elemenane_share['source'], array( 'seo', 'excerpt' ), true ) ? $elemenane_share['description'] : '';
?>
<header class="entry-header ane-entry">
	<?php if ( ! empty( $elemenane_cats ) ) : ?>
		<div class="ane-entry__cats">
			<?php foreach ( $elemenane_cats as $elemenane_cat ) : ?>
				<a class="ane-entry__cat" href="<?php echo esc_url( get_category_link( $elemenane_cat->term_id ) ); ?>"><?php echo esc_html( $elemenane_cat->name ); ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php the_title( '<h1 class="ane-entry__title post-title">', '</h1>' ); ?>

	<?php if ( '' !== $elemenane_lead ) : ?>
		<p class="ane-entry__lead"><?php echo esc_html( $elemenane_lead ); ?></p>
	<?php endif; ?>

	<div class="ane-entry__meta">
		<a class="ane-entry__author" href="<?php echo esc_url( get_author_posts_url( $elemenane_author_id ) ); ?>">
			<?php echo get_avatar( $elemenane_author_id, 40, '', '' ); ?>
			<span><?php echo esc_html( ane_author_name() ); ?></span>
		</a>
		<span class="ane-entry__item">
			<?php elemenane_icon( 'kalender' ); ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'l, j F Y' ) ); ?></time>
		</span>
		<span class="ane-entry__item">
			<?php elemenane_icon( 'jam' ); ?>
			<?php echo esc_html( ane_reading_time() ); ?>
		</span>
		<span class="ane-entry__item">
			<?php elemenane_icon( 'mata' ); ?>
			<?php echo esc_html( ane_get_views( get_the_ID() ) ); ?>
		</span>
	</div>

	<?php echo wp_kses( ane_share_this_post(), elemenane_kses_post_with_svg() ); ?>

	<?php if ( $elemenane_with_image ) : ?>
		<figure class="ane-entry__figure ane-image">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'medium',
					array(
						'alt'           => get_the_title(),
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
						'sizes'         => elemenane_single_image_sizes(),
					)
				);
				$elemenane_caption = (string) get_post( get_post_thumbnail_id() )->post_excerpt;
				if ( '' !== $elemenane_caption ) {
					echo '<figcaption class="featured-image-caption">' . wp_kses_post( $elemenane_caption ) . '</figcaption>';
				}
			} else {
				// Fallback to a YouTube thumbnail for video embeds.
				$elemenane_video = ane_get_embedded_media( array( 'video', 'iframe' ) );
				if ( $elemenane_video ) {
					printf( '<img src="%s" alt="%s">', esc_url( ane_get_youtube_thumbnail( $elemenane_video, 'mq' ) ), esc_attr( get_the_title() ) );
				}
			}
			?>
		</figure>
	<?php endif; ?>
</header>
