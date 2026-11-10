# Katalog Gap Audit & Retrofit — Elemen Ane (`elemenane`)

Dokumen ini mencatat seluruh temuan hasil audit awal terhadap tema **Elemen Ane (`v1.1.8`, commit `74fa6f5`)** dibandingkan dengan standar keamanan & rekayasa template WordPress Webane (`newsane` / `sekolahane`) serta standar performa **Webane PageSpeed & Core Web Vitals (`webane-pagespeed-cwv`)**.

---

## Ringkasan Kategori Gap
1. **Keamanan & Anti-Webshell (`GAP-01` s/d `GAP-10`)** — Prioritas Kritis & Tinggi (Target: **Fase 01 & Fase 02**)
2. **Bug PHP 8.x, Kualitas Kode, & Standar WPCS (`GAP-11` s/d `GAP-18`)** — Prioritas Tinggi & Menengah (Target: **Fase 01, Fase 02, & Fase 03**)
3. **Optimasi Media, Icon SVG, & Core Web Vitals (`GAP-19` s/d `GAP-28`)** — Prioritas Tinggi untuk PageSpeed >= 90 (Target: **Fase 04, Fase 05, & Fase 06**)

---

## 1. Gap Keamanan & Anti-Webshell

| ID | Keparahan | Lokasi File | Deskripsi Masalah | Target Fase | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GAP-01` | **Critical** | `inc/acf.php:34` | Hardcoded plaintext Google Maps API key (`AIzaSy...`) di dalam kode sumber. Wajib diganti dengan `elemenane_get_google_maps_api_key()` (prioritas: konstanta `ELEMENANE_GMAPS_API_KEY` → ACF option `ane_gmap_api_key`/`google_api_key` → fallback XOR-Hex `elemenane_get_default_gmaps_key()`). | Fase 02 | **Fixed** |
| `GAP-02` | **High** | `inc/elemenane.php:948` | `remove_filter( 'pre_user_description', 'wp_filter_kses' );` mematikan sanitasi HTML pada bio user, membuka celah **Stored XSS** dari user role rendah di halaman `author.php`. | Fase 02 | **Fixed** |
| `GAP-03` | **High** | 18 file PHP (`404.php`, `archive.php`, `author.php`, `category.php`, `footer.php`, `header.php`, `page-home.php`, `page-kontak.php`, `page-linktree.php`, `page-nosidebar.php`, `page.php`, `search.php`, `sidebar.php`, `single.php`, `tag.php`, `tp/content-sosmed.php`, `tp/header-asli.php`, `tp/home-iklan-mobile.php`) | Belum memiliki guard `defined( 'ABSPATH' ) || exit;` di awal file untuk mencegah eksekusi file langsung. | Fase 02 | **Fixed** |
| `GAP-04` | **High** | `inc/updater.php:342-357` | Parameter `$_GET['elemenane_debug']` mencetak struktur internal via `print_r()` dan `$_GET['elemenane_force_check']` menghapus transient tanpa pemeriksaan `current_user_can( 'update_themes' )` maupun `check_admin_referer()`. | Fase 01 | **Fixed** |
| `GAP-05` | **High** | `inc/updater.php` | Belum mendukung otentikasi GitHub Token (`ELEMENANE_UPDATER_TOKEN` / `ane_updater_token`) untuk repositori privat & pencegahan rate-limit, serta belum memiliki filter `themes_api` untuk popup detail changelog. | Fase 01 | **Fixed** |
| `GAP-06` | **High** | `inc/elemenane.php:1123-1134` | Filter `upload_mimes` (`ane_allowed_avatar_mime_types`) menimpa seluruh daftar MIME WordPress hanya dengan 6 ekstensi gambar (membuat upload PDF/DOCX/ZIP gagal) dan mengizinkan `image/svg+xml` untuk semua user tanpa sanitasi XML (`wp_handle_upload_prefilter`). | Fase 02 | **Fixed** |
| `GAP-07` | **High** | `inc/tracking-scripts.php` & `inc/acf.php` | Field script pelacakan mentah (`ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`) dicetak tanpa pembatasan kapabilitas `manage_options` pada form ACF. | Fase 02 | **Fixed** |
| `GAP-08` | **High** | `tp/footer-asli.php`, `tp/header-asli.php`, `tp/content-page-kontak.php`, `footer.php`, `inc/elementor.php`, `inc/elemenane.php` | Banyak output variabel yang belum di-escape (`echo $titlesosmed`, `echo $judul`, `echo $sologan`, `echo $about`, `<?= $Phone ?>`, `$logoimg`, `$location['lat']`, `json_encode($numbers)` di inline script `footer.php`). | Fase 02 | **Fixed** |
| `GAP-09` | **Medium** | `inc/product-filter.php:77-84` | Penggunaan `$_POST` pada handler AJAX filter produk perlu distandarkan menggunakan `wp_unslash()` + validasi tipe eksplisit dan escaping penuh pada html response. | Fase 02 | **Fixed** |
| `GAP-10` | **Medium** | `inc/admin/linktree-analytics.php` | Query SQL pada tabel kustom `{$wpdb->prefix}ane_linktree_clicks` dan akses `$_POST`/`$_SERVER` perlu diperketat sesuai aturan `WordPress.DB.PreparedSQL` dan `WordPress.Security.ValidatedSanitizedInput`. | Fase 02 | **Fixed** |

---

## 2. Gap Bug PHP 8.x, Arsitektur Kode, & Standar WPCS

| ID | Keparahan | Lokasi File | Deskripsi Masalah | Target Fase | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GAP-11` | **Critical** | `inc/handle.php:16-24` | `codesnippets_disable_comments_redirect()` me-redirect (`wp_redirect( home_url() )`) setiap halaman single post/page apabila `have_comments() || comments_open()` bernilai true di database, menyebabkan artikel lama yang memiliki status komentar terbuka tidak bisa diakses! Selain itu terdapat fungsi boilerplate `wp_remove_scripts()` yang mematikan seluruh JS di `is_page('homepage')`. | Fase 02 | **Fixed** |
| `GAP-12` | **High** | `functions.php:14-17` | Memeriksa versi WP `< 4.7` dan memanggil `require get_template_directory() . '/inc/back-compat.php'` padahal file `inc/back-compat.php` tidak ada (fatal error jika terpicu). | Fase 01 | **Fixed** |
| `GAP-13` | **Medium** | `functions.php:47-58` | `elemenane_set_default_image_sizes()` dipanggil di setiap `after_setup_theme`. Wajib dipindahkan ke `after_switch_theme` + `admin_init` berbasis konstanta `ELEMENANE_VERSION`. | Fase 01 | **Fixed** |
| `GAP-14` | **Medium** | `functions.php:222-230` | Memuat modul `inc/*.php` dan `inc/**/*.php` menggunakan `glob()` pada setiap request. Wajib diganti dengan daftar `require_once` eksplisit agar deterministik, cepat, dan aman dari file asing. | Fase 03 | **Fixed** |
| `GAP-15` | **Medium** | `inc/elementor.php:26-48` & `tp/footer-asli.php:46,98` | Pemanggilan fungsi yang tidak terdefinisi `hello_elementor_get_setting()` di `inc/elementor.php` serta variabel tidak terdefinisi `$whatsapp` dan `$alamat` di `tp/footer-asli.php` (memicu Warning di PHP 8.x dan error PHPStan). | Fase 02 | **Fixed** |
| `GAP-16` | **Medium** | `inc/admin/*.php`, `tp/footer-asli.php`, `tp/header-asli.php`, `tp/content-page-kontak.php`, `footer.php`, `embed-404.php` | Ketidakseragaman text domain i18n (`'newsane'`, `'webane'`, `'roots'`, `'autoane'`, atau tanpa text domain). Wajib diseragamkan seluruhnya menjadi `'elemenane'`. | Fase 03 | **Fixed** |
| `GAP-17` | **Low** | Seluruh file PHP (`functions.php`, `inc/`, `tp/`, root templates) | Pelanggaran standar format WPCS (indentasi spasi, Yoda conditions, docblock, short echo `<?=`). | Fase 03 | **Fixed** |
| `GAP-18` | **Low** | `.DS_Store` & `css/.DS_Store` | File `.DS_Store` macOS ikut terlacak di Git dan perlu dihapus dari indeks Git (`git rm --cached`). | Fase 03 | **Fixed** |

---

## 3. Gap Media, Icon SVG, & Optimasi PageSpeed (`webane-pagespeed-cwv`)

| ID | Keparahan | Lokasi File | Deskripsi Masalah | Target Fase | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GAP-19` | **High** | `inc/webp-converter.php` | Tema belum memiliki modul auto-convert upload JPG/PNG ke WebP (`ELEMENANE_WEBP_QUALITY = 78`) sebagaimana di `newsane` dan `sekolahane`. | Fase 04 | **Fixed** |
| `GAP-20` | **High** | `css/FontAne.css`, `fonts/`, & template `tp/*.php` / `single-*.php` | Masih memuat icon font eksternal `FontAne.css` + file font (`fonts/FontAne.*`) dan class `<i class="ane-*">` / `<i class="fa-*">`. Wajib dimigrasikan ke helper Inline SVG `elemenane_icon()` (`inc/icons.php`) dan menghapus `css/FontAne.css` serta folder `fonts/`. | Fase 05 | **Fixed** |
| `GAP-21` | **Critical (LCP)** | `tp/content-overlay.php:23`, `tp/news-featured.php`, `tp/news-featured-mobile.php`, `tp/news-sliding.php` | Kartu hero/featured berita menggunakan `style="background-image: url(...)"` sehingga gambar LCP tidak dapat di-preload dan tidak memiliki `srcset`/`sizes` responsif. Wajib diubah menjadi `<img class="ane-overlay-bg">` dengan DPR-aware `srcset`/`sizes`, `fetchpriority="high"` pada slide pertama, dan `loading="lazy"` pada slide berikutnya. | Fase 06 | Open |
| `GAP-22` | **High (LCP)** | `inc/elemenane.php` & `tp/single.php` / `single-product.php` | Belum ada `<link rel="preload" as="image" fetchpriority="high">` di `wp_head` prioritas `1` untuk gambar LCP di halaman depan (`page-home.php`), single post, dan single product, serta `the_post_thumbnail()` di single belum menggunakan `fetchpriority="high"` dan `sizes` yang akurat. | Fase 06 | Open |
| `GAP-23` | **High (FCP/CLS)** | `inc/elemenane.php:66` & `scss/_owlcarousel.scss` | Memuat `owl.carousel.min.css` dari CDN eksternal secara render-blocking dan belum memiliki aturan CSS Zero-CLS pre-init (`.one-item-carousel.owl-carousel:not(.owl-loaded)`, `#home-sliding:not(.owl-loaded)`). Wajib memindahkan base structural CSS OwlCarousel + pre-init guard ke `scss/_owlcarousel.scss` dan menghapus request `owl.carousel.min.css` eksternal. | Fase 06 | Open |
| `GAP-24` | **High (FCP)** | `inc/elemenane.php` | Stylesheet non-kritis (`ane-google-fonts`, `magnific-popup`) masih dimuat secara sinkron (render-blocking). Wajib dimuat secara asinkron (`media="print" onload="this.media='all'"` + `<noscript>`) via filter `style_loader_tag`. | Fase 06 | Open |
| `GAP-25` | **High (FCP/TBT)** | `inc/elemenane.php` | Script JS frontend (`jquery-core`, `OwlCarousel`, `ane-carousel-init`, `magnific`, `ane-lightbox-init`, `main-js`) belum diberi atribut `defer` via `script_loader_tag` dan jQuery Migrate masih dimuat di frontend. | Fase 06 | Open |
| `GAP-26` | **Medium (Preconnect)** | `inc/elemenane.php` / `inc/seo.php` | Perlu memastikan jumlah `<link rel="preconnect">` dibatasi maksimal 2 domain kritis (`fonts.googleapis.com` dan `fonts.gstatic.com`) agar tidak memicu warning PageSpeed *"More than 4 preconnect connections were found"*. | Fase 06 | Open |
| `GAP-27` | **Medium (Rendering)** | `scss/_post.scss`, `scss/_footer.scss`, `scss/_anecontent.scss` | Seksi berat di bawah lipatan layar (`.ane-related`, `.ane-footer`, `.ane-komentar`) belum menggunakan `content-visibility: auto; contain-intrinsic-size: auto 500px;`. | Fase 06 | Open |
| `GAP-28` | **High (A11y 100)** | `tp/header-asli.php`, `tp/footer-asli.php`, `js/carousel-init.js`, `scss/_header.scss` | Audit Aksesibilitas PageSpeed: link ikon search/close/sosmed belum semuanya memiliki `aria-label`, tombol prev/next & dots OwlCarousel belum memiliki `aria-label` (`navText` + callback `onInitialized`/`onRefreshed`), urutan heading `<h3>` tanpa `<h2>` di `tp/footer-asli.php`, dan ukuran tap target mobile (`>= 44x44px`). | Fase 06 | Open |
