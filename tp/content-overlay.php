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

/*
 * Optional args from get_template_part():
 * - priority (bool):  first slide above the fold (LCP) -> fetchpriority="high", never lazy.
 * - sizes (string):   `sizes` attribute describing the rendered width.
 */
$is_priority = ! empty( $args['priority'] );
$img_sizes   = ! empty( $args['sizes'] ) ? (string) $args['sizes'] : '(max-width: 767px) 100vw, 800px';

$img_attrs = array(
	'class'    => 'ane-overlay-bg',
	'alt'      => '',
	'sizes'    => $img_sizes,
	'decoding' => 'async',
);
if ( $is_priority ) {
	$img_attrs['loading']       = 'eager';
	$img_attrs['fetchpriority'] = 'high';
} else {
	$img_attrs['loading'] = 'lazy';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-konten-overlay' ); ?>>
	<header class="entry-header">
		<?php
		if ( has_post_thumbnail() ) {
			echo get_the_post_thumbnail( get_the_ID(), 'large', $img_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated, escaped image markup.
		} else {
			printf(
				'<img class="ane-overlay-bg" src="%s" alt="" loading="%s" decoding="async"%s>',
				esc_url( ane_dummy_thumbnail() ),
				$is_priority ? 'eager' : 'lazy',
				$is_priority ? ' fetchpriority="high"' : ''
			);
		}
		?>
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
										esc_html( $category->name )
									);
								}
							}
							?>
						</div>
					</li>
					<li><?php echo wp_kses( ane_load_times_ago(), elemenane_kses_post_with_svg() ); ?></li>
				</ul>
			</div>
		</div>
	</header>
</article>
