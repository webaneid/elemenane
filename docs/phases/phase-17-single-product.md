# Fase 17: Refactoring Single Product (Kompatibel WooCommerce, Termasuk Produk Variabel)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Masalah Lama
- `single-product.php` satu file 400 baris: `.ane-container` 1200px + breadcrumb buatan sendiri, SVG inline mentah, harga selalu Rupiah dari meta tema, tab tanpa ARIA, `console.log` di JS, komentar debug `<!-- Debug: N branches found -->` di HTML.
- Tidak ada tombol beli WooCommerce: produk variabel, kuantitas, notice keranjang, rating/ulasan, atribut tidak bisa dipakai bila WooCommerce aktif.

## 2. Desain Baru
Satu halaman, dua sumber data, dirakit dari partial:
- `tp/product-gallery.php`: kartu gambar utama (zoom-in / lightbox Magnific), thumbnail `<button>` yang bisa digulir, badge diskon.
- `tp/product-summary.php`: kategori, judul, rating (WooCommerce), SKU, status stok, harga, ringkasan, area beli, share bar.
- `tp/product-tabs.php`: tab ARIA (Deskripsi, Spesifikasi, Fitur, Ulasan) dengan navigasi panah / Home / End.
- `tp/product-branch-modal.php`: dialog "Beli di Cabang" (`role="dialog"`, tanpa komentar debug).
- Produk terkait & "You may also like" memakai kartu dari Fase 16.
- Frame sama dengan halaman lain: `main.ane-archive`, breadcrumb Yoast, `.container`.

### Mode tanpa WooCommerce
Harga Rupiah (diskon + chip), tombol Chat WhatsApp, Beli di Cabang, tautan marketplace, spesifikasi & fitur dari ACF.

### Mode WooCommerce (termasuk produk variabel)
- Harga dari `get_price_html()` (rentang harga produk variabel, mata uang & pajak sesuai pengaturan WooCommerce).
- **Form add-to-cart bawaan WooCommerce** (`woocommerce_template_single_add_to_cart()`): simple, variable (pilihan variasi, harga & stok variasi, tombol Clear), grouped, external; kuantitas, notice keranjang (`woocommerce_before_single_product`).
- Gambar utama berganti sesuai variasi (`found_variation` / `reset_data` di `js/single-product.js`).
- SKU memakai markup `.product_meta .sku` agar SKU variasi ikut diperbarui oleh skrip WooCommerce.
- Tab Spesifikasi menggabungkan tabel ACF dengan atribut / berat / dimensi WooCommerce; tab Ulasan memuat `comments_template()` bila ulasan aktif; rating bintang menautkan ke tab Ulasan.
- Plugin yang memakai hook `woocommerce_single_product_summary` tetap jalan (callback bawaan WooCommerce dilepas lebih dulu agar tidak dobel).
- Related products memakai `wc_get_related_products()`; upsell WooCommerce tampil sebagai "You may also like".
- Opsi ACF (WhatsApp, cabang, marketplace) tetap tampil di bawah form WooCommerce.

## 3. File
- Baru: `scss/_product-single.scss`, `tp/product-gallery.php`, `tp/product-summary.php`, `tp/product-tabs.php`, `tp/product-branch-modal.php`.
- Ditulis ulang: `single-product.php`, `js/single-product.js` (tab ARIA, variasi gambar, tanpa console.log).
- Diubah: `inc/product-card.php` (related/upsell helper), `inc/product-acf.php` (versi skrip 1.2.0 agar cache terbuang), `scss/_product.scss` (blok single lama dihapus; `.ane-btn`, marketplace, modal cabang dipertahankan), `scss/_product-card.scss` (tombol loop WooCommerce menang atas gaya WooCommerce), `scss/catalog.scss`, `languages/*`, `css/main|catalog(.min).css`.

## 4. Verifikasi (termasuk WooCommerce 11.0.1 sungguhan)
WooCommerce diaktifkan sementara di situs lokal dengan satu produk variabel (2 variasi, salah satu diskon) dan satu produk simple (diskon + stok), lalu dibersihkan.
- Tanpa WooCommerce: produk nyata di desktop dan 375px; thumbnail, tab (klik + panah), badge, share, tanpa overflow.
- Dengan WooCommerce: halaman Shop `/shop/` (224 produk) memakai arsip dan kartu tema; `/product/` dialihkan 301 ke `/shop/`; add-to-cart AJAX dari kartu berhasil (tombol berubah hijau + "View cart"); produk variabel: pilih variasi memperbarui harga, tombol Add to cart aktif; produk simple: kuantitas + Add to cart; tab Spesifikasi (atribut) dan Ulasan muncul; shortcode `[products]` merender `ul.products.ane-pgrid` dengan kartu tema; mobile 375px tanpa overflow.
- Pembersihan: produk uji dihapus permanen, `woocommerce_coming_soon` dikembalikan ke `yes`, plugin dinonaktifkan seperti semula. (Aktivasi sempat menjalankan instalasi WooCommerce: tabel dan opsi WooCommerce tetap ada di database; halaman Shop/Cart/Checkout/My account sudah ada sebelumnya.)

## 5. Catatan
- Mata uang di mode WooCommerce mengikuti pengaturan WooCommerce (default USD). Atur ke IDR di WooCommerce > Settings > General agar tampil "Rp".
- Produk grouped / external, ulasan sungguhan dan pengiriman ulasan, serta checkout/keranjang tidak diuji.
- Tema tidak menambahkan dukungan galeri WooCommerce (zoom/slider); galeri memakai implementasi tema sendiri.
