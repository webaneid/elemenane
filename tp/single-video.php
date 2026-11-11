<?php
/**
 * Single Video Post Template
 *
 * Displays single video post format with content and related posts.
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
	</header>

	<div class="entry-content">
		<div class="ane-content">
			<?php the_content(); ?>
		</div>

		<div class="share-this">
			<?php echo wp_kses( ane_share_this_post(), elemenane_kses_post_with_svg() ); ?>
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
