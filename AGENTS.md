# AGENTS.md — Elemen Ane WordPress Theme

> **Single Source of Truth** untuk seluruh AI Agent (Claude Code, Antigravity, Cursor, Copilot, Windsurf) dan developer manusia yang bekerja pada repositori **Elemen Ane** (`elemenane`).

## 1. Identitas Proyek & Konteks Bisnis
- **Nama Tema**: Elemen Ane (`elemenane`)
- **Vendor**: Webane Indonesia (`https://webane.com`)
- **Target Pengguna**: Portal berita/majalah digital, company profile korporat, katalog produk WooCommerce-compatible, direktori cabang/lokasi, serta landing page berbasis Elementor milik klien Webane.
- **Stack Utama**:
  - WordPress `>= 6.4` (Classic/Hybrid Theme dengan dukungan penuh Elementor, Block Editor, & Customizer)
  - PHP `>= 8.0` (kompatibel penuh hingga PHP `8.3`)
  - Advanced Custom Fields (ACF) Pro (wajib aktif untuk Theme Options & Custom Fields)
  - Elementor (opsional/direkomendasikan — sinkronisasi otomatis Global Colors & Fonts ke CSS Custom Properties `--e-global-*` di `:root`)
  - Frontend: Modular SCSS (`scss/[!_]*.scss`) yang dikompilasi ke CSS terpisah per konteks halaman (`css/main.min.css`, `css/catalog.min.css`, `css/landing.min.css`, `css/linktree-page.min.css`, `css/admin.min.css`), jQuery (bawaan WP), OwlCarousel 2, Magnific Popup.

---

## 2. Aturan Emas (Non-Negotiable Rules)

### 2.1. Kompatibilitas Mundur (Backward Compatibility)
Tema ini sudah berjalan di situs produksi klien. **DILARANG KERAS** mengubah:
- Nama field ACF (`get_field(...)`, `get_sub_field(...)`) yang sudah ada.
- Slug Option Page ACF: `ane-setup`, `ane-general-setting`, `ane-customer-care`, `ane-seo-news`.
- Slug Custom Post Type (`product`, `service`, `branch`) dan Taxonomy (`product_cat`, `product_tag`).
- Meta key produk yang mengikuti standar WooCommerce: `_price`, `_regular_price`, `_sale_price`, `_sku`, `_stock`, `_stock_status`, `_manage_stock`, `_product_image_gallery`.
- Nama tabel database kustom: `{$wpdb->prefix}ane_linktree_clicks`.
- Lokasi menu: `menuutama`, `mobilemenu`, `menufooter`.
- ID Sidebar/Widget Area: `default-sidebar`, `home-sidebar`, `home-1`, `home-2`, `home-3`, `banner-sidebar`.
- Ukuran thumbnail kustom: `kotak` (`300x300` hard crop).

### 2.2. Standar Keamanan (Security-First & Anti-Webshell)
1. **Direct Access Guard**: Setiap file PHP (kecuali `functions.php` dan `index.php`) **WAJIB** memiliki guard di baris awal:
   ```php
   defined( 'ABSPATH' ) || exit;
   ```
2. **Escape Output Late**: Setiap variabel PHP yang dicetak ke HTML/atribut/JS **WAJIB** di-escape tepat saat `echo`:
   - Teks biasa: `esc_html()`, `esc_html__()`, `esc_html_e()`
   - Atribut HTML: `esc_attr()`, `esc_attr__()`, `esc_attr_e()`
   - URL / Link: `esc_url()`
   - Blok HTML dari WYSIWYG / ACF textarea: `wp_kses_post()`
   - JSON / Data ke JS: `wp_json_encode()` atau `wp_localize_script()`
3. **Sanitasi Input & Nonce**:
   - Semua akses `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER` wajib melalui `wp_unslash()` + fungsi sanitasi (`sanitize_text_field()`, `absint()`, `sanitize_key()`, dll.).
   - Setiap handler form admin, AJAX (`wp_ajax_*`), atau aksi sensitif wajib memverifikasi nonce (`check_admin_referer()` / `check_ajax_referer()`) dan kapabilitas user (`current_user_can()`).
   - Field ACF yang menyuntikkan HTML/script mentah ke `<head>`/`<body>` (`ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`) wajib dilindungi kapabilitas `manage_options` (hanya Administrator yang dapat melihat dan menyimpan field tersebut).
4. **Zero Hardcoded Secrets**: Dilarang menyimpan API Key atau token GitHub secara plaintext di source code. Gunakan konstanta `wp-config.php` (`ELEMENANE_GMAPS_API_KEY`, `ELEMENANE_UPDATER_TOKEN`) dengan fallback ke ACF Options atau default key terenkripsi/obfuscated.
5. **Larangan Fungsi Berbahaya**: Dilarang menggunakan `eval()`, `exec()`, `shell_exec()`, `system()`, `passthru()`, `base64_decode()`, `create_function()`, atau pemanggilan fungsi dinamis dari superglobal (`$_GET['a']($_POST['b'])`).

### 2.3. Konvensi Penamaan & Prefix
- **Fungsi baru**: wajib menggunakan prefix `elemenane_` (contoh: `elemenane_get_google_maps_api_key()`, `elemenane_icon()`). Fungsi lama ber-prefix `ane_` tetap dipertahankan demi kompatibilitas template.
- **Konstanta**: wajib menggunakan prefix `ELEMENANE_` (`ELEMENANE_VERSION`, `ELEMENANE_DIR`, `ELEMENANE_URI`, `ELEMENANE_WEBP_QUALITY`).
- **Text Domain**: seluruh fungsi translasi (`__()`, `_e()`, `esc_html__()`, `esc_html_e()`, `_n()`, `_x()`) **WAJIB** menggunakan `'elemenane'`. Dilarang menggunakan text domain warisan (`'newsane'`, `'webane'`, `'roots'`, `'autoane'`).

### 2.4. Arsitektur SCSS & Split CSS Per Konteks Halaman
- **Sumber Styling**: Seluruh perubahan CSS wajib ditulis di dalam folder `scss/` (`_tokens.scss`, `_global.scss`, `_header.scss`, `_post.scss`, `_product.scss`, `_branch.scss`, `_landingpage.scss`, `_linktree.scss`, `_admin-*.scss`, dll.).
- **5 Entrypoint SCSS → CSS**:
  1. `scss/main.scss` → `css/main.css` & `css/main.min.css` (dimuat global di frontend)
  2. `scss/catalog.scss` → `css/catalog.css` & `css/catalog.min.css` (dimuat kondisional pada halaman CPT `product` & taxonomy `product_cat`/`product_tag`)
  3. `scss/landing.scss` → `css/landing.css` & `css/landing.min.css` (dimuat kondisional pada `page-home.php` / front page)
  4. `scss/linktree-page.scss` → `css/linktree-page.css` & `css/linktree-page.min.css` (dimuat kondisional pada `page-linktree.php`)
  5. `scss/admin.scss` → `css/admin.css` & `css/admin.min.css` (dimuat di wp-admin & halaman login)
- **Kompilasi Wajib**: Setiap kali mengubah file `.scss`, jalankan kompilasi SCSS ke `.css` dan `.min.css` serta verifikasi dengan `composer css:check`.
- **Reuse Class & CSS Variables**: Gunakan variabel CSS global (`--e-global-color-primary`, `--e-global-color-secondary`, `--e-global-color-text`, `--e-global-color-accent`, `--ane-color-*`) yang disinkronkan dengan Elementor Kit.

---

## 3. Perintah Verifikasi Kualitas (Quality Gate)

Jalankan perintah berikut sebelum menyelesaikan fase atau membuat commit:

```bash
# 1. Cek sintaks seluruh file PHP
composer lint:syntax

# 2. Cek standar kode & keamanan (WPCS) pada file yang diubah
composer lint:changed

# 3. Cek seluruh codebase dengan PHPCS
composer lint:phpcs

# 4. Static analysis PHPStan (Level 5 + WordPress stubs)
composer analyse

# 5. Scan pola keamanan & anti-backdoor
composer security:scan

# 6. Audit keseimbangan kurung kurawal SCSS/CSS & kelengkapan .min.css
composer css:check

# 7. Verifikasi paket distribusi produksi (.distignore)
composer dist:check
```

---

## 4. Peta Dokumentasi (`docs/`)
Sebelum memulai pekerjaan atau fase baru, baca dokumen berikut:
- [`docs/SOP.md`](docs/SOP.md) — Standar Operasional Prosedur pengerjaan bertahap per fase.
- [`docs/PROGRESS.md`](docs/PROGRESS.md) — Status seluruh fase dan checklist progres saat ini.
- [`docs/RETROFIT-GAPS.md`](docs/RETROFIT-GAPS.md) — Daftar lengkap temuan audit keamanan, bug PHP 8.x, dan gap performa.
- [`docs/conventions.md`](docs/conventions.md) — Konvensi kode PHP, SCSS/CSS, JS, dan Git commit (`Conventional Commits`).
- [`docs/architecture/architecture-overview.md`](docs/architecture/architecture-overview.md) — Arsitektur lengkap tema, daftar modul `inc/`, kontrak database, CPT, dan alur kerja.
- [`docs/architecture/architecture-security.md`](docs/architecture/architecture-security.md) — Arsitektur keamanan, pencegahan XSS/CSRF/Webshell, dan checklist security review.
- [`docs/architecture/architecture-css.md`](docs/architecture/architecture-css.md) — Arsitektur SCSS modular, split CSS per halaman, dan sistem desain Elementor Kit.
- [`docs/architecture/architecture-deployment.md`](docs/architecture/architecture-deployment.md) — Pipeline CI/CD GitHub Actions, Semantic Release, dan Auto-Updater.
- [`docs/lessons-learned.md`](docs/lessons-learned.md) — Catatan pembelajaran teknis dan jebakan (pitfalls) yang harus dihindari.
