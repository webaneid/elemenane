# Fase 00: Audit Awal & Pemasangan Fondasi Standar

- **Status**: Completed
- **Tanggal Mulai**: 2026-11-10
- **Tanggal Selesai**: 2026-11-10
- **Gap yang Ditangani**: Fondasi dokumentasi, CI/CD, analisis statis, dan pemetaan seluruh gap (`GAP-01` s/d `GAP-28`)

---

## 1. Tujuan & Ruang Lingkup
### In-Scope
- Melakukan audit menyeluruh terhadap keamanan, kualitas kode PHP 8.x, arsitektur SCSS/CSS, dan kesiapan Core Web Vitals (`webane-pagespeed-cwv`) pada tema **Elemen Ane** (`v1.1.8`).
- Memasang fondasi dokumentasi standar Webane (`AGENTS.md`, `CLAUDE.md`, `docs/SOP.md`, `docs/PROGRESS.md`, `docs/RETROFIT-GAPS.md`, `docs/conventions.md`, `docs/lessons-learned.md`, `docs/glossary.md`, `docs/architecture/*`, `docs/decisions/*`, `docs/phases/*`).
- Memasang perkakas quality & security gate otomatis (`composer.json`, `phpcs.xml.dist`, `phpstan.neon.dist`, `phpstan-bootstrap.php`, `phpstan-baseline.neon`, `bin/security-scan.sh`, `bin/lint-changed.sh`, `bin/check-dist.sh`, `bin/css-audit.py`, `.distignore`, `.gitattributes`, `.gitignore`, `.releaserc.json`, `.github/workflows/ci.yml`, `.github/workflows/release.yml`).
- **Zero Runtime Code Modification**: Tidak mengubah satu baris pun kode PHP/JS/SCSS/CSS tema di Fase 00 agar baseline audit tercatat murni.

### Out-of-Scope
- Perbaikan kode runtime PHP, SCSS/CSS, dan JS (dikerjakan secara bertahap pada Fase 01 s/d Fase 06).

---

## 2. Daftar File yang Dibuat / Diperbarui
- `AGENTS.md` & `CLAUDE.md`
- `.gitignore`, `.gitattributes`, `.distignore`, `.releaserc.json`
- `composer.json`, `phpcs.xml.dist`, `phpstan.neon.dist`, `phpstan-bootstrap.php`, `phpstan-baseline.neon`
- `bin/security-scan.sh`, `bin/lint-changed.sh`, `bin/check-dist.sh`, `bin/css-audit.py`
- `.github/workflows/ci.yml` & `.github/workflows/release.yml`
- `docs/SOP.md`, `docs/PROGRESS.md`, `docs/RETROFIT-GAPS.md`, `docs/conventions.md`, `docs/lessons-learned.md`, `docs/glossary.md`
- `docs/architecture/architecture-overview.md`, `architecture-security.md`, `architecture-css.md`, `architecture-deployment.md`
- `docs/decisions/adr-template.md`, `adr-0001-tool-agnostic.md`, `adr-0002-versioning-dan-rilis.md`, `adr-0003-auto-update-github-private.md`, `adr-0004-auto-convert-webp.md`
- `docs/phases/phase-template.md`, `docs/phases/phase-00-fondasi.md`

---

## 3. Langkah Implementasi & Checklist
- [x] Audit keamanan, PHP 8.x, arsitektur, dan Core Web Vitals terhadap seluruh file `elemenane` v1.1.8.
- [x] Buat aturan `AGENTS.md` (Single Source of Truth) dan arahkan `CLAUDE.md` ke `@AGENTS.md`.
- [x] Buat struktur dokumentasi `docs/` lengkap beserta ADR dan katalog gap (`docs/RETROFIT-GAPS.md`).
- [x] Konfigurasi Composer, PHPCS (`WordPress-Core` + `WordPress.Security` + `PHPCompatibilityWP`), dan PHPStan Level 5.
- [x] Buat skrip otomasi `bin/security-scan.sh`, `bin/lint-changed.sh`, `bin/check-dist.sh`, dan `bin/css-audit.py`.
- [x] Jalankan seluruh verifikasi baseline dan bekukan temuan awal PHPStan di `phpstan-baseline.neon`.

---

## 4. Kriteria Selesai (Definition of Done)
- [x] `composer lint:syntax` lulus (`0` error sintaks PHP).
- [x] `composer analyse` lulus dengan baseline terbekukan di `phpstan-baseline.neon`.
- [x] `composer security:scan` berjalan dan melaporkan baseline warning secara akurat (`0` critical webshell error).
- [x] `composer css:check` lulus (`38` file SCSS dan `11` file CSS memiliki kurung kurawal seimbang).
- [x] `composer dist:check` lulus (paket distribusi bersih dari file dev).
