<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Contains the post embed header template
 *
 * When a post is embedded in an iframe, this file is used to create the header output
 * if the active theme does not include a header-embed.php template.
 *
 * @package WordPress
 * @subpackage Theme_Compat
 * @since 4.5.0
 */

if ( ! headers_sent() ) {
	header( 'X-WP-embed: true' );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo wp_get_document_title(); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php
	/**
	 * Prints scripts or data in the embed template head tag.
	 *
	 * @since 4.4.0
	 */
	do_action( 'embed_head' );
	?>
	<style>
		.ane-embed {
			padding: 10px;
			font-size: 14px;
			font-weight: 400;
			border-radius: 4px;
			font-family: "Montserrat", Helvetica, Arial, sans-serif;
			color: #8c8f94;
			background: #fff;
			border: 1px solid #dcdcde;
			box-shadow: 0 1px 1px rgba(0,0,0,.05);
			overflow: auto;
			zoom: 1;
		}
		.ane-embed p.wp-embed-heading {
			font-size: 20px;
			font-weight: 500;
		}
		.ane-embed p.wp-embed-heading a {
			text-decoration: none;
		}
	</style>
</head>
<body <?php body_class(); ?>>
