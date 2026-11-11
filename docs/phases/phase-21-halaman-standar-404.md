# Fase 21: Halaman Standar (Page, No Sidebar) & 404

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## Inventaris Template Halaman
| Template | Keterangan | Status |
|---|---|---|
| `page.php` | Halaman standar (dengan sidebar) | Dirombak |
| `page-nosidebar.php` ("No Sidebar") | Halaman kolom baca tanpa sidebar | Dirombak |
| `page-home.php` ("Landing Page") | Landing berita | Fase 12 |
| `page-kontak.php` ("Contact Us") | Kontak | Fase 20 |
| `404.php` | Halaman tidak ditemukan | Dirombak |
| `page-linktree.php` ("Link Tree") | Halaman tautan (CSS sendiri, `linktree-page.css`) | Tidak diubah (desain mandiri) |
| `index.php`, `tp/content-none.php` | Fallback / kosong | `content-none` kini memakai empty state bersama |

## Masalah Lama
- `page.php` dan `page-nosidebar.php` memakai dua template konten hampir identik (`content-page*.php`), judul dengan bar warna, gambar placeholder dari `dummyimage.com` (request eksternal) saat tanpa gambar utama, bar share di atas, lebar `.ane-col-8 .ane-isi 80%` + `70%` yang tidak konsisten.
- `404.php` berisi blok `<style>` dan puluhan atribut `style=""` inline, warna hard-coded (`#666`, `#f9f9f9`, `#333`) dan tautan `javascript:history.back()`.
- `tp/content-none.php` memanggil `the_ID()` / `post_class()` di luar loop dan memakai `dummyimage.com`.

## Desain Baru
- **`tp/page-layout.php`** (satu layout untuk page dan No Sidebar): frame arsip (breadcrumb, panel judul dengan kicker nama halaman induk bila ada, `.container`, jarak ke footer); gambar utama hanya bila ada (rounded, caption); isi di `.ane-content` (Fase 15); `wp_link_pages`; bar share di akhir; komentar Facebook; sidebar bila aktif. Tanpa sidebar: kolom baca 860px di tengah.
- Halaman WooCommerce (cart / checkout / akun): lebar penuh container, tanpa sidebar dan tanpa bar share.
- **`tp/error-404.php`**: hero dengan angka 404 bergradasi token, pesan, kolom pencarian, tombol Beranda, chip pintasan dari menu utama (maks. 6), dan 3 artikel terbaru memakai kartu arsip. Tanpa inline style, tanpa `javascript:`; animasi menghormati `prefers-reduced-motion`.
- `page.php`, `page-nosidebar.php`, `404.php` kini tipis; Elementor Theme Builder (`single` location) tetap diutamakan; penghitung views halaman tetap.

## File
- Baru: `tp/page-layout.php`, `tp/error-404.php`.
- Ditulis ulang: `page.php`, `page-nosidebar.php`, `404.php`, `tp/content-none.php`, `scss/_page.scss`.
- Dihapus: `tp/content-page.php`, `tp/content-page-ns.php`.

## Verifikasi
Situs lokal: halaman 404 (hero, pencarian, chip menu, artikel terbaru) dan halaman default `/cart/` (panel judul, `.ane-content`, sidebar, bar share) tampil benar; quality gate lulus.

## Belum Diverifikasi
- Template "No Sidebar" dan halaman dengan gambar utama / halaman anak (tidak ada di data lokal; markup mengikuti jalur yang sama).
- Halaman WooCommerce (cart / checkout / akun) dengan WooCommerce aktif.
