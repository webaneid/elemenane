<?php
/**
 * List Post Format Template
 *
 * Displays post in list format with small square thumbnail and metadata.
 * Falls back to YouTube thumbnail for video posts without featured image.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-lis ane-kgv' ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<div class="ane-image">
					<?php the_post_thumbnail( 'kotak', array( 'alt' => esc_attr( get_the_title() ), 'title' => esc_attr( get_the_title() ) ) ); ?>
				</div>
			</a>
		<?php else : ?>
			<?php
			// Fallback to YouTube thumbnail for video embeds
			$video_url = ane_get_embedded_media( array( 'video', 'iframe' ) );
			$size      = 'mq'; // default, hq, mq, sd, maxres
			$thumbnail = ane_get_youtube_thumbnail( $video_url, $size );
			?>
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<div class="ane-image">
					<img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
				</div>
			</a>
			<div class="icon-content"></div>
		<?php endif; ?>
	</header>

	<div class="entry-content">
		<?php
		// Use h3 for front page and home, h2 for other pages
		if ( is_front_page() || is_home() ) {
			the_title( sprintf( '<h3 class="post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
		} else {
			the_title( sprintf( '<h2 class="post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}
		?>

		<div class="post-meta-info">
			<ul>
				<li>
					<div class="content-category">
						<?php
						$categories = get_the_category();
						if ( ! empty( $categories ) ) {
							foreach ( $categories as $category ) {
								printf(
									'<a href="%s">%s</a>',
									esc_url( get_category_link( $category->term_id ) ),
									esc_html( $category->cat_name )
								);
							}
						}
						?>
					</div>
				</li>
				<li><?php echo ane_load_times_ago(); ?></li>
			</ul>
		</div>
	</div>
</article>
