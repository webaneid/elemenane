# ADR-0003: Hardening GitHub Auto-Updater dengan Dukungan Repositori Privat & Proteksi Nonce

- **Status**: Accepted
- **Tanggal**: 2026-11-10
- **Pengambil Keputusan**: Tim Engineering Webane Indonesia

## Konteks & Masalah
Implementasi awal `inc/updater.php` di `elemenane` memiliki dua kelemahan:
1. Endpoint `?elemenane_debug=1` mencetak data internal dengan `print_r()` dan `?elemenane_force_check=1` tidak memverifikasi nonce maupun kapabilitas `update_themes`.
2. Belum mendukung otentikasi Bearer Token (`ELEMENANE_UPDATER_TOKEN`) untuk repositori privat atau menghindari rate limit GitHub API, serta belum menampilkan popup detail changelog (`themes_api`).

## Keputusan
Memperbarui `inc/updater.php` mengikuti blueprint standar Webane (`newsane` / `sekolahane`):
1. Menghapus kode debug `print_r` dan mewajibkan `current_user_can( 'update_themes' )` + `check_admin_referer( 'elemenane_force_check' )` pada aksi force-check.
2. Menambahkan dukungan token GitHub via konstanta `ELEMENANE_UPDATER_TOKEN` atau opsi `ane_updater_token`.
3. Menambahkan filter `themes_api` untuk menampilkan changelog GitHub Release di modal detail versi WordPress.

## Konsekuensi
### Positif
- Bebas dari celah CSRF dan information disclosure di halaman admin.
- Mendukung deployment pada repositori GitHub publik maupun privat tanpa mengubah kode updater.
