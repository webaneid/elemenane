<?php
/**
 * Automatic conversion of uploaded images (JPG/JPEG/PNG) to WebP format.
 *
 * Workflow (see ADR-0004):
 * 1. When a file finishes uploading (`wp_handle_upload` / `wp_handle_sideload`),
 *    `.jpg`/`.jpeg`/`.png` files are converted to `.webp` preserving original dimensions.
 * 2. Once the `.webp` file is verified on disk (`filesize > 0`), the original `.jpg`/`.png`
 *    file is removed via `wp_delete_file()`.
 * 3. The path and MIME type returned to WordPress are updated to `image/webp`, so
 *    `wp_generate_attachment_metadata()` generates all intermediate sizes (`thumbnail`,
 *    `medium`, `large`, `kotak`, etc.) in `.webp`.
 *
 * @package ElemenAne
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ELEMENANE_WEBP_QUALITY' ) ) {
	define( 'ELEMENANE_WEBP_QUALITY', 78 );
}

/**
 * Retrieve WebP compression quality (1-100).
 *
 * @return int
 */
function elemenane_get_webp_quality() {
	$quality = (int) apply_filters( 'elemenane_webp_quality', ELEMENANE_WEBP_QUALITY );
	if ( $quality < 1 || $quality > 100 ) {
		return ELEMENANE_WEBP_QUALITY;
	}

	return $quality;
}

/**
 * Set default `WP_Image_Editor` quality for `image/webp` output.
 *
 * @param int    $quality   Default editor quality.
 * @param string $mime_type Target MIME type.
 * @return int
 */
function elemenane_set_webp_editor_quality( $quality, $mime_type ) {
	if ( 'image/webp' === $mime_type ) {
		return elemenane_get_webp_quality();
	}

	return $quality;
}
add_filter( 'wp_editor_set_quality', 'elemenane_set_webp_editor_quality', 10, 2 );

/**
 * Detect whether a PNG file is an Animated PNG (APNG) containing an `acTL` chunk.
 *
 * @param string $file_path Absolute path to PNG file.
 * @return bool
 */
function elemenane_is_animated_png( $file_path ) {
	if ( ! is_readable( $file_path ) ) {
		return false;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$bytes = file_get_contents( $file_path, false, null, 0, 4096 );
	if ( false === $bytes ) {
		return false;
	}

	return false !== strpos( $bytes, 'acTL' );
}

/**
 * Fallback conversion for 8-bit indexed palette PNG to WebP using GD while preserving alpha.
 *
 * @param string $source_path Source PNG path.
 * @param string $dest_path   Destination WebP path.
 * @param int    $quality     WebP quality (1-100).
 * @return bool True if saved and file size > 0.
 */
function elemenane_convert_palette_png_to_webp( $source_path, $dest_path, $quality ) {
	if (
		! function_exists( 'imagecreatefrompng' ) ||
		! function_exists( 'imagepalettetotruecolor' ) ||
		! function_exists( 'imagewebp' )
	) {
		return false;
	}

	$image = @imagecreatefrompng( $source_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( false === $image ) {
		return false;
	}

	imagepalettetotruecolor( $image );
	imagealphablending( $image, true );
	imagesavealpha( $image, true );

	$result = @imagewebp( $image, $dest_path, $quality ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	unset( $image );

	if ( ! $result || ! file_exists( $dest_path ) || filesize( $dest_path ) <= 0 ) {
		if ( file_exists( $dest_path ) ) {
			wp_delete_file( $dest_path );
		}
		return false;
	}

	return true;
}

/**
 * Automatically convert newly uploaded images (`image/jpeg`, `image/png`) to `.webp`
 * and remove the original non-WebP file.
 *
 * @param array<string, mixed> $upload  Upload array (`file`, `url`, `type`, or `error`).
 * @param string               $context Upload context (`upload` or `sideload`).
 * @return array<string, mixed>
 */
function elemenane_convert_upload_to_webp( $upload, $context = 'upload' ) {
	unset( $context );

	if (
		! is_array( $upload ) ||
		! empty( $upload['error'] ) ||
		empty( $upload['file'] ) ||
		empty( $upload['url'] ) ||
		empty( $upload['type'] )
	) {
		return $upload;
	}

	$file_path = (string) $upload['file'];
	$file_url  = (string) $upload['url'];
	$mime_type = (string) $upload['type'];

	/**
	 * Filter list of image MIME types automatically converted to WebP upon upload.
	 *
	 * @param array<int, string> $convertible_mimes List of MIME types.
	 */
	$convertible_mimes = (array) apply_filters(
		'elemenane_webp_convertible_mimes',
		array(
			'image/jpeg',
			'image/jpg',
			'image/png',
		)
	);

	if ( ! in_array( $mime_type, $convertible_mimes, true ) ) {
		return $upload;
	}

	if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
		return $upload;
	}

	if ( 'image/png' === $mime_type && elemenane_is_animated_png( $file_path ) ) {
		return $upload;
	}

	if ( ! wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		return $upload;
	}

	$dir       = dirname( $file_path );
	$path_info = pathinfo( $file_path );
	$base_name = isset( $path_info['filename'] ) ? $path_info['filename'] : '';

	if ( '' === $base_name ) {
		return $upload;
	}

	$webp_filename = wp_unique_filename( $dir, $base_name . '.webp' );
	$webp_path     = trailingslashit( $dir ) . $webp_filename;
	$quality       = elemenane_get_webp_quality();
	$converted     = false;
	$final_path    = $webp_path;

	$editor = wp_get_image_editor( $file_path );
	if ( ! is_wp_error( $editor ) ) {
		$editor->set_quality( $quality );
		$saved = $editor->save( $webp_path, 'image/webp' );

		if (
			! is_wp_error( $saved ) &&
			! empty( $saved['path'] ) &&
			file_exists( $saved['path'] ) &&
			filesize( $saved['path'] ) > 0
		) {
			$converted  = true;
			$final_path = (string) $saved['path'];
		}
	}

	// Fallback for 8-bit palette PNG if WP_Image_Editor fails to save it.
	if ( ! $converted && 'image/png' === $mime_type ) {
		if ( elemenane_convert_palette_png_to_webp( $file_path, $webp_path, $quality ) ) {
			$converted  = true;
			$final_path = $webp_path;
		}
	}

	if ( ! $converted || ! file_exists( $final_path ) || filesize( $final_path ) <= 0 ) {
		return $upload;
	}

	// Match permissions of the directory before deleting the original file.
	$stat = @stat( dirname( $final_path ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	if ( is_array( $stat ) && isset( $stat['mode'] ) ) {
		$perms = $stat['mode'] & 0007777;
		$perms = $perms & 0000666;
		@chmod( $final_path, $perms ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_chmod
	}

	// Delete the original (.jpg/.png) file so only the .webp version remains on disk.
	if ( $final_path !== $file_path ) {
		wp_delete_file( $file_path );
	}

	$final_filename = basename( $final_path );
	$upload['file'] = $final_path;
	$upload['url']  = trailingslashit( dirname( $file_url ) ) . $final_filename;
	$upload['type'] = 'image/webp';

	return $upload;
}
add_filter( 'wp_handle_upload', 'elemenane_convert_upload_to_webp', 10, 2 );
add_filter( 'wp_handle_sideload', 'elemenane_convert_upload_to_webp', 10, 2 );
