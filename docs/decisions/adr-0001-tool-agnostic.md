# ADR-0001: Dokumentasi AI Agent yang Tool-Agnostic (`AGENTS.md`)

- **Status**: Accepted
- **Tanggal**: 2026-11-10
- **Pengambil Keputusan**: Tim Engineering Webane Indonesia

## Konteks & Masalah
Sebelumnya repositori `elemenane` hanya memiliki `CLAUDE.md` di root proyek. Tim Webane menggunakan berbagai asisten AI (Claude Code, Google Antigravity, Cursor, GitHub Copilot) yang masing-masing membaca konvensi file instruksi berbeda. Menyimpan duplikasi instruksi di banyak file berisiko menyebabkan ketidakcocokan aturan.

## Keputusan
1. Menjadikan [`AGENTS.md`](../../AGENTS.md) sebagai **Single Source of Truth** untuk seluruh instruksi agen AI dan developer.
2. Mengisi [`CLAUDE.md`](../../CLAUDE.md) dengan referensi `@AGENTS.md`.
3. Memindahkan rincian arsitektur, SOP, progres fase, dan daftar gap ke dalam direktori terstruktur `docs/`.

## Konsekuensi
### Positif
- Seluruh AI agent membaca satu sumber aturan yang konsisten.
- Ukuran konteks awal (`AGENTS.md`) tetap ringkas namun memiliki tautan jelas ke dokumentasi mendalam di `docs/`.

### Negatif / Trade-off
- Setiap perubahan arsitektur harus diperbarui di `docs/architecture/` sesuai langkah 5 pada `docs/SOP.md`.
