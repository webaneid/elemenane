<?php
/**
 * Template Name: Landing Page
 *
 * Custom news landing page template with mobile/desktop detection.
 * This is a standalone template that uses ACF fields and is independent of Elementor.
 * Displays different layouts based on device type (page-home-mobile.php or page-home-desktop.php).
 */
get_header();
?>
<main id="main" class="site-main" role="main">
	<?php
	// Load mobile or desktop homepage based on device detection
	if ( wp_is_mobile() ) {
		get_template_part( 'page-home-mobile' );
	} else {
		get_template_part( 'page-home-desktop' );
	}
	?>
</main>
<?php get_footer();
