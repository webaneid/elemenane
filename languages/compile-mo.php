<?php
/**
 * Simple PO to MO Compiler
 *
 * This script compiles .po files to .mo files for WordPress translation.
 * Run this script via CLI: php compile-mo.php
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'This script can only be run from command line.' );
}

/**
 * Compile PO file to MO file
 *
 * @param string $po_file Path to PO file.
 * @param string $mo_file Path to MO file.
 * @return bool True on success, false on failure.
 */
function compile_po_to_mo( $po_file, $mo_file ) {
	if ( ! file_exists( $po_file ) ) {
		echo "Error: PO file not found: $po_file\n";
		return false;
	}

	// Read PO file
	$po_content = file_get_contents( $po_file );
	$lines = explode( "\n", $po_content );

	$translations = array();
	$current_msgid = '';
	$current_msgstr = '';
	$in_msgid = false;
	$in_msgstr = false;

	foreach ( $lines as $line ) {
		$line = trim( $line );

		// Skip comments and empty lines
		if ( empty( $line ) || $line[0] === '#' ) {
			continue;
		}

		// msgid line
		if ( strpos( $line, 'msgid ' ) === 0 ) {
			// Save previous translation
			if ( ! empty( $current_msgid ) && ! empty( $current_msgstr ) ) {
				$translations[ $current_msgid ] = $current_msgstr;
			}

			$current_msgid = substr( $line, 7, -1 ); // Remove 'msgid "' and '"'
			$in_msgid = true;
			$in_msgstr = false;
			continue;
		}

		// msgstr line
		if ( strpos( $line, 'msgstr ' ) === 0 ) {
			$current_msgstr = substr( $line, 8, -1 ); // Remove 'msgstr "' and '"'
			$in_msgid = false;
			$in_msgstr = true;
			continue;
		}

		// Continuation line
		if ( $line[0] === '"' ) {
			$value = substr( $line, 1, -1 ); // Remove quotes
			if ( $in_msgid ) {
				$current_msgid .= $value;
			} elseif ( $in_msgstr ) {
				$current_msgstr .= $value;
			}
		}
	}

	// Save last translation
	if ( ! empty( $current_msgid ) && ! empty( $current_msgstr ) ) {
		$translations[ $current_msgid ] = $current_msgstr;
	}

	// Remove header (empty msgid)
	unset( $translations[''] );

	// Create MO file
	$mo_content = create_mo_content( $translations );
	if ( file_put_contents( $mo_file, $mo_content ) === false ) {
		echo "Error: Could not write MO file: $mo_file\n";
		return false;
	}

	echo "Success: Compiled $po_file to $mo_file\n";
	echo "Translations: " . count( $translations ) . "\n";
	return true;
}

/**
 * Create MO file binary content
 *
 * @param array $translations Associative array of msgid => msgstr.
 * @return string Binary MO file content.
 */
function create_mo_content( $translations ) {
	$count = count( $translations );

	// MO file magic number (little endian)
	$magic = 0x950412de;

	// Build originals and translations tables
	$originals = array();
	$translations_array = array();

	foreach ( $translations as $msgid => $msgstr ) {
		$originals[] = $msgid;
		$translations_array[] = $msgstr;
	}

	// Calculate offsets
	$header_size = 28; // 7 * 4 bytes
	$hash_table_size = 0; // We're not using hash table

	$originals_table_offset = $header_size;
	$translations_table_offset = $originals_table_offset + ( $count * 8 );
	$originals_strings_offset = $translations_table_offset + ( $count * 8 );

	// Build originals strings
	$originals_strings = '';
	$originals_table = '';
	foreach ( $originals as $original ) {
		$length = strlen( $original );
		$offset = $originals_strings_offset + strlen( $originals_strings );
		$originals_table .= pack( 'VV', $length, $offset );
		$originals_strings .= $original . "\0";
	}

	$translations_strings_offset = $originals_strings_offset + strlen( $originals_strings );

	// Build translations strings
	$translations_strings = '';
	$translations_table = '';
	foreach ( $translations_array as $translation ) {
		$length = strlen( $translation );
		$offset = $translations_strings_offset + strlen( $translations_strings );
		$translations_table .= pack( 'VV', $length, $offset );
		$translations_strings .= $translation . "\0";
	}

	// Build header
	$header = pack(
		'VVVVVVV',
		$magic,                       // magic number
		0,                            // file format revision
		$count,                       // number of strings
		$originals_table_offset,      // offset of table with original strings
		$translations_table_offset,   // offset of table with translation strings
		$hash_table_size,             // size of hashing table
		$originals_strings_offset     // offset of hashing table (we use 0)
	);

	return $header . $originals_table . $translations_table . $originals_strings . $translations_strings;
}

// Main execution
$dir = __DIR__;
$po_file = $dir . '/elemenane-id_ID.po';
$mo_file = $dir . '/elemenane-id_ID.mo';

echo "Compiling PO to MO...\n";
echo "PO file: $po_file\n";
echo "MO file: $mo_file\n\n";

compile_po_to_mo( $po_file, $mo_file );
