<?php
/**
 * Search Form Template
 *
 * Custom search form markup for the theme.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="ane-cari">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="text"
		       class="form-control"
		       placeholder="<?php echo esc_attr_x( 'Looking for something? …', 'placeholder', 'elemenane' ); ?>"
		       value="<?php echo esc_attr( get_search_query() ); ?>"
		       name="s">
		<button class="gradient" type="submit" aria-label="<?php esc_attr_e( 'Search', 'elemenane' ); ?>">
			<i class="ane-search"></i>
		</button>
	</form>
</div>
