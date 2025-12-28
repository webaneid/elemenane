# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Theme Overview

**Elemen Ane** is a production WordPress theme built by Webane Indonesia for news/magazine sites with deep Elementor integration. The theme combines traditional WordPress templates with Elementor page builder capabilities, featuring server-side mobile detection, CDN support, and ACF-powered customization.

**Text Domains**: `newsane` (primary), `elemenane`, `kabarane`

## Architecture

### Modular PHP Structure

All functionality is auto-loaded from `/inc/` directory via `functions.php` using glob patterns:

```php
// Loads all PHP files from /inc/ and subdirectories
$allFiles = array_merge(
    glob(get_template_directory() . '/inc/*.php'),
    glob(get_template_directory() . '/inc/**/*.php')
);
```

**Key modules** in [inc/](inc/):
- [inc/newsane.php](inc/newsane.php) - Core theme functionality (40KB+): asset management, post view tracking, template helpers, pagination, related posts
- [inc/elementor.php](inc/elementor.php) - Elementor Kit integration: syncs colors/typography to CSS variables, Google Fonts loading
- [inc/acf.php](inc/acf.php) - ACF configuration: options pages (General Settings, Banners, Colors, Scripts), Google Maps API
- [inc/widget.php](inc/widget.php) - Custom widget system with `Webane_Posts_Widget`
- [inc/custompost.php](inc/custompost.php) - Custom post types (Product, Service)
- [inc/admin.php](inc/admin.php) - Admin UI customization and dashboard widgets
- [inc/Mobile_Detect.php](inc/Mobile_Detect.php) - Third-party mobile detection library
- [inc/sosmed.php](inc/sosmed.php) - Social sharing (Facebook, Twitter, WhatsApp, LinkedIn)

### Template Parts System

[tp/](tp/) directory contains reusable template components loaded via `get_template_part()`:

**Content templates**: `content-archive.php`, `content-list.php`, `content-overlay.php`, `content-klasik.php`, `content-video.php`, `content-gallery.php`

**Header/Footer variants**:
- `header-asli.php` / `footer-asli.php` - Native theme headers/footers
- `header-elementor.php` / `footer-elementor.php` - Elementor-managed headers/footers

**News modules**: `news-home.php`, `news-classic.php`, `news-sliding.php`, `news-populer.php`, `news-featured.php`

**Single post templates**: `single.php`, `single-video.php`, `single-gallery.php`

**Banner system**: [tp/banner/](tp/banner/) subdirectory with modular banner components

### Custom Page Templates

Template routing based on device detection and Elementor usage:

- **`page-home.php`** (Template Name: "Landing Page") - Routes to mobile or desktop homepage based on `Mobile_Detect`
- **`page-elementor.php`** / **`page-elementor-no-bc.php`** - Full-width Elementor canvas (with/without breadcrumbs)
- **`page-nosidebar.php`** - Full-width page
- **`page-kontak.php`** - Contact page with Google Maps
- **`page-linktree.php`** - Link aggregator page

## CDN & Asset Management

### CDN Strategy

The theme uses a CDN system controlled by constants:

```php
// Define in wp-config.php to override CDN
define('WP_ANE_CDN', 'https://cdn.webane.net/themes/elemenane');
define('WEBANE_DISABLE_CDN', true); // Disable CDN
```

**CDN logic** in [inc/newsane.php](inc/newsane.php):
- Uses CDN when: `WP_ANE_CDN` is defined, `WEBANE_DISABLE_CDN` is not defined, and `WP_DEBUG` is false
- Falls back to local assets during development
- Helper functions: `get_base_url_assets()`, `webane_get_asset_url()`

### External Dependencies (CDN-loaded)

- jQuery 3.3.1
- Owl Carousel 2.2.1
- Magnific Popup 1.1.0
- Bootstrap 4.3.1 JS

## Styling & Build Process

### SCSS Architecture

[scss/](scss/) directory contains component-based SCSS with 16+ partials:

```
scss/main.scss imports:
├── _resets.scss
├── _global.scss
├── _kolom.scss (grid system)
├── _anecontent.scss
├── _banner.scss
├── _owlcarousel.scss
├── _button.scss
├── _header.scss
├── _post.scss
├── _page.scss
├── _linktree.scss
├── _arsip.scss
├── _pagination.scss
├── _landingpage.scss
├── _sidebar.scss
└── _footer.scss
```

### Compilation

**No build tools** (gulp/webpack/grunt) are configured in the repository. SCSS is compiled externally with the following pattern:

```
scss/main.scss → css/main.css → css/main.min.css (+ source maps)
scss/admin.scss → css/admin.css → css/admin.min.css (+ source maps)
```

After editing SCSS files, compile manually using your preferred tool (e.g., Sass CLI, IDE plugin, or external compiler).

## Elementor Integration

### Design System Sync

[inc/elementor.php](inc/elementor.php) syncs Elementor Kit settings to CSS custom properties for consistent theming across the site.

**Color variables** exported to `:root`:
```css
--ane-warna-{slug}: #hexcode
--ane-warna-{slug}-rgb: r, g, b
```

Special mappings: `primary` → `utama`, `secondary` → `utama-2`, `accent` → `alternatif`

**Typography variables** exported to `:root`:
```css
--ane-typography-{slug}-font-family
--ane-typography-{slug}-font-weight
--ane-typography-{slug}-font-size
--ane-typography-{slug}-line-height
--ane-typography-{slug}-letter-spacing
```

**Google Fonts**: Automatically loaded from Elementor Kit settings with proper weights.

**Gutenberg Integration**: Elementor colors are synced to Gutenberg color palette via `add_theme_support('editor-color-palette')`.

### Header/Footer Templates

The theme detects Elementor's header-footer experiment:
- If active: Uses Elementor-managed headers/footers
- If inactive: Falls back to native templates (`header-asli.php`, `footer-asli.php`)

## Mobile Detection

### Device-Aware Rendering

Uses [inc/Mobile_Detect.php](inc/Mobile_Detect.php) for server-side device detection:

```php
$detect = new Mobile_Detect();
if ($detect->isMobile() && !$detect->isTablet()) {
    // Mobile template
} else {
    // Desktop template
}
```

**Applied in**:
- Homepage routing ([page-home.php](page-home.php) → `page-home-mobile.php` or `page-home-desktop.php`)
- Widget template selection ([inc/widget.php](inc/widget.php))
- Archive displays
- Related posts rendering

**Responsive strategies**:
1. Server-side detection (different templates per device)
2. CSS media queries (SCSS breakpoints)
3. Mobile-specific menu (`mobilemenu` location)
4. meanMenu plugin (breakpoint: 991px)

## ACF Integration

### Custom Options Pages

Defined in [inc/acf.php](inc/acf.php):

- **Main**: `theme-general-settings` (slug: `ane_setting`)
- **Sub-pages**:
  - Banner management
  - Color settings
  - Script injection (Google Analytics, Meta Pixel, Schema)

### Script Injection Fields

ACF fields for third-party scripts:
- `ane_ga_header` - Google Analytics (header)
- `ane_sc_header` - Schema markup (header)
- `ane_metapixel_header` - Meta Pixel (header)
- `ane_metasdk_body` - Meta SDK (body)
- `ane_ga_footer` - Analytics (footer)

Injected in [header.php](header.php) and [footer.php](footer.php).

### Google Maps

- API key configured in ACF settings
- Custom map rendering in [js/newsane.js](js/newsane.js)
- Used in [page-kontak.php](page-kontak.php)

### Custom User Fields

- `gravatar_ane` - Custom avatar upload (overrides Gravatar via `get_avatar` filter)

## Custom Post Types

Defined in [inc/custompost.php](inc/custompost.php):

**Product CPT**:
```php
'post_type' => 'product'
'slug' => 'product'
'supports' => ['title', 'thumbnail', 'editor']
'show_in_rest' => true (Gutenberg support)
```
Custom taxonomy: `product-category`

**Service CPT**:
- Editor and thumbnails disabled (managed via ACF fields)

## Post View Tracking

System in [inc/newsane.php](inc/newsane.php) tracks post views with cache-compatibility:

- Meta key: `musi_views`
- AJAX-based tracking for cached pages ([js/postviews-cache.js](js/postviews-cache.js))
- Admin column in post list
- Functions: `set_views()`, `get_views()`

## Performance Optimizations

- **CDN support** for static assets
- **Minified CSS/JS** (`.min.css`, `.min.js`)
- **Selective script loading** (conditional enqueuing)
- **AJAX view tracking** (prevents cache invalidation)
- **WordPress Heartbeat disabled**
- **Version strings removed** from assets (security)
- **Object caching** (e.g., copyright year cached 24 hours)

## Security Measures

- `ABSPATH` checks in all PHP files
- Input sanitization via `esc_*` functions
- Output escaping with `esc_html__()`, `esc_url()`, etc.
- Nonce validation where applicable
- Comments disabled by default ([inc/handle.php](inc/handle.php))

## Common Development Patterns

### Adding New Template Parts

1. Create file in [tp/](tp/) directory (e.g., `tp/content-custom.php`)
2. Load using `get_template_part('tp/content', 'custom')`
3. Pass variables via `set_query_var()` if needed

### Adding New Functionality Module

1. Create PHP file in [inc/](inc/) directory
2. File is auto-loaded via `functions.php` glob pattern
3. Follow naming convention: descriptive filename (e.g., `inc/feature-name.php`)
4. Always include `ABSPATH` check at top

### Modifying Styles

1. Edit SCSS files in [scss/](scss/) directory
2. Compile to [css/main.css](css/main.css) using external SCSS compiler
3. Minify to [css/main.min.css](css/main.min.css)
4. Update version number in `wp_enqueue_style()` call in [inc/newsane.php](inc/newsane.php):56 to bust cache

### Creating Custom Widgets

Extend `Webane_Posts_Widget` class in [inc/widget.php](inc/widget.php) as reference or create new widget class and register in same file.

### Working with Elementor Design Tokens

After changing Elementor Kit colors/typography:
1. Changes automatically sync to CSS variables via [inc/elementor.php](inc/elementor.php)
2. Use variables in custom CSS: `var(--ane-warna-utama)`, `var(--ane-typography-primary-font-family)`
3. RGB values available for transparency: `rgba(var(--ane-warna-utama-rgb), 0.5)`

## Navigation Menus

Three registered menu locations in [functions.php](functions.php):

- `menuutama` - Main desktop menu
- `mobilemenu` - Mobile menu (meanMenu)
- `menufooter` - Footer menu

## Widget Areas

Two registered sidebars in [functions.php](functions.php):

- `default-sidebar` - Default sidebar for posts/pages
- `home-sidebar` - Homepage-specific sidebar

## Image Sizes

Custom image sizes defined in [functions.php](functions.php):

- `kotak` - 394×394 (hard crop)
- `medium` - 700×394 (hard crop)
- `large` - 1000×563 (hard crop)
- `thumbnail` - 400×225 (hard crop)

## File Structure Reference

```
elemenane/
├── functions.php           # Theme setup, auto-loads inc/ files
├── style.css               # Theme metadata
├── inc/                    # Auto-loaded functionality modules
│   ├── newsane.php         # Core functions (40KB)
│   ├── elementor.php       # Elementor integration
│   ├── acf.php             # ACF configuration
│   ├── widget.php          # Custom widgets
│   ├── custompost.php      # CPT definitions
│   ├── admin.php           # Admin customization
│   ├── Mobile_Detect.php   # Device detection
│   ├── sosmed.php          # Social sharing
│   ├── handle.php          # Comment/script cleanup
│   └── single-iklan.php    # Ad integration
├── tp/                     # Template parts
│   ├── content-*.php       # Content display templates
│   ├── single-*.php        # Single post templates
│   ├── header-*.php        # Header variants
│   ├── footer-*.php        # Footer variants
│   ├── news-*.php          # News module templates
│   └── banner/             # Banner components
├── scss/                   # SCSS source files
│   ├── main.scss           # Main stylesheet
│   ├── admin.scss          # Admin styles
│   └── _*.scss             # Component partials
├── css/                    # Compiled CSS
│   ├── main.css            # Compiled main stylesheet
│   ├── main.min.css        # Minified main stylesheet
│   ├── admin.css           # Compiled admin styles
│   └── FontAne.css         # Custom icon font
├── js/                     # JavaScript files
│   ├── newsane.js          # Main theme scripts
│   ├── elementor.js        # Elementor enhancements
│   ├── postviews-cache.js  # AJAX view tracking
│   └── gmap.js             # Google Maps
├── page-*.php              # Custom page templates
├── single.php              # Single post template
├── archive.php             # Archive template
├── header.php              # Main header
└── footer.php              # Main footer
```

## Important Constants

Define in `wp-config.php` to control theme behavior:

```php
// CDN configuration
define('WP_ANE_CDN', 'https://cdn.webane.net/themes/elemenane');
define('WEBANE_DISABLE_CDN', true); // Force local assets

// WordPress debug mode (disables CDN automatically)
define('WP_DEBUG', true);
```

## Admin Dashboard

### Custom Dashboard Integration

The theme includes a custom admin dashboard system in [inc/admin/](inc/admin/) with modern UI components:

**Main Dashboard Page**:
- Menu: **WordPress Admin → Elemen Ane** (registered via [inc/admin.php](inc/admin.php))
- Custom dashboard at `admin.php?page=ane-setup`
- Dashboard widget in WordPress main dashboard

**Admin Modules** in [inc/admin/](inc/admin/):
- [dashboard.php](inc/admin/dashboard.php) - Custom analytics dashboard with post stats and visitor metrics
- [header.php](inc/admin/header.php) - Admin bar customization (New Post/Page buttons, remove WordPress logo)
- [user.php](inc/admin/user.php) - User management enhancements
- [menu.php](inc/admin/menu.php) - Menu management improvements
- [design-tokens.php](inc/admin/design-tokens.php) - CSS custom properties for admin UI
- [footer-mobile-menu.php](inc/admin/footer-mobile-menu.php) - Mobile admin navigation
- [navigation.php](inc/admin/navigation.php) - Admin navigation customization
- [content.php](inc/admin/content.php) - Content management utilities
- [customizer.php](inc/admin/customizer.php) - Customizer enhancements

**Admin Bar Customizations**:
- Removes WordPress logo, comments, updates
- Adds "New Post" and "New Page" quick action buttons
- Changes "Howdy" to "Selamat datang"
- Hides admin bar for non-admin users on frontend
- Custom role-based CSS classes

**CSS Design Tokens**:
All admin UI uses CSS custom properties for consistency:
```css
--ane-radius-xl, --ane-radius-lg, --ane-radius-md, --ane-radius-sm
--ane-space-xs, --ane-space-sm, --ane-space-md, --ane-space-lg
--ane-shadow-sm, --ane-shadow-md, --ane-shadow-lg
```

## Key Filters & Hooks

**Custom filters**:
- `webane_assets_base_url` - Modify CDN/asset base URL
- `ane_login_styles` - Customize login page CSS
- `ane/admin/sections` - Modify admin dashboard sections
- `admin_bar_class` - Add custom classes to admin bar

**Common actions**:
- `after_setup_theme` - Theme setup hook
- `widgets_init` - Widget registration
- `wp_enqueue_scripts` - Asset loading
- `admin_enqueue_scripts` - Admin asset loading
- `admin_bar_menu` - Admin bar menu customization

## Translation

Theme is translation-ready with proper text domain usage:

```php
__('Text', 'newsane')           // Translate
_n('Singular', 'Plural', $count, 'newsane')  // Pluralization
esc_html__('Text', 'newsane')   // Translate & escape
```

POT file generation: Use standard WordPress i18n tools (Poedit, wp-cli, etc.) with text domain `newsane`.
