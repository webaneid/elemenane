<?php
/**
 * Top Header Content Template
 *
 * Displays scrolling news marquee and current date in header top section.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="ane-top">
	<div class="container">
		<div class="ane-top-isi">
			<div class="ane-kiri">
				<div class="marquee-news">
					<marquee class="news-items" scrollamount="4" onmouseover="this.stop()" onmouseout="this.start()">
						<?php
						$recent_posts = get_posts(
							array(
								'numberposts' => 10,
								'post_status' => 'publish',
							)
						);

						if ( ! empty( $recent_posts ) ) {
							foreach ( $recent_posts as $post ) {
								setup_postdata( $post );
								the_title( sprintf( '<div class="item"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></div>' );
							}
							wp_reset_postdata();
						}
						?>
					</marquee>
				</div>
			</div>
			<div class="ane-kanan">
				<i class="ane-kalender"></i> <?php echo esc_html( wp_date( 'l, j F Y' ) ); ?>
			</div>
		</div>
	</div>
</div>
