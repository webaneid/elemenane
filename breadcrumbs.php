<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="breadcrumb">
    <div class="container">
        <div class="row">
            <div class="col-md-12 prlm-0">
                <?php
                if ( function_exists('yoast_breadcrumb') ) {
                  yoast_breadcrumb();
                }
                ?>
            </div>
        </div>
    </div>
</div>
