# Fase 12: Refactoring Total Landing Page News (`page-home.php`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

## 1. Masalah Lama
- Dua jalur render (`wp_is_mobile()`): `page-home-desktop.php` dan `page-home-mobile.php` dengan layout berbeda; halaman tidak bisa di-cache per-URL dengan benar.
- Hero memakai Owl Carousel + jQuery (ratusan KB JS) untuk satu slider; daftar "terbaru" di kirinya kecil dan datar.
- Setiap section memakai `.section-title` kotak berborder, kolom `.ane-col-*` lama, dan kartu `content-*` yang tidak seragam dengan arsip.
- Latest content di desktop tidak punya paginasi (komentar).

## 2. Desain Baru
Satu layout responsif untuk semua perangkat.
- **Hero** (`tp/news-featured.php`): slider scroll-snap 5 artikel (flag ACF `ane_news_utama`, fallback artikel terbaru bila belum ada yang ditandai) dengan gambar penuh + efek zoom halus, gradasi, chip kategori, judul besar, kutipan, penulis dan waktu; dots dengan progress autoplay, tombol prev/next kaca, panel "Latest" bernomor di sampingnya (artikel yang belum tampil di slider). Hampir tanpa JS: tetap bisa digeser tanpa `js/home.js`.
- **Judul section** (`tp/home-head.php`): gaya "kicker" yang sama dengan judul sidebar (`.ane-section-title`), tombol "Display More" berbentuk pil.
- **Ritme halaman**: tiap `.ane-block` berselang-seling dengan latar tint lembut dan padding fluid.
- **Default** (`ane_box_default`): kartu featured + grid kartu + panel "Popular" bernomor (sticky di desktop).
- **Classic** (`ane_box_classic`): kartu lebar horizontal + grid kartu + panel "Popular".
- **Sliding** (`ane_box_sliding`): rail kartu potret scroll-snap dengan tombol prev/next otomatis hanya saat ada overflow.
- **Column** (`ane_box_column`): 3 kolom kategori, kartu featured + baris ringkas.
- **Popular** (`ane_box_populer`): kartu featured + daftar peringkat bergambar.
- **Latest** (`tp/news-home.php`): loop kartu yang sama dengan arsip (featured di halaman 1), sidebar `home-sidebar`, dan paginasi.
- Kartu memakai `tp/archive-card.php` (varian baru `row`, argumen `sizes`) dan `tp/archive-loop.php` (argumen `query`) dari Fase 10; daftar peringkat di `tp/home-rank.php`.
- **JS**: `js/home.js` (vanilla) menggantikan Owl di landing; autoplay berhenti saat hover / fokus / sentuh / tab tersembunyi / hero di luar layar / `prefers-reduced-motion`.

## 3. Kompatibilitas
- ACF Flexible Content `ane_home_box` (layout `ane_box_default|sliding|column|classic|populer`, `ane_home_banner`), field `ane_news_utama`, `ane_title`, `ane_category`, `ane_judul`, `judul_custom`, `ane_box_column_isi`, sidebar `home-sidebar`, meta `musi_views` tidak berubah.
- Preload LCP (`elemenane_get_lcp_image`) memakai slide pertama hero lewat `elemenane_home_hero_ids()`.
- Owl Carousel (`ane_enqueue_carousel()`, `js/carousel-init.js`, `scss/_owlcarousel.scss`) tidak lagi dipakai template mana pun, tetapi dibiarkan terdaftar untuk child theme / snippet Elementor; kandidat dihapus di fase pembersihan.

## 4. File
- Baru: `inc/landing.php`, `js/home.js`, `tp/home-head.php`, `tp/home-rank.php`.
- Ditulis ulang: `page-home.php`, `tp/news-featured.php`, `tp/news-default.php`, `tp/news-classic.php`, `tp/news-sliding.php`, `tp/news-column.php`, `tp/news-populer.php`, `tp/news-home.php`, `scss/_landingpage.scss`.
- Diubah: `tp/archive-card.php`, `tp/archive-loop.php`, `scss/_arsip.scss`, `inc/elemenane.php` (enqueue, LCP, paginasi membaca var `page`), `functions.php`, `languages/*`, `css/landing(.min).css`, `css/main(.min).css`.
- Dihapus: `page-home-desktop.php`, `page-home-mobile.php`, `page-home-mobile-banner.php`, `tp/news-featured-mobile.php`, `tp/news-featured-new.php`, `tp/mobile-news-populer.php`, `tp/home-iklan-mobile.php`, `tp/content-klasik-mobile.php`.

## 5. Checklist
- [x] Template + helper + JS
- [x] SCSS + kompilasi
- [x] Verifikasi visual desktop dan 375px (hero, default, sliding, latest); tombol next hero + autoplay; tanpa error konsol
- [x] Quality gate
- [x] Dokumentasi + commit

## 6. Belum Diverifikasi
- Section `column`, `classic`, `populer`, dan banner `ane_home_banner` (tidak ada di data lokal; markup mengikuti pola yang sama).
- Paginasi latest pada data > 10 artikel (URL `/news/page/2/` merespons 200, tetapi tidak ada halaman 2 nyata).
- Gestur sentuh sebenarnya pada hero dan rail, serta pembaca layar.
