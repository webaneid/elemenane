# [1.2.0](https://github.com/webaneid/elemenane/compare/v1.1.8...v1.2.0) (2026-11-11)


### Bug Fixes

* **content:** rebuild .ane-content so article body never overflows on mobile ([ce6e847](https://github.com/webaneid/elemenane/commit/ce6e847e93b1ff375f033a5ecd9706618392acfe))
* **layout:** align header and footer width with .container via shared container-box mixin ([4268e8f](https://github.com/webaneid/elemenane/commit/4268e8f2d04f6b18ca5a36c18bed458b2ebd5871))
* render time-ago meta icon as HTML instead of escaped text ([86c595a](https://github.com/webaneid/elemenane/commit/86c595abb3139575fb534d0b1aca659bb363a69f))


### Features

* **archive:** rebuild post archives, author profile and pagination ([c9f07a5](https://github.com/webaneid/elemenane/commit/c9f07a5d713261b39eeb24b191d08f99caa5df6b))
* **branch:** rebuild branch archive and card with token colors, themed map pins and accessible dialog ([7b4f4d7](https://github.com/webaneid/elemenane/commit/7b4f4d7786f2366dcd8bdd8d63c689cb4d300336))
* **branch:** rebuild single branch page with company-contact fallback and shared branch cards ([1a5bc45](https://github.com/webaneid/elemenane/commit/1a5bc45236dca2c5834bc89190cd668de5d6ef33))
* **chat:** rebuild floating WhatsApp chat as accessible FAB and panel ([070bced](https://github.com/webaneid/elemenane/commit/070bcedd2e62cf10d0267e2984b5e7e181de3eb0))
* **footer:** rebuild default footer, mobile-first and fully responsive ([71c9229](https://github.com/webaneid/elemenane/commit/71c9229a8f5acbd73997f1676b6017214c9c36a8))
* **header:** rebuild default header with drawer, dropdowns, search dialog and sticky bar ([0fa46dd](https://github.com/webaneid/elemenane/commit/0fa46dd0230308141ecaeae3993688114f738245))
* **landing:** rebuild news landing page with responsive hero and modern sections ([f861864](https://github.com/webaneid/elemenane/commit/f861864b5b291f33cb286b03ac5bc6362048bd2a))
* **product:** rebuild product archive and ane-product-card with WooCommerce compatibility ([8a579d9](https://github.com/webaneid/elemenane/commit/8a579d9fb3a7056724d00a03c752b60a30644c95))
* **product:** rebuild single product page with WooCommerce variable product support ([b8dec83](https://github.com/webaneid/elemenane/commit/b8dec83fc3ae8858ecbae21f16289aa529a9f6dc))
* retrofit theme to Webane standard (phases 00-05) ([650cc06](https://github.com/webaneid/elemenane/commit/650cc0680118d9c8b24794a2733ad9376ff10860))
* **sidebar:** rebuild sidebar and widget styling ([d3a3583](https://github.com/webaneid/elemenane/commit/d3a3583ef295bf888f02ae8504885acc97ef21ea))
* **single:** rebuild entry header and share bar with copy link and title/description/url ([ea80797](https://github.com/webaneid/elemenane/commit/ea80797f4be86c3ced28f4e468778a0b93cc859d))
* **single:** rebuild tags, author box, post nav and related blocks ([edfd099](https://github.com/webaneid/elemenane/commit/edfd0991966933319c67141bc7f69a4a57b1fcc7))


### Performance Improvements

* Core Web Vitals optimizations (phase 06) ([48fc2d4](https://github.com/webaneid/elemenane/commit/48fc2d479f2c3100f7d68114151add63c70ecfe3))

# Changelog

All notable changes to the Elemen Ane WordPress theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Planned Features
- Additional custom widgets
- Performance optimization improvements
- More template variations

---
## [1.1.8] - 2026-09-29
### Added
- **WooCommerce-compatible product data** ([inc/product-woocommerce.php](inc/product-woocommerce.php)). Products already used WooCommerce's post type, taxonomies, slugs and most meta keys; now the stored formats match too, so installing WooCommerce later keeps the whole catalog. WooCommerce stays optional: without it, products work exactly as before
  - Gallery stored in WooCommerce's `_product_image_gallery` (`"12,34,56"`)
  - "Manage stock" stored as `yes`/`no`
  - One-time migration of existing products on the next admin visit (the old `ane_product_gallery` meta is kept as a backup)
- **When WooCommerce is active**
  - The theme no longer registers `product`, `product_cat` and `product_tag` (it used to overwrite WooCommerce's registration and its Shop Manager capabilities)
  - Declares WooCommerce support, so WooCommerce renders products with the theme's templates instead of adding its own product page inside the description
  - Hides the theme's price, stock and gallery fields (edited in WooCommerce's Product data and Product gallery boxes) and stops writing `_price`
  - Prices and stock are read through WooCommerce, so sale schedules and variable products are respected
  - The old `/product/` archive redirects (301) to the Shop page, keeping pagination and filter parameters

### Fixed
- **Stock status** - A "Manage stock" value of "no" was read as enabled, which could show products as out of stock
- **Price filter and sorting** - Products with several `_price` rows (WooCommerce variable products) are listed once, filtered by their lowest price

### Notes
- Tested with WooCommerce 11.0.1 (WooCommerce 11.1+ requires WordPress 7.0)
- After activating WooCommerce on a site, run WooCommerce → Status → Tools → "Regenerate product lookup tables" for WooCommerce's own price features

## [1.1.7] - 2026-09-28
### Added
- **Product archive filters** on the product archive, product category and product tag pages ([inc/product-filter.php](inc/product-filter.php))
  - Category dropdown with autocomplete (type to search, accent-insensitive, keyboard and screen reader support), shown only when the page has more than one category. On category pages it goes to the chosen category's URL; on tag pages it narrows the tag listing
  - Sort by newest, oldest, name A–Z / Z–A, and price low/high (price sorting only when products have prices; products without a price sort last)
  - Price range (min–max), shown only when products on the page have prices
  - Filtering runs server-side from the URL (`kategori`, `urutkan`, `harga_min`, `harga_max`): works without JavaScript, filtered links can be shared, pagination keeps the filters
  - Filtered pages are marked `noindex, follow` (WordPress and Yoast) to avoid duplicate content
  - Lightweight: `js/product-filter.js` is 2.1 KB gzip without jQuery and loads only with the filter bar; styles add 0.8 KB gzip to `catalog.min.css`
  - Indonesian translations for all filter labels

### Fixed
- **"Rp 0" no longer shown** - Products with an empty or zero price (including `0.00`) hide the price on product cards and on the product page, where the whole price box (with the tax note) is hidden
- A sale price set without a regular price is now shown instead of "Rp 0"

### Changed
- Product prices shown everywhere come from one helper, `ane_get_product_price_data()`, with `ane_format_rupiah()` for formatting
- The saved active price (`_price`) uses the same rules as the displayed price. A one-time update fills in `_price` for existing and imported products on the next admin visit
- Page stylesheets (`catalog`, `landing`, `linktree-page`) are versioned by file modification time, so browsers always get updated CSS

## [1.1.6] - 2026-09-26
### Performance
Theme assets on a typical post page dropped from about 290 KB to 130 KB (without images), and no theme JavaScript blocks rendering anymore.
- **Google Maps** loads only on the contact page and branch pages (it loaded on every page, ~100–210 KB)
- **Owl Carousel** loads only on the Landing Page template; **Magnific Popup** only on product pages and content with image links
- **Icon font** served as WOFF2 (18 KB instead of a 38 KB TTF), with fixed font paths and a preload; old EOT/SVG/TTF files removed
- **Google Fonts** loaded in one request with a preconnect, skipping families Elementor already loads on the page
- **CSS split by page**: `main.min.css` went from 20.6 KB to 12.2 KB (gzip). Product/branch styles moved to `catalog.min.css`, Landing Page styles to `landing.min.css`, Linktree styles to `linktree-page.min.css`, each loaded only on its pages (filter `ane_load_all_page_styles` loads all of them everywhere)
- **jQuery no longer loads on plain pages** (posts, archives, home, linktree). `js/newsane.js` (search, mobile menu, floating chat) is rewritten without jQuery and moved to the footer; post views and linktree tracking use `fetch`
- Carousel and lightbox setup moved to `js/carousel-init.js` / `js/lightbox-init.js`, loaded with their libraries

### Security
- **jQuery 3.3.1 (CDN) replaced by WordPress's bundled jQuery 3.7.1**, fixing CVE-2020-11022 / CVE-2020-11023
- **Lightbox title XSS** - Image alt text was inserted into the lightbox as HTML; it is now escaped

### Fixed
- **Landing Page on phones** - The mobile menu and the popular news carousel did not work: Owl Carousel 2.2.1 crashes on fractional `items` (2.2) and the crash stopped the menu code too. Small screens now show 2 whole items
- **Product gallery** - Clicking a thumbnail did not change the main image on sites where images have `srcset` (e.g. square product photos), and elsewhere switched to a non-square image. The lightbox always opened the first image with a "NaN of 5" counter; it now opens the selected image
- **Branch maps** - The Google Maps key setting read by branch pages was never registered, so branch maps only worked because Maps loaded on every page. Added one key helper and a "Google Maps API Key" field in General Settings (the shared key stays as a fallback)
- **Branch map callbacks** - Maps now loads after the branch scripts, fixing "initAneBranchMap is not a function"
- **Google Fonts** - Named weights (`normal`, `bold`) in the Elementor Kit no longer produce an invalid Fonts API URL
- Admin header width

### Changed
- Release workflow compiles every non-partial `scss/*.scss` entry
- Shared SCSS variables and mixins moved to `scss/_mixins.scss`

## [1.1.5] - 2026-09-24
### Security
- **Stored XSS in branch map** - Branch title, address, phone, email, WhatsApp and hours were inserted into the map popup and detail modal without escaping. Any user able to publish branches (Author role and up) could run scripts in visitors' and admins' browsers
  - All branch fields are now escaped; WhatsApp links only accept digits
  - Single branch map popup title is escaped too
  - Files: [js/branch-archive.js](js/branch-archive.js), [js/single-branch.js](js/single-branch.js), [archive-branch.php](archive-branch.php), [single-branch.php](single-branch.php)
- **Linktree click endpoint abuse** - The public endpoint accepted any URL, label and type, with no limit
  - Only links actually shown on the linktree page are accepted; type and label come from the server
  - Rate limited to 60 clicks per IP per 10 minutes (filter `ane_linktree_rate_limit`)
  - File: [inc/admin/linktree-core.php](inc/admin/linktree-core.php)
- **Post views endpoint** - Only counts published, publicly viewable posts (previously wrote meta for any ID) ([inc/elemenane.php](inc/elemenane.php))
- **JSON-LD schema** - Encoded with `JSON_HEX_TAG` so data can never close the `<script>` tag ([inc/seo.php](inc/seo.php))
- **Posts widget** - Settings validated against allowed values; title and form values escaped ([inc/widget.php](inc/widget.php))
- **Direct file access** - Added `ABSPATH` guard to `inc/admin.php` and `inc/admin/user.php`

### Changed
- **Asset cache busting** - Only the WordPress core version is stripped from asset URLs; theme asset versions are kept so updated files reach browsers ([functions.php](functions.php))
- **Linktree data retention** - Click records older than 12 months are deleted daily (filter `ane_linktree_retention_months`, 0 = keep forever)
- Fixed branch titles showing HTML entities (e.g. `&#8211;`) in the map tooltip and cards
- Fixed PHP 8.4 deprecation notice in `ane_post_pagination()`

## [1.1.4] - 2026-09-24
### Fixed
- **Fatal error (HTTP 500) when ACF Pro is not active** - Activating the theme on a site without ACF Pro crashed every page, including wp-admin (`Call to undefined function get_field()`)
  - Theme now runs in safe mode when ACF is missing: modules in `inc/` are not loaded
  - wp-admin shows a notice with a link to upload ACF Pro
  - Frontend shows a "site is being set up" page (HTTP 503)
  - Theme activation setup (Elementor defaults) is deferred until ACF is active
  - Added Indonesian translations for the new messages
  - File: [functions.php](functions.php)

## [1.1.3] - 2026-01-02
### Fixed
- **Fixed Phone Number** in contact page
  - displaying phone in contact page

## [1.1.2] - 2026-01-02
### Added
- **Add Fax Number** - added fax number in acf
  - added fax number to footer
  - added fax number in to contact page
  - fixed contact form input css in contact page

## [1.1.1] - 2026-01-02
### Fixed
- **Theme Updater Cache Issue** - Fixed version display not updating after theme upgrade

## [1.1.0] - 2026-01-02

### Fixed
- **Theme Updater Cache Issue** - Fixed version display not updating after theme upgrade
  - Added `clear_theme_cache_after_upgrade()` method to auto-clear WordPress theme cache
  - Clears `wp_cache`, calls `wp_clean_themes_cache()` after successful update
  - Deletes update transients to force fresh version check
  - Previously: Version stayed cached in database even after successful ZIP install
  - Now: Version updates immediately after upgrade completes
  - File: [inc/updater.php](inc/updater.php:291-311)

## [1.0.9] - 2026-01-02

### Changed
- **Product Single Page Mobile Layout** - Improved mobile responsiveness
  - `.ane-product-main` mobile styling optimization
  - Added `display: flex` with `flex-direction: column` for better content flow
  - Reduced padding to `1rem` for compact mobile view
  - Adjusted gap to `1.5rem` between elements
  - Added `margin-bottom: 30px` for proper spacing
  - File: [scss/_product.scss](scss/_product.scss:71-77)

---

## [1.0.8] - 2026-01-02

### Fixed
- **Product Branch System** - Fixed latitude/longitude data retrieval
  - **Map Integration Issue** - "Petunjuk Arah" button now displays correctly
    - Changed from non-existent `ane_branch_latitude` and `ane_branch_longitude` fields
    - Now correctly retrieves lat/lng from `ane_branch_map` ACF google_map field
    - File: [inc/product-acf.php](inc/product-acf.php:626-639)

  - **Independent Action Buttons** - WhatsApp and Map buttons now work independently
    - Previously "Petunjuk Arah" was nested inside WhatsApp conditional
    - Now each button has its own independent condition
    - WhatsApp button shows only if `branch.whatsapp` exists
    - Map button shows only if `branch.lat` and `branch.lng` exist
    - File: [js/single-product.js](js/single-product.js:284-312)

  - **Branch Archive Address Display** - Fixed HTML entities appearing in truncated addresses
    - Addresses now display correctly without `&#8211;` artifacts
    - Example: "Nagrak – Gunung Putri" displays correctly (not "Nagrak &#8211; Gunung Putri")
    - Replaced `wp_trim_words()` with custom word truncation using `explode()` and `array_slice()`
    - WordPress `wp_trim_words()` converts dashes to HTML entities, then `esc_html()` escapes them again
    - Custom truncation prevents double-encoding while maintaining security
    - Consistent with JavaScript `trimWords()` function in branch-archive.js
    - File: [archive-branch.php](archive-branch.php:99-107)

### Added
- **Branch Archive Pagination** - Client-side Load More functionality
  - **Initial Load** - Shows first 12 branches on page load
  - **Load More Button** - Appends 12 additional branches per click
    - Counter display: "(12 / 30)" updates dynamically
    - Auto-hides when all branches are loaded
    - File: [archive-branch.php](archive-branch.php:84-131)

  - **Filter Integration** - Pagination resets when filters change
    - Province/City filters reset pagination to first 12 results
    - Filtered results maintain Load More functionality
    - Map displays all markers regardless of pagination
    - File: [js/branch-archive.js](js/branch-archive.js:166-284)

  - **JavaScript Functions** - Complete pagination system
    - `renderBranchCards()` - Replace cards on filter change
    - `appendBranchCards()` - Append next batch on Load More
    - `buildBranchCard()` - Build branch card HTML
    - `loadMoreBranches()` - Load 12 more branches
    - `updateLoadMoreButton()` - Update counter and visibility
    - `trimWords()` - Truncate address text
    - `escapeHtml()` - Sanitize output

  - **Styling** - Modern Load More button design
    - Primary color with hover effects
    - Mobile-responsive (full-width on mobile)
    - Disabled state styling
    - Smooth transitions and shadows
    - File: [scss/_branch.scss](scss/_branch.scss:41-95)

### Changed
- **Branch Display** - Optimized for better performance
  - PHP renders only 12 branches initially (was: all branches)
  - JavaScript handles pagination client-side
  - No page reload required for loading more branches
  - Map markers remain visible for all branches

---

## [1.0.7] - 2025-12-31

### Added
- **Floating Customer Care Widget** - WhatsApp chat widget with multi-admin support
  - **ACF Integration** - Customizable via ACF Options Page
    - `ane_cs_aktif` - Enable/disable customer care widget
    - `ane_cs_label` - Widget button label text
    - `ane_cs_welcome` - Welcome message in chat box
    - `ane_cs` - Repeater field for multiple customer service contacts
      - `ane_nama` - Admin name
      - `ane_area` - Admin area/department
      - `ane_whatsapp` - WhatsApp number
      - `ane_image` - Admin profile photo
  - **Smart Round-Robin System** - Distributes messages evenly across admins
    - Uses localStorage to track last contacted admin
    - Automatically cycles through all available admins
    - Prevents single admin from being overwhelmed
  - **Interactive Chat Interface** - Modern floating chat widget
    - Collapsible chat box with close button
    - Custom message input field
    - Direct admin selection from list
    - Profile photos for each admin
    - Visual send button with SVG icon
  - **Dual Contact Methods**
    - Quick message input with automatic admin selection
    - Direct contact list showing all available admins with photos
  - **Mobile-Responsive Design** - Works seamlessly on all devices
  - File: [footer.php](footer.php)

- **Admin Dashboard Migration Guide** - Complete documentation for duplicating admin system
  - **Comprehensive Guide** - Step-by-step migration documentation
    - File listing with required/optional modules
    - Dependency tree visualization
    - Configuration instructions
    - Troubleshooting section with 10+ common issues
  - **File Structure Documentation**
    - 10 PHP admin modules in `inc/admin/`
    - 13 SCSS partials for styling
    - 6 JavaScript files for interactivity
  - **Migration Checklist** - Quick start guide for experienced developers
  - **Customization Points** - Text domain, function prefixes, colors, breakpoints
  - **Debug Instructions** - Enable debug mode, common error solutions
  - **Feature Coverage**
    - Icon-only sidebar menu with tooltips
    - Mobile bottom navigation (≤782px)
    - Custom dashboard with Chart.js analytics
    - User profile enhancements
    - Design token system
  - File: [ADMIN-DASHBOARD-GUIDE.md](ADMIN-DASHBOARD-GUIDE.md)

### Fixed
- **Mobile Admin Dashboard Layout** - Fixed height and tooltip issues
  - Fixed unlimited height issue on mobile admin dashboard
  - Added `max-height: 100vh` to `#wpwrap` to prevent infinite scrolling
  - Added `overflow-y: auto` to ensure proper content scrolling
  - Hidden desktop tooltips (`.ane-tooltip`, `.ane-tooltip-arrow`) on mobile devices
  - Mobile admin now uses bottom navigation menu exclusively
  - Improved mobile admin UX with cleaner interface
  - File: [scss/_admin-footer-mobile-menu.scss](scss/_admin-footer-mobile-menu.scss)

---

## [1.0.6] - 2025-12-31

### Added
- **Linktree Analytics** - Complete analytics system for linktree pages
  - **Database Table** - Custom table `{prefix}ane_linktree_clicks` with auto-migration system
    - Tracks link type, label, URL, timestamp, referrer, platform, device, user agent
    - Supports custom database prefixes via `$wpdb->prefix`
    - Database versioning system for safe schema updates
    - Auto-migration on admin load and theme activation
    - Uses WordPress `dbDelta()` for safe table creation/updates
    - File: [inc/linktree-analytics.php](inc/linktree-analytics.php)
  - **Click Tracking** - AJAX-based tracking for all linktree links
    - Tracks custom links (WhatsApp, Website, TikTok, Telegram)
    - Tracks social media links (Facebook, Instagram, Twitter, YouTube, etc.)
    - Silent tracking (no user interruption)
    - Nonce-based security
    - Files: [inc/linktree-analytics.php](inc/linktree-analytics.php), [js/linktree-tracking.js](js/linktree-tracking.js)
  - **Referrer Detection** - Identifies traffic sources
    - Detects 15+ platforms: Facebook, Instagram, TikTok, Twitter, YouTube, LinkedIn, Telegram, Threads, WhatsApp, Google, Bing, Yahoo, DuckDuckGo
    - Fallback to domain parsing for unknown referrers
    - "Direct" classification for no-referrer traffic
    - Function: `ane_detect_referrer_platform()` in [inc/linktree-analytics.php](inc/linktree-analytics.php)
  - **Device Detection** - Mobile/Tablet/Desktop classification
    - Uses Mobile_Detect library if available
    - Fallback to regex-based detection
    - User agent analysis for accurate categorization
    - Function: `ane_detect_device_type()` in [inc/linktree-analytics.php](inc/linktree-analytics.php)
  - **Admin Dashboard** - Professional analytics interface
    - Submenu under "Elemen Ane" → "Linktree Analytics"
    - **Stats Cards**: Total Clicks, Clicks This Month, Unique Links
    - **Chart**: Clicks Last 12 Months (line chart with Chart.js)
    - **3-Column Layout**: Device Breakdown (doughnut chart), Top Links (table), Top Traffic Sources (table with percentage)
    - Consistent design with main dashboard (uses `ane-dashboard-grid`, `postbox`, etc.)
    - Real-time data from database
    - Files: [inc/admin/linktree-analytics.php](inc/admin/linktree-analytics.php), [js/admin-linktree-analytics.js](js/admin-linktree-analytics.js)
  - **Database Migration System** - Safe schema updates for future versions
    - Version tracking in `wp_options` table (`ane_linktree_db_version`)
    - Incremental migration support (1.0 → 1.1 → 1.2)
    - Lightweight version check on every admin page load
    - Example migration structure for future updates
    - Functions: `ane_get_linktree_db_version()`, `ane_update_linktree_db_version()`, `ane_check_linktree_db_migration()`
    - File: [inc/linktree-analytics.php](inc/linktree-analytics.php)

- **Social Media Platform Expansion** - Added 3 new platforms
  - **TikTok Support** - Added to linktree and general social media templates
    - ACF field: `ane_tiktok` (Option Page)
    - Icon background: `#000000` (black)
    - Files: [tp/content-linktree.php](tp/content-linktree.php), [tp/content-sosmed.php](tp/content-sosmed.php), [scss/_global.scss](scss/_global.scss), [scss/_linktree.scss](scss/_linktree.scss)
  - **Telegram Support** - Full integration
    - ACF field: `ane_telegram` (Option Page)
    - Icon background: `#0088cc` (telegram blue)
    - Files: [tp/content-linktree.php](tp/content-linktree.php), [tp/content-sosmed.php](tp/content-sosmed.php), [scss/_global.scss](scss/_global.scss), [scss/_linktree.scss](scss/_linktree.scss)
  - **Threads Support** - Meta's new platform
    - ACF field: `ane_threads` (Option Page)
    - SVG mask icon technique for scalability
    - Icon background: `#000000` (black)
    - Files: [tp/content-linktree.php](tp/content-linktree.php), [tp/content-sosmed.php](tp/content-sosmed.php), [scss/_global.scss](scss/_global.scss), [scss/_linktree.scss](scss/_linktree.scss)
  - **LinkedIn Enhancement** - Added SVG mask icon
    - Replaced font icon with SVG mask for better rendering
    - Icon background: `#0077b5` (LinkedIn blue)
    - Files: [scss/_linktree.scss](scss/_linktree.scss)

- **Product Schema.org Markup** - SEO enhancement for Product CPT
  - Added complete Product schema with pricing, availability, brand, SKU
  - Supports regular price and sale price with automatic price selection
  - Stock status mapped to schema.org availability (InStock/OutOfStock)
  - Brand information from company settings
  - SKU, MPN, and GTIN fields for better product identification
  - Price valid until date set to 1 year from current date
  - Seller organization information
  - Integrated into main SEO system via `ane_output_product_schema()`
  - File: [inc/seo.php](inc/seo.php)

### Fixed
- **Linktree Social Media Function** - Fixed incorrect ACF field access pattern
  - Removed nested `have_rows()` loop for non-repeater fields
  - Changed from `get_sub_field()` to direct `get_field()` calls
  - Removed hardcoded URL prefixes (e.g., `https://wa.me/`, `https://twitter.com/`)
  - ACF fields now contain full URLs as user input
  - Social media links now display correctly on linktree pages
  - File: [tp/content-linktree.php](tp/content-linktree.php)

### Changed
- **Linktree Analytics Chart Period** - Changed from 7 days to 12 months
  - Chart now shows monthly data instead of daily
  - Label format: "Jan 2025", "Feb 2025", etc.
  - Better long-term trend visualization
  - Query optimized for monthly aggregation
  - Function: `ane_get_linktree_analytics_data()` in [inc/admin/linktree-analytics.php](inc/admin/linktree-analytics.php)

- **Product Template Layout** - Improved metadata presentation
  - Stock status moved from standalone block to inline with SKU
  - SKU and stock status now displayed side-by-side in single meta line
  - More compact and cleaner product information layout
  - Conditional display: only shows meta section if SKU or stock status exists
  - File: [single-product.php](single-product.php)

### Removed
- **Hardcoded Product Reviews** - Removed placeholder review elements
  - Removed fake 4-star rating display (★★★★☆)
  - Removed hardcoded "84 Reviews" text
  - Cleaner product presentation without misleading review data
  - File: [single-product.php](single-product.php)

---

## [1.0.5] - 2025-12-30

### Fixed
- **Dashboard View Count Accuracy** - Fixed incorrect visitor statistics
  - All view count queries now use correct meta key `musi_views`
  - Previously dashboard used inconsistent meta keys (`ane_views`, `post_views_count`)
  - Fixed in dashboard statistics, popular posts list, author stats, and monthly views chart
  - Fixed widget "Popular Post" to use correct meta key
  - Files: [inc/admin/dashboard.php](inc/admin/dashboard.php), [inc/widget.php](inc/widget.php)
- **User Profile View Count Accuracy** - Fixed incorrect view statistics on user profile page
  - User profile total views now use correct meta key `musi_views`
  - User profile monthly views chart now use correct meta key `musi_views`
  - Previously user profile used wrong meta key `ane_views`
  - Fixed at `/wp-admin/profile.php` and `/wp-admin/user-edit.php`
  - File: [inc/admin/user.php](inc/admin/user.php)
- **Dashboard Growth Percentage Display** - Fixed visitor growth percentage showing correctly
  - Now displays dynamic sign: `+` for positive growth, `-` for negative, `+0.0%` for no change
  - CSS class changes dynamically: `--up` (green), `--down` (red), `--neutral` (gray)
  - Uses `abs()` to prevent double negative signs (e.g., `--5%` instead of `-5%`)
  - Previously always showed `+` sign regardless of actual growth direction
  - File: [inc/admin/dashboard.php](inc/admin/dashboard.php)
- **Dashboard Chart Tooltips** - Fixed chart tooltips not displaying
  - Added missing `body` color definition (`#3c434a`) to chart colors
  - Tooltips now show correctly when hovering over chart data points
  - Displays "Published Posts" and "Total Views" with formatted numbers
  - Previously tooltips were invisible due to undefined color variable
  - File: [inc/admin/dashboard.php](inc/admin/dashboard.php)
- **Dashboard Chart Colors** - Improved chart line visibility with distinct colors
  - Published Posts line: Red (`#dc3545`) - matches "Published Posts" legend
  - Total Views line: Orange (`#ff6b35`) - matches "Total Views" legend
  - Lines now clearly distinguishable with high contrast colors
  - Previously both lines used dark gray colors that were hard to differentiate
  - File: [inc/admin/dashboard.php](inc/admin/dashboard.php)
- **Dashboard Chart Axis Labels** - Fixed chart axis labels for better readability
  - X-axis labels (months) now use dark color (`#3c434a`) instead of orange
  - Y-axis labels (numbers) now use dark color (`#3c434a`) instead of orange
  - Improved visibility and contrast while keeping chart lines red and orange
  - Previously axis labels used orange color which reduced readability
  - File: [js/admin-dashboard.js](js/admin-dashboard.js)

---

## [1.0.4] - 2025-12-30

### Added
- **Company Information Helper Functions** - Centralized ACF data access
  - `ane_get_company_name()` - Get company name from ACF (ane_com_nama)
  - `ane_get_company_description()` - Get company description (ane_com_des)
  - `ane_get_company_url()` - Get company URL (ane_com_link)
  - `ane_get_company_address()` - Get company address (ane_com_alamat)
  - `ane_get_company_phone()` - Get company phone (ane_telepon)
  - `ane_get_company_mobile()` - Get company mobile (ane_kontak->ane_handphone)
  - `ane_get_company_email()` - Get company email (ane_kontak->ane_email)
  - `ane_get_company_website()` - Get company website (ane_kontak->ane_website)
  - `ane_get_company_location()` - Get Google Maps data (ane_gmap)
  - `ane_get_company_logo($size)` - Get company logo URL
  - `ane_get_company_info()` - Get all company info as array
  - All functions have smart fallbacks to WordPress defaults
  - File: [inc/seo.php](inc/seo.php)

- **Premium SEO Features** - Comprehensive SEO enhancement module
  - **NewsArticle Schema (JSON-LD)** - Rich snippets for Google News
    - Complete structured data with headline, description, author, publisher
    - Uses company info from ACF options (name, URL, logo)
    - Image metadata with dimensions for rich results
    - Article section (category) and keywords (tags)
    - Published and modified dates for freshness signals
  - **CollectionPage Schema (JSON-LD)** - Archive pages optimization
    - Automatic schema for category, tag, author, date archives, and homepage
    - ItemList with all articles on current page as NewsArticle items
    - Each item includes position, headline, dates, author, image
    - Publisher info from ACF company data
    - Optimized for Google News article discovery
  - **Dublin Core Metadata** - Academic/news citations and AI crawler optimization
    - DC.title, DC.creator, DC.date, DC.description
    - DC.identifier (permalink), DC.publisher (from ACF company name), DC.subject (category)
    - Optimized for ChatGPT, Claude, Perplexity citations
  - **Citation Metadata** - Proper attribution in AI models
    - citation_title, citation_author, citation_publication_date
    - citation_journal_title (from ACF company name), citation_public_url
    - Support for citation_pdf_url (custom field)
  - **Enhanced Open Graph Tags** - Facebook, LinkedIn optimization
    - Intelligent fallback: uses Yoast if active, otherwise theme provides
    - Article-specific tags: published_time, modified_time, author, section
    - Tag meta for each post tag
    - Archive support (category, tag, author pages)
  - **Twitter Card Tags** - Twitter sharing optimization
    - summary_large_image card type
    - Intelligent fallback: uses Yoast if active, otherwise theme provides
    - Featured image or logo fallback
  - **Enhanced Robots Meta** - Max snippet and preview settings
    - index, follow, max-snippet:-1 (unlimited)
    - max-image-preview:large, max-video-preview:-1
  - **Enhanced RSS Feed** - Full content with featured images
    - Featured image embedded in RSS
    - Full post content instead of excerpt
  - **Page Schema Support** - Advanced schema.org types for pages
    - ACF select field for choosing schema type per page
    - **8 schema types supported**:
      - `WebPage` (default) - Standard pages
      - `AboutPage` - About/profile pages with organization info
      - `ContactPage` - Contact pages with full contact details (phone, email, address)
      - `FAQPage` - FAQ pages (ready for Q&A structured data)
      - `ProfilePage` - Organization/company profile pages
      - `CollectionPage` - Collection/grouped content pages
      - `ItemPage` - Individual item pages
      - `SearchResultsPage` - Search results pages
    - All types include company data from ACF (name, logo, URL)
    - ContactPage schema automatically includes company contact info (phone, mobile, email, address)
    - AboutPage schema includes organization description
    - ACF field appears in sidebar on page edit screen
    - Smart fallback: defaults to WebPage if no selection
    - Function: `ane_output_page_schema()` in [inc/seo.php](inc/seo.php)
    - ACF field group: `ane_register_page_schema_field()` in [inc/acf.php](inc/acf.php)
  - **Google Sitelinks Optimization** - Enhanced structured data for Google enhanced sitelinks
    - **Breadcrumb Schema (BreadcrumbList)** - Shows hierarchy in Google search
      - Automatic breadcrumb generation for all pages
      - Home → Category → Post structure
      - Supports posts, pages, categories, tags, author archives
      - Critical for Google Sitelinks appearance
      - Function: `ane_output_breadcrumb_schema()` in [inc/seo.php](inc/seo.php)
    - **WebSite Schema with SearchAction** - Enables Google Search Box in SERP
      - Site-wide search functionality in Google results
      - Search box appears like NU Online screenshot
      - Uses WordPress native search (?s=query)
      - Only outputs on homepage
      - Function: `ane_output_website_schema()` in [inc/seo.php](inc/seo.php)
    - **Navigation Schema (SiteNavigationElement)** - Main menu structure
      - Top-level menu items marked for Google
      - Helps Google identify important pages
      - Auto-reads from 'menuutama' menu location
      - Outputs in [tp/header-asli.php](tp/header-asli.php)
      - Function: `ane_output_navigation_schema()` in [inc/seo.php](inc/seo.php)
    - **How to get sitelinks like NU Online**:
      1. Keep consistent site structure (clear categories/pages)
      2. Build internal linking between important pages
      3. Set up main navigation menu properly
      4. Wait 2-4 weeks for Google to crawl and index
      5. Sitelinks appear automatically when Google trusts your site structure
    - **Note**: Sitelinks cannot be forced - Google decides based on site authority, structure, and user behavior
  - **Yoast SEO Compatibility** - Zero conflicts with Yoast SEO
    - Detects Yoast Open Graph and Twitter settings
    - Only outputs tags when Yoast is disabled or not handling
    - Skips robots meta if Yoast is active (prevents duplication)
    - Adds premium features (NewsArticle, Dublin Core, Citation, Page Schema, Sitelinks) that Yoast Free doesn't provide
    - **Verified working**: NewsArticle schema + Dublin Core + Citation metadata appear alongside Yoast
  - File: [inc/seo.php](inc/seo.php), [inc/acf.php](inc/acf.php), [tp/header-asli.php](tp/header-asli.php)
- **SEO Action Plan Dashboard** - Interactive 30-day SEO guide for users
  - Added comprehensive SEO checklist in admin page `/wp-admin/admin.php?page=ane-seo-news`
  - **6-step action plan** with daily tasks for 30 days
  - Step 1-3: Google Search Console setup and sitemap submission
  - Step 4-7: Structured data validation with Rich Results Test
  - Step 8-14: Website structure optimization (categories, menus, important pages)
  - Step 15-21: Internal linking strategy and anchor text optimization
  - Step 22-28: Content quality guidelines and publishing consistency
  - Step 29-30: Performance monitoring and optimization
  - **Bonus section**: Google Sitelinks explanation and tips
  - **Timeline expectations**: Realistic 2-6 month timeline for SEO results
  - **Tools dashboard**: Quick access to Search Console, Rich Results Test, PageSpeed Insights
  - **Warning notices**: Black-hat SEO risks and best practices
  - Written in Indonesian for better user understanding
  - File: [inc/admin.php](inc/admin.php)

### Fixed
- **SEO & News Admin Page** - Fixed fatal error on admin page
  - Added missing `ane_get_news_sitemap_url()` function in [inc/admin.php](inc/admin.php)
  - Function checks for Yoast SEO News sitemap, fallback to WordPress core sitemap
  - Admin page now loads without errors at `/wp-admin/admin.php?page=ane-seo-news`
  - File: [inc/admin.php](inc/admin.php)

### Changed
- **Google Maps JavaScript** - Removed debugging code
  - Cleaned up console.log debugging statements from [js/gmap.js](js/gmap.js)
  - Kept essential error handler for production use
  - Production-ready code without debugging clutter
  - File: [js/gmap.js](js/gmap.js)

---

## [1.0.3] - 2025-12-29

### Added
- **Product Category Display** - Added category above product title
  - Category appears above the product title on single product pages
  - Uses existing `.ane-product-category` styling for consistency
  - Links to category archive page
  - File: [single-product.php](single-product.php)

- **Product Tags Display** - Product tags below content with hashtag styling
  - Tags displayed below product content with proper spacing (2rem margin-top)
  - Each tag prefixed with hashtag (#) using CSS `::before` pseudo-element
  - Border box styling (1.5px solid border) with transparent background
  - Hover effect changes to primary color background
  - File: [single-product.php](single-product.php), [scss/_product.scss](scss/_product.scss)

- **Product Card Hover Animation** - Interactive zoom and rotate effect
  - Hover animation with `translateY(-5px) scale(1.03) rotate(1deg)`
  - Creates "approaching" visual effect on product cards
  - Smooth 0.3s transition for better user experience
  - File: [scss/_product.scss](scss/_product.scss)

### Changed
- **Product Gallery Main Image** - Changed to use square image size
  - Updated main product image from 'large' (1000×563) to 'kotak' (394×394)
  - Ensures consistent square display in product gallery
  - File: [single-product.php](single-product.php)

- **Product Gallery Container** - Added square aspect ratio styling
  - Added `aspect-ratio: 1/1` to force square container
  - Added `object-fit: cover` for proper image cropping
  - Container uses flexbox for centering
  - File: [scss/_product.scss](scss/_product.scss)

- **Archive Product Grid Layout** - Increased to 4 columns on desktop
  - Changed grid from 3 columns to 4 columns on desktop
  - Responsive: 2 columns on mobile, 4 columns on desktop
  - Updated Bootstrap classes from `col-6 col-md-4 col-lg-3` to `col-lg-4 col-md-3 col-6`
  - File: [tp/content-product.php](tp/content-product.php)

- **Product Card Styling** - Removed default box-shadow
  - Removed `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1)` from default state
  - Box-shadow now only appears on hover for cleaner look
  - File: [scss/_product.scss](scss/_product.scss)

- **Category-Title Spacing** - Optimized spacing for better visual hierarchy
  - Reduced gap between category and product title
  - Added negative margin (`margin-bottom: -1rem`) to category
  - Reduces default 1.5rem gap to 0.5rem for better visual grouping
  - File: [scss/_product.scss](scss/_product.scss)

- **Author Box Display** - Excluded from product post type
  - Author box no longer displays on single product pages
  - Added post type check in `ane_author_info_box()` function
  - Author box still appears on regular posts and other content types
  - File: [inc/elemenane.php](inc/elemenane.php)

- **Related Products Display** - Refactored to use template parts
  - Changed from custom loop to WP_Query with proper template part
  - Now uses `get_template_part('tp/content', 'product')` for consistency
  - Ensures images and styling match archive display
  - Uses 'kotak' image size (394×394) for square thumbnails
  - File: [single-product.php](single-product.php)

- **Translation Loading** - Force load mechanism
  - Changed from `load_theme_textdomain()` to `load_textdomain()`
  - Uses `determine_locale()` to bypass WordPress translation cache
  - Prevents cached translations from blocking new translations
  - File: [functions.php](functions.php)

### Fixed
- **Google Maps Display** - Fixed conflict preventing maps from rendering
  - Resolved duplicate Google Maps initialization in `newsane.js` and `gmap.js`
  - Disabled conflicting code in `newsane.js` to prevent double rendering
  - Fixed Google Maps API loading with proper `callback=Function.prototype`
  - Removed beta version API (`v=beta`) for compatibility with older map code
  - Updated script dependency: `gmap.js` now depends on `googlemap` for proper load order
  - Fixed CSS for map container: added `position: relative`, `overflow: hidden`
  - Fixed image max-width issue that prevented tiles from displaying
  - Map now renders correctly on contact page with custom styled map
  - Files: [inc/acf.php](inc/acf.php), [js/newsane.js](js/newsane.js), [js/gmap.js](js/gmap.js), [scss/_global.scss](scss/_global.scss)

---

## [1.0.2] - 2025-12-29

### Changed
- **Header Redesign** - Restructured desktop header layout
  - Changed from 2-column (logo + banner) to 3-column layout (logo + menu + actions)
  - Logo now fixed width (max 200px) on left
  - Main menu centered in header with flexbox
  - Action buttons and icons grouped on right side
  - Maintained original class names for compatibility (`.ane-isi`, `.ane-kanan`)
  - File: [tp/header-asli.php](tp/header-asli.php)

- **Active Menu Styling** - Modern gradient and border design
  - Replaced solid background with gradient (white to terang color)
  - Added 3px bottom border with primary color (warna utama)
  - Active menu link color changed to primary color
  - File: [scss/_header.scss](scss/_header.scss)

### Added
- **ACF Header Fields** - Dynamic header customization
  - Get In Touch Button Link - ACF Link field for page selection or custom URL
  - Show Product/Cart Link - True/False toggle to show/hide product cart icon
  - Phone Label - Customizable text field (default: "Sales Team")
  - All fields accessible in **General Setting** admin page
  - File: [inc/header-acf.php](inc/header-acf.php)

- **Header Action Elements** - New interactive components
  - Primary CTA button with ACF customization support
  - SVG cart icon with mask technique (inherits currentColor)
  - Conditional product archive link (only shows if enabled)
  - Phone number display with customizable label
  - Search button icon
  - Files: [tp/header-asli.php](tp/header-asli.php), [scss/_header.scss](scss/_header.scss)

### Fixed
- **Submenu Hover Functionality** - Resolved click requirement issue
  - Enhanced CSS specificity with child selectors (`> li`, `> a`)
  - Added both `visibility` and `opacity` for smooth transitions
  - Submenu now appears on hover without requiring click
  - Fixed 3D rotateX animation with proper transform-origin
  - Prevented navigation to parent menu when hovering for submenu
  - File: [scss/_header.scss](scss/_header.scss)

- **Text Domain Loading** - Fixed "text domain loaded too early" error
  - Wrapped ACF field registration in `acf/init` hook
  - Ensures translations load at proper time (WordPress 6.7+ compatibility)
  - Prevents `_load_textdomain_just_in_time` notice
  - File: [inc/header-acf.php](inc/header-acf.php)

---

## [1.0.1] - 2025-12-28

### Fixed
- **Bug Fix: ane_get_embedded_media()** - Fixed "Undefined offset: 0" error
  - Added validation check for empty embed array in `inc/elemenane.php:173`
  - Prevents error when post has no embedded media
  - Returns empty string instead of accessing undefined array index

- **Update Check Integration** - Dual URL parameter support
  - Support both `ane_force_check` and `elemenane_force_check` parameters
  - Support both `ane_debug` and `elemenane_debug` parameters
  - Ensures compatibility with admin dashboard links in `inc/updater.php`

### Changed
- **page-home.php Template** - Restored "Landing Page" template name
  - Template can be selected from Page Attributes dropdown
  - Not used as default homepage (controlled via Settings → Reading)
  - Mobile/desktop detection with separate template parts

---

## [1.0.0] - 2025-12-28

### Added

#### Core Features
- **Theme Auto-Updater** - GitHub-based automatic update system
  - Integrated with WordPress native update mechanism
  - 24-hour update check caching
  - Manual force update check via URL parameter
  - Debug mode for troubleshooting
  - File: `inc/updater.php`

- **GitHub Actions CI/CD** - Automated release workflow
  - Automatic SCSS compilation on release
  - Distribution package creation
  - ZIP file generation and upload to GitHub releases
  - File: `.github/workflows/release.yml`

#### Product Custom Post Type
- **Product Archive** - Grid layout with responsive design (2-4 columns)
  - Product cards with image, title, price, discount badge
  - Stock status indicator (In Stock/Out of Stock)
  - "New" badge for recent products
  - Marketplace links integration
  - File: `archive-product.php`, `tp/content-product.php`

- **Single Product Page** - Comprehensive product display
  - Image gallery with lightbox
  - Product information (price, discount, stock)
  - WhatsApp inquiry integration
  - Branch finder modal with search functionality
  - Marketplace links (Tokopedia, Shopee, Lazada, Bukalapak, Blibli)
  - Tabbed content (Description, Specifications, Features)
  - File: `single-product.php`

- **Product ACF Fields** - Complete product data management
  - Gallery images
  - Pricing (regular price, sale price, discount percentage)
  - Stock management
  - Branch associations
  - Marketplace URLs
  - Product specifications (repeater field)
  - Product features (repeater field)
  - WhatsApp integration
  - File: `inc/product-acf.php`

- **Product JavaScript** - Interactive features
  - Branch search and filter
  - Modal window for branch selection
  - WhatsApp message templates
  - File: `js/single-product.js`

- **Product Styles** - Custom SCSS styling
  - Responsive product cards
  - Gallery lightbox styles
  - Branch modal styles
  - Mobile-optimized layout
  - File: `scss/_product.scss`

#### Branch Custom Post Type
- Branch locations management
- ACF fields for branch data (address, phone, WhatsApp, email, maps)
- Integration with product branch finder
- File: `inc/custompost.php`, `inc/branch-acf.php`

#### Elementor Integration
- **Color Sync System** - Dynamic color variables from Elementor Kit
  - Front-end color synchronization (`wp_head`)
  - Admin dashboard color synchronization (`admin_head`)
  - RGB value generation for transparency support
  - Custom color mapping (primary→utama, secondary→utama-2, accent→alternatif)
  - File: `inc/elementor.php`

- **Typography Sync** - Font settings from Elementor Kit
  - Font family, weight, size synchronization
  - Line height and letter spacing
  - Google Fonts auto-loading on front-end
  - File: `inc/elementor.php`

- **Gutenberg Integration** - Elementor colors in block editor
  - Color palette sync to Gutenberg
  - File: `inc/elementor.php`

#### Translation System
- **Multi-language Support** - Full translation infrastructure
  - Indonesian (id_ID) translation files
  - PO/MO file structure
  - Custom PHP compiler for PO to MO conversion
  - JavaScript localization via `wp_localize_script()`
  - Text domain: `elemenane`
  - Files: `languages/elemenane-id_ID.po`, `languages/elemenane-id_ID.mo`, `languages/compile-mo.php`

- **Translation Coverage**
  - Product archive and taxonomy strings
  - Single product page strings
  - Product card loop strings
  - JavaScript messages (branch search, WhatsApp templates)
  - ACF field labels and instructions
  - Admin notices and messages

#### Admin Dashboard
- **Custom Dashboard Integration**
  - Analytics dashboard with post stats
  - Custom admin bar modifications
  - Mobile admin navigation
  - Design token system for consistent UI
  - Files: `inc/admin/` directory

- **Admin Color Tokens** - Dynamic color system
  - Fallback colors in SCSS
  - Elementor Kit color override
  - Utility colors (light, dark, white, black)
  - File: `scss/_tokens.scss`

#### CDN & Performance
- **CDN Support** - External asset delivery
  - Configurable CDN URL via constants
  - Local fallback during development
  - Version-based cache busting
  - File: `inc/newsane.php`

- **Asset Optimization**
  - Minified CSS and JavaScript
  - Conditional script loading
  - WordPress Heartbeat disabled
  - Version strings removed from assets
  - File: `inc/newsane.php`

#### Mobile Detection
- **Server-side Device Detection**
  - Mobile vs Desktop template routing
  - Device-specific widget rendering
  - Responsive navigation menus
  - File: `inc/Mobile_Detect.php`

### Changed
- **Theme Description** - Updated style.css header with comprehensive description
- **Version Format** - Standardized to semantic versioning (MAJOR.MINOR.PATCH)

### Technical Details
- **WordPress**: Requires 5.0+, tested up to 6.4
- **PHP**: Requires 7.4+
- **Text Domain**: elemenane
- **GitHub Repository**: webaneid/elemenane
- **License**: GPL v2+ (Licensed for Webane clients only)

---

## Development Notes

### Release Process
1. Update version in `style.css`
2. Update `CHANGELOG.md` with changes
3. Commit changes: `git commit -m "Bump version to x.x.x"`
4. Create tag: `git tag vx.x.x`
5. Push: `git push origin main && git push origin vx.x.x`
6. GitHub Actions will automatically create release

### Update Check URLs
- Force update check: `?elemenane_force_check`
- Debug update data: `?elemenane_debug`

### Contributors
- Webane Squad (2019-2025)
- Built with ❤️ for Webane Indonesia clients
