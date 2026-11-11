<?php
/**
 * WooCommerce override: product in a loop (shop shortcodes, widgets, blocks that use the classic loop).
 *
 * Renders the same card as the theme's own product archive (tp/content-product.php).
 *
 * @package elemenane
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'ane-pgrid__item', $product ); ?>>
	<?php get_template_part( 'tp/content', 'product', array( 'product_id' => $product->get_id() ) ); ?>
</li>
