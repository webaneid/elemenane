# Arsitektur Keamanan — Elemen Ane (`elemenane`)

Dokumen ini mendefinisikan standar keamanan berlapis (defense-in-depth) yang diterapkan pada tema **Elemen Ane** untuk mencegah serangan XSS, CSRF, SQL Injection, kebocoran kredensial, dan injeksi webshell.

---

## 1. Perlindungan Eksekusi File Langsung (Anti-Webshell)
1. Seluruh file PHP di dalam tema (kecuali `functions.php` dan `index.php`) wajib diawali dengan guard:
   ```php
   defined( 'ABSPATH' ) || exit;
   ```
2. Skrip otomatis `bin/security-scan.sh` (`composer security:scan`) memverifikasi pada setiap commit/CI bahwa:
   - Tidak ada satu pun file PHP yang kehilangan guard `ABSPATH`.
   - Tidak ada fungsi eksekusi sistem/obfuscation berbahaya (`eval`, `exec`, `shell_exec`, `system`, `passthru`, `proc_open`, `popen`, `base64_decode`, `gzinflate`, `str_rot13`, `create_function`).
   - Tidak ada pemanggilan fungsi dinamis dari superglobal (`$_GET[...]()` atau `$_POST[...]()`).

---

## 2. Pencegahan Cross-Site Scripting (XSS)
1. **Late Output Escaping**:
   - Semua data dinamis (baik dari `get_field()`, `get_option()`, `get_the_title()`, maupun database) wajib di-escape tepat saat dicetak:
     - `esc_html()` untuk teks di dalam elemen HTML.
     - `esc_attr()` untuk nilai atribut HTML.
     - `esc_url()` untuk atribut `href`, `src`, `action`, dan URL skema.
     - `wp_kses_post()` untuk konten HTML kaya (artikel/deskripsi WYSIWYG).
     - `wp_json_encode()` untuk menyisipkan data PHP ke dalam blok `<script>` atau atribut JSON.
2. **Sanitasi Bio User**:
   - Filter inti WordPress `wp_filter_kses` pada `pre_user_description` **tidak boleh dihapus** agar user dengan role rendah tidak dapat menyuntikkan `<script>` pada halaman profil penulis (`author.php`).
3. **Pembatasan Field Script Pelacakan (Admin-Only Guard)**:
   - Field ACF di `ane-general-setting` yang menyuntikkan kode mentah ke `<head>`, `<body>`, dan `<footer>` (`ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`) wajib dilindungi dengan filter `acf/prepare_field` dan `acf/update_value` yang memeriksa `current_user_can( 'manage_options' )`.
4. **Keamanan Upload Media & SVG**:
   - Tema tidak boleh menimpa daftar MIME type default WordPress (`upload_mimes`).
   - Dukungan upload `image/svg+xml` hanya diberikan kepada Administrator (`current_user_can( 'manage_options' )`) dan wajib melewati pemeriksaan `wp_handle_upload_prefilter` yang menolak file SVG apabila mengandung `<script`, `onload=`, `onerror=`, `javascript:`, atau `<foreignObject`.

---

## 3. Pencegahan CSRF & Kontrol Akses (Authorization)
1. Setiap endpoint AJAX (`wp_ajax_*` / `wp_ajax_nopriv_*`) wajib memverifikasi nonce menggunakan `check_ajax_referer()`.
2. Aksi administratif via query string (seperti force-check pembaruan tema `?elemenane_force_check=1`) wajib memverifikasi:
   - Kapabilitas `current_user_can( 'update_themes' )`
   - Nonce valid melalui `check_admin_referer( 'elemenane_force_check' )`
3. Parameter debug yang menampilkan struktur internal (`print_r` / `var_dump`) dilarang keras di kode produksi.

---

## 4. Manajemen Kredensial & API Key
1. **Google Maps API Key** (`elemenane_get_google_maps_api_key()`):
   - Prioritas 1: Konstanta `ELEMENANE_GMAPS_API_KEY` di `wp-config.php`.
   - Prioritas 2: Field ACF `ane_gmap_api_key` atau `google_api_key` di Theme Options (`option`).
   - Prioritas 3: Default key ter-obfuscate XOR-Hex (`elemenane_get_default_gmaps_key()`) sehingga tidak ada string plaintext `AIza...` di dalam repositori.
2. **GitHub Updater Token** (`inc/updater.php`):
   - Mendukung repositori publik maupun privat melalui konstanta `ELEMENANE_UPDATER_TOKEN` di `wp-config.php` atau opsi `ane_updater_token`.

---

## 5. Checklist Security Review (Wajib Sebelum Rilis)
- [ ] `composer security:scan` lulus tanpa error dan tanpa warning.
- [ ] `composer lint:phpcs` lulus tanpa pelanggaran `WordPress.Security.*`.
- [ ] Tidak ada `echo` variabel mentah tanpa fungsi escaping.
- [ ] Semua query database kustom di `inc/admin/linktree-analytics.php` menggunakan `$wpdb->prepare()`.
