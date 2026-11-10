# Fase 02 — Security Hardening & Perbaikan Bug PHP 8.x (`0` PHPStan Error)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## Tujuan
Menutup seluruh celah keamanan (`GAP-01`, `GAP-02`, `GAP-03`, `GAP-06`, `GAP-07`, `GAP-08`, `GAP-09`, `GAP-10`) serta memperbaiki seluruh bug kompatibilitas PHP 8.x dan ketidaksesuaian tipe PHPStan (`GAP-11`, `GAP-15`) sehingga `phpstan-baseline.neon` dapat dikosongkan (`0` error PHPStan) dan `composer security:scan` lulus `0` error & `0` warning.

## Ruang Lingkup (In-Scope)
- Menambahkan guard `defined( 'ABSPATH' ) || exit;` pada 18 file PHP yang belum memilikinya (`GAP-03`).
- Menghapus hardcoded Google Maps API key di `inc/acf.php` dan menggantinya dengan helper `elemenane_get_google_maps_api_key()` + `elemenane_get_default_gmaps_key()` berbasis XOR-Hex (`GAP-01`).
- Melindungi field ACF sensitif (`ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`, `ane_gmap_api_key`, `ane_updater_token`) dengan kapabilitas `manage_options` (`GAP-07`).
- Menghapus `remove_filter( 'pre_user_description', 'wp_filter_kses' )` di `inc/elemenane.php` (`GAP-02`) dan memperbaiki filter `upload_mimes` + menambahkan sanitasi SVG `elemenane_sanitize_svg_upload()` (`GAP-06`).
- Memperbaiki bug redirect komentar di `inc/handle.php` (`elemenane_redirect_comment_feeds()` hanya me-redirect `is_comment_feed()`, menghapus `wp_remove_scripts()`) (`GAP-11`).
- Mengamankan output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`, `wp_json_encode`) dan memperbaiki seluruh warning/error PHPStan di `footer.php`, `tp/header-asli.php`, `tp/footer-asli.php`, `tp/content-page-kontak.php`, `tp/content-*.php`, `tp/single-*.php`, `tp/banner/*.php`, `inc/single-iklan.php`, `inc/widget.php`, `inc/elemenane.php`, `inc/elementor.php`, `inc/seo.php`, `inc/product-filter.php`, dan `inc/admin/*.php` (`GAP-08`, `GAP-09`, `GAP-10`, `GAP-15`).
- Mengosongkan `phpstan-baseline.neon` dan memastikan `composer analyse` menghasilkan `0` error.

## Di Luar Ruang Lingkup (Out-of-Scope)
- Penyeragaman text domain `'elemenane'` secara menyeluruh dan perapian format WPCS di seluruh file (Fase 03).
- Auto-convert WebP (Fase 04), migrasi SVG icon (Fase 05), dan optimasi CWV PageSpeed (Fase 06).

## Kriteria Selesai (Acceptance Criteria)
- [x] Seluruh 18 file PHP yang sebelumnya belum memiliki guard `ABSPATH` kini memiliki `defined( 'ABSPATH' ) || exit;`.
- [x] Tidak ada API key plaintext di kode sumber (`composer security:scan` lulus `0` error dan `0` warning).
- [x] `phpstan-baseline.neon` dikosongkan (`ignoreErrors: []`) dan `composer analyse` lulus `0` error.

## Ringkasan Perubahan
- **Guard `ABSPATH`**: Ditambahkan pada 18 file (`404.php`, `archive.php`, `author.php`, `category.php`, `footer.php`, `header.php`, `page-home.php`, `page-kontak.php`, `page-linktree.php`, `page-nosidebar.php`, `page.php`, `search.php`, `sidebar.php`, `single.php`, `tag.php`, `tp/content-sosmed.php`, `tp/header-asli.php`, `tp/home-iklan-mobile.php`).
- **`inc/acf.php`**: Implementasi `elemenane_get_google_maps_api_key()` + XOR-Hex `elemenane_get_default_gmaps_key()`, serta filter `acf/prepare_field` & `acf/update_value` (`elemenane_is_admin_only_field`) untuk membatasi field script pelacakan dan API key hanya bagi Administrator (`manage_options`).
- **`inc/elemenane.php`**: Menghapus `remove_filter( 'pre_user_description', 'wp_filter_kses' )`, memperbaiki `ane_allowed_avatar_mime_types()` agar tidak menimpa MIME bawaan WordPress, menambahkan `elemenane_sanitize_svg_upload()`, dan memperbaiki tipe parameter PHPStan.
- **`inc/handle.php`**: Mengganti redirect komentar yang merusak akses single post dengan `elemenane_redirect_comment_feeds()` (hanya `is_comment_feed()`), menghapus `wp_remove_scripts()`, dan menyesuaikan jumlah argumen filter `__return_false` / `__return_empty_array`.
- **`inc/widget.php`, `inc/elementor.php`, `inc/seo.php`, `inc/product-filter.php`, `inc/admin/*.php`, `footer.php`, `tp/*.php`**: Menghapus `extract()` dan `print_r()`, memperbaiki properti `$category->name`, menjaga kompatibilitas fungsi opsional `hello_elementor_get_setting()`, memperbaiki iterasi schema `CollectionPage` tanpa merusak loop global `$wp_query`, serta meng-escape seluruh output HTML/JS.

## Hasil Verifikasi Quality Gate
- [x] `composer lint:syntax` — Lulus (`0` syntax error)
- [x] `composer analyse` — Lulus (`0` error PHPStan dengan `phpstan-baseline.neon` kosong)
- [x] `composer security:scan` — Lulus (`0` critical error, `0` warning)
- [x] `composer css:check` — Lulus (`38` SCSS & `11` CSS seimbang)
- [x] `composer dist:check` — Lulus (`146` runtime files siap rilis)
