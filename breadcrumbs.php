<?php
/**
 * Breadcrumb bar (Yoast SEO breadcrumbs).
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yoast_breadcrumb' ) ) {
	return;
}
?>
<div class="breadcrumb">
	<div class="container">
		<?php yoast_breadcrumb(); ?>
	</div>
</div>
