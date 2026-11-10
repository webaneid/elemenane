<?php
/**
 * Search Form Template
 *
 * Pill-style search form shared with the Search block (see scss/_sidebar.scss).
 *
 * @package elemenane
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_search_id = wp_unique_id( 'ane-searchform-' );
?>
<div class="ane-cari">
	<form role="search" method="get" class="search-form ane-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="<?php echo esc_attr( $elemenane_search_id ); ?>"><?php esc_html_e( 'Search for:', 'elemenane' ); ?></label>
		<span class="ane-searchform__icon"><?php elemenane_icon( 'search' ); ?></span>
		<input type="search"
				id="<?php echo esc_attr( $elemenane_search_id ); ?>"
				class="ane-searchform__input"
				placeholder="<?php echo esc_attr_x( 'Looking for something? …', 'placeholder', 'elemenane' ); ?>"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				name="s">
		<button class="ane-searchform__btn" type="submit"><?php esc_html_e( 'Search', 'elemenane' ); ?></button>
	</form>
</div>
