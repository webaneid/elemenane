<?php
/**
 * Archive Loop
 *
 * Card grid shared by every post archive. Page 1 opens with one featured card; later pages are a
 * uniform grid. Layout is pure CSS (no server-side device detection), so pages stay cacheable.
 *
 * @package elemenane
 * @since   1.1.9
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$elemenane_total_posts  = (int) $wp_query->post_count;
$elemenane_has_featured = ! is_paged() && $elemenane_total_posts >= 3;
// Banners go after complete rows so the grid never has a hole before an ad.
$elemenane_banner_after = $elemenane_has_featured ? array( 5, 11 ) : array( 4, 10 );
$elemenane_cells        = 0; // Cards placed in the current two-column row.
$elemenane_index        = 0;
?>
<div class="ane-grid">
	<?php
	while ( have_posts() ) :
		the_post();
		++$elemenane_index;

		$elemenane_variant = 'standard';
		if ( 1 === $elemenane_index && $elemenane_has_featured ) {
			$elemenane_variant = 'featured';
		} elseif ( $elemenane_index === $elemenane_total_posts && 0 === $elemenane_cells % 2 ) {
			// Last card alone on its row: stretch it across both columns.
			$elemenane_variant = 'wide';
		}

		get_template_part(
			'tp/archive',
			'card',
			array(
				'variant'  => $elemenane_variant,
				'priority' => 1 === $elemenane_index,
			)
		);

		// A featured card fills its own row; other cards add one cell each.
		$elemenane_cells = ( 'featured' === $elemenane_variant ) ? 0 : $elemenane_cells + 1;

		if ( in_array( $elemenane_index, $elemenane_banner_after, true ) && $elemenane_index < $elemenane_total_posts ) {
			ob_start();
			get_template_part( 'tp/banner/header' );
			$elemenane_banner = trim( (string) ob_get_clean() );
			if ( '' !== $elemenane_banner ) {
				echo '<div class="ane-grid__banner">' . wp_kses_post( $elemenane_banner ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Banner template escapes its own output.
			}
		}
	endwhile;
	?>
</div>
