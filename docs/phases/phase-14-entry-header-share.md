# Fase 14: Refactoring Entry Header & Share Bar (Copy Link + Title/Deskripsi/URL)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

## 1. Masalah Lama
- `entry-header` ditulis ulang di tiga template single dengan meta berupa string `By: ... on: ...`, judul `text-transform: capitalize`, dan CSS terpisah untuk video/galeri.
- Share bar: kotak 29px tanpa label, tidak ada Copy link / native share; pesan WhatsApp hanya berisi judul + 20 kata kutipan; id duplikat (`share-facebook`, dll.).
- Layout single memakai kolom 65/35 (`.ane-col-64`), lebarnya berbeda dari arsip.

## 2. Desain Baru
- **Entry header** (`tp/single-header.php`, dipakai single / video / galeri): chip kategori, judul fluid, *lead* (meta description SEO atau excerpt manual; tidak ditampilkan bila hanya hasil potongan konten), byline (avatar + penulis, tanggal, waktu baca, views, dengan ikon), share bar, lalu gambar utama (rounded + caption).
- **Share bar** (`ane_share_this_post( $label )`, `.ane-share`): tombol pil WhatsApp, Facebook, X, LinkedIn, Email, **Salin tautan**, dan **Bagikan lewat...** (native share sheet, hanya muncul bila `navigator.share` ada). Nama tombol terlihat ≥ 768px; ikon saja di ponsel (tetap punya `aria-label`). Tampil di header dan di akhir artikel; halaman (`content-page*.php`) tetap memakai satu bar.
- **Isi yang dibagikan** (`elemenane_get_share_data()`): judul, deskripsi, URL.
  - Deskripsi: meta description Yoast (variabel `%%...%%` di-resolve) → excerpt manual → 30 kata pertama konten.
  - WhatsApp: `*Judul*` + baris kosong + deskripsi + baris kosong + URL.
  - Salin tautan: `Judul\n\nDeskripsi\n\nURL` ke clipboard (fallback `execCommand` untuk http), konfirmasi inline.
  - Email: subject = judul, body = ketiganya. X: judul + deskripsi singkat + URL. Facebook & LinkedIn hanya menerima URL (mengambil judul/deskripsi dari tag Open Graph).
- **Layout** `single.php` memakai `.ane-archive__layout` yang sama dengan arsip (lebar konten seragam, jarak ke footer terjaga).

## 3. Kompatibilitas
- `ane_share_this_post()` tetap bernama sama (parameter label opsional); `ane_post_meta_v2()` dibiarkan untuk snippet lama tetapi tidak dipakai template.
- Elementor Theme Builder single tetap diutamakan.

## 4. File
- Baru: `tp/single-header.php`, `js/share.js`.
- Diubah: `tp/single.php`, `tp/single-video.php`, `tp/single-gallery.php`, `tp/content-page.php`, `tp/content-page-ns.php`, `single.php`, `inc/sosmed.php`, `inc/icons.php` (copy, check, share), `inc/elemenane.php` (enqueue/defer/l10n), `scss/_single-extras.scss`, `scss/_post.scss`, `scss/_global.scss` (`.share-this` dihapus), `languages/*`, `css/main(.min).css`.

## 5. Catatan
- `esc_url()` membuang `%0A` sehingga baris baru hilang dari tautan WhatsApp; href share dibangun dari skema tetap + `rawurlencode()` dan dicetak dengan `esc_attr()`.

## 6. Checklist
- [x] Template, fungsi share, JS, SCSS
- [x] Verifikasi: href WhatsApp memuat judul/deskripsi/URL dengan baris baru; klik Salin tautan (klik sungguhan) menampilkan konfirmasi; desktop dan 375px tanpa overflow
- [x] Quality gate
- [x] Dokumentasi + commit

## 7. Belum Diverifikasi
- Isi clipboard sebenarnya setelah menyalin dan native share sheet di ponsel nyata.
- Deskripsi dari Yoast dengan variabel `%%...%%` (data lokal memakai excerpt) dan template video / galeri.
