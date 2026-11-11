# Fase 15: Perbaikan Total `.ane-content` (Isi Artikel) Konsisten di Mobile

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11

## 1. Akar Masalah
- `.ane-single .entry-content` memakai `FlexColumn` (`flex-direction: column; flex-wrap: wrap`). Pada flex kolom yang membungkus, lebar baris mengikuti anak terlebar (gambar 600px), bukan lebar container. Hasilnya `.ane-content` selebar 600px di layar 375px sehingga gambar dan elemen lain "keluar jalur".
- Mekanisme bleed ke tepi layar memakai `DisplayLebar` (`width: auto; display: flex; margin -15px`) yang mengubah `figure` menjadi flex container dan rapuh.
- `.wp-block-cover` memakai `height: 400px` + `background-attachment: fixed` (rusak di iOS); embed memakai trik `height: 0 !important; padding-bottom: 116%` yang memotong embed sosial; `float: center` tidak valid; ornamen `z-index: -1`; `ul.wp-block-latest-posts` melayang 50% lebar.

## 2. Perbaikan
- `.entry-content` kini `display: block; min-width: 0`; `.ane-content` `width: 100%; min-width: 0; max-width: 100%`, semua media `max-width: 100%`.
- `scss/_anecontent.scss` ditulis ulang, tanpa lebar piksel tetap:
  - ritme teks (heading `clamp()`, jarak 1.4em, tautan bergaris bawah, `mark`), daftar (panah/kotak aksen, `::marker` berwarna pada `ol`).
  - `blockquote` / quote / pullquote kartu bertepi aksen; `pre` / `code` bergulir horizontal.
  - gambar, caption, `alignleft/right` (float hanya ≥ 768px), cover dengan `min-height`, galeri, embed (video 16:9 via `aspect-ratio`, embed sosial menyesuaikan diri), kolom, tombol (pil tema), file, details, separator.
  - tabel (block dan klasik) bergulir ke samping, tidak melebarkan halaman.
  - bleed ke tepi layar hanya di ≤ 767px lewat satu mixin `ane-bleed`, hanya untuk anak langsung (`> .wp-block-image`, cover, galeri, embed video).

## 3. Verifikasi
- iframe 375px dengan artikel nyata + markup uji (heading, tautan panjang, daftar bersarang, quote, kode, tabel block & klasik, embed 16:9, gambar rata kiri, cover, tombol, kolom): lebar `.ane-content` 326px, tanpa overflow horizontal halaman; tabel dan kode hanya bergulir di dalam kotaknya.
- Desktop: lebar konten mengikuti lebar artikel.

## 4. Belum Diverifikasi
- Embed sosial nyata (Instagram/X/Facebook), galeri dengan banyak gambar, dan cover dengan gambar latar.
- Teks hard-coded "Konten Terbaru" pada block Latest Posts di dalam artikel (tetap seperti sebelumnya, belum bisa diterjemahkan lewat CSS).
