<?php
/**
 * Translation Test Script
 *
 * This script tests if translation files are working correctly.
 * Run via CLI: php test-translation.php
 * Or via web: Access this file in browser (if WordPress is loaded)
 *
 * @package elemenane
 * @since 1.0.0
 */

// Check if running in WordPress context
if ( ! function_exists( 'add_action' ) ) {
	echo "This script needs to run in WordPress context.\n";
	echo "Please access this file via browser or include WordPress bootstrap.\n";
	exit;
}

// Force Indonesian locale for testing
add_filter( 'locale', function() {
	return 'id_ID';
} );

// Load text domain
load_theme_textdomain( 'elemenane', __DIR__ );

echo "<h2>Translation Test Results</h2>\n";
echo "<p>Locale: " . get_locale() . "</p>\n";
echo "<hr>\n";

// Test translations
$tests = array(
	'Products'                    => __( 'Products', 'elemenane' ),
	'In Stock'                    => __( 'In Stock', 'elemenane' ),
	'Out of Stock'                => __( 'Out of Stock', 'elemenane' ),
	'New'                         => __( 'New', 'elemenane' ),
	'Chat WhatsApp'               => __( 'Chat WhatsApp', 'elemenane' ),
	'Buy at Branch'               => __( 'Buy at Branch', 'elemenane' ),
	'Or buy on marketplace:'      => __( 'Or buy on marketplace:', 'elemenane' ),
	'Description'                 => __( 'Description', 'elemenane' ),
	'Specifications'              => __( 'Specifications', 'elemenane' ),
	'Features'                    => __( 'Features', 'elemenane' ),
	'Select Branch'               => __( 'Select Branch', 'elemenane' ),
	'WhatsApp'                    => __( 'WhatsApp', 'elemenane' ),
	'Detail'                      => __( 'Detail', 'elemenane' ),
	'Address'                     => __( 'Address', 'elemenane' ),
	'Phone'                       => __( 'Phone', 'elemenane' ),
	'Email'                       => __( 'Email', 'elemenane' ),
	'No products found.'          => __( 'No products found.', 'elemenane' ),
	'No branches found for search' => __( 'No branches found for search', 'elemenane' ),
);

echo "<table border='1' cellpadding='10'>\n";
echo "<tr><th>English (Original)</th><th>Indonesian (Translation)</th><th>Status</th></tr>\n";

foreach ( $tests as $original => $translated ) {
	$status = $original !== $translated ? '✅ OK' : '❌ NOT TRANSLATED';
	echo "<tr>";
	echo "<td>$original</td>";
	echo "<td><strong>$translated</strong></td>";
	echo "<td>$status</td>";
	echo "</tr>\n";
}

echo "</table>\n";

echo "<hr>\n";
echo "<h3>Summary</h3>\n";
echo "<p>Total strings tested: " . count( $tests ) . "</p>\n";
echo "<p>MO file location: " . __DIR__ . "/elemenane-id_ID.mo</p>\n";
echo "<p>PO file location: " . __DIR__ . "/elemenane-id_ID.po</p>\n";
