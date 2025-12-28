<?php
/**
 * Modern Content Area Styling.
 *
 * Cards, buttons, forms, tables - modern UI components.
 *
 * @package elemenane
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Styles now loaded from scss/_admin-content.scss
 * Compiled into css/admin.css
 *
 * No inline styles needed - all styles externalized to SCSS
 */

/**
 * Convert checkbox to toggle switch.
 *
 * Helper function untuk render toggle switch.
 *
 * @param string $name  Input name.
 * @param bool   $checked Checked state.
 * @param string $label Label text.
 * @return string HTML markup.
 */
function ane_toggle_switch( string $name, bool $checked = false, string $label = '' ) : string {
	$checked_attr = $checked ? ' checked' : '';
	$output = '<label class="ane-toggle-wrapper">';
	$output .= '<span class="ane-toggle">';
	$output .= '<input type="checkbox" name="' . esc_attr( $name ) . '"' . $checked_attr . '>';
	$output .= '<span class="ane-toggle-slider"></span>';
	$output .= '</span>';
	if ( $label ) {
		$output .= '<span>' . esc_html( $label ) . '</span>';
	}
	$output .= '</label>';

	return $output;
}
