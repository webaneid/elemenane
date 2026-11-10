# Fase 04 — Auto-Convert Upload Gambar ke WebP (`inc/webp-converter.php`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## Tujuan
Menambahkan modul auto-convert upload gambar JPG/JPEG/PNG ke format WebP (`inc/webp-converter.php`) dengan kualitas default `ELEMENANE_WEBP_QUALITY = 78` (`GAP-19`, sesuai [ADR-0004](../decisions/adr-0004-auto-convert-webp.md) dan referensi `newsane`).

## Ruang Lingkup (In-Scope)
- Membuat `inc/webp-converter.php` dengan fungsi ber-prefix `elemenane_`:
  - `elemenane_get_webp_quality()`
  - `elemenane_set_webp_editor_quality()` pada filter `wp_editor_set_quality`
  - `elemenane_is_animated_png()` untuk melewati file APNG
  - `elemenane_convert_palette_png_to_webp()` sebagai fallback GD untuk PNG 8-bit palette
  - `elemenane_convert_upload_to_webp()` pada filter `wp_handle_upload` & `wp_handle_sideload`
- Mendaftarkan `/inc/webp-converter.php` di daftar `$elemenane_includes` pada `functions.php`.

## Kriteria Selesai (Acceptance Criteria)
- [x] `inc/webp-converter.php` dibuat sesuai standar keamanan & WPCS/PHPStan.
- [x] Terdaftar di `functions.php`.
- [x] Lulus seluruh Quality Gate (`lint:syntax`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check`).

## Hasil Verifikasi Quality Gate
- [x] `composer lint:syntax` — Lulus (`0` error)
- [x] `composer lint:phpcs` — Lulus (`0` error)
- [x] `composer analyse` — Lulus (`0` error PHPStan)
- [x] `composer security:scan` — Lulus (`0` error, `0` warning)
- [x] `composer css:check` & `composer dist:check` — Lulus
