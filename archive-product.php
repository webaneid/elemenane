<?php
/**
 * Product Archive Template (shop / all products)
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'tp/product', 'archive' );
get_footer();
