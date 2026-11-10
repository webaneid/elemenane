# Fase 09: Refactoring Total Sidebar & Widget (`scss/_sidebar.scss`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

---

## 1. Masalah Sidebar Lama
- Judul widget (`.widget-title.general-title`) hanya bar kuning di kiri; semua widget memakai kartu bergaris bawah tebal.
- Block Latest Posts (`.wp-block-latest-posts__list`), Categories/Archives, Latest Comments, Calendar, dropdown, dan Search block tidak punya gaya sendiri (tampil seperti default WordPress).
- `searchform.php` (`.ane-cari`) dan Search block memakai dua gaya berbeda, input kotak + tombol terpisah.
- Widget kustom `Webane_Posts_Widget` mencetak judulnya sendiri (hard-coded) sehingga tidak ikut `before_title`.

## 2. Desain Baru
- **Kartu widget** (`.ane-widget`): putih, radius 16px, border tipis + bayangan lembut; `section` dengan id/class widget bawaan WP (`%1$s`, `%2$s`).
- **Judul widget** (semua tipe: `.widget-title`, label Search block, heading block): gaya "kicker" editorial: huruf kapital kecil ber-letter-spacing, berlian aksen di depan, garis tipis memudar yang mengisi sisa baris.
- **Pencarian**: satu gaya "pill" untuk `searchform.php` dan Search block: ikon, input tanpa border, tombol pil; ring fokus; mendukung varian tombol-di-dalam dan tombol-ikon.
- **Latest Posts block**: daftar bernomor (angka besar outline), judul maks. 3 baris, tanggal kecil, thumbnail opsional rapi; hover bergeser.
- **Widget Webane Posts**: thumbnail persegi 84px di kiri (zoom saat hover), judul 3 baris, meta kecil.
- **Daftar umum** (Categories, Archives, Pages, Meta, Nav Menu, Latest Comments): baris dengan chevron aksen, jumlah di kanan, hover bergeser; submenu menjorok.
- **Tag cloud** pil outline dengan `#`; **dropdown/select**, **kalender**, gambar/galeri/embed ikut gaya konsisten.
- Mobile-first: kartu penuh lebar container, tanpa margin negatif.

### Kompatibilitas Mundur
- ID widget area `default-sidebar`, `home-sidebar` tidak berubah; kelas `ane-sidebar` tetap ada di wrapper widget; `<aside id="sticky-sidebar" class="ane-kanan">` dan `.right-sidebar` tetap.
- `searchform.php` tetap berwrapper `.ane-cari` (dipakai `_arsip.scss`).

### Out-of-Scope
- Sticky sidebar (sidebar tinggi akan terpotong), widget area footer.

---

## 3. Daftar File
- `functions.php` (markup widget area), `sidebar.php`, `searchform.php`, `inc/widget.php` (judul via `before_title`), `scss/_sidebar.scss` (tulis ulang), `css/main(.min).css`.
- Dokumentasi: `PROGRESS.md`, fase ini.

## 4. Checklist
- [x] Markup widget area + searchform + widget kustom
- [x] SCSS baru + kompilasi
- [x] Verifikasi visual (desktop & mobile) dengan widget nyata
- [x] Quality gate + dokumentasi

## 5. Kriteria Selesai
- [x] `composer lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check` lulus.
- [x] Search, Latest Posts, dan Webane Posts tampil dengan gaya baru tanpa overflow di 375px.

## Catatan Implementasi
- Widget area kini membungkus widget dengan `<section id="%1$s" class="ane-sidebar ane-widget %2$s">`; judul `<h2 class="widget-title general-title"><span>...</span></h2>`. `Webane_Posts_Widget` memakai `before_title`/`after_title` dari widget area.
- Tiga mixin SCSS dipakai ulang: `ane-pill-field/input/button` (Search block + `searchform.php`), `ane-widget-title`, `ane-link-list`.
- Latest Posts: nomor besar redup (menjadi aksen saat hover); `grid-row: 1` (bukan `span 6`, yang membuat baris kosong memuai).
- Search block tidak punya placeholder/ikon bawaan: ikon kaca pembesar ditambahkan lewat background SVG pada input.
- Kelas `sticky-top` dihapus dari `.right-sidebar` (tidak pernah berfungsi); sticky sidebar tetap out-of-scope.
- Dihapus: seluruh gaya lama (`h2.widget-title` bar kuning, kartu garis bawah tebal, `.ane-cari form` lama).

## Hasil Verifikasi
- Browser: Search block, Latest Posts block, dan Webane Posts widget (data nyata) diperiksa di desktop; markup standar WP untuk Categories (bersarang), Tag cloud, Archives dropdown, Latest Comments, dan `searchform.php` disuntikkan untuk memeriksa gaya; 375px tanpa overflow.
- Quality gate lulus (`lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check`).
- Belum dilihat langsung: Calendar, Nav Menu, Recent Comments dengan avatar, dan gallery block (gaya ditulis tapi tidak ada datanya di situs lokal).
