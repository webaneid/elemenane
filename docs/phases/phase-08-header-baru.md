# Fase 08: Refactoring Total Header Default (`tp/header-default.php`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain; menutup masalah struktur header lama)

---

## 1. Masalah Header Lama (`tp/header-asli.php`)
- Menu `menuutama` dirender 3x (mobile, desktop atas, desktop bawah) + diklon JS ala meanMenu (`js/newsane.js`) -> DOM berat, id ganda.
- Top bar memakai `<marquee>` (usang, tidak aksesibel) dan menyebabkan overflow horizontal di 768px.
- Ikon keranjang memakai class `solar--cart-bold` yang tidak ada; tiga tombol search berbeda; search bar `max-width` hack.
- Tidak sticky, tidak ada dropdown yang bisa diakses keyboard, tidak ada drawer mobile yang benar.

## 2. Desain Baru
- **Satu `<nav>` untuk semua ukuran layar**: horizontal di desktop, off-canvas drawer di bawah 992px (tanpa kloning).
- **Utility bar** (>= 768px): judul berita terbaru (satu tautan, terpotong rapi) + telepon (`tel:`). `<marquee>` dan tanggal dihapus.
- **Main bar sticky**: logo, nav, aksi (cari, produk/keranjang, CTA). Mengecil + bayangan saat scroll; sembunyi saat scroll turun, muncul saat scroll naik (hanya mobile).
- **Dropdown & submenu aksesibel**: tombol toggle `aria-expanded`, hover/focus-within di desktop, akordeon di drawer.
- **Drawer mobile**: backdrop, kunci scroll, ESC, focus trap, tutup otomatis saat resize ke desktop; berisi pencarian, nav, tautan cepat (`mobilemenu`), telepon & CTA.
- **Pencarian**: `<dialog>` modal layar penuh dengan form, saran artikel terbaru, dan banner header (`ane_iklan_header`) tetap tampil.
- Mobile-first, token warna tema, `prefers-reduced-motion`, safe-area, tap target >= 44px.

### Kompatibilitas Mundur
- Field ACF: `ane_header_cta_link`, `ane_header_show_product`, `ane_telepon`, `ane_telepon_label`, `ane_iklan_header` (+ sub field banner).
- Lokasi menu `menuutama` (nav utama + schema `ane_output_navigation_schema()`) dan `mobilemenu` (kini tautan cepat di drawer).
- Custom logo / `theme_mod( 'logo' )` / `img/logo-elemenane.png`. Header Elementor Theme Builder tetap diprioritaskan.

### Out-of-Scope
- Header admin, Elementor header, mega menu.

---

## 3. Daftar File
- Baru: `tp/header-default.php`, `js/header.js`. Dihapus: `tp/header-asli.php`, `tp/content-top-header.php`.
- Diubah: `header.php`, `inc/elemenane.php` (enqueue), `inc/icons.php` (ikon `cart`, `chevron-down`), `js/newsane.js` (hapus kode menu/search lama), `scss/_header.scss` (tulis ulang), `scss/_global.scss` (`overflow-x: clip` agar sticky bekerja), `css/main(.min).css`.
- Dokumentasi: `PROGRESS.md`, `architecture-overview.md`, fase ini.

---

## 4. Checklist
- [x] Template + ikon + enqueue
- [x] JS header (drawer, dropdown, search dialog, sticky)
- [x] SCSS + kompilasi
- [x] Verifikasi 320 / 768 / 1100 + interaksi
- [x] Quality gate + dokumentasi

## 5. Kriteria Selesai
- [x] `composer lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check` lulus.
- [x] Menu utama hanya dirender sekali di header; tanpa horizontal overflow akibat header di 320-1100px.

## Catatan Implementasi
- Header sticky memakai `position: sticky` pada `.ane-header` dengan `top` negatif setinggi utility bar, sehingga utility bar ikut tergulung dan main bar menempel. Tanpa `transform`/`backdrop-filter` saat diam, karena keduanya membuat drawer `position: fixed` terperangkap.
- `.webane-wrapper` di mobile memakai `overflow-x: clip` (bukan `hidden`) supaya sticky tetap bekerja.
- `.screen-reader-text` kini utilitas global di `scss/_resets.scss`.
- `mobilemenu` kini dirender sebagai "tautan cepat" (pill) di drawer, hanya bila ada menu yang ditetapkan; fallback `menuutama` memakai `elemenane_fallback_menu()` (daftar halaman).
- Dihapus: `<marquee>`, tanggal hari ini, tiga render menu, kloning menu ala meanMenu, tiga tombol search berbeda, margin bawah header khusus `.page-template-page-home` (tidak lagi diperlukan; periksa visual beranda).
- Ikon baru di `inc/icons.php`: `cart` (24x24), `chevron-down`.

## Hasil Verifikasi
- Browser (iframe 375 / 820 / 1280): tidak ada overflow horizontal; tap target 44px; drawer (aria-expanded, scroll lock, submenu aktif terbuka), dropdown desktop, dan dialog pencarian (fokus ke input, saran artikel) berfungsi; menu utama dirender sekali.
- Quality gate (`lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check`) lulus.
- Belum diuji: perilaku "sembunyi saat scroll turun" pada perangkat sentuh nyata, VoiceOver/NVDA, dan halaman beranda `page-home.php` (situs lokal memakai template Elementor Canvas).

## Penyelarasan Lebar Container
- Header (1240px) dan footer (gutter `clamp`) sebelumnya tidak sejajar dengan `.container` konten (1140px, padding 15px di <= 992px). Kini satu sumber: mixin `container-box` + `$ane-container-max` di `scss/_mixins.scss`, dipakai oleh `.container`, `.ane-header__inner`, dan `.ane-footer__inner`.
- Diukur di browser: 1140px dan titik awal yang sama pada header, utility bar, konten, dan footer (desktop, 820px, 375px).
- Catatan lama (tidak diubah): di rentang 993-1140px `.container` tidak punya padding samping sehingga konten menempel tepi layar.
