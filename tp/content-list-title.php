<?php
/**
 * List Title Only Template
 *
 * Displays post title only in list format (no thumbnail or meta).
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-lis-title' ); ?>>
	<?php
	// Use h3 for front page and home, h2 for other pages
	if ( is_front_page() || is_home() ) {
		the_title( sprintf( '<h3 class="post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
	} else {
		the_title( sprintf( '<h2 class="post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
	}
	?>
</article>
