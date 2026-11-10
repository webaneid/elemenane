# Fase 07: Refactoring Total Footer Default (`tp/footer-default.php`)

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan perbaikan desain; menutup bug lama di `tp/footer-asli.php`)

---

## 1. Tujuan & Ruang Lingkup
### In-Scope
- Footer default baru, mobile-first dan sangat responsif (320px sampai layar lebar): `tp/footer-default.php` menggantikan `tp/footer-asli.php`; `footer.php` memanggil template baru (Elementor Theme Builder footer tetap diprioritaskan).
- SCSS ditulis ulang total di `scss/_footer.scss` (prefix `.ane-footer__*`, CSS Grid, `clamp()`, token `--ane-warna-*`), dikompilasi ke `css/main(.min).css`.
- Markup semantik: `<footer>`, `<nav aria-labelledby>`, daftar kontak dengan link `tel:` / `mailto:`, heading berurutan (`h2`), tap target >= 44px, focus-visible, `prefers-reduced-motion`.
- Perbaikan bug warisan: baris alamat/telepon yang terduplikasi per baris repeater `ane_kontak`; link website yang selalu menuju `home_url()`; judul sosmed default yang `<span>`-nya ter-escape.

### Kompatibilitas Mundur (tidak boleh berubah)
- Field ACF: `ane_title_sosmed`, `ane_com_nama`, `ane_com_tag`, `ane_com_des`, `ane_com_alamat`, `ane_telepon`, repeater `ane_kontak` (`ane_fax`, `ane_handphone`, `ane_email`, `ane_website`).
- Lokasi menu `menufooter`; `tp/content-sosmed.php` dipakai ulang; `ane_copyright_year()`; floating chat di `footer.php` tidak diubah.

### Out-of-Scope
- Footer Elementor Theme Builder, floating chat (`_bottomchat.scss`), footer admin.

---

## 2. Daftar File yang Diubah / Dibuat
- `tp/footer-default.php` (baru), `tp/footer-asli.php` (dihapus), `footer.php`.
- `scss/_footer.scss` (ditulis ulang), `css/main.css`, `css/main.min.css` (+ map).
- Dokumentasi: `docs/PROGRESS.md`, `docs/architecture/architecture-overview.md`, fase ini.

---

## 3. Langkah Implementasi & Checklist
- [x] Template baru + `footer.php`
- [x] SCSS baru + kompilasi
- [x] Verifikasi responsif (320 / 768 / 1024 / 1440)
- [x] Sinkronisasi dokumentasi

---

## 4. Kriteria Selesai (Definition of Done)
- [x] `composer lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check` lulus.
- [x] Tanpa horizontal scroll pada 320px; tidak ada referensi class footer lama.

## Catatan Implementasi
- Layout CSS Grid mobile-first: 1 kolom (< 640px), brand penuh + 2 kolom (640-991px), 3 kolom `1.5fr 1fr 1.3fr` (>= 992px). Gutter/jarak fluid dengan `clamp()`; konten maks. 1140px.
- Pita sosmed hanya dirender bila `tp/content-sosmed.php` menghasilkan minimal satu `<li>`; tombol sosmed 44x44px.
- Kontak: alamat & telepon utama sekali saja; tiap baris repeater `ane_kontak` menambah handphone/fax/email/website. Link `tel:`, `mailto:`, dan website kini menuju URL yang diisi (sebelumnya selalu `home_url()`).
- Menu `menufooter` memakai `depth` 2, fallback `wp_page_menu`; tap target link 44px di mobile.
- `content-visibility: auto` dari Fase 06 dipertahankan. Jarak bawah mengikuti `env(safe-area-inset-bottom)`.
- `.screen-reader-text` didefinisikan di dalam scope `.ane-footer` (tema belum punya utilitas global).

## Hasil Verifikasi
- Diuji lewat iframe 320 / 768 / 1100px di Chrome: tidak ada elemen footer yang melebihi viewport; tap target link >= 44px (mobile/tablet).
- Quality gate (`lint:syntax`, `lint:changed`, `lint:phpcs`, `analyse`, `security:scan`, `css:check`, `dist:check`) lulus.
- Temuan di luar lingkup: pada 768px halaman memiliki overflow horizontal dari `.item` carousel di luar footer.
