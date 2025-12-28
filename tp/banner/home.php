<?php
/**
 * Home Banner Template
 *
 * Displays wide banner ads for homepage based on ACF schedule.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$company = get_sub_field( 'ane_iklan_nama' );
$url     = get_sub_field( 'ane_url' );
$title   = get_sub_field( 'ane_iklan_judul' );
$image   = get_sub_field( 'ane_image' );
$start   = ceil( ( strtotime( get_sub_field( 'ane_tanggal_mulai' ) ) - time() ) / ( 60 * 60 * 24 ) );
$end     = ceil( ( strtotime( get_sub_field( 'ane_tanggal_akhir' ) ) - time() ) / ( 60 * 60 * 24 ) );
$today   = ceil( ( strtotime( gmdate( 'Y-m-d' ) ) - time() ) / ( 60 * 60 * 24 ) );
$online  = $end - $today;

// Determine display status
$display = 'none';
if ( 0 === $start || $online >= 0 ) {
	$display = 'block';
}

$banner_text = sprintf(
	/* translators: %s: company name */
	__( 'ad by %s', 'elemenane' ),
	$company
);
?>
<section class="ane-wide-banner" style="display: <?php echo esc_attr( $display ); ?>">
	<div class="container">
		<a href="<?php echo esc_url( $url ); ?>"
		   target="_blank"
		   rel="nofollow noopener"
		   title="<?php echo esc_attr( $title . ' by ' . $company ); ?>">
			<div class="ane-image">
				<img src="<?php echo esc_url( $image ); ?>"
				     alt="<?php echo esc_attr( $title . ' by ' . $company ); ?>"
				     title="<?php echo esc_attr( $title . ' by ' . $company ); ?>">
				<div class="banner-info">
					<?php echo esc_html( $banner_text ); ?>
				</div>
			</div>
		</a>
	</div>
</section>
