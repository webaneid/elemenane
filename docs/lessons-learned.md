# Lessons Learned & Catatan Teknis — Elemen Ane (`elemenane`)

Dokumen ini mencatat temuan penting dan jebakan teknis (pitfalls) yang ditemukan selama audit dan pengembangan tema **Elemen Ane** agar tidak terulang di masa depan.

---

## 1. Keamanan & WordPress Core Filters
- **Jangan pernah menghapus `wp_filter_kses` dari `pre_user_description`**:
  - Di versi warisan (`inc/elemenane.php:948`), terdapat `remove_filter( 'pre_user_description', 'wp_filter_kses' );`. Hal ini membuka celah **Stored XSS** karena user dengan role rendah (Contributor/Author/Subscriber) dapat menyuntikkan `<script>` berbahaya pada kolom Biographical Info profil mereka. Jika ingin mengizinkan HTML aman pada bio, gunakan `wp_filter_post_kses` atau biarkan default `wp_filter_kses`.
- **Hati-hati memfilter `upload_mimes` secara global**:
  - Di versi warisan (`inc/elemenane.php:1123`), filter `upload_mimes` mengganti seluruh array `$mimes` hanya dengan 6 tipe gambar (`jpg`, `gif`, `png`, `bmp`, `tiff`, `svg`). Akibatnya, upload file PDF, DOCX, XLSX, MP4, atau ZIP di Media Library WordPress gagal total! Selain itu, mengizinkan `image/svg+xml` tanpa sanitasi XML membuka celah Stored XSS via file SVG.
  - **Solusi**: Jangan menimpa array `$mimes` bawaan WordPress; tambahkan `image/webp` ke dalam `$mimes` yang sudah ada, batasi upload SVG hanya untuk `current_user_can( 'manage_options' )`, dan sanitasi konten SVG di `wp_handle_upload_prefilter`.

## 2. Jebakan Redirect & Komentar (`inc/handle.php`)
- Di versi warisan (`inc/handle.php:16-24`), fungsi `codesnippets_disable_comments_redirect()` melakukan `wp_redirect( home_url() )` setiap kali `is_singular() && ( have_comments() || comments_open() )`.
- Akibatnya, postingan lama yang terlanjur memiliki status `comment_status = 'open'` di database atau memiliki komentar lama akan **mental (redirect) ke halaman depan** dan tidak bisa dibaca pengunjung!
- **Solusi**: Penonaktifan komentar cukup melalui filter `comments_open` (`__return_false`), `pings_open` (`__return_false`), dan `comments_array` (`__return_empty_array`). Redirect hanya boleh dilakukan jika request adalah halaman comment feed (`is_comment_feed()`).

## 3. Performa Database pada Hook `after_setup_theme`
- Memanggil `get_option()` dan `update_option()` di dalam `after_setup_theme` (seperti `elemenane_set_default_image_sizes` di `functions.php`) untuk mengatur ukuran thumbnail default dieksekusi pada setiap siklus hidup tema jika opsi belum terset.
- **Solusi Standar Webane**: Jalankan pengaturan ukuran gambar default pada hook `after_switch_theme` dan satu kali saat versi tema berubah di `admin_init` (membandingkan `get_option( 'elemenane_image_sizes_version' ) !== ELEMENANE_VERSION`).

## 3b. Ikon Inline SVG & `wp_kses_post()`
- `wp_kses_post()` membuang `<svg>`/`<path>`. String yang memuat `elemenane_get_icon_svg()` harus difilter dengan `wp_kses( $html, elemenane_kses_post_with_svg() )`; kalau tidak, ikon hilang diam-diam.
- Di macOS, `sed -i -E ...` memperlakukan `-E` sebagai suffix backup dan membuat file `*-E`. Gunakan `sed -i '' -E` atau skrip Python untuk edit massal.

## 4. Core Web Vitals (LCP & Zero-CLS) pada Slider Berita & Hero
- Menggunakan `style="background-image: url(...)"` pada kartu berita overlay (`tp/content-overlay.php`) membuat browser **tidak bisa** mem-preload gambar LCP, tidak bisa memilih resolusi `srcset`/`sizes` sesuai DPR layar mobile, dan menyembunyikan gambar dari scanner preload HTML awal.
- **Solusi Standar Webane (`webane-pagespeed-cwv`)**:
  - Ganti `background-image` dengan elemen `<img class="ane-overlay-bg">` (`position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;`).
  - Berikan `fetchpriority="high"` dan jangan pasang `loading="lazy"` pada item pertama (index `0`) di slider/featured section; pasang `loading="lazy"` dan `decoding="async"` pada item ke-2 dst.
  - Preload gambar LCP di `wp_head` prioritas `1` lengkap dengan `imagesrcset` dan `imagesizes`.
  - Cegah layout shift (CLS) sebelum `owl.carousel.min.js` dieksekusi (karena JS di-`defer`) dengan menyuntikkan CSS pre-init `:not(.owl-loaded)` yang menyembunyikan slide ke-2 dst (`> *:not(:first-child) { display: none; }`).

## 5. Defer JS & Async CSS
- Gunakan `wp_script_add_data( $handle, 'strategy', 'defer' )` (WP 6.3+) untuk menunda script tema. Core otomatis memakai blocking bila ada dependen/inline script yang butuh jQuery, sehingga plugin & Elementor tidak rusak. Jangan men-`defer` `jquery-core` lewat `script_loader_tag`.
- `owl.carousel.js` yang di-defer berarti `.owl-carousel { display: none }` menyembunyikan slider sampai init. Pre-init guard `:not(.owl-loaded)` di `scss/_owlcarousel.scss` wajib dipertahankan agar slide pertama tampil tanpa CLS.
- `wp_get_attachment_image()` menambah `sizes="auto, ..."` pada gambar lazy; untuk preload, `imagesizes` harus berisi nilai `sizes` tanpa `auto,` agar kandidat srcset sama.

## Gaya header/footer vs kit Elementor
Pada halaman yang dirender Elementor Theme Builder, body mendapat `.elementor-kit-N` dan kit menulis `.elementor-kit-N a { color }` / `button { background }` (specificity 0,1,1) yang dimuat setelah `main.css`. Selector BEM satu kelas (`.ane-header__toggle`) kalah. Bungkus komponen global (header, footer) dengan `html body { ... }` agar menang tanpa `!important`.

## Template arsip tidak selalu dipakai
`elementor_theme_do_location( 'archive' )` mengambil alih category/tag/search/author bila ada template Elementor. Saat menguji template tema, jangan mengira halaman `?s=` memakai `search.php`; cek template aktif dulu.

## WooCommerce mengganti permalink arsip produk
Saat WooCommerce diaktifkan ia menulis ulang aturan permalink dan memindahkan arsip produk dari `/product/` ke `/shop/`. Menonaktifkannya tidak mengembalikan aturan lama sampai permalink di-flush, sehingga `/product/` memberi 404. Setelah mengaktifkan / menonaktifkan WooCommerce (atau mengujinya) selalu jalankan `flush_rewrite_rules()` / simpan ulang Settings > Permalinks.

## Nama kelas BEM bentrok antar halaman di `catalog.css`
`catalog.css` memuat gaya produk dan cabang sekaligus. Dialog cabang di halaman produk memakai `.ane-branch-modal` / `.ane-branch-list` (non-BEM), sehingga dialog arsip cabang dengan nama sama tertimpa (`display: none`, `display: flex`). Arsip cabang kini memakai `.ane-branch-dialog` dan `.ane-branch-grid`. Pakai awalan unik per komponen.

## Performa: ukur dulu, lalu buang
- Audit CSS terpakai: muat halaman-halaman nyata di iframe, ambil `main.css`, cocokkan tiap selector (tanpa pseudo) dengan `querySelector`. Aturan yang tidak cocok di semua halaman dipilah: **mati** (komponen lama yang templatenya sudah tidak ada) dihapus, **digerakkan data** (blok editor, widget, status `.is-open`) dipertahankan.
- Pecah CSS per konteks lebih efektif daripada menghapus aturan: `content`, `widgets`, `contact-page` hanya dimuat di halaman yang memakainya (`main.min.css` 143 KB -> 84 KB, gzip 22,9 -> 13,9 KB). Selalu uji ulang: bandingkan selector bundel yang *tidak dimuat* dengan DOM tiap halaman (tidak boleh ada yang cocok).
- Stylesheet yang menumpang `array( 'main-css' )` sebagai dependensi **tidak dicetak** bila `main-css` tidak di-enqueue (mis. halaman Canvas).
- Komentar `/*# sourceMappingURL=... */` di `.min.css` menyebabkan 404 di konsol bila `.map` tidak dirilis: kompilasi `.min.css` dengan `--no-source-map`.
- `preconnect` ke host yang tidak dipakai (Google Fonts saat Elementor menyajikan font lokal) dinilai PageSpeed sebagai "unused preconnect": cetak hanya saat stylesheet fonts benar-benar di-enqueue.
- Hindari pustaka pihak ketiga (Magnific CDN) untuk fitur sederhana: `<dialog>` native + ~3 KB JS cukup, dan menghilangkan 2 request lintas-origin.
- Skor PageSpeed berfluktuasi antar run (86 -> 81 tanpa perubahan relevan di tema). Bandingkan beberapa run; atribusikan dulu tiap temuan ke sumbernya (tema / Elementor / pihak ketiga) sebelum mengubah kode.
- Laporan PageSpeed yang dibuka lewat browser otomatis tidak bisa diekspor oleh alat (URL aset ber-query-string diblokir); minta pengguna menempel tabel temuan.
