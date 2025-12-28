<?php
/**
 * Mobile Homepage Template Part
 *
 * Displays news homepage layout optimized for mobile devices.
 * Uses custom query with pagination and alternating content layouts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-landing-page' ); ?>>
	<div class="if-mobile">
		<?php
		// Get current page number
		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = absint( get_query_var( 'paged' ) );
		} elseif ( get_query_var( 'page' ) ) {
			$paged = absint( get_query_var( 'page' ) );
		}

		// Show featured news only on first page
		if ( 1 === $paged ) {
			get_template_part( 'tp/news', 'featured-mobile' );
		}

		// Query posts for mobile homepage
		$mobile_query_args = array(
			'post_type'      => 'post',
			'posts_per_page' => 25,
			'paged'          => $paged,
			'post_status'    => 'publish',
		);
		$mobile_query = new WP_Query( $mobile_query_args );
		?>

		<section class="ane-konten-mobile">
			<?php
			get_template_part( 'page-home-mobile-banner' );

			if ( $mobile_query->have_posts() ) :
				$counter = -1;
				while ( $mobile_query->have_posts() ) :
					$mobile_query->the_post();
					$counter++;

					// Alternate content layouts at specific positions
					if ( 4 === $counter ) {
						get_template_part( 'tp/content', 'overlay' );
					} elseif ( 8 === $counter ) {
						get_template_part( 'tp/content', 'list' );
						get_template_part( 'tp/mobile', 'news-populer' );
					} elseif ( 12 === $counter ) {
						get_template_part( 'tp/content', 'list' );
						get_template_part( 'tp/banner/header' );
					} elseif ( 16 === $counter ) {
						get_template_part( 'tp/content', 'overlay' );
					} elseif ( 20 === $counter ) {
						get_template_part( 'tp/content', 'overlay' );
					} elseif ( 24 === $counter ) {
						get_template_part( 'tp/content', 'overlay' );
					} else {
						get_template_part( 'tp/content', 'list' );
					}

				endwhile;
				wp_reset_postdata();
				?>
			</section>

			<?php
			// Pagination
			if ( $mobile_query->max_num_pages > 1 ) :
				global $wp_query;
				$orig_query = $wp_query;
				$wp_query   = $mobile_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				echo ane_post_pagination();
				$wp_query = $orig_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			endif;

			else :
				?>
				<section class="ane-konten-mobile">
					<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'elemenane' ); ?></p>
				</section>
				<?php
			endif;

			get_template_part( 'page-home-mobile-banner' );
			?>
	</div>
</article>
