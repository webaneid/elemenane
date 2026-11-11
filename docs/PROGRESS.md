# Progres Retrofit & Optimasi — Elemen Ane (`elemenane`)

Dokumen ini melacak status pengerjaan setiap fase retrofit keamanan, rekayasa kode, dan optimasi PageSpeed pada tema **Elemen Ane**.

---

## Ringkasan Status Fase

| Fase | Nama Fase | Target Gap | Status | Tanggal Selesai |
| :--- | :--- | :--- | :--- | :--- |
| **[Fase 00](phases/phase-00-fondasi.md)** | Audit Awal & Pemasangan Fondasi Standar (`AGENTS.md`, `docs/`, CI/CD, PHPCS, PHPStan, Security/CSS/Dist Scripts) | Fondasi & Pemetaan `GAP-01` s/d `GAP-28` | **Completed** | 2026-11-10 |
| **[Fase 01](phases/phase-01-updater-dan-bootstrap.md)** | Hardening GitHub Auto-Updater (`inc/updater.php`), Konstanta `ELEMENANE_VERSION`, & Optimasi Bootstrap `functions.php` | `GAP-04`, `GAP-05`, `GAP-12`, `GAP-13` | **Completed** | 2026-11-11 |
| **[Fase 02](phases/phase-02-security-dan-bugfix.md)** | Security Hardening (Anti-Webshell `ABSPATH`, Obfuscasi API Key Maps, Tutup Stored XSS & Unescaped Output, Sanitasi SVG, Guard ACF Admin) & Perbaikan Bug PHP 8.x (`0` PHPStan Error) | `GAP-01`, `GAP-02`, `GAP-03`, `GAP-06`, `GAP-07`, `GAP-08`, `GAP-09`, `GAP-10`, `GAP-11`, `GAP-15` | **Completed** | 2026-11-11 |
| **[Fase 03](phases/phase-03-clean-code-dan-wpcs.md)** | Pembersihan Codebase, Eksplisit `require_once` (Hapus `glob()`), Penyeragaman Text Domain `'elemenane'`, & Format WPCS (`0` PHPCS Error) | `GAP-14`, `GAP-16`, `GAP-17`, `GAP-18` | **Completed** | 2026-11-11 |
| **[Fase 04](phases/phase-04-webp-converter.md)** | Auto-Convert Upload Gambar ke WebP (`inc/webp-converter.php` dengan `ELEMENANE_WEBP_QUALITY = 78`) | `GAP-19` | **Completed** | 2026-11-11 |
| **Fase 05** | Migrasi Icon `FontAne` ke Helper Inline SVG (`elemenane_icon()` di `inc/icons.php`) & Penghapusan `css/FontAne.css` + `fonts/` | `GAP-20` | **Completed** | 2026-11-11 |
| **Fase 06** | Optimasi Speed Test & Core Web Vitals Standar Webane (`webane-pagespeed-cwv`: Konversi Hero `background-image` ke `<img>` DPR-aware, Preload LCP, Inline CSS OwlCarousel + Zero-CLS, Async CSS, Defer JS, `content-visibility`, & Aksesibilitas 100) | `GAP-21`, `GAP-22`, `GAP-23`, `GAP-24`, `GAP-25`, `GAP-26`, `GAP-27`, `GAP-28` | **Completed** | 2026-11-11 |
| **[Fase 07](phases/phase-07-footer-baru.md)** | Refactoring Total Footer Default (`tp/footer-default.php`, `scss/_footer.scss` baru, mobile-first & responsif) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 08](phases/phase-08-header-baru.md)** | Refactoring Total Header Default (`tp/header-default.php`, `js/header.js`, `scss/_header.scss` baru: drawer mobile, dropdown aksesibel, dialog pencarian, sticky) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 09](phases/phase-09-sidebar-baru.md)** | Refactoring Total Sidebar & Widget (judul "kicker", kartu baru, Search pill, Latest Posts bernomor, Webane Posts, daftar/tag/select/kalender) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 10](phases/phase-10-arsip-baru.md)** | Refactoring Total Arsip Post (category, tag, search, archive, author, paginasi) dengan layout kartu bersama | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 11](phases/phase-11-single-bawah-artikel.md)** | Refactoring blok bawah artikel single (Tags, Author box, Post nav, Related) dengan judul konsisten gaya sidebar | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 12](phases/phase-12-landing-news.md)** | Refactoring total landing page news (hero slider vanilla, section konsisten, rail, ranked list, latest + paginasi) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 13](phases/phase-13-floating-chat.md)** | Refactoring total floating chat WhatsApp (FAB + panel aksesibel, tanpa script inline) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 14](phases/phase-14-entry-header-share.md)** | Refactoring entry header single + share bar (WhatsApp/Facebook/X/LinkedIn/Email, Salin tautan, native share) berisi judul, deskripsi, URL | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 15](phases/phase-15-ane-content.md)** | Perbaikan total `.ane-content`: akar masalah overflow mobile (flex column wrap), blok konten WordPress konsisten | Permintaan perbaikan | **Completed** | 2026-11-11 |
| **[Fase 16](phases/phase-16-product-archive-card.md)** | Arsip produk (`/product/`, kategori, tag) + `ane-product-card` baru, adapter data tema/WooCommerce, override template WooCommerce | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 17](phases/phase-17-single-product.md)** | Single product baru: galeri, ringkasan, tab ARIA, WooCommerce (variabel, add-to-cart, atribut, ulasan, related/upsell) | Permintaan desain | **Completed** | 2026-11-11 |
| **[Fase 18](phases/phase-18-branch-archive.md)** | Arsip cabang `/branch/` + `ane-branch-card` baru, pin peta bertoken, dialog aksesibel, warna sesuai token | Permintaan desain | **Completed** | 2026-11-11 |

---

## Baseline Metrik Kualitas (Akhir Fase 00)
- **PHP Syntax (`composer lint:syntax`)**: `0` error sintaks pada seluruh file PHP.
- **PHPStan Level 5 (`composer analyse`)**: Lulus (`0` error baru; `70` error warisan dibekukan di `phpstan-baseline.neon` dan dijadwalkan menjadi `0` error pada Fase 02).
- **Security Scan (`composer security:scan`)**: `0` critical webshell error, `20` warning baseline (`18` file tanpa `ABSPATH`, `2` `print_r`, `1` hardcoded Google Maps API key) — seluruhnya dijadwalkan tuntas di Fase 01 & Fase 02.
- **SCSS & CSS Check (`composer css:check`)**: Lulus (`38` file SCSS dan `11` file CSS seimbang, 5 entrypoint `.min.css` terverifikasi).
- **Distribution Check (`composer dist:check`)**: Lulus (`146` file runtime siap rilis, `0` file dev bocor).
