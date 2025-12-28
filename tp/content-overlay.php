<?php
/**
 * Overlay Post Format Template
 *
 * Displays post with background image overlay and content positioned over it.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get background image URL
if ( has_post_thumbnail() ) {
	$thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
} else {
	$thumbnail_url = ane_dummy_thumbnail();
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-overlay' ); ?>>
	<header class="entry-header" style="background-image:url(<?php echo esc_url( $thumbnail_url ); ?>)">
		<div class="post-content">
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
										'<a class="post-cat" href="%s">%s</a>',
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
	</header>
</article>
