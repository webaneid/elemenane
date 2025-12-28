<?php
/**
 * Single Gallery Post Template
 *
 * Displays single gallery post format with content and related posts.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-single single-video ane-kiri' ); ?>>
	<header class="entry-header text-center">
		<div class="ane-category">
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
		<?php
		the_title( '<h1 class="post-title">', '</h1>' );
		echo ane_post_meta_v2();
		?>
	</header>

	<div class="entry-content">
		<div class="ane-content">
			<?php the_content(); ?>
		</div>

		<div class="share-this">
			<?php echo ane_share_this_post(); ?>
		</div>

		<div class="ane-tags">
			<?php
			$tag_list = get_the_tag_list( '<ul><li>', '</li><li>', '</li></ul>' );
			if ( $tag_list ) {
				echo $tag_list;
			}
			?>
		</div>

		<div class="ane-post-nav">
			<?php echo ane_prev_next_post(); ?>
		</div>

		<div class="ane-comment-form">
			<?php echo ane_load_facebook_comment(); ?>
		</div>

		<?php ane_related_posts(); ?>
	</div>
</article>
