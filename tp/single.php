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
	<?php get_template_part( 'tp/single', 'header' ); ?>

	<div class="entry-content">
		<div class="ane-content">
			<?php the_content(); ?>
		</div>

		<?php
		echo wp_kses( ane_share_this_post( __( 'Share this article', 'elemenane' ) ), elemenane_kses_post_with_svg() );
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
