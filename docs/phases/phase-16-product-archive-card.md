# Fase 16: Arsip Produk + `ane-product-card` (Kompatibel WooCommerce)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Masalah Lama
- `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php` menyalin markup yang sama, memakai `.ane-container` (1200px, berbeda dari `.container` 1140px yang dipakai header/footer/arsip), header tengah dengan garis bawah, grid Bootstrap `.row > .col-*` dengan margin hack di mobile.
- Kartu `ane-product-card` berisi SVG inline mentah, hover `scale(1.03) rotate(1deg)`, warna hard-coded (`#fff`, `#1f2937`, `#ef4444`), harga selalu Rupiah dari helper theme, dan tidak ada rating / tombol beli. Dua set CSS kartu saling menimpa (`_product.scss`).
- Jika WooCommerce aktif, loop WooCommerce (shortcode, widget, blok) memakai markup `li.product` bawaan yang tampil berbeda dari arsip tema.

## 2. Jawaban "apakah mungkin?"
Ya. Model data tema sudah identik dengan WooCommerce (CPT `product`, taksonomi `product_cat`/`product_tag`, meta `_price`/`_regular_price`/`_sale_price`/`_sku`/`_stock`/`_stock_status`/`_manage_stock`/`_product_image_gallery`; lihat `inc/product-woocommerce.php`). Fase ini menyatukan sisi tampilan:
- **Satu adapter data** `elemenane_get_product_card_data()` (`inc/product-card.php`) menghasilkan array yang sama dari dua sumber: meta tema (tanpa WooCommerce) atau objek `WC_Product` (dengan WooCommerce). Kartu hanya membaca array itu.
- **Dengan WooCommerce aktif**: harga memakai `wc_price()` (mata uang sesuai pengaturan WooCommerce), produk variabel/grouped menampilkan rentang `get_price_html()`, rating bintang + jumlah ulasan (jika ulasan diaktifkan), badge sale mengikuti jadwal diskon WooCommerce, tombol memakai `woocommerce_template_loop_add_to_cart()` (Add to cart AJAX / Select options / Read more) dengan kelas tema ditambahkan, sehingga skrip WooCommerce tetap bekerja.
- **Tanpa WooCommerce**: harga Rupiah, tombol "Lihat detail", tanpa rating.
- **Override template WooCommerce** (`woocommerce/content-product.php`, `woocommerce/loop/loop-start.php`): setiap loop produk WooCommerce (shortcode `[products]`, produk terkait, dst.) merender kartu yang sama di grid `ul.products.ane-pgrid`.

## 3. Desain Baru
- **Frame arsip** sama dengan arsip artikel: breadcrumb, panel header (kicker Katalog / Kategori produk / Tag produk, judul, deskripsi term, chip jumlah), `.container`, grid, paginasi baru, jarak ke footer (`tp/product-archive.php`; ketiga file arsip kini tipis).
- **Filter** (`inc/product-filter.php` tidak diubah, CSS saja): panel kartu, kolom berbentuk pil, tombol Apply pil.
- **Kartu** (`tp/content-product.php`, `scss/_product-card.scss`, global di `main.css`): gambar persegi putih dengan padding, badge Sale / New / Stok Habis di pojok kiri atas, kategori, judul 2 baris dengan seluruh kartu sebagai area klik, rating, harga (coret harga lama), status stok, tombol. Hover mengangkat kartu 3px (tanpa rotate). Stok habis: gambar abu-abu.
- **Grid** `.ane-pgrid`: 2 kolom (mobile) → 3 (≥ 640px) → 4 (≥ 993px). Produk terkait di halaman single memakai grid dan judul section yang sama.

## 4. File
- Baru: `inc/product-card.php`, `tp/product-archive.php`, `scss/_product-card.scss`, `woocommerce/content-product.php`, `woocommerce/loop/loop-start.php`.
- Ditulis ulang: `tp/content-product.php`, `archive-product.php`, `taxonomy-product_cat.php`, `taxonomy-product_tag.php`.
- Diubah: `single-product.php` (produk terkait), `scss/_product.scss` (blok arsip/kartu lama dihapus, filter direstyle), `scss/main.scss`, `functions.php`, `languages/*`, `css/main|catalog(.min).css`.

## 5. Verifikasi
- Tanpa WooCommerce (kondisi situs lokal; 222 produk): `/product/`, `/product/page/3/` (23 halaman), `/product-category/fungisida/`, dan produk terkait tampil benar di desktop dan 375px tanpa overflow.
- Jalur WooCommerce disimulasikan dengan kelas/fungsi pengganti (`WooCommerce`, `wc_get_product`, `wc_price`, `woocommerce_template_loop_add_to_cart`, rating): kartu simple (harga `wc_price`, diskon, rating, tombol dengan kelas WooCommerce + tema) dan variabel (rentang harga WooCommerce) merender benar. PHPStan, PHPCS, security scan, `css:check`, `dist:check` lulus.

## 6. Belum Diverifikasi
- Dengan plugin WooCommerce sungguhan aktif (belum diaktifkan di situs lokal): tampilan di halaman Shop, AJAX add-to-cart, produk variabel nyata, rating nyata, dan override `woocommerce/` pada versi WooCommerce terbaru.
- Halaman single produk (`single-product.php`) masih desain lama (`.ane-container` 1200px); tidak termasuk fase ini.
- Hook `woocommerce_before/after_shop_loop_item` tidak dipanggil (menimbulkan tautan pembungkus ganda); plugin yang menambah elemen lewat hook itu tidak akan tampil di kartu.
