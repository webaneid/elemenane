# Fase 20: Halaman Kontak (`page-kontak.php`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Masalah Lama
- Satu kotak berisi nama + alamat di kiri dan daftar kontak rata-kanan di kanan, dengan ikon dekoratif `z-index:-1`, tanpa kartu / ikon per kontak, tanpa tombol aksi.
- Nomor telepon dicetak berulang di setiap baris repeater `ane_kontak`; WhatsApp perusahaan tidak ditampilkan.
- Formulir (Contact Form 7 di isi halaman) tidak diberi gaya dan tampil seperti HTML bawaan; peta tanpa tinggi / bingkai yang pasti dan memakai pin merah Google.

## 2. Desain Baru (`tp/contact-page.php`, `scss/_contact.scss`)
- Frame sama dengan halaman lain: breadcrumb, panel header (kicker nama perusahaan, judul halaman, ringkasan profil dari `ane_com_des`/`ane_com_tag`), `.container`, jarak ke footer.
- **Aksi cepat**: tombol Chat WhatsApp dan Telepon Sekarang.
- **Informasi Kontak**: kartu per kontak berikon bulat (alamat, telepon, handphone, fax, WhatsApp, email, website; nomor yang sama tidak diulang) lewat `elemenane_get_company_contacts( true )` yang juga dipakai fallback kontak cabang, plus ikon sosial (`tp/content-sosmed.php`).
- **Kirim Pesan**: isi halaman (formulir) di panel kartu; gaya Contact Form 7 (kolom pil, textarea rounded, tombol utama, pesan validasi / sukses). Bila halaman tanpa isi, kartu kontak memakai lebar penuh.
- **Lokasi Kami**: peta `ane_gmap` dalam kartu rounded dengan pin bertoken `--ane-warna-utama` (`js/gmap.js`) dan tombol Petunjuk Arah.
- **Banner cabang** ("Cari cabang terdekat") ke arsip cabang bila cabang aktif dan ada datanya.
- Semua warna memakai token tema; responsif: 1 kolom (mobile), 2 kolom kartu (tablet), 5/7 (≥ 993px).

## 3. Kompatibilitas
- Template tetap `page-kontak.php` ("Contact Us"); field ACF options yang dipakai tidak berubah (`ane_com_nama`, `ane_com_tag`, `ane_com_des`, `ane_com_alamat`, `ane_telepon`, `ane_kontak`, `ane_gmap`, `ane_whatsapp_no`, `ane_cs`, sosial).
- `tp/content-page-kontak.php` dihapus (hanya dipakai template ini); gaya `.ane-page-kontak` lama dihapus dari `_page.scss`. Skrip peta dimuat seperti sebelumnya (hanya di halaman ini, bila `ane_gmap` terisi).

## 4. File
- Baru: `tp/contact-page.php`, `scss/_contact.scss`.
- Ditulis ulang: `page-kontak.php`.
- Diubah: `inc/branch-data.php` (`elemenane_get_company_contacts( $include_fax )`), `js/gmap.js` (pin; versi 1.1.0 di `inc/acf.php`), `scss/_page.scss`, `scss/main.scss`, `css/main(.min).css`.
- Teks memakai msgid Indonesia seperti halaman cabang.

## 5. Verifikasi
Situs lokal: `/kontak/` desktop dan 375px tanpa overflow; kartu alamat / telepon / WhatsApp, ikon sosial, formulir Contact Form 7 bergaya (tombol Submit berukuran wajar), peta dengan pin tema, banner cabang.

## 6. Belum Diverifikasi
- Pengiriman formulir sungguhan (pesan sukses / validasi CF7), halaman tanpa peta atau tanpa formulir, dan kartu email / website (kosong di data lokal).
