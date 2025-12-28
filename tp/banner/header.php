<?php
/**
 * Header Banner Template
 *
 * Displays wide banner ads for header section based on ACF schedule.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! have_rows( 'ane_iklan_header', 'option' ) ) {
	return;
}

while ( have_rows( 'ane_iklan_header', 'option' ) ) :
	the_row();

	$banner = get_sub_field( 'ane_image' );
	$title  = get_sub_field( 'ane_iklan_judul' );
	$url    = get_sub_field( 'ane_url' );
	$name   = get_sub_field( 'ane_iklan_nama' );
	$end    = ceil( ( strtotime( get_sub_field( 'ane_tanggal_akhir' ) ) - time() ) / ( 60 * 60 * 24 ) );
	$start  = ceil( ( strtotime( get_sub_field( 'ane_tanggal_mulai' ) ) - time() ) / ( 60 * 60 * 24 ) );
	$today  = ceil( ( strtotime( gmdate( 'Y-m-d' ) ) - time() ) / ( 60 * 60 * 24 ) );
	$online = $end - $today;

	// Show banner if started today or still active
	if ( 0 === $start || $online >= 0 ) :
		?>
		<div class="ane-wide-banner" style="display: block">
			<a href="<?php echo esc_url( $url ); ?>"
			   target="_blank"
			   rel="nofollow noopener"
			   title="<?php echo esc_attr( $title ); ?>">
				<div class="ane-image">
					<img src="<?php echo esc_url( $banner ); ?>"
					     alt="<?php echo esc_attr( $title ); ?>"
					     title="<?php echo esc_attr( $title ); ?>">
					<div class="banner-info"><?php echo esc_html( $name ); ?></div>
				</div>
			</a>
		</div>
		<?php
	endif;
endwhile;
