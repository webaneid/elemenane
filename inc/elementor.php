<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Helper function to check if Header & Footer Experiment is Active/Inactive
 */
function ane_header_footer_experiment_active() {
	// If Elementor is not active, return false
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	// Backwards compat.
	if ( ! method_exists( \Elementor\Plugin::$instance->experiments, 'is_feature_active' ) ) {
		return false;
	}

	return (bool) ( \Elementor\Plugin::$instance->experiments->is_feature_active( 'hello-theme-header-footer' ) );
}

/**
 * Helper function to translate the footer layout setting into a class name.
 *
 * @return string
 */
function ane_get_footer_layout_class() {
	$footer_layout = hello_elementor_get_setting( 'hello_footer_layout' );

	$layout_classes = [];

	if ( 'inverted' === $footer_layout ) {
		$layout_classes[] = 'footer-inverted';
	} elseif ( 'stacked' === $footer_layout ) {
		$layout_classes[] = 'footer-stacked';
	}

	$footer_width = hello_elementor_get_setting( 'hello_footer_width' );

	if ( 'full-width' === $footer_width ) {
		$layout_classes[] = 'footer-full-width';
	}

	if ( hello_elementor_get_setting( 'hello_footer_copyright_display' ) && '' !== hello_elementor_get_setting( 'hello_footer_copyright_text' ) ) {
		$layout_classes[] = 'footer-has-copyright';
	}

	return implode( ' ', $layout_classes );
}

// Function to get Elementor colors
function get_elementor_colors() {
    // For exporting global settings
    $default_post_id = get_option('elementor_active_kit');
    $global_data = get_post_meta($default_post_id, '_elementor_page_settings', true);

    $colors = [];

    // Check if global_data is valid
    if (!is_array($global_data)) {
        return $colors;
    }

    // Adding system colors
    if (!empty($global_data['system_colors']) && is_array($global_data['system_colors'])) {
        foreach ($global_data['system_colors'] as $color) {
            $colors[] = [
                'name'  => $color['title'],
                'slug'  => sanitize_title($color['title']),
                'color' => $color['color'],
            ];
        }
    }

    // Adding custom colors
    if (!empty($global_data['custom_colors']) && is_array($global_data['custom_colors'])) {
        foreach ($global_data['custom_colors'] as $color) {
            $colors[] = [
                'name'  => $color['title'],
                'slug'  => sanitize_title($color['title']),
                'color' => $color['color'],
            ];
        }
    }

    return $colors;
}
// Add custom colors to Gutenberg
function add_elementor_colors_to_gutenberg() {
    add_theme_support('editor-color-palette', get_elementor_colors());
}
add_action('after_setup_theme', 'add_elementor_colors_to_gutenberg');

add_action('save_post', function ($post_id, $post, $update) {
    // Check if the post is an instance of Elementor's Kit
    if ('elementor_library' === get_post_type($post_id) && 'kit' === get_post_meta($post_id, '_elementor_template_type', true)) {
        $meta = get_post_meta($post_id, '_elementor_page_settings', true);

        // Handle custom colors
        if (!empty($meta['custom_colors'])) {
            foreach ($meta['custom_colors'] as &$custom_color) {
                // Replace the hyphens inside of titles with underscores to ensure consistency
                $custom_color['_id'] = str_replace('-', '_', sanitize_title($custom_color['title']));
            }
        }

        // Update post meta
        update_post_meta($post_id, '_elementor_page_settings', $meta);
    }
}, 10, 3);

/**
 * Convert hex color to RGB values.
 *
 * @param string $colour Hex color code (with or without #).
 * @return string|false RGB values as "r, g, b" or false on error.
 * @since 1.0.0
 */
function ane_hex2rgb( $colour ) {
	if ( $colour[0] === '#' ) {
		$colour = substr( $colour, 1 );
	}

	if ( strlen( $colour ) === 6 ) {
		list( $r, $g, $b ) = array(
			$colour[0] . $colour[1],
			$colour[2] . $colour[3],
			$colour[4] . $colour[5],
		);
	} elseif ( strlen( $colour ) === 3 ) {
		list( $r, $g, $b ) = array(
			$colour[0] . $colour[0],
			$colour[1] . $colour[1],
			$colour[2] . $colour[2],
		);
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	return "$r, $g, $b";
}
/**
 * Output Elementor Kit colors as CSS custom properties.
 *
 * Syncs Elementor global colors to :root CSS variables for use throughout the theme.
 * Includes both hex and RGB values for transparency support.
 *
 * @since 1.0.0
 * @return void
 */
function ane_add_elementor_colors_to_root() {
	// Define the default Elementor global color keys
	$default_post_id = get_option( 'elementor_active_kit' );
	$global_data = get_post_meta( $default_post_id, '_elementor_page_settings', true );

	// Check if global_data is valid
	if ( ! is_array( $global_data ) ) {
		return;
	}

	echo '<style>:root {';

	if ( ! empty( $global_data['system_colors'] ) && is_array( $global_data['system_colors'] ) ) {
		foreach ( $global_data['system_colors'] as $color ) {
			$colors[] = array(
				'name'  => $color['title'],
				'slug'  => sanitize_title( $color['title'] ),
				'color' => $color['color'],
			);
			$colorname = sanitize_title( $color['title'] );
			if ( 'primary' === $colorname ) {
				$colorname = 'utama';
			}
			if ( 'secondary' === $colorname ) {
				$colorname = 'utama-2';
			}
			if ( 'accent' === $colorname ) {
				$colorname = 'alternatif';
			}
			echo esc_html( '--ane-warna-' . $colorname . ':' . $color['color'] . ';' );
			echo esc_html( '--ane-warna-' . $colorname . '-rgb: ' . ane_hex2rgb( $color['color'] ) . ';' );
		}
	}

	if ( ! empty( $global_data['custom_colors'] ) && is_array( $global_data['custom_colors'] ) ) {
		foreach ( $global_data['custom_colors'] as $color ) {
			$colors[] = array(
				'name'  => $color['title'],
				'slug'  => sanitize_title( $color['title'] ),
				'color' => $color['color'],
			);
			echo esc_html( '--ane-warna-' . sanitize_title( $color['title'] ) . ':' . $color['color'] . ';' );
			echo esc_html( '--ane-warna-' . sanitize_title( $color['title'] ) . '-rgb: ' . ane_hex2rgb( $color['color'] ) . ';' );
		}
	}

	echo '}</style>';
}
add_action( 'wp_head', 'ane_add_elementor_colors_to_root' );

/**
 * Output Elementor Kit colors as CSS custom properties for admin dashboard.
 *
 * Syncs Elementor global colors to :root CSS variables for use in admin area.
 * Uses the same naming convention as _tokens.scss for compatibility.
 * Maps Elementor system colors to admin color scheme.
 *
 * @since 1.0.0
 * @return void
 */
function ane_add_elementor_colors_to_admin() {
	// Get Elementor Kit settings
	$default_post_id = get_option( 'elementor_active_kit' );
	$global_data = get_post_meta( $default_post_id, '_elementor_page_settings', true );

	// Check if global_data is valid
	if ( ! is_array( $global_data ) ) {
		return;
	}

	echo '<style>:root {';

	// Default color mappings from Elementor to admin tokens
	$color_map = array(
		'primary'   => 'primary',
		'secondary' => 'secondary',
		'text'      => 'body',
		'accent'    => 'accent',
	);

	if ( ! empty( $global_data['system_colors'] ) && is_array( $global_data['system_colors'] ) ) {
		foreach ( $global_data['system_colors'] as $color ) {
			$colorname = sanitize_title( $color['title'] );

			// Map Elementor system colors to admin color variables
			if ( isset( $color_map[ $colorname ] ) ) {
				$admin_color_name = $color_map[ $colorname ];
				echo esc_html( '--ane-color-' . $admin_color_name . ':' . $color['color'] . ';' );
				echo esc_html( '--ane-color-' . $admin_color_name . '-rgb: ' . ane_hex2rgb( $color['color'] ) . ';' );
			}
		}
	}

	// Add custom colors from Elementor
	if ( ! empty( $global_data['custom_colors'] ) && is_array( $global_data['custom_colors'] ) ) {
		foreach ( $global_data['custom_colors'] as $color ) {
			$slug = sanitize_title( $color['title'] );
			echo esc_html( '--ane-color-' . $slug . ':' . $color['color'] . ';' );
			echo esc_html( '--ane-color-' . $slug . '-rgb: ' . ane_hex2rgb( $color['color'] ) . ';' );
		}
	}

	// Add static utility colors (light, dark, white, black)
	// These are fallback colors that maintain consistency across admin
	echo esc_html( '--ane-color-light:#f1f0ea;' );
	echo esc_html( '--ane-color-light-rgb:241, 240, 234;' );
	echo esc_html( '--ane-color-dark:#1a1a1a;' );
	echo esc_html( '--ane-color-dark-rgb:26, 26, 26;' );
	echo esc_html( '--ane-color-white:#ffffff;' );
	echo esc_html( '--ane-color-white-rgb:255, 255, 255;' );
	echo esc_html( '--ane-color-black:#000000;' );
	echo esc_html( '--ane-color-black-rgb:0, 0, 0;' );

	echo '}</style>';
}
add_action( 'admin_head', 'ane_add_elementor_colors_to_admin' );

/**
 * Output Elementor Kit typography as CSS custom properties.
 *
 * Syncs Elementor global typography to :root CSS variables including
 * font-family, font-weight, font-size, line-height, and letter-spacing.
 *
 * @since 1.0.0
 * @return void
 */
function ane_add_elementor_typography_to_root() {
	// Get the default post ID for the active Elementor kit
	$default_post_id = get_option( 'elementor_active_kit' );
	$meta = get_post_meta( $default_post_id, '_elementor_page_settings', true );

	// Check if meta is valid
	if ( ! is_array( $meta ) ) {
		return;
	}

	// Check if system_typography exists and is an array
	if ( empty( $meta['system_typography'] ) || ! is_array( $meta['system_typography'] ) ) {
		return;
	}

	// Start outputting the CSS variables
	echo '<style>:root {';

	foreach ( $meta['system_typography'] as $index => $typography ) {
		$css_variable_name = '--ane-typography-';
		$title_slug = sanitize_title( $typography['title'] );

		if ( isset( $typography['typography_typography'] ) ) {
			if ( isset( $typography['typography_font_family'] ) ) {
				echo $css_variable_name . $title_slug . "-font-family:'" . esc_attr( $typography['typography_font_family'] ) . "';";
			}
			if ( isset( $typography['typography_font_weight'] ) ) {
				echo $css_variable_name . $title_slug . '-font-weight:' . esc_attr( $typography['typography_font_weight'] ) . ';';
			}
			if ( isset( $typography['typography_text_font_size'] ) ) {
				echo $css_variable_name . $title_slug . '-font-size:' . esc_attr( $typography['typography_text_font_size'] ) . ';';
			}
		}

		// Letter Spacing (if defined)
		if ( isset( $typography['typography_letter_spacing'] ) && is_array( $typography['typography_letter_spacing'] ) ) {
			$spacing_data = $typography['typography_letter_spacing'];

			// Check if 'size' and 'unit' keys exist
			if ( isset( $spacing_data['size'], $spacing_data['unit'] ) ) {
				$size = floatval( $spacing_data['size'] ); // Ensure the size is a valid number
				$unit = sanitize_text_field( $spacing_data['unit'] ); // Sanitize the unit

				echo $css_variable_name . $title_slug . '-letter-spacing:' . $size . $unit . ';';
			}
		}

		// Font size (responsive if array)
		if ( isset( $typography['typography_font_size'] ) && is_array( $typography['typography_font_size'] ) ) {
			$font_size = $typography['typography_font_size'];

			// Check if 'size' and 'unit' keys exist
			if ( isset( $font_size['size'], $font_size['unit'] ) ) {
				$size = floatval( $font_size['size'] ); // Ensure the size is a valid number
				$unit = sanitize_text_field( $font_size['unit'] ); // Sanitize the unit

				echo $css_variable_name . $title_slug . '-font-size:' . $size . $unit . ';';
			}
		}

		// Line height
		if ( isset( $typography['typography_line_height'] ) && is_array( $typography['typography_line_height'] ) ) {
			$line_height = $typography['typography_line_height'];

			// Check if 'size' and 'unit' keys exist
			if ( isset( $line_height['size'], $line_height['unit'] ) ) {
				$size = floatval( $line_height['size'] ); // Ensure the size is a valid number
				$unit = sanitize_text_field( $line_height['unit'] );
				if ( 'custom' === $unit ) {
					$unitnya = '';
				} else {
					$unitnya = $unit;
				}

				echo $css_variable_name . $title_slug . '-line-height:' . $size . $unitnya . ';';
			}
		}
	}

	// Close the <style> tag
	echo '}</style>';
}
add_action( 'wp_head', 'ane_add_elementor_typography_to_root' );

/**
 * Enqueue Google Fonts from Elementor Kit typography settings for front-end.
 *
 * Note: Elementor's Google Fonts are only loaded in the editor/dashboard.
 * This function ensures the same fonts are available on the front-end by reading
 * the Elementor Kit typography settings and loading the fonts from Google Fonts API.
 *
 * The function collects all unique Google Fonts with their weights and loads them
 * efficiently using Google Fonts API v2 with font-display: swap for performance.
 *
 * @since 1.0.0
 * @return void
 */
function ane_enqueue_google_fonts() {
	// Only load on front-end, not in admin/editor
	if ( is_admin() ) {
		return;
	}

	$default_post_id = get_option( 'elementor_active_kit' );
	$meta = get_post_meta( $default_post_id, '_elementor_page_settings', true );

	if ( ! is_array( $meta ) || empty( $meta['system_typography'] ) ) {
		return;
	}

	$google_fonts = array();

	foreach ( $meta['system_typography'] as $typography ) {
		if ( ! isset( $typography['typography_typography'] ) || ! isset( $typography['typography_font_family'] ) ) {
			continue;
		}

		$font_family = $typography['typography_font_family'];
		$system_fonts = array( 'Arial', 'Helvetica', 'Times New Roman', 'Georgia', 'Verdana', 'Courier New' );

		if ( in_array( $font_family, $system_fonts, true ) ) {
			continue;
		}

		$font_weight = isset( $typography['typography_font_weight'] ) ? $typography['typography_font_weight'] : '400';

		if ( ! isset( $google_fonts[ $font_family ] ) ) {
			$google_fonts[ $font_family ] = array();
		}

		if ( ! in_array( $font_weight, $google_fonts[ $font_family ], true ) ) {
			$google_fonts[ $font_family ][] = $font_weight;
		}
	}

	// If no Google Fonts found, exit early
	if ( empty( $google_fonts ) ) {
		return;
	}

	foreach ( $google_fonts as $font_family => $weights ) {
		sort( $weights );
		$weights_string = implode( ';', $weights );
		$font_url = add_query_arg(
			array(
				'family' => urlencode( $font_family . ':wght@' . $weights_string ),
				'display' => 'swap',
			),
			'https://fonts.googleapis.com/css2'
		);

		$handle = 'ane-gfont-' . sanitize_title( $font_family );
		wp_enqueue_style( $handle, $font_url, array(), null );
	}
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_google_fonts' );