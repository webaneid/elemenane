<?php
/**
 * Gallery Post Format Template
 *
 * Displays gallery post format in archive/loop with thumbnail and metadata.
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
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<div class="ane-image">
					<?php the_post_thumbnail( 'thumbnail', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
				</div>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<div class="ane-image">
					<img src="<?php echo esc_url( ane_dummy_thumbnail() ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
				</div>
			</a>
		<?php endif; ?>
	</header>

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

		<div class="hidden-mobile">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
