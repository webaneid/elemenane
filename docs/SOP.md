# Standard Operating Procedure (SOP) — Pengembangan & Retrofit Elemen Ane

Dokumen ini mendefinisikan alur kerja wajib bagi setiap developer maupun AI Agent ketika mengerjakan fitur, perbaikan bug, atau fase retrofit pada tema **Elemen Ane** (`elemenane`).

---

## Siklus 6 Langkah Per Fase

Setiap pekerjaan besar wajib dipecah ke dalam fase terstruktur di `docs/phases/phase-XX-*.md` dan mengikuti 6 langkah berikut secara berurutan:

### Langkah 1 — Baca Konteks & Dokumentasi
1. Baca [`AGENTS.md`](../AGENTS.md) dan [`docs/PROGRESS.md`](PROGRESS.md) untuk mengetahui status terakhir.
2. Baca [`docs/RETROFIT-GAPS.md`](RETROFIT-GAPS.md) untuk melihat daftar gap yang menjadi target fase tersebut.
3. Baca dokumen arsitektur terkait di `docs/architecture/` dan catatan di [`docs/lessons-learned.md`](lessons-learned.md).

### Langkah 2 — Spesifikasi Fase (`docs/phases/phase-XX-*.md`)
1. Buat atau perbarui dokumen fase menggunakan template [`docs/phases/phase-template.md`](phases/phase-template.md).
2. Tentukan secara eksplisit:
   - **Tujuan & Ruang Lingkup (In-Scope / Out-of-Scope)**.
   - **Daftar File yang Akan Diubah/Dibuat**.
   - **Kriteria Selesai (Definition of Done)** yang terukur.

### Langkah 3 — Implementasi Bertahap & Terisolasi
1. Lakukan perubahan kode hanya pada file yang termasuk dalam ruang lingkup fase aktif.
2. Patuhi seluruh aturan di [`docs/conventions.md`](conventions.md) dan [`docs/architecture/architecture-security.md`](architecture/architecture-security.md):
   - Wajib menyertakan `defined( 'ABSPATH' ) || exit;` di setiap file PHP.
   - Wajib melakukan late escaping (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
   - Wajib menjaga kompatibilitas nama field ACF, CPT, meta key WooCommerce, dan struktur HTML yang digunakan SCSS.
   - Jika memodifikasi file `.scss`, kompilasi ulang ke `css/*.css` dan `css/*.min.css`.

### Langkah 4 — Verifikasi Otomatis (Quality & Security Gate)
Jalankan seluruh perintah verifikasi dan pastikan lulus (`exit code 0`):
```bash
composer lint:syntax
composer lint:changed
composer analyse
composer security:scan
composer css:check
composer dist:check
```

### Langkah 5 — Sinkronisasi Dokumentasi
1. Tandai checklist pada dokumen `docs/phases/phase-XX-*.md`.
2. Perbarui status fase di [`docs/PROGRESS.md`](PROGRESS.md) dan status gap di [`docs/RETROFIT-GAPS.md`](RETROFIT-GAPS.md).
3. Jika ada perubahan arsitektur atau pelajaran teknis baru, perbarui `docs/architecture/*` atau [`docs/lessons-learned.md`](lessons-learned.md).

### Langkah 6 — Commit & Persetujuan Fase Berikutnya
1. Gunakan format **Conventional Commits** (`feat:`, `fix:`, `perf:`, `refactor:`, `docs:`, `chore:`).
2. Laporkan ringkasan hasil fase kepada pengguna dan minta persetujuan sebelum melanjutkan ke fase berikutnya.
