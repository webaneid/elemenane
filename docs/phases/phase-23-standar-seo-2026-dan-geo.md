# Fase 23 — Standar SEO Modern 2026, Hybrid Yoast Booster, Unified Schema `@graph`, GEO (`/llms.txt` & `/llms-full.txt`), & Auto-Redirect 301

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-24
- **Tanggal Selesai**: 2026-11-24

## 1. Tujuan & Analisis Gap (Referensi: `sekolahane` Fase 25 & Fase 44)

Berdasarkan perbandingan menyeluruh antara `elemenane/inc/seo.php` dan standar baru di `sekolahane/inc/seo.php`, ditemukan **9 Gap SEO & GEO** berikut:

| ID Gap | Area | Kondisi Sebelum (`elemenane`) | Standar Baru yang Diimplementasikan |
| :--- | :--- | :--- | :--- |
| `GAP-SEO-01` | **Company Profile Cache & Repeater Fix** | `ane_get_company_mobile()`, `ane_get_company_email()`, `ane_get_company_website()` membaca repeater `ane_kontak` sebagai array asosiatif langsung (sehingga selalu kosong), fallback logo ke `/img/logo-webane.png` (file tidak ada), dan belum meng-cache profil perusahaan + sosmed + agen WA (`ane_cs`) per request. | Fungsi `elemenane_get_company_profile()` dengan static cache per request, membaca repeater `ane_kontak` & `ane_cs` dengan benar, mengumpulkan link sosmed (`sameAs`), koordinat `ane_gmap`, dan fallback logo ke `/img/logo-elemenane.svg`. Seluruh helper warisan `ane_get_company_*` tetap dipertahankan di atas cache ini. |
| `GAP-SEO-02` | **Hybrid Yoast SEO Booster & Smart Description** | Tidak ada fallback otomatis untuk meta description Yoast ketika dikosongkan admin, dan tidak mencetak `<meta name="description">` / `<link rel="canonical">` saat Yoast tidak aktif. | Filter `wpseo_metadesc`, `wpseo_opengraph_desc`, `wpseo_twitter_description`, `wpseo_opengraph_image`, `wpseo_twitter_image` dengan generator deskripsi cerdas (`elemenane_generate_smart_description()` & `elemenane_build_post_smart_description()`) untuk `post`, `page`, `product` (harga & kategori), `branch` (kota/provinsi/alamat), `service`, dan `page-kontak.php`. |
| `GAP-SEO-03` | **Multi-Layer Tag-to-Keywords Engine** | Hanya mengambil `post_tag` pada schema artikel; tidak menghasilkan `<meta name="keywords">` dan tidak menggabungkan Focus Keyphrase Yoast + Kategori + Taxonomy Produk/Cabang. | `elemenane_get_current_keywords()` menggabungkan `_yoast_wpseo_focuskw`, `post_tag`, `category`, `product_cat`, `product_tag`, lokasi `branch`, serta nama brand ke `<meta name="keywords">` dan JSON-LD. |
| `GAP-SEO-04` | **Unified JSON-LD `@graph` & Yoast Graph Sync** | Mencetak 5–6 blok `<script type="application/ld+json">` terpisah (duplikasi `WebSite` & `BreadcrumbList` dengan Yoast), schema `Product` masih membaca field lama (`ane_harga_normal`, `product-category`), belum ada schema `LocalBusiness` untuk `branch`, belum ada `SpeakableSpecification`, `abstract`, maupun ekstraksi otomatis `FAQPage`. | Satu kesatuan `@graph` (`elemenane_build_schema_graph()`) berisi `Organization` (dengan `contactPoint` & `GeoCoordinates`), `WebSite` (`SearchAction`), `ItemList` `SiteNavigationElement` (`menuutama`), `BreadcrumbList`, `NewsArticle` (`abstract` + `speakable` + `keywords`), `Product` (terhubung ke `elemenane_get_product_card_data()` & `product_cat`), `LocalBusiness` (untuk `branch`), `CollectionPage`, dan auto-extracted `FAQPage` (`.schema-faq-*` & `<details><summary>`). Terintegrasi ke `wpseo_schema_graph` saat Yoast aktif agar 0 duplikasi. |
| `GAP-SEO-05` | **GEO Endpoints `/llms.txt` & `/llms-full.txt`** | Belum memiliki endpoint manifest pengetahuan AI (`/llms.txt` dan `/llms-full.txt`) maupun tag penemuan `<link rel="alternate">` di `<head>`. | Endpoint virtual `/llms.txt` (`?llms_txt=1`) dan `/llms-full.txt` (`?llms_txt=full`) dengan transient cache 6 jam (auto-flush pada `save_post` & `acf/save_post`), memuat identitas perusahaan, kontak, CS WhatsApp, sitelinks, halaman profil, katalog produk, jaringan cabang, layanan, dan artikel terbaru. |
| `GAP-SEO-06` | **Kontrol AI Search Crawlers di `robots.txt` & `wp_robots`** | `ane_add_robots_meta()` hanya berjalan di single post jika Yoast tidak aktif, dan belum ada aturan AI crawler di `robots.txt`. | Filter `wp_robots` (`max-snippet:-1`, `max-image-preview:large`, `max-video-preview:-1`) untuk Google Discover & AI Overviews, serta filter `robots_txt` yang mengizinkan `OAI-SearchBot`, `GPTBot`, `ChatGPT-User`, `PerplexityBot`, `ClaudeBot`, `Google-Extended`, `Applebot-Extended`, memblokir `CCBot` & `Bytespider`, dan mencantumkan manifest LLMs + Sitemap. |
| `GAP-SEO-07` | **Auto-Sync Meta Yoast saat `save_post`** | Belum ada pengisian otomatis `_yoast_wpseo_focuskw` dan `_yoast_wpseo_metadesc` saat artikel/produk/cabang dipublikasikan tanpa meta Yoast. | Hook `wp_after_insert_post` (`elemenane_sync_yoast_meta_on_save`) mengisi otomatis Focus Keyphrase & Meta Description Yoast jika dibiarkan kosong oleh admin. |
| `GAP-SEO-08` | **Auto-Redirect 301 Saat Slug Berubah** | Perubahan slug pada post/page/product/branch yang sudah terbit menghasilkan 404. | Hook `post_updated` (`elemenane_log_slug_change_redirect`) menyimpan maksimal 200 pemetaan `old_path => post_id` (`autoload = false`) dan `template_redirect` (`elemenane_execute_404_slug_redirect`) melakukan redirect 301 **hanya saat `is_404()`** (0 overhead di halaman 200 OK). |
| `GAP-SEO-09` | **Auto Image `alt` Fallback** | Gambar attachment tanpa atribut `alt` di database tercetak dengan `alt=""`. | Filter `wp_get_attachment_image_attributes` (`elemenane_auto_image_alt_attributes`) otomatis mengisi `alt` kosong dengan judul konten + nama perusahaan. |

## 2. File yang Diubah
- `inc/seo.php`
- `docs/phases/phase-23-standar-seo-2026-dan-geo.md`
- `docs/PROGRESS.md`

## 3. Kriteria Selesai (Acceptance Criteria)
- [x] Seluruh 9 gap SEO & GEO terimplementasi di `inc/seo.php` dengan tetap menjaga kompatibilitas mundur seluruh fungsi `ane_get_company_*` dan `ane_output_*`.
- [x] Lulus `composer lint:syntax`, `composer lint:changed`, `composer lint:phpcs`, `composer analyse`, `composer security:scan`, `composer css:check`, dan `composer dist:check`.
