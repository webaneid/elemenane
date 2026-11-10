# ADR-0004: Konversi Otomatis Upload Gambar ke WebP (`inc/webp-converter.php`)

- **Status**: Accepted
- **Tanggal**: 2026-11-10
- **Pengambil Keputusan**: Tim Engineering Webane Indonesia

## Konteks & Masalah
Klien sering mengunggah foto berita, banner, atau foto produk berformat JPEG/PNG berukuran besar (1–5 MB) langsung dari kamera/WhatsApp, yang menurunkan skor Largest Contentful Paint (LCP) di Google PageSpeed Insights.

## Keputusan
Menambahkan modul `inc/webp-converter.php` yang terhubung ke filter `wp_handle_upload`:
1. Setiap upload baru berformat `image/jpeg` atau `image/png` otomatis dikonversi ke format `.webp` menggunakan `wp_get_image_editor()` (GD/Imagick) dengan kualitas default `ELEMENANE_WEBP_QUALITY` (`78`).
2. Jika konversi berhasil dan ukuran file `.webp` lebih kecil dari file asli, file asli dihapus dan path attachment diarahkan ke file `.webp`.
3. Jika server tidak mendukung WebP atau hasil WebP lebih besar dari file asli, sistem melakukan fallback aman ke file asli tanpa menggagalkan proses upload.

## Konsekuensi
### Positif
- Penghematan ukuran gambar rata-rata 40%–75% sejak saat pertama diunggah tanpa memerlukan plugin tambahan berbayar.
- Seluruh ukuran sub-size thumbnail WordPress otomatis dibuat dalam format `.webp`.

### Negatif / Trade-off
- Membutuhkan ekstensi PHP GD atau Imagick dengan dukungan WebP aktif di server hosting.
