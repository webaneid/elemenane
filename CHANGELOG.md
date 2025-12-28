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
