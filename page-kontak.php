<?php
/**
 * Template Name: Contact Us
 *
 * Contact page: company details from the theme settings (ACF options), the page content (usually a contact form),
 * a Google map and a link to the branches. Layout lives in tp/contact-page.php.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'tp/contact', 'page' );
get_footer();
