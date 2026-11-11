# Fase 13: Refactoring Total Floating Chat WhatsApp

- **Status**: **Completed**
- **Tanggal Mulai**: 2026-11-11
- **Tanggal Selesai**: 2026-11-11
- **Gap yang Ditangani**: - (permintaan desain)

## 1. Masalah Lama
- Seluruh `div.floating-chat` bisa diklik (bukan tombol): tidak bisa difokus keyboard, tanpa `aria-expanded`, tanpa Esc.
- Ikon bubble lewat CSS mask, tombol tutup `<i class="ane-close-x">`, kelas `enter` ditambah JS setelah 1 detik tanpa CSS pendukung.
- `footer.php` memuat `<script>` inline (`alert()` untuk validasi, round-robin nomor via localStorage tanpa try/catch) dan logika panel di `js/newsane.js`.
- Panel melebar 400px / 100% di mobile tanpa batas tinggi; desain polos.

## 2. Desain Baru (`tp/floating-chat.php`, `js/floating-chat.js`, `scss/_bottomchat.scss`)
- **FAB**: tombol hijau WhatsApp dengan ikon, denyut halus 3x, label pil "Customer Care" di desktop yang melipat setelah 9 detik atau saat panel terbuka. Masuk dengan animasi naik + fade 1 detik setelah muat (CSS murni).
- **Panel**: kartu 380px di atas FAB (maks 600px / tinggi layar; di ponsel selebar layar dikurangi margin), header gradasi dengan status "via WhatsApp", area pesan bergaya chat dengan balon sambutan, daftar admin berupa kartu (foto, nama, area, panah), kolom pesan + tombol kirim bulat. Fokus ke input di perangkat pointer halus; Esc menutup dan fokus kembali ke FAB.
- **Validasi inline** (`role="alert"`) menggantikan `alert()`.
- **Round-robin** nomor admin tetap (`localStorage.waLastIndex`), kini aman saat storage diblokir; nomor dikirim lewat atribut `data-numbers`, bukan script inline.
- `prefers-reduced-motion` dihormati; input 16px agar iOS tidak zoom; `env(safe-area-inset-*)`.
- Ikon baru `send` di `inc/icons.php`; `.ane-close-x` dihapus (tidak dipakai lagi).

## 3. Kompatibilitas
- Field ACF tidak berubah: `ane_cs_aktif`, `ane_cs_label`, `ane_cs_welcome`, repeater `ane_cs` (`ane_whatsapp`, `ane_image`, `ane_nama`, `ane_area`). Tetap hanya tampil bila `ane_cs_aktif` aktif; script hanya di-enqueue saat aktif.
- Label default "Chat with us" bila `ane_cs_label` kosong; admin tanpa nomor dilewati.
- `footer.php` memanggil `tp/floating-chat`; blok chat dihapus dari `js/newsane.js`.

## 4. File
- Baru: `tp/floating-chat.php`, `js/floating-chat.js`.
- Diubah: `footer.php`, `js/newsane.js`, `scss/_bottomchat.scss` (ditulis ulang), `inc/elemenane.php` (enqueue + defer + l10n), `inc/icons.php`, `languages/*`, `css/main(.min).css`.

## 5. Checklist
- [x] Template + JS + SCSS
- [x] Verifikasi desktop & 375px: buka/tutup, validasi kosong, Esc + fokus balik, lebar panel
- [x] Quality gate
- [x] Dokumentasi + commit

## 6. Belum Diverifikasi
- Pengiriman pesan sungguhan ke wa.me (membuka tab baru) dan rotasi lebih dari satu admin (data lokal hanya 1 nomor).
- Pembaca layar, keyboard virtual iOS/Android, dan tampilan saat admin tanpa foto.
