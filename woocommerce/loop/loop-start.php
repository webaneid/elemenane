<?php
/**
 * WooCommerce override: product loop start. Adds the theme's grid class.
 *
 * @package elemenane
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;
?>
<ul class="products ane-pgrid columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>">
