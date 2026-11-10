# Fase 06: Optimasi Speed Test & Core Web Vitals (`webane-pagespeed-cwv`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: `GAP-21`, `GAP-22`, `GAP-23`, `GAP-24`, `GAP-25`, `GAP-26`, `GAP-27`, `GAP-28`

---

## 1. Tujuan & Ruang Lingkup
### In-Scope
- `GAP-21`: `tp/content-overlay.php` memakai `<img class="ane-overlay-bg">` (srcset/sizes, `fetchpriority="high"` hanya pada slide pertama beranda, sisanya `loading="lazy"`), bukan `background-image`.
- `GAP-22`: `<link rel="preload" as="image" fetchpriority="high">` di `wp_head` prioritas 1 untuk beranda (`page-home.php`), single post, dan single product; `the_post_thumbnail()` di single dengan `fetchpriority="high"` + `sizes`.
- `GAP-23`: hapus `owl.carousel.min.css` dari CDN; CSS struktural Owl + guard pre-init Zero-CLS dipindah ke `scss/_owlcarousel.scss`.
- `GAP-24`: stylesheet non-kritis (`ane-google-fonts`, `magnific-popup`) dimuat async (`media="print" onload`) + `<noscript>`.
- `GAP-25`: script frontend di-`defer`. Memakai API `strategy => defer` WordPress (>= 6.3) alih-alih filter `script_loader_tag`, karena API ini otomatis jatuh ke blocking bila ada dependen/inline script yang butuh jQuery (lebih aman bagi plugin & Elementor).
- `GAP-26`: preconnect dibatasi maksimal 2 domain (`fonts.googleapis.com`, `fonts.gstatic.com`).
- `GAP-27`: `content-visibility: auto` untuk `.ane-related`, `.ane-footer`, dan blok komentar.
- `GAP-28`: aksesibilitas: `aria-label` pada nav/dots Owl, ikon-only link, tap target >= 44px pada kontrol utama.

### Out-of-Scope
- Self-host Owl/Magnific JS dari CDN (butuh vendoring file; dicatat sebagai tindak lanjut).
- Pengukuran PageSpeed live (butuh URL publik); verifikasi dilakukan lewat quality gate + inspeksi HTML lokal.

---

## 2. Daftar File yang Diubah / Dibuat
- `tp/content-overlay.php`, `tp/news-featured.php`, `tp/news-featured-mobile.php`, `tp/news-sliding.php` — `<img>` hero & argumen prioritas.
- `inc/elemenane.php` — preload LCP, async CSS, defer JS, hapus Owl CSS CDN.
- `inc/elementor.php` — preconnect Google Fonts.
- `tp/single.php`, `single-product.php` — `fetchpriority`/`sizes` gambar utama.
- `js/carousel-init.js` — `aria-label` nav/dots.
- `scss/_owlcarousel.scss`, `scss/_post.scss`, `scss/_footer.scss`, `scss/_anecontent.scss`, `scss/_header.scss` — CSS Owl, `content-visibility`, tap target.
- `css/*.css`, `css/*.min.css` (+ map) — hasil kompilasi.

---

## 3. Langkah Implementasi & Checklist
- [x] GAP-21 hero `<img>`
- [x] GAP-22 preload LCP + single thumbnail
- [x] GAP-23 CSS Owl lokal + Zero-CLS
- [x] GAP-24 async CSS
- [x] GAP-25 defer JS
- [x] GAP-26 preconnect <= 2
- [x] GAP-27 content-visibility
- [x] GAP-28 aksesibilitas

---

## 4. Kriteria Selesai (Definition of Done)
- [x] `composer lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check` lulus.
- [x] Smoke test HTML lokal: preload LCP ada, tidak ada `background-image` inline pada overlay, tidak ada request `owl.carousel.min.css`.

## Catatan Implementasi & Batasan
- Preload LCP: `elemenane_preload_lcp_image()` (`inc/elemenane.php`, `wp_head` prioritas 1). Terverifikasi lokal pada single post (preload + `<img fetchpriority="high">` memakai file yang sama). Jalur beranda (`page-home.php`) dan single product belum bisa diuji lokal karena beranda situs ini memakai template Elementor Canvas dan belum ada produk; logikanya sama.
- Overlay: argumen `priority`/`sizes` via `get_template_part( ..., $args )`. Hanya slide pertama `tp/news-featured*.php` yang eager + `fetchpriority="high"`; slide lain dan `news-sliding` lazy.
- Defer memakai `wp_script_add_data( $handle, 'strategy', 'defer' )`, bukan filter `script_loader_tag` (lihat lingkup).
- Async CSS: `ane-google-fonts` dan `magnific-popup` lewat `style_loader_tag` + `<noscript>`. Terverifikasi di HTML (`media='print' onload`).
- Preconnect: `fonts.googleapis.com` + `fonts.gstatic.com` saja (2).
- `content-visibility` dipasang pada `.ane-related` dan `.ane-footer`. Tema tidak punya area komentar sendiri (`.ane-komentar` tidak ada), jadi dilewati.
- Heading footer sudah berurutan (`h2` -> `h3` -> `h4`), tidak diubah.
- Tap target 44px diterapkan pada dots & panah Owl. Tombol search header belum diubah (risiko layout; perlu cek visual).
- Tindak lanjut: Owl/Magnific JS masih dari cdnjs; self-host akan menghilangkan satu origin lagi. Skor PageSpeed live perlu diukur di URL publik.

## Hasil Verifikasi Quality Gate
- [x] `lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse` (0 error), `security:scan` (0/0), `css:check`, `dist:check` lulus.
