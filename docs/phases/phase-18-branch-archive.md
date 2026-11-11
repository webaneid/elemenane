# Fase 18: Arsip Cabang (`/branch/`) + `ane-branch-card`

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Masalah Lama
- `archive-branch.php` 150 baris dengan markup kartu yang diduplikasi di JS, memakai `.ane-container` (1200px), header tengah polos, ikon `dashicons`, `style=""` inline, dan modal `fadeIn` tanpa ARIA.
- Warna hard-coded (`#666`, `#999`, `#e0e0e0`, `rgba(0,123,255,…)`, hijau/abu bootstrap) bercampur dengan token tema; pin peta memakai ikon titik merah Google.
- Nama kelas dialog / daftar (`.ane-branch-modal`, `.ane-branch-list`) bentrok dengan dialog cabang di halaman produk (CSS `catalog.css` yang sama).

## 2. Desain Baru
- **Frame** sama dengan arsip lain: breadcrumb, panel header (kicker Cabang, judul, deskripsi, jumlah cabang), `.container`, jarak ke footer (`tp/branch-archive.php`).
- **Filter**: panel kartu, label kapital kecil, select berbentuk pil (ID `ane-filter-province` / `ane-filter-city` / `ane-reset-filter` tetap).
- **Peta**: kartu rounded dengan bayangan; **pin dibuat dari token `--ane-warna-utama`** (SVG), bukan ikon merah Google; info window bertoken.
- **Kartu cabang** (`tp/branch-card.php`, `.ane-branch-card`): foto 16:10 (placeholder ikon lokasi bila tanpa foto), chip nama kota (nama, bukan ID), judul bertaut ke halaman cabang, alamat (3 baris), telepon, jam buka (baris pertama), tombol Detail (dialog), Petunjuk Arah, dan tombol WhatsApp. Kartu aktif (dipilih dari peta) bercincin warna utama.
- **Grid** `.ane-branch-grid`: 1 → 2 (≥ 640px) → 3 (≥ 993px) kolom; tombol "Tampilkan Lebih Banyak" pil.
- **Dialog detail** (`.ane-branch-dialog*`): `role="dialog"`, fokus ke tombol tutup lalu kembali ke pemicu, Esc / klik overlay menutup, daftar info berikon (alamat, telepon, email, WhatsApp, jam).
- **JS** (`js/branch-archive.js`): `buildBranchCard()` menghasilkan markup yang sama dengan `tp/branch-card.php`; ikon dan teks datang dari `aneMapConfig` (`inc/branch-acf.php`), tidak lagi hard-coded.

## 3. Warna
Seluruh warna di `_branch.scss` (arsip dan halaman single cabang) kini memakai token `--ane-warna-utama`, `-gelap`, `-putih`, `-alternatif` (dan varian `-rgb`) yang disinkronkan dari Elementor Kit. `.ane-btn-branch` / `.ane-btn-outline` di `_product.scss` juga dipindah ke token. Warna merek pihak ketiga (hijau WhatsApp, warna marketplace) sengaja dipertahankan.

## 4. File
- Baru: `tp/branch-archive.php`, `tp/branch-card.php`.
- Ditulis ulang: `archive-branch.php`, bagian arsip `scss/_branch.scss`, `js/branch-archive.js` (kartu, dialog, pin, i18n).
- Diubah: `inc/branch-acf.php` (aneMapConfig: pageSize, ikon, i18n; versi skrip 1.1.0), `scss/_product.scss` (token), `docs/lessons-learned.md`, `css/main|catalog(.min).css`.
- Teks menggunakan msgid Indonesia (sama seperti string cabang yang sudah ada), sehingga tidak perlu entri `.po` baru.

## 5. Verifikasi
Situs lokal (21 cabang, Google Maps aktif): desktop dan 375px tanpa overflow; chip kota menampilkan nama; Detail membuka dialog dengan fokus di tombol tutup; Load More 12 → 21 kartu; pin bertoken; dialog cabang di halaman produk tetap berfungsi (tidak lagi bentrok).

## 6. Belum Diverifikasi
- Filter provinsi / kota setelah perubahan markup (logika JS tidak diubah, tetapi tidak diuji ulang secara manual), klik pin peta → kartu aktif, dan tombol WhatsApp pada cabang dengan nomor.
- Halaman single cabang (`single-branch.php`): hanya warnanya yang ditokenkan, desainnya belum dirombak.
