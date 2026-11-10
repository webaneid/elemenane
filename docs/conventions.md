# Konvensi Kode & Rekayasa — Elemen Ane (`elemenane`)

## 1. Standar PHP & WordPress Coding Standards (WPCS)
- **Versi PHP Target**: PHP `8.0` ke atas (diuji pada `8.0`, `8.2`, `8.3`).
- **Indentasi**: Gunakan **Tab** untuk indentasi (standar WordPress), bukan spasi.
- **Yoda Conditions**: Gunakan Yoda condition untuk perbandingan terhadap literal (`if ( 'yes' === $value )`).
- **Array Syntax**: Gunakan `array( ... )` sesuai standar WordPress Core, atau ikuti aturan `phpcs.xml.dist`.
- **Direct Access Guard**:
  Setiap file PHP selain `functions.php` dan `index.php` wajib diawali dengan:
  ```php
  <?php
  defined( 'ABSPATH' ) || exit;
  ```
- **Prefixing**:
  - Fungsi baru: `elemenane_<nama_fungsi>()`
  - Konstanta: `ELEMENANE_<NAMA_KONSTANTA>`
  - Hook/Filter internal: `elemenane_<nama_hook>` (tetap dukung hook lama `ane_*` bila sudah dipakai di template)
- **Internasionalisasi (i18n)**:
  - Text domain tunggal: `'elemenane'`
  - Selalu gunakan fungsi translasi yang langsung melakukan escaping (`esc_html__()`, `esc_html_e()`, `esc_attr__()`, `esc_attr_e()`).

## 2. Keamanan Data (Sanitasi & Escaping)
- **Input (`$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`)**:
  - Selalu gunakan `wp_unslash()` sebelum fungsi sanitasi:
    ```php
    $paged = isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 1;
    ```
- **Output**:
  - Jangan pernah melakukan `echo $var;` mentah.
  - Gunakan `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`, atau `wp_json_encode()`.
  - Untuk helper icon SVG internal (`elemenane_icon()`), bungkus dengan komentar `// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static trusted SVG output` atau gunakan whitelist `wp_kses()` khusus SVG.

## 3. Konvensi SCSS & Split CSS
- **Lokasi Sumber**: `scss/`
- **File Entrypoint (Non-Partial)**:
  - `scss/main.scss` → `css/main.css` & `css/main.min.css`
  - `scss/catalog.scss` → `css/catalog.css` & `css/catalog.min.css`
  - `scss/landing.scss` → `css/landing.css` & `css/landing.min.css`
  - `scss/linktree-page.scss` → `css/linktree-page.css` & `css/linktree-page.min.css`
  - `scss/admin.scss` → `css/admin.css` & `css/admin.min.css`
- **Penamaan Class**:
  - Gunakan prefix `.ane-` untuk komponen tema (contoh: `.ane-katalog`, `.ane-branch`, `.ane-overlay`, `.ane-Overlay-bg`).
  - Manfaatkan CSS Custom Properties Elementor (`var(--e-global-color-primary)`, dll.) dan token tema (`var(--ane-color-primary)`).

## 4. Konvensi Commit & Rilis (Semantic Release)
Semua pesan commit ke branch `main` wajib mengikuti spesifikasi **Conventional Commits** karena digunakan oleh `semantic-release` untuk menentukan versi rilis otomatis:
- `feat(scope): deskripsi fitur baru` → menaikkan versi **MINOR** (`1.1.x` → `1.2.0`)
- `fix(scope): deskripsi perbaikan bug/keamanan` → menaikkan versi **PATCH** (`1.1.8` → `1.1.9`)
- `perf(scope): optimasi performa / Core Web Vitals` → menaikkan versi **PATCH**
- `refactor(scope): perapian kode tanpa ubah perilaku` → tidak memicu rilis baru kecuali disertakan catatan rilis
- `docs(scope): pembaruan dokumentasi` → tidak memicu rilis
- `chore(scope): pembaruan tooling/CI` → tidak memicu rilis
