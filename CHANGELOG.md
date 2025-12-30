# Changelog

All notable changes to the Elemen Ane WordPress theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Planned Features
- Enhanced SEO features
- Additional custom widgets
- Performance optimization improvements
- More template variations

---

## [1.0.4] - 2025-12-30

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
