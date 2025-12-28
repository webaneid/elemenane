<?php
if ( ! is_active_sidebar( 'default-sidebar' ) ) {
	return;
}
?>
<aside id="sticky-sidebar" class="ane-kanan">
	<div class="right-sidebar sticky-top">
		<?php dynamic_sidebar( 'default-sidebar' ); ?>
	</div>
</aside>
