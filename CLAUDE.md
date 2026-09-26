# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Theme Overview

**Elemen Ane** is a WordPress theme by Webane Indonesia for news/magazine and company-profile sites. It pairs native PHP templates with Elementor (Kit design tokens, Theme Builder header/footer) and relies on ACF Pro for settings and custom fields. Requires PHP 7.4+, WordPress 5.0+.

There is no test suite, linter config, or package.json. The only build step is compiling SCSS.

## Commands

```bash
# Compile SCSS: every non-partial scss/*.scss is an entry (same loop CI runs on release)
for f in scss/[!_]*.scss; do sass "$f" "css/$(basename "$f" .scss).min.css" --style=compressed --silence-deprecation=import; done

# Compile translations (.po → .mo)
php languages/compile-mo.php
```

The repo also commits the unminified `css/*.css` and `.map` files, so recompile those too when you change SCSS. After changing front-end CSS, bump the matching version in [inc/elemenane.php](inc/elemenane.php) (`main-css` in `ane_enqueue_scripts()`, page styles in `ane_enqueue_page_styles()`) to bust the cache.

Front-end CSS is split by page:
- `scss/main.scss` → `main.min.css`, loaded everywhere. It uses `@import` partials.
- `scss/catalog.scss` (`_branch` + `_product`), `scss/landing.scss` (`_landingpage`) and `scss/linktree-page.scss` (`_linktree`) are loaded only on their pages by `ane_enqueue_page_styles()`. The `ane_load_all_page_styles` filter loads all of them everywhere.
- Shared variables and mixins live in `_mixins.scss` (no CSS output); each entry imports it. Don't give an entry the same name as a partial (`linktree.scss` next to `_linktree.scss` makes `@import` ambiguous).

Admin styles live in `scss/admin.scss`, which uses `@use` and shares `_tokens.scss`.

## Release & Auto-Update

The theme updates itself from GitHub releases of `webaneid/elemenane` ([inc/updater.php](inc/updater.php), `Elemenane_Updater`). To release:

1. Bump `Version:` in [style.css](style.css).
2. Add an entry to [CHANGELOG.md](CHANGELOG.md) (Keep a Changelog format).
3. Commit (the convention is `Version X.Y.Z - short description`), then tag `vX.Y.Z` and push the tag.

[.github/workflows/release.yml](.github/workflows/release.yml) compiles the SCSS, builds `elemenane-X.Y.Z.zip` (leaving out `scss/`, `*.map`, `.github`) and publishes the release. The updater compares that release against the installed `style.css` version.

To debug updates in wp-admin, use `themes.php?elemenane_force_check` and `themes.php?elemenane_debug`.

## Architecture

### Auto-loaded modules

[functions.php](functions.php) sets up theme supports, menus (`menuutama`, `mobilemenu`, `menufooter`), sidebars (`default-sidebar`, `home-sidebar`) and image sizes. It then runs `include_once` on **every** `.php` file in `inc/*.php` and `inc/*/*.php`. Adding a file there activates it automatically, so never put non-module PHP (scripts, examples) under `inc/`. Every file starts with an `ABSPATH` guard.

Main modules:
- [inc/elemenane.php](inc/elemenane.php) is the core: asset enqueueing, helpers, pagination, related posts, and post views (meta `musi_views`, AJAX action `postviews` for cached pages).
- [inc/elementor.php](inc/elementor.php) and [inc/elementor-defaults.php](inc/elementor-defaults.php) sync the Elementor Kit to CSS variables, Google Fonts and the Gutenberg palette.
- [inc/custompost.php](inc/custompost.php) registers the CPTs `product` (taxonomies `product_cat`, `product_tag`), `service` and `branch`.
- `inc/*-acf.php` (`product-acf.php`, `branch-acf.php`, `header-acf.php`) and [inc/feature-toggles.php](inc/feature-toggles.php) define ACF field groups **in code** with `acf_add_local_field_group`. There is no acf-json.
- [inc/seo.php](inc/seo.php) adds NewsArticle schema, Open Graph, Dublin Core and citation meta on top of Yoast Free. See [SEO-IMPLEMENTATION-GUIDE.md](SEO-IMPLEMENTATION-GUIDE.md).
- [inc/tracking-scripts.php](inc/tracking-scripts.php) injects GA, Meta Pixel and schema scripts from ACF option fields (`ane_ga_header`, `ane_metapixel_header`, …).
- [inc/admin.php](inc/admin.php) and [inc/admin/](inc/admin/) provide the custom admin UI and the "Elemen Ane" menu (`ane-setup`). ACF options sub-pages are defined from `ane_get_admin_sections()`, which is filterable via `ane/admin/sections`. The Linktree analytics feature is also here. See [ADMIN-DASHBOARD-GUIDE.md](ADMIN-DASHBOARD-GUIDE.md).

### Feature toggles

Optional features are switched on or off through ACF options and checked with helpers in [inc/feature-toggles.php](inc/feature-toggles.php): `ane_is_product_cpt_enabled()`, `ane_is_service_cpt_enabled()`, `ane_is_post_views_enabled()`, `ane_is_related_posts_enabled()`, `ane_is_social_sharing_enabled()`, `ane_is_breadcrumbs_enabled()`, and so on. The branch CPT checks `get_field('ane_enable_branch_cpt', 'option')`. When you add an optional feature, gate it the same way.

### Templates

- Root templates handle WordPress hierarchy routing. There are also CPT templates: `archive-product.php`, `single-product.php`, `archive-branch.php`, `single-branch.php`, and `taxonomy-product_*.php`.
- [tp/](tp/) holds template parts loaded with `get_template_part('tp/...')`. These cover content variants, `news-*` modules, `header-asli.php` / `footer-asli.php`, and `tp/banner/`.
- Header and footer: [header.php](header.php) and [footer.php](footer.php) call `elementor_theme_do_location('header'|'footer')`. They fall back to `tp/header-asli.php` / `tp/footer-asli.php` when Elementor Pro Theme Builder renders nothing.
- `page-home.php` ("Landing Page" template) chooses between `page-home-mobile.php` and `page-home-desktop.php`.

### Device detection

Server-side switching uses `wp_is_mobile()` (in `page-home.php`, `tp/content-archive.php`, `inc/widget.php` and related posts). The bundled Mobile_Detect library was removed. `inc/admin/linktree-core.php` uses `Mobile_Detect` only when that class exists and otherwise falls back to a simpler check.

### Assets

All theme assets load locally. The old CDN constants (`WP_ANE_CDN`, `WEBANE_DISABLE_CDN`) are **no longer used**. `get_base_url_assets()` returns the theme URI, filterable via `ane_assets_base_url`, and `ane_get_asset_url($path)` builds full URLs from it.

**jQuery is not loaded globally.** The `jquery` handle points at WordPress's bundled `jquery-core` (no jQuery Migrate) and loads only when an enqueued script lists it as a dependency. `js/newsane.js` (search, mobile menu, floating chat) is vanilla JS in the footer, and its mobile menu reproduces meanMenu 2.0.8's markup, which the header styles target. Always declare `array( 'jquery' )` on scripts that use jQuery; never rely on it being present. On plain pages (posts, archives, home, linktree) no jQuery loads at all; Elementor pages load it for Elementor itself.

Assets load only where needed, so keep new features on the same pattern:
- Owl Carousel and Magnific Popup (cdnjs) are only registered. Call `ane_enqueue_carousel()` / `ane_enqueue_lightbox()` from the template or filter that needs them; calling after the head prints them in the footer. Their setup code is `js/carousel-init.js` / `js/lightbox-init.js`. Owl 2.2.1 crashes on fractional `items` with `loop`, so use whole numbers.
- Google Maps loads only on the contact page and branch pages. Get the key from `ane_get_google_maps_api_key()` (per-site `ane_gmap_api_key` option, then legacy `google_api_key`, then the shared default).
- The icon font is `fonts/FontAne.woff2` (+ `.woff`), preloaded in the head.
- Kit Google Fonts load in one request on `wp_print_styles`, skipping families Elementor already enqueued for the page.

### Elementor design tokens

[inc/elementor.php](inc/elementor.php) exports Kit settings to `:root`:
- Colors: `--ane-warna-{slug}` and `--ane-warna-{slug}-rgb`. The mappings are `primary`→`utama`, `secondary`→`utama-2`, `accent`→`alternatif`.
- Typography: `--ane-typography-{slug}-font-family|font-weight|font-size|line-height|letter-spacing`.

In SCSS or custom CSS, use `var(--ane-warna-utama)` or `rgba(var(--ane-warna-utama-rgb), .5)` rather than hard-coded colors. The admin UI uses its own tokens (`--ane-radius-*`, `--ane-space-*`, `--ane-shadow-*`) from [inc/admin/design-tokens.php](inc/admin/design-tokens.php) and `scss/_tokens.scss`.

## Translation

The primary text domain is **`elemenane`**. Some admin files still use the legacy `newsane` domain, so use `elemenane` in new code. Translations are loaded with `load_textdomain()` from `languages/elemenane-{locale}.mo`, not `load_theme_textdomain()`. Indonesian (`id_ID`) ships with the theme. After editing the `.po` file, recompile the `.mo` and commit both.
