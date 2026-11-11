# Fase 22: Evaluasi Performa Situs Klien & Pembersihan Aset (CSS / JS)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: lanjutan Fase 06 (PageSpeed / Core Web Vitals)

## 1. Data Pengukuran (kenchick.id, PageSpeed Insights)
| | Sebelum 1.2.0 | Sesudah 1.2.0 |
|---|---|---|
| Mobile Performance | **67** (FCP 3.0 s, LCP 3.0 s, TBT 570 ms, SI 7.9 s) | **86** (FCP 1.7 s, LCP 1.7 s, TBT 280 ms, SI 7.8 s) |
| Desktop Performance | **64** | **89** (FCP 0.4 s, LCP 0.4 s, TBT 180 ms) |
| Accessibility / Best Practices / SEO | 91 / 77 / 100 | 91 / 77 / 100 |

Sisa temuan pada laporan 1.2.0 (halaman beranda klien = template **Elementor Canvas**):
- *Render-blocking requests* ±3,1 s (mobile): `main.min.css` (20,7 KiB) + CSS widget Elementor + `roboto.css`.
- *Unused CSS* 145 KiB: `main.min.css` 19,9 dari 20,1 KiB tidak terpakai di halaman Canvas (halaman itu tidak memakai markup tema); sisanya Elementor / YouTube.
- *Unused JS* 742 KiB: YouTube embed (Elementor), Google Tag Manager, `accounts.google.com/gsi/client`, **Facebook SDK (88 KiB)**.
- *Unused preconnect*: `fonts.googleapis.com` / `fonts.gstatic.com` (dari tema) karena font disajikan lokal oleh Elementor.
- *Cache lifetimes*, *image delivery* (Elementor / server), *main landmark* (template Canvas Elementor tidak punya `<main>`).
- Konsol: **404 source map** `main.min.css.map` (file `.min.css` memuat komentar `sourceMappingURL`, sementara `*.map` tidak ikut paket rilis).

## 2. Audit CSS Terpakai (15 halaman nyata)
Seluruh aturan `main.css` / `catalog.css` / `landing.css` dicocokkan dengan DOM 15 halaman (beranda berita, kategori, tag, author, single, `/product/`, single produk, kategori produk, `/branch/`, 2 single cabang, kontak, 404, cart, hasil cari): `main` 405 dari 1.060 aturan tidak cocok, `catalog` 109 / 345, `landing` 29 / 119. Klasifikasi:
- **Mati (dihapus)**: Owl Carousel (`.owl-*`, `#home-sliding`), grid lama `.ane-col-*` / `.col-*`, kartu konten lama (`.ane-konten-overlay|default|klasik|lis-title`, `.format-video|gallery`, `.ane-kgv`, `.content-area`, `.post-tengah`) beserta template `tp/content-{overlay,klasik,list-title,video,gallery}.php` dan `tp/content.php` yang tidak dipanggil lagi.
- **Dipertahankan (digerakkan data / editor, tidak ada di halaman uji)**: varian widget sidebar, blok editor di `.ane-content`, status `.is-open` / `.has-overflow` / `.is-single`, iklan (`.ane-wide-banner`, `.ane-iklan`), tombol utilitas `.btn*` (kemungkinan dipakai kelas kustom Elementor klien).

## 3. Perubahan
1. **Source map**: `*.min.css` dikompilasi `--no-source-map` (`bin/css-audit.py`, `release.yml`); `*.min.css.map` dihapus dari repo (tidak pernah ikut paket rilis).
2. **Owl Carousel dihapus** (tidak ada template memakainya): `ane_enqueue_carousel()`, registrasi CDN, `js/carousel-init.js`, `scss/_owlcarousel.scss`.
3. **Magnific Popup (CDN cdnjs) diganti lightbox vanilla** `<dialog>` (`js/lightbox.js`, `scss/_lightbox.scss`): galeri konten (`a.gallery-popup`), galeri produk, galeri cabang, popup detail cabang di halaman produk. `ane_popup_gallery()` tidak menyentuh konten buatan Elementor. Tidak ada lagi request ke `cdnjs.cloudflare.com` dari tema.
4. **Elementor Canvas**: `main.min.css`, `header.js`, `newsane.js`, `floating-chat.js`, dan bar share tidak dimuat (halaman Canvas tidak memakai markup tema). Filter `elemenane_load_main_css`.
5. **Preconnect Google Fonts** hanya dicetak saat tema benar-benar memuat fonts (sebelumnya selalu, padahal Elementor menyajikan font lokal).
6. **CSS dipecah per konteks**: `content.min.css` (isi artikel), `widgets.min.css` (sidebar), `contact-page.min.css`; formulir pencarian dipindah ke `_searchform.scss` (tetap di main). Aturan dasar `.ane-icon` hanya di main.
7. **Kode mati dihapus**: grid lama (`_kolom.scss`, `.row`, `.col-*`), kartu konten lama (`.ane-konten-overlay|default|klasik|lis-title`, `.format-video|gallery`, `.ane-kgv`), template `tp/content-{overlay,klasik,list-title,video,gallery}.php` dan `tp/content.php`, `js/initializer.js`; `breadcrumbs.php` disederhanakan.
8. Facebook SDK komentar sudah hanya dimuat di single post; SDK Meta yang terlihat di laporan berasal dari kode kustom di ACF (`ane_metasdk_body`) milik klien.

## 4. Hasil
| | Sebelum | Sesudah |
|---|---|---|
| `main.min.css` | 143.368 B (gzip 22,9 KB) | 83.934 B (gzip 13,9 KB) |
| Halaman Canvas (beranda klien) | memuat `main.min.css` (render-blocking) + 3 JS tema | tidak memuat CSS / JS tema |
| Halaman produk / cabang | 1 CSS + 1 JS dari cdnjs | 0 dari pihak ketiga |
| Source map 404 | ya | tidak ada |
Pemeriksaan silang: untuk 16 halaman (termasuk Canvas dan halaman Elementor), selector bundel yang tidak dimuat tidak cocok dengan DOM mana pun (satu-satunya kecocokan adalah aturan dasar ikon, kini hanya di main).

## 5. Yang Bukan Wewenang Tema (rekomendasi untuk situs klien)
- **Elementor**: CSS widget Elementor (`widget-*.min.css`, `roboto.css`) dan `jquery` (±240 ms) adalah sisa render-blocking terbesar; aktifkan *Improved CSS Loading* / *Optimized Gallery & Lazy Load*, nonaktifkan widget yang tidak dipakai, dan jadikan video YouTube sebagai gambar pratinjau (lightbox / "Image Overlay") agar `base.js` YouTube (472 KB) tidak dimuat di awal.
- **Pihak ketiga**: Google Tag Manager, `accounts.google.com/gsi/client` (Google One Tap), Meta SDK; tunda ke interaksi pertama bila memungkinkan.
- **Server / cache**: *cache lifetimes* (181 KiB) menandakan header `Cache-Control` aset statis kurang panjang (atur di LiteSpeed / .htaccess).
- **Gambar**: `perhitungan-table` 1536 px ditampilkan 1140 px, produk 500 px ditampilkan 252 px: gunakan ukuran gambar Elementor yang sesuai.
- **Aksesibilitas (91)**: template Canvas Elementor tidak menyediakan `<main>`; kontras warna dan tautan tanpa nama berasal dari konten Elementor halaman itu.

## 6. Belum Diverifikasi
- Skor PageSpeed setelah versi ini (perlu rilis ke situs klien).
- Lightbox pada konten artikel sungguhan dengan banyak gambar (data lokal tidak punya tautan gambar di isi artikel) dan pada layar sentuh nyata.

## 7. Pengukuran Setelah 1.3.0 (kenchick.id, mobile)
| Kategori | Skor |
|---|---|
| Performance | 81 (FCP 1,5 s, LCP 1,5 s, TBT 510 ms, CLS 0,011, SI 6,4 s) |
| Accessibility / Best Practices / SEO | 91 / 77 / 100 |

Skor performa 81 (sebelumnya 86 pada 1.2.0) berfluktuasi antar run PageSpeed; TBT dan Speed Index yang bergerak, bukan FCP / LCP. Satu run tidak cukup untuk menyimpulkan kemunduran. Ukur beberapa kali sebelum membandingkan.

Atribusi temuan (data detail dari laporan):
- **CSS tidak terpakai (±130 KiB)**: 72 KiB tak teratribusi (`:root{--t…}` dan `.elementor-kit-80{…}`, milik Elementor) + 58 KiB `www-player.css` YouTube. `main.min.css` tema **tidak muncul lagi**.
- **JS tidak terpakai (±742 KiB)**: YouTube (`base.js` 472 KiB + modul player, ±518 KiB hemat), Google One Tap `gsi/client` (±82 KiB), Google Tag Manager (±73 KiB), Meta SDK (±53 KiB), `swiper.min.js` Elementor (±20 KiB).
- **Render-blocking (±2,1 s)**: puluhan stylesheet widget Elementor (`widget-*.min.css`, `custom-frontend`, `e-swiper`, `motion-fx`, `swiper`, font lokal `poppins|roboto|inter|bebasneue.css`) + `jquery.min.js` (29 KiB). Tidak ada berkas `elemenane`.
- **Lainnya**: cache lifetimes 178 KiB, image delivery 231 KiB (Elementor / server), 16 long task, main-thread 5,3 s, 4 cookie pihak ketiga, kontras warna (a11y 91).

Kesimpulan: sisa temuan bukan dari tema. Rekomendasi untuk klien tetap seperti bagian 5, urut dampak:
1. Video YouTube -> *Lazy Load* / gambar overlay (hemat ±520 KiB JS + 58 KiB CSS).
2. Matikan atau tunda Google One Tap.
3. Tunda GTM dan Meta SDK sampai interaksi pertama (script Meta ada di ACF `ane_metasdk_body`, isi dari klien).
4. Elementor > Features: *Improved Asset Loading*, *Improved CSS Loading*, *Inline Font Icons*.
5. Plugin cache untuk menggabung / inline CSS, menunda JS, dan header `Cache-Control`.

**Keputusan**: facade YouTube dari tema dan perbaikan kontras tidak dikerjakan sekarang (diputuskan pemilik proyek: dampaknya di luar tema dan tidak dipermasalahkan). Dapat dibuka sebagai fase baru bila diperlukan.

## 8. Gambar Unggahan (WebP)
`inc/webp-converter.php` (Fase 04) mengonversi otomatis saat unggah (`wp_handle_upload`, `wp_handle_sideload`): `image/jpeg` dan `image/png` menjadi `.webp` (kualitas `ELEMENANE_WEBP_QUALITY` = 78, filter `elemenane_webp_quality`), berkas asli dihapus setelah `.webp` terverifikasi, dan ukuran turunan WordPress ikut WebP. Tidak dikonversi: PNG beranimasi, GIF, SVG, dan server tanpa dukungan WebP pada editor gambar (diteruskan apa adanya). Konversi hanya berlaku untuk **unggahan baru**; gambar yang sudah ada di Media Library tidak diubah.
