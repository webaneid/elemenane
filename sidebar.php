<?php
defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'default-sidebar' ) ) {
	return;
}
?>
<aside id="sticky-sidebar" class="ane-kanan" aria-label="<?php esc_attr_e( 'Sidebar', 'elemenane' ); ?>">
	<div class="right-sidebar">
		<?php dynamic_sidebar( 'default-sidebar' ); ?>
	</div>
</aside>
