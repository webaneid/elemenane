# Fase 01: Hardening GitHub Auto-Updater & Optimasi Bootstrap `functions.php`

- **Status**: Completed
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: `GAP-04`, `GAP-05`, `GAP-12`, `GAP-13`

---

## 1. Tujuan & Ruang Lingkup
### In-Scope
- Mendefinisikan konstanta inti tema (`ELEMENANE_VERSION`, `ELEMENANE_DIR`, `ELEMENANE_URI`) di `functions.php`.
- Menghapus pengecekan usang WP `< 4.7` (`inc/back-compat.php` tidak ada) di `functions.php`.
- Memindahkan pengaturan ukuran gambar default dari setiap `after_setup_theme` ke fungsi `elemenane_configure_image_sizes()` yang dijalankan pada `after_switch_theme` dan satu kali per versi di `admin_init`.
- Melakukan hardening penuh pada `inc/updater.php` sesuai blueprint standar Webane (`newsane` / `sekolahane`):
  - Dukungan token GitHub untuk repositori privat & rate-limit tinggi (`ELEMENANE_UPDATER_TOKEN` atau opsi/field ACF `ane_updater_token`).
  - Dukungan modal detail changelog rilis (`themes_api`).
  - Proteksi `current_user_can( 'update_themes' )` + `check_admin_referer( 'elemenane_force_check' )` pada aksi force-check.
  - Menghapus kode debug `print_r()` (`$_GET['ane_debug']` / `$_GET['elemenane_debug']`).
- Memperbarui tautan "Check Updates Now" di `inc/admin.php` agar menyertakan nonce `wp_nonce_url( ..., 'elemenane_force_check' )`.

### Out-of-Scope
- Penggantian `glob()` menjadi eksplisit `require_once` di `functions.php` (dijadwalkan di Fase 03).

---

## 2. Daftar File yang Diubah / Dibuat
- `docs/phases/phase-01-updater-dan-bootstrap.md`
- `functions.php`
- `inc/updater.php`
- `inc/admin.php`

---

## 3. Langkah Implementasi & Checklist
- [x] Buat spesifikasi `docs/phases/phase-01-updater-dan-bootstrap.md`.
- [x] Update `functions.php` (konstanta `ELEMENANE_*`, hapus `inc/back-compat.php`, optimasi `elemenane_configure_image_sizes()`).
- [x] Refactor & hardening `inc/updater.php`.
- [x] Tambahkan `wp_nonce_url()` pada link force check di `inc/admin.php`.
- [x] Jalankan verifikasi `composer lint:syntax`, `composer lint:changed`, `composer analyse`, `composer security:scan`, `composer css:check`, dan `composer dist:check`.

---

## 4. Kriteria Selesai (Definition of Done)
- [x] `composer lint:syntax` lulus (`0` error).
- [x] `composer lint:changed` lulus (`0` error WPCS pada file yang diubah).
- [x] `composer analyse` lulus (`0` error).
- [x] `composer security:scan` tidak lagi menampilkan warning `print_r` di `inc/updater.php`.
- [x] `composer css:check` & `composer dist:check` lulus.
