# Fase 03 — Eksplisit `require_once`, Penyeragaman Text Domain `'elemenane'`, & Standar WPCS (`0` PHPCS Error)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## Tujuan
Menghapus pemuatan modul dinamis `glob()` di `functions.php` (`GAP-14`), menyeragamkan seluruh text domain ke `'elemenane'` (`GAP-16`), menghapus file `.DS_Store` dari Git (`GAP-18`), serta merapikan seluruh file PHP agar lulus `composer lint:phpcs` dengan `0` error (`GAP-17`).

## Ruang Lingkup (In-Scope)
- Mengganti `glob( get_template_directory() . '/inc/*.php' )` dan `glob( get_template_directory() . '/inc/**/*.php' )` di `functions.php` dengan daftar `require_once` eksplisit (`GAP-14`).
- Menyeragamkan seluruh text domain warisan (`'newsane'`, `'webane'`, `'roots'`, `'autoane'`) ke `'elemenane'` (`GAP-16`).
- Menghapus `.DS_Store` dan `css/.DS_Store` (`GAP-18`).
- Menjalankan `phpcbf` dan memperbaiki pelanggaran WPCS yang tersisa hingga `composer lint:phpcs` menghasilkan `0` error (`GAP-17`).

## Kriteria Selesai (Acceptance Criteria)
- [x] `functions.php` memuat seluruh file `inc/` dan `inc/admin/` menggunakan `require_once` eksplisit tanpa `glob()`.
- [x] Seluruh fungsi i18n menggunakan text domain `'elemenane'`.
- [x] `.DS_Store` dihapus dari repositori.
- [x] `composer lint:phpcs`, `composer analyse`, dan `composer security:scan` lulus `0` error.

## Ringkasan Perubahan
- **`functions.php`**: Mengganti `glob()` dengan array `$elemenane_includes` eksplisit untuk 30 file modul `inc/` dan `inc/admin/`.
- **Text Domain `'elemenane'`**: Menyeragamkan seluruh panggilan i18n di `inc/admin/*.php` dan `embed-404.php` ke `'elemenane'`.
- **Format & Keamanan WPCS**: Memformat 49 file dengan `phpcbf` (1.901 perbaikan otomatis) dan memperbaiki seluruh sisa temuan `OutputNotEscaped`, `ValidatedSanitizedInput`, `PreparedSQL`, `gmdate()`, dan `snake_case` hingga `phpcs` menghasilkan `0` error di seluruh repositori.
- **Pembersihan `.DS_Store`**: Menghapus `.DS_Store` dan `css/.DS_Store`.

## Hasil Verifikasi Quality Gate
- [x] `composer lint:syntax` — Lulus (`0` syntax error)
- [x] `composer lint:phpcs` — Lulus (`0` error di seluruh 73 file PHP)
- [x] `composer analyse` — Lulus (`0` error PHPStan)
- [x] `composer security:scan` — Lulus (`0` error, `0` warning)
- [x] `composer css:check` & `composer dist:check` — Lulus
