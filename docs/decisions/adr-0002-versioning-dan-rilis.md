# ADR-0002: Otomatisasi Versioning & Pemaketan Rilis dengan Semantic Release dan `.distignore`

- **Status**: Accepted
- **Tanggal**: 2026-11-10
- **Pengambil Keputusan**: Tim Engineering Webane Indonesia

## Konteks & Masalah
Pembuatan rilis tema secara manual atau hanya mengandalkan `git archive` berisiko menyertakan file pengembangan (`scss/`, `docs/`, `bin/`, `composer.json`, `phpcs.xml.dist`, `CLAUDE.md`) ke dalam paket instalasi produksi klien.

## Keputusan
1. Menggunakan **Semantic Release** (`.releaserc.json` & `.github/workflows/release.yml`) berbasis Conventional Commits untuk menaikkan versi di `style.css` dan memperbarui `CHANGELOG.md` secara otomatis.
2. Menggunakan `.distignore` + `rsync` + `bin/check-dist.sh` (`composer dist:check`) untuk menyaring seluruh file pengembangan sebelum membuat arsip `elemenane-${version}.zip`.

## Konsekuensi
### Positif
- Paket ZIP rilis produksi dijamin bersih dari file pengembangan dan dokumentasi internal.
- Versi di `style.css`, tag Git, dan `CHANGELOG.md` selalu sinkron otomatis.

### Negatif / Trade-off
- Pesan commit ke branch `main` wajib mematuhi format Conventional Commits.
