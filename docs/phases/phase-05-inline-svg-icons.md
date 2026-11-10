# Fase 05 — Migrasi Icon `FontAne` ke Inline SVG (`inc/icons.php`) & Penghapusan `FontAne.css`

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## Tujuan
Mengganti seluruh icon font `FontAne` (`css/FontAne.css` + `fonts/FontAne.*` + `<i class="ane-*">` / `<i class="fa-*">`) dengan helper Inline SVG `elemenane_icon()` di `inc/icons.php` (`GAP-20`) untuk menghilangkan request HTTP font ikon eksternal dan memperbaiki FCP/CLS.

## Ruang Lingkup (In-Scope)
- Membuat `inc/icons.php` (`elemenane_get_icon_svg()`, `elemenane_icon()`, `elemenane_kses_svg_allowed_html()`) dan mendaftarkannya di `functions.php`.
- Mengganti seluruh `<i class="ane-*">` dan `<i class="fa-*">` di seluruh template PHP (`footer.php`, `searchform.php`, `single-branch.php`, `single-product.php`, `tp/*.php`, `inc/*.php`) serta `js/carousel-init.js` dan SCSS (`scss/_global.scss`, `scss/_header.scss`, `scss/_post.scss`, `scss/_owlcarousel.scss`, dll.).
- Menghapus enqueue `FontAne` (`css/FontAne.css`), menghapus preload `ane_preload_icon_font()` di `inc/elemenane.php`, serta menghapus file `css/FontAne.css` dan direktori `fonts/`.
- Mengompilasi ulang seluruh 5 entrypoint SCSS ke `.css` dan `.min.css`.

## Kriteria Selesai (Acceptance Criteria)
- [x] `inc/icons.php` tersedia dan terdaftar di `functions.php`.
- [x] Seluruh ikon di template dan JS menggunakan SVG inline (`elemenane_icon()`).
- [x] `css/FontAne.css` dan `fonts/` dihapus serta tidak ada referensi yang tersisa.
- [x] Seluruh SCSS dikompilasi ulang dan lulus `composer css:check`, `composer lint:phpcs`, `composer analyse`, `composer security:scan`, dan `composer dist:check`.

## Catatan Implementasi
- Glyph `FontAne.woff2` dikonversi (fontTools) menjadi path SVG statis di `inc/icons.php` (viewBox `0 0 1024 1024`); `linkedin` & `threads` memakai path 24x24 yang sebelumnya sudah ada di SCSS.
- Hiasan pseudo-element (`::before`/`::after`) memakai mixin `ane-glyph-mask()` (`scss/_icons.scss`) dengan data-URI dari `scss/_icon-glyphs.scss` (hasil generate); warna mengikuti `currentColor`.
- `ane-phone` (tidak ada di font) dipetakan ke `telepon`; `ane-circle-left/right` pada Owl nav (tidak ada di font) diganti chevron SVG di `js/carousel-init.js`.
- String yang memuat ikon dan melewati `wp_kses_post()` kini memakai `wp_kses( ..., elemenane_kses_post_with_svg() )` karena `wp_kses_post()` membuang `<svg>`.
- Selector `i { ... }` di SCSS diperluas menjadi `i, .ane-icon { ... }`; `i.ane-<brand>` di linktree menjadi `.ane-icon--<brand>`.
- Ikon `tiktok`, `telegram` tidak ada di font lama (sebelumnya juga kosong) sehingga `elemenane_icon()` mengembalikan string kosong; tambahkan path-nya di `inc/icons.php` bila diperlukan.
- `.ane-close-x` (`scss/_bottomchat.scss`) sudah berupa CSS mask SVG, tidak bergantung pada FontAne, jadi dibiarkan.

## Hasil Verifikasi Quality Gate
- [x] `lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse` (0 error), `security:scan` (0/0), `css:check`, `dist:check` (145 file) — lulus
- [x] Smoke test beranda lokal: SVG ter-render, tidak ada referensi `FontAne`, tanpa PHP error.
