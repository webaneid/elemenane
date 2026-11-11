# Fase 11: Refactoring Blok di Bawah Artikel Single (Tags, Author Box, Post Nav, Related)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

## 1. Masalah Lama
- `.ane-tags`, `.ane-post-nav`, `.ane-related`, `.ane-author(-isi)` ditulis tiga kali (`tp/single.php`, `single-video.php`, `single-gallery.php`) dengan CSS terpisah dan gaya judul berbeda-beda (`.ane-related h1` bar biru).
- Kotak penulis ditempel lewat filter `the_content` sehingga ikut terkena gaya `.ane-content` (`p { margin-bottom: 20px !important }`) dan memakai ikon dekoratif `z-index:-1`.
- Post nav: panah SVG hilang karena `wp_kses_post()` membuang `<svg>`; tampilan mobile memakai margin negatif.
- Related memakai `wp_is_mobile()` di server dan template konten beranda.

## 2. Desain Baru
- **Satu gaya judul** `.ane-section-title` = mixin `ane-widget-title` (dipindah ke `_mixins.scss`) sama dengan judul widget sidebar; dipakai Tags dan Related.
- **Tags** (`tp/single-tags.php`): pil outline dengan `#` aksen, hover terisi.
- **Author box** (`tp/single-author.php`, kelas `.ane-author-box*`): panel gradasi lembut, avatar bulat, "Ditulis oleh", nama (tautan), bio, tombol "Lihat semua N artikel", tautan website. Rata tengah di mobile. Tetap hanya tampil bila penulis punya bio.
- **Post nav** (`tp/single-nav.php`): dua kartu (prev / next) dengan label, thumbnail 64px, judul 3 baris; satu kolom di mobile; jika hanya ada "next" tetap di kanan.
- **Related** (`ane_related_posts()`): memakai kartu arsip (`tp/archive-card.php`, varian standard/wide, judul `h3`) dalam `.ane-grid`; 4 artikel (sebelumnya 5) agar grid genap. Tanpa `wp_is_mobile()`.
- Ketiga template single memakai partial yang sama.

## 3. Kompatibilitas
- `ane_prev_next_post()` dan `ane_author_info_box()` tetap ada. Filter `the_content` untuk author box tetap aktif untuk template Elementor; `single.php` native melepasnya (`remove_filter`) dan merender box lewat partial.
- `ane_render_nav_post()` dihapus (hanya dipakai `ane_prev_next_post()`).

## 4. File
- Baru: `scss/_single-extras.scss`, `tp/single-tags.php`, `tp/single-nav.php`, `tp/single-author.php`.
- Diubah: `single.php`, `tp/single.php`, `tp/single-video.php`, `tp/single-gallery.php`, `tp/archive-card.php` (arg `heading`), `inc/elemenane.php`, `scss/_post.scss` (blok lama dihapus), `scss/_anecontent.scss`, `scss/_sidebar.scss`, `scss/_mixins.scss`, `scss/main.scss`, `languages/*`, `css/main(.min).css`.

## 5. Checklist
- [x] Partial PHP + refactor fungsi
- [x] SCSS + kompilasi
- [x] Verifikasi visual desktop dan 375px (tags, author, nav, related)
- [x] Quality gate
- [x] Dokumentasi + commit

## 6. Belum Diverifikasi
- `single-video.php` / `single-gallery.php` (pakai partial yang sama, tapi tidak ada post format tersebut di data lokal).
- Post tanpa tag (blok Tags dan Related disembunyikan), post tanpa prev/next, dan penulis tanpa bio.
