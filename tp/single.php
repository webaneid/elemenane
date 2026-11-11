<?php
/**
 * Single Post Template
 *
 * Displays single post with featured image, content, and related posts.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-kiri ane-single' ); ?>>
	<header class="entry-header">
		<div class="ane-category">
			<?php
			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					printf(
						'<a class="post-cat" href="%s">%s</a>',
						esc_url( get_category_link( $category->term_id ) ),
						esc_html( $category->name )
					);
				}
			}
			?>
		</div>

		<?php
		the_title( '<h1 class="post-title">', '</h1>' );
		echo wp_kses( ane_post_meta_v2(), elemenane_kses_post_with_svg() );
		?>

		<div class="ane-image">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php
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
				?>
				<?php
				$thumbnail_id = get_post_thumbnail_id();
				$caption      = get_post( $thumbnail_id )->post_excerpt;
				if ( ! empty( $caption ) ) :
					?>
					<span class="featured-image-caption">
						<?php echo wp_kses_post( $caption ); ?>
					</span>
				<?php endif; ?>
			<?php else : ?>
				<?php
				// Fallback to YouTube thumbnail for video embeds
				$video_url = ane_get_embedded_media( array( 'video', 'iframe' ) );
				$size      = 'mq'; // default, hq, mq, sd, maxres
				$thumbnail = ane_get_youtube_thumbnail( $video_url, $size );
				?>
				<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
			<?php endif; ?>
		</div>
	</header>

	<div class="entry-content">
		<div class="share-this">
			<?php echo wp_kses( ane_share_this_post(), elemenane_kses_post_with_svg() ); ?>
		</div>

		<div class="ane-content">
			<?php the_content(); ?>
		</div>

		<?php
		get_template_part( 'tp/single', 'tags' );
		get_template_part( 'tp/single', 'author', array( 'author_id' => (int) get_the_author_meta( 'ID' ) ) );
		get_template_part( 'tp/single', 'nav' );
		?>

		<div class="ane-comment-form">
			<?php echo wp_kses_post( ane_load_facebook_comment() ); ?>
		</div>

		<?php ane_related_posts(); ?>
	</div>
</article>
<?php get_sidebar(); ?>
