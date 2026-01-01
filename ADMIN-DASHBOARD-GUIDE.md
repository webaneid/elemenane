# Admin Dashboard System - Migration Guide

Complete guide to duplicate the Elemen Ane custom admin dashboard system to another WordPress theme.

## Table of Contents
1. [Overview](#overview)
2. [Required Files](#required-files)
3. [Step-by-Step Migration](#step-by-step-migration)
4. [Configuration](#configuration)
5. [Dependencies](#dependencies)
6. [Troubleshooting](#troubleshooting)

---

## Overview

The Elemen Ane admin dashboard system provides:
- Modern custom admin interface with dark sidebar
- Mobile-responsive admin with bottom navigation
- Custom dashboard with analytics and charts
- Icon-only sidebar menu with tooltips
- Slide-in submenu panel
- Custom user profile enhancements
- Design token system for consistent styling

**Tech Stack:**
- PHP (WordPress hooks & ACF integration)
- SCSS (modular architecture)
- JavaScript (vanilla JS, no jQuery dependency for admin)
- Chart.js (for analytics visualization)

---

## Required Files

### 1. PHP Files (Backend Logic)

**Core Admin System:**
```
inc/admin.php                          ← Main admin orchestrator
inc/admin/dashboard.php                ← Custom dashboard page with analytics
inc/admin/design-tokens.php            ← CSS custom properties for admin UI
inc/admin/header.php                   ← Admin bar customization
inc/admin/footer-mobile-menu.php       ← Mobile bottom navigation
inc/admin/navigation.php               ← Sidebar menu modifications
inc/admin/menu.php                     ← Menu management utilities
inc/admin/user.php                     ← User profile enhancements
inc/admin/content.php                  ← Content management utilities
inc/admin/customizer.php               ← Customizer tweaks
```

**Optional (Feature-specific):**
```
inc/admin/linktree-analytics.php       ← Linktree analytics dashboard (optional)
```

### 2. SCSS Files (Styling)

**Core Styles:**
```
scss/admin.scss                        ← Main entry point (imports all partials)
scss/_tokens.scss                      ← CSS custom properties/design tokens
scss/_admin-general.scss               ← General admin styling
scss/_admin-style.scss                 ← Typography & base styles
scss/_admin-menu.scss                  ← Sidebar menu styling
scss/_admin-header.scss                ← Admin bar & mobile header
scss/_admin-navigation.scss            ← Navigation utilities
scss/_admin-dashboard.scss             ← Dashboard page styling
scss/_admin-user.scss                  ← User profile styling
scss/_admin-content.scss               ← Content editor styling
scss/_admin-login.scss                 ← Login page styling
scss/_admin-menu-icon.scss             ← Custom menu icons
scss/_admin-animations.scss            ← Transitions & animations
scss/_admin-footer-mobile-menu.scss    ← Mobile bottom nav styling
```

### 3. JavaScript Files (Interactivity)

**Core Scripts:**
```
js/admin-menu.js                       ← Sidebar menu interactions
js/admin-header.js                     ← Admin bar & mobile header
js/admin-dashboard.js                  ← Dashboard charts (Chart.js)
js/admin-user.js                       ← User profile enhancements
js/admin-animations.js                 ← UI animations & transitions
```

**Optional:**
```
js/admin-linktree-analytics.js         ← Linktree analytics charts (optional)
```

### 4. Compiled CSS (Output)

```
css/admin.css                          ← Compiled CSS (unminified)
css/admin.min.css                      ← Compiled CSS (minified, production)
```

---

## Step-by-Step Migration

### Step 1: Check File Existence

Before starting migration, verify all required files exist in source theme:

**Command to check PHP files:**
```bash
ls -la inc/admin.php
ls -la inc/admin/*.php
```

**Command to check SCSS files:**
```bash
ls -la scss/admin.scss
ls -la scss/_admin-*.scss
ls -la scss/_tokens.scss
```

**Command to check JS files:**
```bash
ls -la js/admin-*.js
```

**If any file is missing:**
> **STOP** - Ask the user: "File `{filename}` tidak ditemukan. Apakah file ini ada di theme sumber? Haruskah saya copy dari theme lain?"

### Step 2: Copy PHP Files

**2.1. Create admin directory structure:**
```bash
mkdir -p inc/admin
```

**2.2. Copy main orchestrator:**
```bash
cp inc/admin.php [target-theme]/inc/admin.php
```

**2.3. Copy all admin modules:**
```bash
cp inc/admin/*.php [target-theme]/inc/admin/
```

**2.4. Verify auto-loading:**

The theme must have auto-loading mechanism in `functions.php`. Check if this pattern exists:

```php
// Auto-load all files from inc/ directory
$allFiles = array_merge(
    glob(get_template_directory() . '/inc/*.php'),
    glob(get_template_directory() . '/inc/**/*.php')
);
foreach ($allFiles as $file) {
    require_once $file;
}
```

If not exist, add it to `functions.php` or manually require files:

```php
require_once get_template_directory() . '/inc/admin.php';
// Admin modules auto-loaded by glob pattern in inc/admin/
```

### Step 3: Copy SCSS Files

**3.1. Copy SCSS partials:**
```bash
cp scss/admin.scss [target-theme]/scss/
cp scss/_tokens.scss [target-theme]/scss/
cp scss/_admin-*.scss [target-theme]/scss/
```

**3.2. Verify SCSS compilation setup:**

Check if target theme has SCSS compilation configured. Look for:
- `package.json` with sass scripts
- `gulpfile.js` with SCSS tasks
- OR manual compilation workflow

If not configured, ask user:
> "Theme target belum ada setup SCSS compilation. Apakah saya harus setup npm + sass, atau Anda compile manual?"

### Step 4: Copy JavaScript Files

**4.1. Copy JS files:**
```bash
cp js/admin-*.js [target-theme]/js/
```

**4.2. Verify enqueue in PHP:**

Check `inc/admin.php` or equivalent file has `wp_enqueue_script()` calls:

```php
function ane_admin_enqueue_scripts( $hook ) {
    // Chart.js for dashboard
    wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js', array(), '4.4.0', true );

    // Admin scripts
    wp_enqueue_script( 'ane-admin-menu', get_template_directory_uri() . '/js/admin-menu.js', array(), '1.0.0', true );
    wp_enqueue_script( 'ane-admin-header', get_template_directory_uri() . '/js/admin-header.js', array(), '1.0.0', true );
    wp_enqueue_script( 'ane-admin-dashboard', get_template_directory_uri() . '/js/admin-dashboard.js', array('chartjs'), '1.0.0', true );
    // ... etc
}
add_action( 'admin_enqueue_scripts', 'ane_admin_enqueue_scripts' );
```

### Step 5: Compile SCSS to CSS

**5.1. Using npm + sass:**
```bash
npx sass scss/admin.scss css/admin.css --no-source-map
npx sass scss/admin.scss css/admin.min.css --style=compressed
```

**5.2. Verify CSS output:**
```bash
ls -lh css/admin.css
ls -lh css/admin.min.css
```

**5.3. Enqueue compiled CSS:**

Check in `inc/admin.php`:

```php
function ane_admin_enqueue_styles( $hook ) {
    wp_enqueue_style(
        'ane-admin-style',
        get_template_directory_uri() . '/css/admin.min.css',
        array(),
        '1.0.7'
    );
}
add_action( 'admin_enqueue_scripts', 'ane_admin_enqueue_styles' );
```

### Step 6: Configure Design Tokens

**6.1. Check color variables:**

Open `scss/_tokens.scss` and verify brand colors match target theme:

```scss
:root {
    // Brand Colors - CUSTOMIZE THESE
    --ane-color-primary: #2d232e;
    --ane-color-primary-rgb: 45, 35, 46;
    --ane-color-secondary: #7d8491;
    --ane-color-accent: #e0ddcf;
    --ane-color-light: #f1f0ea;
    --ane-color-dark: #1a1a1a;

    // ... etc
}
```

**6.2. Update to match new theme:**

Replace hex values with target theme's brand colors. Keep variable names consistent for compatibility.

### Step 7: ACF Configuration (Optional)

If admin dashboard uses ACF options pages, ensure ACF Pro is installed:

**7.1. Check ACF dependency:**
```php
if ( ! function_exists( 'acf_add_options_page' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p>Custom Admin Dashboard requires ACF Pro.</p></div>';
    });
    return;
}
```

**7.2. Register options pages:**

The `inc/admin.php` file contains ACF options page registration. Customize page slugs and titles as needed:

```php
function ane_register_acf_options_pages() {
    acf_add_options_page(array(
        'page_title' => __( 'Theme Control Center', 'your-theme' ),
        'menu_title' => __( 'Theme Setup', 'your-theme' ),
        'menu_slug'  => 'theme-setup',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 59,
    ));
}
add_action( 'acf/init', 'ane_register_acf_options_pages' );
```

### Step 8: Test Admin Dashboard

**8.1. Clear WordPress cache:**
```bash
# If using WP-CLI:
wp cache flush

# Or via admin: WordPress Admin → Performance → Clear Cache
```

**8.2. Test checklist:**
- [ ] Admin sidebar shows icon-only menu
- [ ] Tooltips appear on hover (desktop)
- [ ] Submenu slides in on click
- [ ] Mobile menu shows bottom navigation (≤782px)
- [ ] No tooltips on mobile
- [ ] Dashboard page loads with charts
- [ ] No JavaScript console errors
- [ ] No CSS layout breaks

**8.3. Debug mode:**

Enable WordPress debug to catch errors:

```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check `wp-content/debug.log` for errors.

---

## Configuration

### Customization Points

**1. Text Domain:**

Search and replace text domain across all PHP files:

```bash
# Replace 'newsane' or 'elemenane' with your theme text domain
find inc/admin -type f -name "*.php" -exec sed -i 's/newsane/yourtheme/g' {} +
find inc/admin -type f -name "*.php" -exec sed -i 's/elemenane/yourtheme/g' {} +
```

**2. Function Prefixes:**

All functions use `ane_` prefix. For consistency, keep it OR search-replace:

```bash
# Replace 'ane_' prefix with 'yourprefix_'
# WARNING: This affects 50+ functions, test thoroughly
```

**3. Menu Slug:**

Default main menu slug is `ane-setup`. Update in `inc/admin.php`:

```php
'menu_slug' => 'your-theme-setup',
```

**4. Chart.js Version:**

Dashboard uses Chart.js 4.4.0 from CDN. To use different version or local file:

```php
// In inc/admin.php or inc/admin/dashboard.php
wp_enqueue_script(
    'chartjs',
    get_template_directory_uri() . '/js/vendor/chart.min.js', // Local
    array(),
    '4.4.0',
    true
);
```

**5. Mobile Breakpoint:**

Default mobile breakpoint is 782px (WordPress standard). To change:

```scss
// In scss/_admin-footer-mobile-menu.scss
@media screen and (max-width: 782px) { // Change this value
    // Mobile styles
}
```

**6. Dashboard Analytics:**

To customize dashboard stats, edit `inc/admin/dashboard.php`:

```php
function ane_get_dashboard_stats() {
    // Customize queries here
    return array(
        'total_posts' => ...,
        'total_pages' => ...,
        // Add custom metrics
    );
}
```

---

## Dependencies

### Required WordPress Features
- WordPress 5.0+ (for Block Editor compatibility)
- PHP 7.4+ (for typed properties and arrow functions)

### Required Plugins
- **ACF Pro** (for options pages) - Optional but recommended
  - Fallback: Admin pages work without ACF but some features disabled

### External Libraries
- **Chart.js 4.4.0** (loaded from CDN)
  - Used for: Dashboard analytics charts
  - Fallback: Dashboard works without charts, just shows stats

### SCSS Compilation
- **Dart Sass** (modern SCSS compiler)
  - Install: `npm install -g sass`
  - Usage: `sass --version` should show 1.x.x
  - Don't use: LibSass (deprecated) or node-sass (slow)

### Optional Enhancements
- **Mobile_Detect** library (already in theme at `inc/Mobile_Detect.php`)
  - Used for server-side device detection
  - Theme works without it (uses CSS media queries as fallback)

---

## Troubleshooting

### Issue 1: Admin sidebar not showing icon-only layout

**Symptoms:** Sidebar shows full menu with text, not icon-only

**Solution:**
1. Check if `css/admin.min.css` is enqueued and loaded
2. Inspect element - look for `#adminmenu { width: 60px }` in CSS
3. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
4. Check for CSS conflicts from other plugins

**Debug:**
```php
// Add to functions.php temporarily
add_action('admin_head', function() {
    echo '<!-- Admin CSS loaded: ' . (wp_style_is('ane-admin-style', 'done') ? 'YES' : 'NO') . ' -->';
});
```

### Issue 2: Tooltips not appearing

**Symptoms:** Hover on menu items, no tooltip shows

**Solution:**
1. Check if `js/admin-menu.js` is loaded
2. Open browser console - look for JavaScript errors
3. Verify tooltip element exists: Inspect and search for `.ane-tooltip`

**Debug:**
```javascript
// In browser console
console.log('Tooltip element:', document.querySelector('.ane-tooltip'));
console.log('Menu script loaded:', typeof aneInitMenuTooltips !== 'undefined');
```

### Issue 3: Mobile menu not showing

**Symptoms:** On mobile, no bottom navigation appears

**Solution:**
1. Check viewport width is ≤782px
2. Verify `js/admin-header.js` and `js/admin-menu.js` are loaded
3. Check if `#wpwrap` has `padding-bottom: 70px`

**Debug:**
```javascript
// In mobile browser console
console.log('Viewport width:', window.innerWidth);
console.log('Mobile menu:', document.querySelector('.ane-footer-mobile-menu'));
```

### Issue 4: Dashboard charts not rendering

**Symptoms:** Dashboard loads but charts are blank/missing

**Solution:**
1. Check if Chart.js is loaded (check Network tab in DevTools)
2. Verify chart data is passed correctly via `wp_localize_script()`
3. Check browser console for Chart.js errors

**Debug:**
```javascript
// In browser console on dashboard page
console.log('Chart.js loaded:', typeof Chart !== 'undefined');
console.log('Chart data:', typeof aneDashboardData !== 'undefined' ? aneDashboardData : 'NOT FOUND');
```

### Issue 5: SCSS won't compile

**Symptoms:** Running `sass` command shows errors

**Common Errors:**

**Error: "Can't find stylesheet to import"**
```
Solution: Check @use/@import paths in admin.scss
Make sure all _admin-*.scss files exist
```

**Error: "Invalid CSS after"**
```
Solution: Check for syntax errors in SCSS files
Run sass with --trace flag: sass --trace scss/admin.scss css/admin.css
```

**Error: "Undefined variable"**
```
Solution: Verify _tokens.scss is imported first in admin.scss
@use 'tokens' must be at the top
```

### Issue 6: White screen of death (WSOD)

**Symptoms:** Admin area shows blank white screen

**Solution:**
1. Enable WP_DEBUG in wp-config.php
2. Check wp-content/debug.log for fatal errors
3. Common causes:
   - Missing semicolon in PHP files
   - Function name conflicts
   - Missing required files

**Quick Fix:**
```bash
# Temporarily disable admin system
# Rename admin.php to disable auto-loading
mv inc/admin.php inc/admin.php.disabled
```

### Issue 7: ACF fields not showing

**Symptoms:** Custom admin pages load but no ACF fields appear

**Solution:**
1. Verify ACF Pro is active
2. Check ACF field group is assigned to correct location
3. Field group location rule should be: "Options Page is equal to [your-menu-slug]"

**Debug:**
```php
// Check if ACF is available
var_dump( function_exists('acf_add_options_page') ); // Should output: bool(true)

// Check registered options pages
global $acf;
var_dump( $acf->options_pages );
```

### Issue 8: Mobile admin height unlimited

**Symptoms:** Mobile admin scrolls forever, footer menu not visible

**Solution:**
1. Check if `scss/_admin-footer-mobile-menu.scss` is compiled
2. Verify CSS contains:
   ```css
   #wpwrap {
       max-height: 100vh;
       overflow-y: auto;
   }
   ```
3. Recompile SCSS if missing

### Issue 9: Submenu won't close

**Symptoms:** Click menu item, submenu opens but won't close on second click

**Solution:**
1. Check `js/admin-menu.js` for click event handlers
2. Verify `opensub` class is toggled correctly
3. Clear browser cache

**Debug:**
```javascript
// Check if menu item has opensub class
document.querySelector('#adminmenu li.opensub'); // Should exist when submenu is open
```

### Issue 10: Custom menu icons not showing

**Symptoms:** Custom SVG icons in menu not appearing, showing default dashicons

**Solution:**
1. Check `scss/_admin-menu-icon.scss` is imported in `admin.scss`
2. Verify SVG data URI is properly encoded
3. Check if menu item has correct ID (e.g., `#toplevel_page_ane-setup`)

**Debug:**
```css
/* Check computed style of menu icon */
/* Should have background-image with data:image/svg+xml */
#adminmenu #toplevel_page_ane-setup .wp-menu-image:before {
    /* Inspect this element in DevTools */
}
```

---

## File Dependency Tree

Visual representation of file dependencies:

```
functions.php
    └── inc/admin.php (orchestrator)
        ├── inc/admin/design-tokens.php
        ├── inc/admin/header.php
        │   └── js/admin-header.js
        ├── inc/admin/footer-mobile-menu.php
        ├── inc/admin/navigation.php
        ├── inc/admin/menu.php
        │   └── js/admin-menu.js
        ├── inc/admin/dashboard.php
        │   └── js/admin-dashboard.js
        │       └── Chart.js (CDN)
        ├── inc/admin/user.php
        │   └── js/admin-user.js
        ├── inc/admin/content.php
        └── inc/admin/customizer.php

scss/admin.scss (entry point)
    ├── scss/_tokens.scss
    ├── scss/_admin-general.scss
    ├── scss/_admin-style.scss
    ├── scss/_admin-menu.scss
    ├── scss/_admin-header.scss
    ├── scss/_admin-navigation.scss
    ├── scss/_admin-dashboard.scss
    ├── scss/_admin-user.scss
    ├── scss/_admin-content.scss
    ├── scss/_admin-login.scss
    ├── scss/_admin-menu-icon.scss
    ├── scss/_admin-animations.scss
    └── scss/_admin-footer-mobile-menu.scss
        ↓ (compile)
    css/admin.css (development)
    css/admin.min.css (production)
```

---

## Quick Start Checklist

For experienced developers who want to migrate quickly:

- [ ] Copy all `inc/admin.php` and `inc/admin/*.php` files
- [ ] Copy all `scss/_admin-*.scss` and `scss/admin.scss` files
- [ ] Copy all `js/admin-*.js` files
- [ ] Verify auto-loading in `functions.php`
- [ ] Update text domain in all files
- [ ] Customize brand colors in `scss/_tokens.scss`
- [ ] Compile SCSS: `npx sass scss/admin.scss css/admin.min.css --style=compressed`
- [ ] Clear WordPress cache
- [ ] Test admin in desktop browser
- [ ] Test admin in mobile (≤782px width)
- [ ] Check browser console for errors
- [ ] Done! 🎉

---

## Support & Updates

**Version:** 1.0.7
**Last Updated:** 2025-12-31
**Compatibility:** WordPress 5.0+, PHP 7.4+

**Changelog Location:** See `CHANGELOG.md` in theme root for version history and updates.

**Common Customizations:**
- Change sidebar width: Edit `scss/_admin-menu.scss` line ~79
- Modify mobile breakpoint: Edit `scss/_admin-footer-mobile-menu.scss` line ~7
- Add custom dashboard widgets: Edit `inc/admin/dashboard.php`
- Change menu icon: Edit `scss/_admin-menu-icon.scss`

---

## Credits

**Original Theme:** Elemen Ane by Webane Indonesia
**Admin Dashboard System:** Custom-built for modern WordPress admin experience
**Libraries Used:**
- Chart.js 4.4.0 (MIT License)
- WordPress Core (GPL v2+)
- ACF Pro (Commercial License)

---

## License

This admin dashboard system inherits the license of the Elemen Ane theme.
When migrating to another theme, ensure compatibility with target theme's license.

GPL-compatible, free to use and modify for your projects.
