<?php
/**
 * No Content Found Template
 *
 * Displayed when no posts are found matching the current query.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-default' ); ?>>
	<header class="entry-header">
		<div class="ane-image">
			<img src="https://dummyimage.com/700x394/f1f1f1/545454.jpg&text=No+Post+Image+Feature" alt="<?php esc_attr_e( 'No posts found', 'elemenane' ); ?>">
		</div>
	</header>

	<div class="post-content">
		<?php if ( is_search() ) : ?>
			<h2><?php esc_html_e( 'No Search Results Found', 'elemenane' ); ?></h2>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'elemenane' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<h2><?php esc_html_e( 'No Posts Found', 'elemenane' ); ?></h2>
			<p><?php esc_html_e( 'No content has been published yet. Please check back later.', 'elemenane' ); ?></p>
		<?php endif; ?>
	</div>
</article>
