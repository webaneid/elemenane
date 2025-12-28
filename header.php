<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="webane-wrapper">

<?php
/**
 * Site Header
 *
 * Uses Elementor Theme Builder header if available,
 * otherwise falls back to native theme header.
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	// No Elementor header found, use native theme header
	get_template_part( 'tp/header-asli' );
}
// If elementor_theme_do_location() returns true, Elementor already rendered the header
?>

