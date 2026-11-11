# Fase 19: Single Cabang + "Hubungi Kami" dengan Fallback Kontak Perusahaan

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Masalah Lama
- Kartu "Hubungi Kami" kosong bila cabang tidak punya telepon / WhatsApp / email (di data lokal 19 dari 21 cabang).
- `single-branch.php` 330 baris: `.ane-container` (1200px), ikon `dashicons`, galeri memakai `data-fancybox` (pustaka tidak dimuat), nomor WhatsApp dipakai mentah (`wa.me/{nomor}` tanpa normalisasi), peta dengan pin merah Google, dan "Cabang Terdekat" memakai kartu mini sendiri (`ane-branch-card-mini`) yang berbeda dari arsip.
- Nama provinsi / kota dibaca ulang dari JSON di setiap halaman dan setiap kartu terkait.

## 2. Aturan Kontak
`elemenane_get_branch_contacts()` (`inc/branch-data.php`):
1. Bila cabang punya **salah satu** dari telepon / WhatsApp / email → tampilkan data cabang saja (nomor WhatsApp dinormalisasi: digit, awalan 0 → 62).
2. Bila **tidak ada satu pun** → ambil dari pengaturan kontak perusahaan (ACF options) dan tampilkan catatan "Cabang ini belum memiliki kontak sendiri. Silakan hubungi kantor pusat kami.":
   - Telepon: `ane_telepon`, lalu nomor handphone di repeater `ane_kontak` (nomor yang sama tidak diulang).
   - WhatsApp: `ane_whatsapp_no`, jika kosong nomor pertama di `ane_cs`, jika kosong angka pada tautan `ane_whatsapp`.
   - Email dan website: baris pertama repeater `ane_kontak` yang terisi.
3. Tidak ada keduanya → kartu disembunyikan.
Cabang tidak dicampur per-jenis (mis. telepon cabang + WhatsApp pusat) agar pengunjung tidak salah menghubungi lokasi lain.

## 3. Desain Baru
- Frame sama dengan halaman lain: breadcrumb, panel header (kicker nama kota, judul, chip lokasi), `.container`, jarak ke footer.
- Foto utama rounded; dua kolom (konten + aside 340px, aside menempel di desktop): Alamat, Peta Lokasi (pin token `--ane-warna-utama`, tombol Petunjuk Arah), Galeri Foto (grid + lightbox Magnific `.gallery-popup`), kartu **Hubungi Kami** (ikon bulat, tombol Chat WhatsApp) dan **Jam Operasional**.
- **Cabang Terdekat Lainnya** memakai kartu arsip yang sama (`tp/branch-card.php`) dalam `.ane-branch-grid`; tombol Detail menjadi tautan ke halaman cabang (argumen `detail => link`), Petunjuk Arah disembunyikan bila cabang tidak punya koordinat.
- Judul section memakai `.ane-section-title` (gaya sidebar). Semua warna memakai token tema.

## 4. File
- Baru: `inc/branch-data.php` (`elemenane_get_branch_data()`, nama lokasi sekali baca, kontak cabang / perusahaan), `tp/branch-contact.php`.
- Ditulis ulang: `single-branch.php`, bagian single di `scss/_branch.scss`.
- Diubah: `tp/branch-archive.php` (memakai helper yang sama), `tp/branch-card.php` (mode link, koordinat opsional), `js/single-branch.js` (pin bertoken; versi 1.1.0), `inc/branch-acf.php`, `functions.php`, `css/main|catalog(.min).css`.
- Teks memakai msgid Indonesia seperti string cabang lainnya.

## 5. Verifikasi
Situs lokal: 12 cabang pertama diperiksa lewat fetch. 11 cabang tanpa kontak memakai kontak perusahaan (2 item: telepon 085210626455 dan WhatsApp +6281225949952, plus catatan); Cabang Kertajati yang punya kontak sendiri menampilkan 3 item miliknya tanpa catatan. Tiga kartu cabang terdekat tampil di tiap halaman. Desktop dan 375px tanpa overflow; peta dan info window tampil.

## 6. Belum Diverifikasi
- Galeri foto dengan lightbox (data lokal tidak punya foto galeri) dan cabang tanpa koordinat / tanpa foto utama.
- Email dan website perusahaan pada kartu fallback (nilai kosong di data lokal).
