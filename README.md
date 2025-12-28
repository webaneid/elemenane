# Elemen Ane WordPress Theme

A production-ready WordPress theme built by Webane Indonesia for news/magazine sites with deep Elementor integration.

## Features

### Core Functionality
- **Deep Elementor Integration** - Color and typography sync from Elementor Kit
- **Server-side Mobile Detection** - Device-specific templates
- **CDN Support** - External asset delivery with local fallback
- **ACF-Powered Customization** - Extensive custom fields
- **Modular PHP Architecture** - Auto-loaded inc/ directory
- **Translation Ready** - Full i18n support (Indonesian included)

### Custom Post Types
- **Product CPT** - Complete e-commerce product showcase
  - Image galleries
  - Pricing and discounts
  - Stock management
  - Branch associations
  - Marketplace integrations
- **Service CPT** - Service offerings
- **Branch CPT** - Location management

### Admin Features
- Custom dashboard with analytics
- Design token system
- Mobile admin navigation
- Elementor color sync to admin

### Performance
- Minified CSS/JavaScript
- Conditional script loading
- 24-hour update check caching
- CDN asset delivery

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Recommended Plugins**:
  - Elementor (for design system integration)
  - Advanced Custom Fields Pro (for custom fields)

## Installation

### From GitHub Release (Recommended)

1. Download latest release ZIP from [Releases](https://github.com/webaneid/elemenane/releases)
2. Go to **WordPress Admin → Appearance → Themes → Add New → Upload Theme**
3. Upload the ZIP file
4. Click **Install Now** and then **Activate**

### From Source (Development)

```bash
cd wp-content/themes/
git clone https://github.com/webaneid/elemenane.git
cd elemenane
```

Compile SCSS (if you made changes):
```bash
npm install -g sass
sass scss/main.scss css/main.min.css --style=compressed
sass scss/admin.scss css/admin.min.css --style=compressed
```

## Auto-Update System

The theme includes a built-in auto-updater that checks GitHub releases.

### For Users

Updates appear in **WordPress Admin → Appearance → Themes** just like official WordPress themes.

When an update is available:
1. You'll see a notice: "Elemen Ane Theme Update Available!"
2. Click **Update now** to install the latest version
3. WordPress will automatically download and install from GitHub

### For Developers

To create a new release:

```bash
# 1. Update version in style.css
Version: 1.0.1

# 2. Document changes in CHANGELOG.md
## [1.0.1] - 2025-12-28
### Fixed
- Bug fix description

# 3. Commit changes
git add style.css CHANGELOG.md
git commit -m "Bump version to 1.0.1"

# 4. Create and push tag
git tag v1.0.1
git push origin main
git push origin v1.0.1
```

GitHub Actions will automatically:
- Compile SCSS to minified CSS
- Create a distribution ZIP (excluding dev files)
- Create a GitHub release
- Upload the ZIP as a release asset

### Manual Update Check

Force a manual update check:
```
wp-admin/themes.php?elemenane_force_check
```

Debug update data:
```
wp-admin/themes.php?elemenane_debug
```

## Development

### File Structure

```
elemenane/
├── .github/workflows/    # GitHub Actions CI/CD
├── inc/                  # Auto-loaded PHP modules
│   ├── updater.php       # GitHub update integration
│   ├── elementor.php     # Elementor color/typography sync
│   ├── newsane.php       # Core theme functions
│   ├── acf.php          # ACF configuration
│   ├── product-acf.php   # Product custom fields
│   ├── branch-acf.php    # Branch custom fields
│   ├── custompost.php    # Custom post types
│   └── admin/           # Admin customization
├── tp/                   # Template parts
├── scss/                 # SCSS source files
├── css/                  # Compiled CSS
├── js/                   # JavaScript files
├── languages/            # Translation files
├── style.css             # Theme header
├── functions.php         # Theme setup
├── CHANGELOG.md         # Version history
└── README.md            # This file
```

### SCSS Compilation

Source files in `scss/` are compiled to minified CSS:

```bash
# Main stylesheet
sass scss/main.scss css/main.min.css --style=compressed

# Admin styles
sass scss/admin.scss css/admin.min.css --style=compressed
```

**Note**: SCSS compilation happens automatically during GitHub release process.

### Translation

Edit translation files in `languages/`:

```bash
# Edit PO file
vim languages/elemenane-id_ID.po

# Compile to MO
cd languages
php compile-mo.php
```

## Configuration

### CDN Setup

Define in `wp-config.php`:

```php
// Enable CDN
define('WP_ANE_CDN', 'https://cdn.example.com/themes/elemenane');

// Or disable CDN
define('WEBANE_DISABLE_CDN', true);
```

### Elementor Integration

1. Install and activate Elementor
2. Go to **Elementor → Settings → Global Colors/Fonts**
3. Configure your colors and typography
4. Theme will automatically sync to both front-end and admin

## Support

For issues, feature requests, or questions:
- **GitHub Issues**: [https://github.com/webaneid/elemenane/issues](https://github.com/webaneid/elemenane/issues)
- **Webane Indonesia**: [https://webane.com](https://webane.com)

## License

GNU General Public License v3 or later
Licensed for Webane Indonesia clients only.

## Credits

**Developed by**: Webane Squad
**Copyright**: © 2019-2025 Webane Indonesia
**Version**: 1.0.0

---

Built with ❤️ for Webane Indonesia clients
