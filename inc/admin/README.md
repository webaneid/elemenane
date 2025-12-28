# Tempone Admin Customization System

Complete custom WordPress admin interface system with dynamic branding, modern UI/UX, and mobile-first design.

**IMPORTANT FOR AI ASSISTANTS:** This document provides complete integration guide for admin customization system including files outside this folder, SCSS structure, ACF color integration, and Chart.js patterns.

---

## 📁 File Structure

### Files in inc/admin/ Folder

```
inc/admin/
├── README.md           # This file - Complete AI documentation
├── dashboard.php       # Custom dashboard (replaces wp-admin/index.php)
├── user.php            # Enhanced user profile page (wp-admin/profile.php)
├── customizer.php      # Glassmorphism login page styling
├── menu.php            # Admin menu styling & customization
├── menu-icons.php      # Custom menu icon system with SVG support
├── header.php          # Admin bar & top header styling
├── navigation.php      # Modern navigation system
├── content.php         # Content editor area styling
└── design-tokens.php   # Centralized design system (colors, spacing, radius)
```

### Files OUTSIDE inc/admin/ Folder (But Handle Admin Functions)

**Location: `/inc/` (root level)**

1. **`inc/admin.php`** - Main ACF Options Pages Registration
   - Registers "Tempone Setup" top-level menu
   - Creates subpages: Customization, SEO & News, Theme Setup
   - Uses `acf_add_options_page()` and `acf_add_options_sub_page()`
   - Location: Line 17-758 in inc/admin.php

2. **`inc/editor.php`** - Gutenberg Editor Customization
   - WordPress block editor typography and styling
   - Loads Google Fonts (Poppins, Inter) for editor
   - Heading styles (H1-H6), body text, blockquotes, code blocks
   - Compiled to: `css/editor-style.css`
   - Location: inc/editor.php

**Why these files are separate:**
- `inc/admin.php` handles ACF options registration (theme-wide settings)
- `inc/editor.php` handles frontend editor (not admin interface)
- Both are loaded in `functions.php` alongside inc/admin/ files

---

## 🎨 Design System

### Color Variables (Dynamic from ACF)

The admin interface uses **dynamic colors** from ACF options or falls back to theme defaults:

**ACF Fields (Location: Tempone Setup → Customization):**
- `ane-warna-utama` → `--tempone-color-primary` (Main brand color)
- `ane-warna-utama-2` → `--tempone-color-secondary` (Secondary brand color)
- `ane-warna-text` → `--tempone-color-body` (Body text color)
- `ane-warna-terang` → `--tempone-color-light` (Light background)
- `ane-warna-gelap` → `--tempone-color-dark` (Dark elements)
- `ane-warna-alternatif` → `--tempone-color-accent` (Accent color)

**Fallback Colors (scss/_tokens.scss):**
```scss
--tempone-color-primary: #2d232e;
--tempone-color-secondary: #474448;
--tempone-color-body: #1e2d2f;
--tempone-color-light: #f1f0ea;
--tempone-color-dark: #1a1a1a;
--tempone-color-accent: #e0ddcf;
```

**Implementation:**
- Colors injected via `inc/acf-layouts.php:138-180` (admin_head hook)
- CSS variables available in all admin pages
- Automatic RGB variants generated for transparency

### Border Radius Consistency

**Standard Values:**
- `20px` - Large containers (login form, modals)
- `12px` - Medium elements (cards, postboxes)
- `10px` - Inputs, small buttons
- `8px` - Icons, badges, avatars
- `50%` / `999px` - Circular/pill elements

### Spacing Scale

**Standard Padding/Margin:**
- `0.5rem` (8px) - Tight spacing
- `1rem` (16px) - Default spacing
- `1.5rem` (24px) - Section spacing
- `2rem` (32px) - Large spacing

### Typography

**Font Families:**
- Headings: `Poppins` (weights: 600, 700, 800)
- Body: `Inter` (weights: 400, 500, 600)
- Fallback: `-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`

**Font Sizes:**
- Hero title: `clamp(1.8rem, 3vw, 2.6rem)`
- Section title: `1.5rem`
- Card title: `1.05rem`
- Body text: `0.95rem`
- Small text: `0.8125rem`

---

## 🚀 Features

### 1. Custom Dashboard (dashboard.php)

**Replaces default WordPress dashboard** with:
- Real-time analytics (visitors, posts, comments, authors)
- Chart.js line chart (posts & views per month)
- Popular posts ranking (top 5 by views)
- Recent posts list with "Create New Post" button
- Author performance leaderboard
- Content distribution breakdown

**Redirect:**
- `wp-admin/index.php` auto-redirects to `admin.php?page=tempone-dashboard`
- Default dashboard menu hidden
- Custom menu added at position 2

### 1.5. Enhanced User Profile (user.php)

**Enhances wp-admin/profile.php** with:
- **Header Card:** Avatar (from ACF field `gravatar_ane`), display name, username, role badge, email, registration date, bio
- **Performance Section:**
  - 5 stat cards: Total Posts, Total Views, Avg Views/Post, Total Comments, Posts This Month
  - Dual-axis Chart.js chart: Posts vs Views over 12 months (filtered by user)
- **Recent Activity:**
  - Recent Posts (5 items): title with edit link, time ago, views count, comments count, status badge
  - Recent Comments (5 items): excerpt (20 words), post link, time ago

**Implementation:**
- Hook: `personal_options` with priority 1 (appears ABOVE WordPress default form)
- Uses CSS variables for all colors (responds to ACF customization)
- Reuses Chart.js pattern from dashboard
- Responsive design with 782px mobile breakpoint

**Key Functions:**
- `tempone_render_user_profile()` - Main wrapper (lines 454-496)
- `tempone_user_profile_header()` - Header card with avatar (lines 67-117)
- `tempone_user_profile_performance()` - Stats and chart (lines 124-213)
- `tempone_user_profile_recent_activity()` - Posts and comments (lines 220-316)
- `tempone_get_user_stats()` - User statistics (lines 324-382)
- `tempone_get_user_posts_data()` - 12-month chart data (lines 390-447)

### 2. Glassmorphism Login (customizer.php)

**Modern login page** with:
- Dark background with gradient blur circles
- Glassmorphism card effect (backdrop-filter blur)
- Webane green color scheme
- Custom logo from webane.com
- Responsive mobile design
- Custom title, subtitle, signup link

**Branding:**
- Logo URL: Links to homepage (not wordpress.org)
- Logo title: Site name (from get_bloginfo)
- Footer text: "Designed by Webane Indonesia"

### 3. Admin Menu Styling (menu.php)

**Modern sidebar menu** with:
- Dynamic brand colors
- Icon styling consistency
- Hover effects with smooth transitions
- Active state indicators
- Submenu animations
- Collapsed menu support

### 4. Admin Header/Topbar (header.php)

**Top admin bar customization:**
- Dynamic background color
- Custom branding in admin bar
- User menu styling
- Notification badge styling
- Mobile-responsive

### 5. Modern Navigation (navigation.php)

**Enhanced navigation UX:**
- Tab-style interfaces
- Breadcrumb styling
- Pagination improvements
- Filter bar modernization
- Mobile tap-friendly buttons (min 44x44px)

---

## 📱 Mobile-First Design

### Responsive Breakpoints

```scss
@media (max-width: 782px) {
  // WordPress mobile admin breakpoint
  // Stack elements vertically
  // Increase tap target sizes
  // Simplify navigation
}

@media (max-width: 600px) {
  // Small mobile
  // Hide non-essential elements
  // Full-width cards
}
```

### Touch Optimization

- **Minimum tap target:** 44x44px (Apple HIG guideline)
- **Button padding:** 0.75rem minimum
- **Spacing between tappable elements:** 8px minimum
- **Font size minimum:** 16px (prevents auto-zoom on iOS)

---

## 🔧 Implementation Guide

### Step 1: Activate Custom Admin

Files are automatically loaded via `functions.php`:

```php
require_once TEMPONE_PATH . '/inc/admin/dashboard.php';
require_once TEMPONE_PATH . '/inc/admin/customizer.php';
require_once TEMPONE_PATH . '/inc/admin/menu.php';
require_once TEMPONE_PATH . '/inc/admin/header.php';
require_once TEMPONE_PATH . '/inc/admin/navigation.php';
require_once TEMPONE_PATH . '/inc/admin/design-tokens.php';
```

### Step 2: Configure Colors (Optional)

1. Go to: **WP Admin → Tempone Setup → Customization**
2. Set brand colors in ACF fields:
   - Warna Utama (Primary)
   - Warna Utama 2 (Secondary)
   - Warna Text (Body)
   - Warna Terang (Light)
   - Warna Gelap (Dark)
   - Warna Alternatif (Accent)
3. Save → Colors automatically apply to entire admin

**If not configured:**
- System uses default colors from `_tokens.scss`
- Still looks professional with monochrome theme

### Step 3: Compile SCSS

**CRITICAL:** All SCSS files MUST be compiled to CSS after ANY changes to admin styling.

```bash
# Development (watch mode - auto-compile on save)
npx sass scss/admin.scss css/admin.css --watch

# Single compilation
npx sass scss/admin.scss css/admin.css

# Production (minified)
npx sass scss/admin.scss css/admin.min.css --style=compressed

# Compile BOTH regular and minified
npx sass scss/admin.scss css/admin.css && npx sass scss/admin.scss css/admin.min.css --style=compressed
```

**SCSS File Structure:**

```
scss/
├── _tokens.scss              # Design tokens (CSS variables)
├── admin.scss                # Main admin import file
├── _admin-style.scss         # General admin styling (hero, cards)
├── _admin-dashboard.scss     # Dashboard page styles
├── _admin-user.scss          # User profile page styles
├── _admin-menu.scss          # Sidebar menu styling
├── _admin-header.scss        # Top admin bar styling
├── _admin-navigation.scss    # Navigation elements
├── _admin-animations.scss    # Animation utilities
└── editor-style.scss         # Editor styles (standalone, not imported)
```

**admin.scss Import Order:**
```scss
@use 'tokens';              // MUST be first - defines CSS variables
@use 'admin-style';         // General admin styles
@use 'admin-dashboard';     // Dashboard-specific
@use 'admin-user';          // User profile-specific
@use 'admin-menu';          // Sidebar menu
@use 'admin-header';        // Top bar
@use 'admin-navigation';    // Navigation elements
@use 'admin-animations';    // Animations (must be last for cascading)
```

**Output Files:**
- `css/admin.css` - Development version (~31KB uncompressed)
- `css/admin.min.css` - Production version (~18KB compressed)
- `css/editor-style.css` - Separate editor styles (~7KB)

**IMPORTANT:** Editor styles are compiled separately:
```bash
npx sass scss/editor-style.scss css/editor-style.css
```

### Step 4: Test in Browser

1. **Login Page:** `wp-login.php`
   - Check glassmorphism effect
   - Verify green branding
   - Test responsive mobile view

2. **Dashboard:** `wp-admin/`
   - Should redirect to custom dashboard
   - Verify Chart.js loads
   - Check all widgets render

3. **Other Admin Pages:** Posts, Pages, Settings
   - Check menu styling
   - Verify header customization
   - Test navigation elements

---

## 🔄 Duplication to Other Themes

### Quick Setup (5 Minutes)

**1. Copy Files:**
```bash
# Copy entire admin folder
cp -r tempone/inc/admin/ yourtheme/inc/admin/

# Copy admin SCSS partials
cp tempone/scss/_admin-*.scss yourtheme/scss/

# Copy design tokens
cp tempone/scss/_tokens.scss yourtheme/scss/
```

**2. Update functions.php:**
```php
// Add these requires
require_once YOURTHEME_PATH . '/inc/admin/dashboard.php';
require_once YOURTHEME_PATH . '/inc/admin/customizer.php';
require_once YOURTHEME_PATH . '/inc/admin/menu.php';
require_once YOURTHEME_PATH . '/inc/admin/header.php';
require_once YOURTHEME_PATH . '/inc/admin/navigation.php';
require_once YOURTHEME_PATH . '/inc/admin/design-tokens.php';
```

**3. Update Branding:**

In `inc/admin/customizer.php` line 94:
```php
// Change logo URL
background-image: url('https://yourbrand.com/logo.svg');
```

In `inc/admin/dashboard.php` lines 780-786:
```php
// Change footer text
function yourtheme_remove_footer_admin() {
    return sprintf(
        esc_html__( 'Designed with love by %s', 'yourtheme' ),
        '<a href="https://yourbrand.com/">Your Brand</a>'
    );
}
```

**4. Update Text Domain:**

Find & replace:
- `'tempone'` → `'yourtheme'`
- `tempone_` → `yourtheme_`
- `Tempone` → `YourTheme`

**5. Configure ACF Fields:**

Create ACF field group **"Theme Customization"** with these fields:
- `ane-warna-utama` (Color Picker)
- `ane-warna-utama-2` (Color Picker)
- `ane-warna-text` (Color Picker)
- `ane-warna-terang` (Color Picker)
- `ane-warna-gelap` (Color Picker)
- `ane-warna-alternatif` (Color Picker)

Location: Options Page → Theme Customization

**6. Compile & Test:**
```bash
sass scss/admin.scss css/admin.css --style=compressed
```

**Done!** Your new theme now has identical custom admin.

---

## 🎯 Best Practices

### DO:
✅ Use CSS variables for all colors (dynamic branding)
✅ Follow WordPress coding standards
✅ Escape all output (`esc_html`, `esc_url`, `esc_attr`)
✅ Use translation functions (`__()`, `_e()`)
✅ Test on mobile devices
✅ Maintain consistent border-radius
✅ Use BEM naming for SCSS classes
✅ Keep design tokens centralized

### DON'T:
❌ Hardcode colors directly in SCSS
❌ Skip mobile testing
❌ Mix border-radius values randomly
❌ Use inline styles (use classes)
❌ Forget to compile SCSS to CSS
❌ Skip translation strings
❌ Remove WordPress branding completely (legal issues)
❌ Break accessibility (maintain contrast ratios)

---

## 🐛 Troubleshooting

### Colors Not Applying

**Problem:** Admin still shows default WordPress colors

**Solution:**
1. Check ACF fields exist in database
2. Verify field names match exactly: `ane-warna-utama` etc.
3. Clear browser cache
4. Check `admin_head` hook priority (should be 999)
5. Inspect HTML `<head>` for `<style id="tempone-custom-colors-admin">`

### Dashboard Not Loading

**Problem:** Redirect to custom dashboard fails

**Solution:**
1. Check menu slug: `tempone-dashboard`
2. Verify `add_menu_page()` capability: `'read'`
3. Check WordPress transient cache (delete `_transient_*`)
4. Deactivate conflicting plugins
5. Check PHP error log for fatal errors

### Chart Not Rendering

**Problem:** Chart.js graph shows blank

**Solution:**
1. Check Chart.js CDN loads: `cdn.jsdelivr.net/npm/chart.js@4.4.0`
2. Verify `wp_localize_script` data: `temponeDashboard.postsData`
3. Open browser console for JavaScript errors
4. Check canvas has height: `320px`
5. Verify database has posts with views meta

### Login Page Broken

**Problem:** Glassmorphism not showing

**Solution:**
1. Check `login_enqueue_scripts` hook fires
2. Verify CSS in `<style>` tag inline (not external file)
3. Test in different browsers (Safari requires `-webkit-backdrop-filter`)
4. Check logo URL is accessible
5. Clear browser cache & test incognito

### Mobile Layout Issues

**Problem:** Admin UI broken on mobile

**Solution:**
1. Check viewport meta tag exists
2. Test at 782px breakpoint (WordPress mobile)
3. Verify tap targets minimum 44x44px
4. Check `overflow-x: hidden` on body
5. Test in real device, not just DevTools

---

## 📊 Performance

**Admin CSS Size:**
- Uncompressed: ~45KB
- Compressed: ~28KB
- Gzip: ~8KB

**Page Load Impact:**
- Custom dashboard: +0.2s (Chart.js load)
- Login page: +0.05s (inline CSS)
- Other admin pages: Negligible

**Optimization Tips:**
- Use minified CSS in production
- Load Chart.js only on dashboard page
- Defer non-critical admin scripts
- Use CSS sprites for repeated icons

---

## 🔒 Security

**Implemented Safeguards:**
1. **Capability checks:** All admin pages require proper capabilities
2. **Nonce verification:** Forms use WordPress nonces
3. **Input sanitization:** All user input sanitized
4. **Output escaping:** All output escaped properly
5. **SQL prepared statements:** All queries use $wpdb->prepare()
6. **Direct access prevention:** All files check `ABSPATH`

**No Security Risks:**
- No user input directly in queries
- No file uploads in admin
- No external API calls without validation
- No eval() or create_function()

---

## 🎨 ACF Color System Integration

### How Dynamic Colors Work

The theme uses ACF fields to allow admin customization of all colors. This system injects CSS variables that override default tokens.

**Integration Location:** `inc/acf-layouts.php` Lines 138-180

### Function: tempone_custom_colors_admin_css()

```php
function tempone_custom_colors_admin_css() {
    // ACF field name → CSS variable mapping
    $color_map = array(
        'ane-warna-utama'      => 'primary',      // Main brand color
        'ane-warna-utama-2'    => 'secondary',    // Secondary brand color
        'ane-warna-text'       => 'body',         // Body text color
        'ane-warna-terang'     => 'light',        // Light background
        'ane-warna-gelap'      => 'dark',         // Dark elements
        'ane-warna-alternatif' => 'accent',       // Accent color
    );

    // For each ACF field, inject CSS variable
    foreach ( $color_map as $acf_field => $var_name ) {
        $color = get_field( $acf_field, 'option' );
        if ( $color ) {
            echo '--tempone-color-' . $var_name . ': ' . esc_attr( $color ) . ';' . "\n";

            // Generate RGB variant for transparency
            list( $r, $g, $b ) = sscanf( $color, '#%02x%02x%02x' );
            echo '--tempone-color-' . $var_name . '-rgb: ' . $r . ', ' . $g . ', ' . $b . ';' . "\n";
        }
    }
}
add_action( 'admin_head', 'tempone_custom_colors_admin_css', 999 );
```

**Hook:** `admin_head` with priority 999 (latest) to override all other styles.

**Output Example:**
```html
<style id="tempone-custom-colors-admin">
:root {
    --tempone-color-primary: #2d232e;
    --tempone-color-primary-rgb: 45, 35, 46;
    --tempone-color-secondary: #474448;
    --tempone-color-secondary-rgb: 71, 68, 72;
    /* ... more variables */
}
</style>
```

### How to Use CSS Variables in SCSS

**ALWAYS use this pattern:**

```scss
// ✅ CORRECT - Uses CSS variable with fallback
.element {
    color: var(--tempone-color-primary, #2d232e);
    background: var(--tempone-color-white, #ffffff);
    border: 1px solid rgba(var(--tempone-color-primary-rgb, 45, 35, 46), 0.08);
}

// ❌ WRONG - Hardcoded color, won't respond to ACF
.element {
    color: #2d232e;
    background: #ffffff;
    border: 1px solid rgba(45, 35, 46, 0.08);
}
```

**Available CSS Variables:**

**Standard Variables:**
- `var(--tempone-color-primary)` - Primary brand color
- `var(--tempone-color-secondary)` - Secondary brand color
- `var(--tempone-color-body)` - Body text color
- `var(--tempone-color-light)` - Light background
- `var(--tempone-color-dark)` - Dark elements
- `var(--tempone-color-accent)` - Accent color
- `var(--tempone-color-white)` - White (#ffffff)
- `var(--tempone-color-black)` - Black (#000000)

**RGB Variants (for transparency):**
- `rgba(var(--tempone-color-primary-rgb), 0.1)` - 10% opacity
- `rgba(var(--tempone-color-secondary-rgb), 0.5)` - 50% opacity
- `rgba(var(--tempone-color-body-rgb), 0.8)` - 80% opacity

**Pattern for Gradients:**
```scss
background: linear-gradient(135deg,
    var(--tempone-color-primary, #2d232e),
    var(--tempone-color-secondary, #474448)
);
```

### Testing Color Customization

1. Go to: **WP Admin → Tempone Setup → Customization**
2. Change "Warna Utama" (Primary Color) to a bright color (e.g., #ff0000 red)
3. Save changes
4. Visit any admin page
5. Verify colors update across entire admin interface

**Verification:**
- Dashboard cards should use new primary color
- User profile stats should use new primary color
- Menu hover states should use new primary color
- Login page uses separate Webane green palette (not affected)

---

## 📊 Chart.js Integration Pattern

### Overview

Dashboard and User Profile pages use Chart.js for interactive analytics. Follow this pattern when adding new charts.

### Step-by-Step Implementation

**1. Enqueue Chart.js CDN**

```php
function your_page_enqueue_scripts() : void {
    $screen = get_current_screen();
    if ( ! $screen || 'your-page-id' !== $screen->id ) {
        return;
    }

    // Enqueue Chart.js from CDN
    wp_enqueue_script(
        'tempone-chartjs',
        'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
        array(),
        '4.4.0',
        true
    );

    // Enqueue your custom JavaScript
    wp_enqueue_script(
        'your-chart-script',
        TEMPONE_URI . '/js/your-chart.js',
        array( 'tempone-chartjs' ),
        TEMPONE_VERSION,
        true
    );

    // Localize chart data
    wp_localize_script(
        'your-chart-script',
        'yourChartData', // JavaScript object name
        array(
            'postsData' => your_get_chart_data(),
            'colors'    => array(
                'primary' => 'var(--tempone-color-primary)',
                'accent'  => 'var(--tempone-color-accent)',
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'your_page_enqueue_scripts' );
```

**2. Create PHP Function to Get Chart Data**

```php
function your_get_chart_data() : array {
    global $wpdb;

    $data = array(
        'labels' => array(), // Month names
        'values' => array(), // Data points
    );

    // Get last 12 months
    for ( $i = 11; $i >= 0; $i-- ) {
        $month = date( 'Y-m-01', strtotime( "-{$i} months" ) );
        $month_name = date_i18n( 'M Y', strtotime( $month ) );

        // Query database for data
        $count = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
            WHERE post_type = 'post'
            AND post_status = 'publish'
            AND MONTH(post_date) = %d
            AND YEAR(post_date) = %d",
            date( 'm', strtotime( $month ) ),
            date( 'Y', strtotime( $month ) )
        ) );

        $data['labels'][] = $month_name;
        $data['values'][] = (int) $count;
    }

    return $data;
}
```

**3. Add Canvas Element in HTML**

```php
<div class="chart-container" style="position: relative; height: 350px;">
    <canvas id="your-chart-id"></canvas>
</div>
```

**4. Create JavaScript File (js/your-chart.js)**

```javascript
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const chartCanvas = document.getElementById('your-chart-id');

        // Check Chart.js loaded and canvas exists
        if (!chartCanvas || typeof Chart === 'undefined') {
            return;
        }

        // Get localized data
        const chartData = yourChartData.postsData || {};
        const colors = yourChartData.colors || {};

        // Create chart
        new Chart(chartCanvas, {
            type: 'line', // line, bar, doughnut, etc.
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: 'Your Data Label',
                    data: chartData.values || [],
                    borderColor: colors.primary || '#2d232e',
                    backgroundColor: 'rgba(45, 35, 46, 0.1)',
                    tension: 0.4, // Curve smoothness
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(45, 35, 46, 0.95)',
                        padding: 12,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
})();
```

### Dual-Axis Chart Example (User Profile)

For charts with two Y-axes (e.g., Posts vs Views):

```javascript
datasets: [
    {
        label: 'Posts',
        data: postsData.posts || [],
        borderColor: colors.primary,
        yAxisID: 'y', // Left axis
    },
    {
        label: 'Views',
        data: postsData.views || [],
        borderColor: colors.accent,
        yAxisID: 'y1', // Right axis
    }
],
scales: {
    y: {
        type: 'linear',
        position: 'left',
        title: { display: true, text: 'Posts' }
    },
    y1: {
        type: 'linear',
        position: 'right',
        title: { display: true, text: 'Views' },
        grid: { drawOnChartArea: false } // Don't overlap grid
    }
}
```

### Reference Implementations

**Dashboard Chart:** `inc/admin/dashboard.php` + `js/admin-dashboard.js`
- Single-axis line chart
- Posts per month
- Simple implementation

**User Profile Chart:** `inc/admin/user.php` + `js/admin-user.js`
- Dual-axis line chart
- Posts vs Views per month
- Filtered by user_id
- Complex implementation

### Chart Troubleshooting

**Chart not appearing:**
1. Check browser console for JavaScript errors
2. Verify Chart.js CDN loaded: Network tab → chart.umd.min.js
3. Confirm canvas element exists: `document.getElementById('your-chart-id')`
4. Check localized data: `console.log(yourChartData)`

**Data not displaying:**
1. Verify PHP function returns correct array structure
2. Check database has data to display
3. Confirm labels and values arrays have same length
4. Test with dummy data: `data: [1, 2, 3, 4, 5]`

---

## 🔧 Integration Guide for New Templates

### When Adding New Admin Page

**Follow this checklist:**

1. **Create PHP file in inc/admin/**
   ```php
   <?php
   // File: inc/admin/your-feature.php

   // Enqueue scripts/styles
   function tempone_your_feature_enqueue_scripts() : void {
       $screen = get_current_screen();
       if ( ! $screen || 'your-page-id' !== $screen->id ) {
           return;
       }

       wp_enqueue_style( 'tempone-admin', TEMPONE_URI . '/css/admin.css', array(), TEMPONE_VERSION );
   }
   add_action( 'admin_enqueue_scripts', 'tempone_your_feature_enqueue_scripts' );

   // Register admin page
   function tempone_your_feature_register_page() : void {
       add_menu_page(
           __( 'Your Feature', 'tempone' ),
           __( 'Your Feature', 'tempone' ),
           'manage_options',
           'tempone-your-feature',
           'tempone_your_feature_render_page',
           'dashicons-admin-generic',
           20
       );
   }
   add_action( 'admin_menu', 'tempone_your_feature_register_page' );

   // Render page
   function tempone_your_feature_render_page() : void {
       ?>
       <div class="wrap tempone-admin-page">
           <h1><?php esc_html_e( 'Your Feature', 'tempone' ); ?></h1>
           <!-- Your content here -->
       </div>
       <?php
   }
   ```

2. **Create SCSS file in scss/**
   ```scss
   // File: scss/_admin-your-feature.scss

   .tempone-your-feature {
       // Always use CSS variables
       background: var(--tempone-color-white, #ffffff);
       color: var(--tempone-color-body, #1e2d2f);
       border: 1px solid rgba(var(--tempone-color-primary-rgb, 45, 35, 46), 0.08);

       &__title {
           color: var(--tempone-color-primary, #2d232e);
           font-family: 'Poppins', sans-serif;
           font-weight: 700;
       }

       &__card {
           background: var(--tempone-color-white, #ffffff);
           border-radius: 12px; // Use consistent border-radius
           padding: 1.5rem;
           box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
       }
   }
   ```

3. **Import SCSS in admin.scss**
   ```scss
   @use 'tokens';
   @use 'admin-style';
   @use 'admin-dashboard';
   @use 'admin-user';
   @use 'admin-your-feature'; // Add new import
   @use 'admin-menu';
   @use 'admin-header';
   @use 'admin-navigation';
   @use 'admin-animations';
   ```

4. **Compile SCSS**
   ```bash
   npx sass scss/admin.scss css/admin.css
   ```

5. **Require PHP file in functions.php**
   ```php
   require_once TEMPONE_PATH . '/inc/admin/your-feature.php';
   ```

6. **Test thoroughly**
   - Test color variables respond to ACF changes
   - Test responsive layout (mobile 782px breakpoint)
   - Test in different browsers
   - Test with long content
   - Test translation strings

### When Adding Chart to Existing Page

1. Add Chart.js enqueue to existing enqueue function
2. Create PHP function to generate chart data
3. Localize data using `wp_localize_script()`
4. Add canvas element in HTML
5. Create JavaScript file or add to existing
6. Follow Chart.js pattern from dashboard/user profile

### When Adding ACF Fields for New Features

1. Create field group in ACF
2. Set location: Options Page → Your Page
3. Add color picker fields with naming: `ane-your-field`
4. Add to color map in `inc/acf-layouts.php`
5. Use CSS variables in SCSS: `var(--tempone-your-variable)`

---

## 📚 Resources

**WordPress Standards:**
- Coding Standards: https://developer.wordpress.org/coding-standards/
- Plugin Handbook: https://developer.wordpress.org/plugins/
- Theme Handbook: https://developer.wordpress.org/themes/

**Design References:**
- WordPress Design Library: https://wordpress.github.io/design-library/
- Admin Color Schemes: https://developer.wordpress.org/reference/functions/wp_admin_css_color/
- Dashicons: https://developer.wordpress.org/resource/dashicons/

**Tools:**
- SCSS Compiler: https://sass-lang.com/
- Chart.js Docs: https://www.chartjs.org/docs/
- ACF Documentation: https://www.advancedcustomfields.com/resources/

---

## 📝 Changelog

### v1.1.0 - User Profile Enhancement (Current)
- Enhanced user profile page (wp-admin/profile.php)
- User-specific performance stats and analytics
- Dual-axis Chart.js chart (Posts vs Views by user)
- Recent posts and comments activity feed
- Avatar integration with ACF field `gravatar_ane`
- Complete CSS variable integration
- Comprehensive README documentation for AI assistants

### v1.0.0 - Initial Release
- Custom dashboard with Chart.js analytics
- Glassmorphism login page
- Dynamic color system from ACF
- Mobile-first responsive design
- Admin menu, header, navigation styling
- Custom menu icon system

### Future Enhancements
- Dark mode toggle
- Widget drag-and-drop dashboard
- Email notification styling
- Advanced analytics filters
- User role-based dashboard widgets

---

## 💬 Support

**For Tempone Theme:**
- Documentation: `/CLAUDE.md` (main theme docs)
- Documentation: `/inc/admin/README.md` (this file - admin system)
- GitHub: https://github.com/webaneid/tempone
- Website: https://webane.com

**Developer:**
Webane Indonesia - Web Design & Development
https://webane.com

---

**Last Updated:** 2025-01-25
**Version:** 1.1.0
**Maintainer:** Webane Indonesia
