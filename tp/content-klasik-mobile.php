<?php
/**
 * Classic Mobile Post Format Template
 *
 * Displays classic post format for mobile view.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'konten-klasik' ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<a href="<?php echo esc_url( get_permalink() ); ?>">
				<div class="img-100">
					<?php the_post_thumbnail( 'thumbnail', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
				</div>
			</a>
		<?php endif; ?>

		<div class="category-list bga-utama">
			<span>
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
			</span>
		</div>
		<div class="icon-content"></div>
	</header>

	<div class="post-content prlm-15">
		<?php the_title( sprintf( '<h3 class="post-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
		<?php echo ane_load_times_ago(); ?>
	</div>
</article>
