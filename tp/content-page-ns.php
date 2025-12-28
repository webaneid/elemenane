<?php
/**
 * Page Content Template (No Sidebar)
 *
 * Displays page content for full-width pages without sidebar.
 * Includes featured image with caption, social sharing, and Facebook comments.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-page ane-page-ns' ); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="post-title general-title">', '</h1>' ); ?>

		<div class="ane-image">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
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
				<img src="https://dummyimage.com/700x394/f1f1f1/545454.jpg&text=Set+Your+Featured+Image+(700x394+pixel)"
				     alt="<?php echo esc_attr( get_the_title() ); ?>">
			<?php endif; ?>
		</div>
	</header>

	<div class="entry-content">
		<div class="share-this">
			<?php echo ane_share_this_post(); ?>
		</div>

		<div class="ane-content">
			<?php the_content(); ?>
		</div>

		<div class="comments-form">
			<?php echo ane_load_facebook_comment(); ?>
		</div>
	</div>
</article>
