# Fase 10: Refactoring Total Arsip Post (Search, Tag, Category, Archive, Author) & Paginasi

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

---

## 1. Masalah Arsip Lama
- Lima template (`archive.php`, `category.php`, `tag.php`, `search.php`, `author.php`) menyalin markup yang sama dengan judul berbeda-beda; `index.php` sendiri menyalin lagi.
- Loop (`tp/content-archive.php`) memilih layout dengan `wp_is_mobile()` di server (merusak cache halaman) dan berselang-seling overlay / baris, terasa monoton.
- Judul halaman hanya `<h1>` polos; hasil pencarian memakai `style=""` inline; author memakai kotak `author_bio_section` lama dengan ikon dekoratif `z-index:-1`.
- Paginasi: tombol prev/next `pointer-events: none` (tidak bisa diklik), kotak 40px dengan latar abu, tidak rapi di mobile, tanpa label.
- `tp/content-none.php` memanggil `the_ID()`/`post_class()` di luar loop dan memakai gambar `dummyimage.com` (request eksternal).
- Jarak antara konten, paginasi, dan footer tidak konsisten.

## 2. Desain Baru
Satu kerangka dipakai semua jenis arsip: `tp/archive-layout.php`.

- **Kerangka**: `<main class="site-main ane-archive ane-archive--{tipe}">`, grid dua kolom (konten + sidebar ≥ 993px, satu kolom di bawahnya), `padding-bottom` fluid agar jarak ke footer selalu terjaga. Jika sidebar tidak aktif konten memakai lebar penuh.
- **Header arsip** (`tp/archive-header.php`): panel lembut bergradasi dengan "kicker" (Kategori / Tag / Pencarian / Arsip), judul fluid `clamp()`, deskripsi term, chip jumlah artikel. Pencarian menampilkan kata kunci + form pil.
- **Profil penulis** (`tp/archive-author.php`): sampul gradasi, avatar bulat bertepi, kicker "Penulis", nama, bio, chip statistik (jumlah artikel, artikel terbaru), tombol website.
- **Loop kartu** (`tp/archive-loop.php` + `tp/archive-card.php`): satu bahasa visual.
  - Halaman 1: kartu **featured** lebar penuh (gambar besar + teks di atas gradasi), sisanya kartu grid 2 kolom (gambar 3:2, chip kategori, judul 3 baris, kutipan, meta penulis + waktu).
  - Halaman ≥ 2: grid seragam tanpa featured.
  - Kartu terakhir yang sendirian di baris otomatis melebar (`--wide`) agar tidak ada lubang.
  - Mobile (< 640px): kartu biasa menjadi baris horizontal ringkas (thumbnail kiri); featured tetap besar.
  - Banner header disisipkan setelah baris lengkap (bukan di tengah baris) dan hanya jika ada iklan aktif.
  - Badge ikon untuk format video / galeri.
  - Tanpa `wp_is_mobile()`: seluruhnya CSS responsif.
- **Paginasi** (`ane_post_pagination()`): `nav.ane-pagination`; tombol 44px, halaman aktif terisi, prev/next berlabel (placeholder nonaktif agar posisi stabil). Mobile: angka disembunyikan, diganti "Sebelumnya · 2 / 9 · Berikutnya".
- **Tidak ada hasil** (`tp/archive-none.php`): ilustrasi ikon, pesan, form pencarian (untuk search) dan chip kategori populer.

### Kompatibilitas Mundur
- Elementor Theme Builder location `archive` tetap diutamakan di setiap template.
- Fungsi `ane_post_pagination()` (juga dipakai arsip produk & `page-home-mobile.php`) tetap bernama dan berparameter sama.
- `tp/content-archive.php` dihapus (hanya dipakai lima template di atas); template konten beranda (`content`, `content-list`, `content-overlay`, dst.) tidak diubah.
- `searchform.php` / `.ane-cari` tidak diubah.

### Out-of-Scope
- Kotak penulis di bawah artikel single (`ane_author_info_box`), arsip CPT product/branch, template beranda.

---

## 3. Daftar File
- Baru: `inc/archive.php`, `tp/archive-layout.php`, `tp/archive-header.php`, `tp/archive-author.php`, `tp/archive-loop.php`, `tp/archive-card.php`, `tp/archive-none.php`.
- Diubah: `archive.php`, `category.php`, `tag.php`, `search.php`, `author.php`, `index.php`, `functions.php`, `inc/elemenane.php` (paginasi), `scss/_arsip.scss` (tulis ulang), `scss/_pagination.scss` (tulis ulang), `scss/_global.scss` (hapus `.author_bio_section`), `languages/elemenane-id_ID.po/.mo`, `css/main(.min).css`.
- Dihapus: `tp/content-archive.php`.
- Dokumentasi: `PROGRESS.md`, fase ini.

## 4. Checklist
- [x] Helper + template PHP
- [x] SCSS + kompilasi
- [x] Verifikasi visual: category, tag, author (desktop + 375px), paginasi (desktop + 375px, markup dari `ane_post_pagination()` + mock), empty state, kartu `wide`
- [x] Quality gate (`lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse` 0 error, `security:scan` 0/0, `css:check`, `dist:check`)
- [x] Dokumentasi + commit

## 5. Catatan Implementasi & Temuan
- Di situs lokal, pencarian (`?s=`) dirender oleh **Elementor Theme Builder** (template "Our Products", lokasi `archive`), bukan oleh `search.php`. Layout tema hanya dipakai jika tidak ada template Elementor; tampilan search tema diuji dengan mode uji sementara (sudah dihapus).
- Kit Elementor (`.elementor-kit-N a` / `button`, specificity 0,1,1) menimpa warna link dan latar tombol header/footer pada halaman yang dirender Elementor. `scss/_header.scss` dan `scss/_footer.scss` kini dibungkus `html body { ... }` (specificity 0,1,2) agar tetap menang.
- `compile-mo.php` tidak mendukung plural; string jumlah memakai dua msgid terpisah (`%s article` / `%s articles`), bukan `_n()`.
- `get_avatar()` pada situs ini tidak menerapkan argumen `class`; avatar penulis distyle lewat `.ane-author-hero__avatar img`.
- Paginasi `ane_post_pagination()` sekarang mengembalikan `null` bila hanya ada 1 halaman (sebelumnya sama), dan dipakai juga oleh arsip produk.

## 6. Belum Diverifikasi
- Pencarian dengan hasil nyata dan halaman ≥ 2 pada data nyata (data lokal hanya 6 artikel / 1 halaman; paginasi diuji dengan markup asli dari fungsi + mock).
- Arsip tanggal (`/2024/05/`), banner iklan di antara kartu (tidak ada iklan aktif lokal), format video/galeri (badge).
