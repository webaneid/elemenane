# Arsitektur SCSS & Split CSS — Elemen Ane (`elemenane`)

## 1. Filosofi Desain & Arsitektur Split CSS
Berbeda dari tema sederhana yang menggabungkan seluruh CSS ke satu file raksasa, **Elemen Ane** melayani berbagai tipe halaman (Portal Berita, Landing Page Elementor, Katalog Produk E-Commerce, Direktori Cabang, dan Halaman Bio Linktree). Untuk menjaga ukuran CSS tetap ramping di setiap halaman (mendukung skor PageSpeed Mobile >= 90), `elemenane` menerapkan **arsitektur SCSS modular dengan 5 entrypoint terpisah**.

---

## 2. Peta Entrypoint SCSS → CSS

| Entrypoint SCSS | Output CSS Produksi | Kondisi Pemuatan (Enqueue) |
| :--- | :--- | :--- |
| `scss/main.scss` | `css/main.min.css` | Dimuat secara global di seluruh halaman frontend (`inc/elemenane.php`) |
| `scss/catalog.scss` | `css/catalog.min.css` | Dimuat hanya pada halaman CPT `product`, `archive-product.php`, `single-product.php`, dan taxonomy `product_cat`/`product_tag` |
| `scss/landing.scss` | `css/landing.min.css` | Dimuat hanya pada template `page-home.php` / halaman beranda |
| `scss/linktree-page.scss` | `css/linktree-page.min.css` | Dimuat hanya pada template `page-linktree.php` (`inc/linktree.php`) |
| `scss/admin.scss` | `css/admin.min.css` | Dimuat hanya di area `wp-admin` dan `wp-login.php` (`inc/admin.php`) |

---

## 3. Struktur Partial SCSS (`scss/_*.scss`)
- **Fondasi & Variabel**:
  - `_tokens.scss` — Token warna, tipografi, border-radius, dan jembatan ke variabel Elementor (`--e-global-color-*`).
  - `_mixins.scss` — Mixin media query responsif, flexbox, dan transisi.
  - `_resets.scss` & `_global.scss` — Reset CSS dasar, tipografi body, dan utilitas layout.
  - `_kolom.scss` — Sistem grid/kolom ringan (`.ane-col-*`, `.row`).
  - `_button.scss` & `_pagination.scss` — Komponen tombol dan navigasi halaman.
- **Layout & Berita**:
  - `_header.scss`, `_footer.scss`, `_sidebar.scss`, `_page.scss`
  - `_anecontent.scss`, `_post.scss`, `_arsip.scss`, `_banner.scss`, `_owlcarousel.scss`, `_bottomchat.scss`
- **Modul Spesifik**:
  - `_product.scss` (diimpor oleh `catalog.scss`)
  - `_branch.scss` (diimpor oleh `main.scss`)
  - `_landingpage.scss` (diimpor oleh `landing.scss`)
  - `_linktree.scss` (diimpor oleh `linktree-page.scss`)
  - `_admin-*.scss` (12 partial admin yang diimpor oleh `admin.scss`)

---

## 4. Integrasi CSS Custom Properties dengan Elementor Kit
Modul `inc/elementor.php` membaca palet warna dari Elementor Active Kit (`system_colors` & `custom_colors`) maupun fallback ACF Theme Options, lalu menyuntikkan variabel CSS ke `:root` (baik di frontend maupun block editor):
- `--e-global-color-primary` / `--ane-color-primary`
- `--e-global-color-secondary` / `--ane-color-secondary`
- `--e-global-color-text` / `--ane-color-text`
- `--e-global-color-accent` / `--ane-color-accent`

Dengan demikian, perubahan warna di **Elementor Site Settings → Global Colors** otomatis mengubah tampilan seluruh komponen tradisional tema (header, kartu berita, katalog produk, hingga tombol WhatsApp).

---

## 5. Alur Kerja Kompilasi & Audit CSS
1. Edit file partial di `scss/_*.scss` atau entrypoint di `scss/*.scss`.
2. Kompilasi dan verifikasi menggunakan perintah:
   ```bash
   python3 bin/css-audit.py --compile
   composer css:check
   ```
3. Pada pipeline rilis otomatis (`.github/workflows/release.yml`), Dart Sass mengompilasi ulang seluruh `scss/[!_]*.scss` ke `css/*.css` dan `css/*.min.css` sebelum membangun arsip ZIP produksi.
