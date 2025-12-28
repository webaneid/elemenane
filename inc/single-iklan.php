<?php
/**
 * Single Post Ad Injection
 *
 * Automatically injects ads into single post content based on ACF settings.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Insert ad code into single post content.
 *
 * @param string $content Post content.
 * @return string Modified content with ad inserted.
 */
function ane_iklan_single_post_umum( $content ) {
	// Only process on singular posts (not in admin)
	if ( ! is_singular( 'post' ) || is_admin() ) {
		return $content;
	}

	// Initialize ad code as empty
	$ad_code = '';

	// Check if ACF repeater field has ads configured
	if ( have_rows( 'ane_iklan_post', 'option' ) ) {
		while ( have_rows( 'ane_iklan_post', 'option' ) ) {
			the_row();

			$banner = esc_url( get_sub_field( 'ane_image' ) );
			$url    = esc_url( get_sub_field( 'ane_url' ) );
			$nama   = esc_attr( get_sub_field( 'ane_iklan_nama' ) );
			$akhir  = ceil( ( strtotime( get_sub_field( 'ane_tanggal_akhir' ) ) - time() ) / ( 60 * 60 * 24 ) );
			$mulai  = ceil( ( strtotime( get_sub_field( 'ane_tanggal_mulai' ) ) - time() ) / ( 60 * 60 * 24 ) );
			$today  = ceil( ( strtotime( date( 'Y-m-d' ) ) - time() ) / ( 60 * 60 * 24 ) );
			$online = $akhir - $today;

			// Check if ad should be displayed (start date is today or ad is still active)
			if ( $mulai === 0 || $online >= 0 ) {
				$ad_code = '
				<div class="ane-iklan" style="display: block">
					<div class="isi">
						<a href="' . $url . '" target="_blank" rel="nofollow noopener" aria-label="' . $nama . '">
							<div class="ane-image">
								<img src="' . $banner . '" alt="' . $nama . '" title="' . $nama . '">
							</div>
						</a>
						<div class="banner-info">' . esc_html( $nama ) . '</div>
					</div>
				</div>
				';
			}
		}
	}

	// Insert ad after 5th paragraph if ad code exists
	if ( ! empty( $ad_code ) ) {
		return ane_insert_after_paragraph( $ad_code, 5, $content );
	}

	return $content;
}
add_filter( 'the_content', 'ane_iklan_single_post_umum' );

/**
 * Insert content after a specific paragraph.
 *
 * @param string $insertion  Content to insert.
 * @param int    $paragraph_id Paragraph number to insert after.
 * @param string $content    Original content.
 * @return string Modified content.
 */
function ane_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
	$closing_p  = '</p>';
	$paragraphs = explode( $closing_p, $content );

	foreach ( $paragraphs as $index => $paragraph ) {
		if ( trim( $paragraph ) ) {
			$paragraphs[ $index ] .= $closing_p;
		}

		if ( $paragraph_id === $index + 1 ) {
			$paragraphs[ $index ] .= $insertion;
		}
	}

	return implode( '', $paragraphs );
}
