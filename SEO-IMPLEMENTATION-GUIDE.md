# SEO System Implementation Guide

Guide untuk implementasi SEO system berdasarkan Elemen Ane theme. File `inc/seo.php` bersifat **highly reusable** dan bisa diadaptasi ke theme baru dengan customization minimal.

## Overview

**File:** `inc/seo.php` (1157 lines)

**Tujuan:**
- Enhance Yoast SEO Free dengan fitur premium
- Schema.org markup lengkap (NewsArticle, Product, WebPage, Organization, dll)
- Dublin Core & Citation metadata
- Enhanced Open Graph & Twitter Cards
- Google News compatible
- AI crawler optimization

**Strategi:** Complement, not replace Yoast SEO Free

---

## File Structure Analysis

### Section 1: Helper Functions (Lines 1-147)

**Company Information Helpers** - 13 functions untuk retrieve ACF data:

```php
function ane_get_company_name() : string
function ane_get_company_description() : string
function ane_get_company_url() : string
function ane_get_company_address() : string
function ane_get_company_phone() : string
function ane_get_company_mobile() : string
function ane_get_company_email() : string
function ane_get_company_website() : string
function ane_get_company_location()
function ane_get_company_logo( $size = 'full' ) : string
function ane_get_company_info() : array
```

**Reusability:** ✅ **100% reusable**
- Hanya perlu sesuaikan ACF field names
- Pattern: `get_field( 'ane_xxx', 'option' ) ?: fallback`
- Fallback ke WordPress defaults (get_bloginfo, home_url, etc.)

**Customization Required:**
1. Replace ACF field names sesuai theme baru
2. Atau buat mapping function untuk compatibility

### Section 2: Main Orchestrator (Lines 158-207)

**Function:** `ane_add_seo_metadata()`
**Hook:** `wp_head` (priority 5)

**What it does:**
- Detects post type & template
- Calls appropriate schema functions
- Outputs Dublin Core metadata
- Outputs Citation metadata
- Conditionally outputs OG/Twitter if Yoast disabled

**Reusability:** ✅ **90% reusable**
- Logic sama untuk semua theme
- Hanya perlu adjust post types (add/remove)
- Template detection bisa disesuaikan

**Structure:**
```php
function ane_add_seo_metadata() {
    // Schema for different content types
    if ( is_singular('post') ) {
        ane_output_newsarticle_schema();
    } elseif ( is_front_page() || is_home() ) {
        ane_output_collectionpage_schema();
    } elseif ( is_singular('page') ) {
        ane_output_page_schema();
    } elseif ( is_singular('product') ) {
        ane_output_product_schema();
    } elseif ( is_archive() || is_search() ) {
        ane_output_collectionpage_schema();
    }

    // Metadata
    ane_output_dublin_core_metadata();
    ane_output_citation_metadata();

    // OG & Twitter (if Yoast not handling)
    if ( ! ane_yoast_handles_opengraph() ) {
        ane_output_opengraph_tags();
    }
    if ( ! ane_yoast_handles_twitter() ) {
        ane_output_twitter_card_tags();
    }

    // Breadcrumbs & global schemas
    ane_output_breadcrumb_schema();
    ane_output_website_schema();
    ane_output_navigation_schema();
}
add_action( 'wp_head', 'ane_add_seo_metadata', 5 );
```

### Section 3: Yoast Detection (Lines 213-234)

**Functions:**
- `ane_yoast_handles_opengraph()`
- `ane_yoast_handles_twitter()`

**Purpose:** Detect if Yoast SEO Premium is active to avoid duplicate tags

**Reusability:** ✅ **100% reusable**
- No modification needed
- Universal Yoast detection

### Section 4: Schema Functions (Lines 240-1060)

**8 Schema Functions:**

1. **NewsArticle Schema** (Lines 240-340)
   - For blog posts
   - Google News compatible
   - Publisher, Author, Image, Date

2. **CollectionPage Schema** (Lines 347-468)
   - For archives, search, homepage
   - List of articles

3. **WebPage Schema** (Lines 474-641)
   - For regular pages
   - Contact, About, etc.

4. **Product Schema** (Lines 1061-1157)
   - For products
   - Price, availability, SKU

5. **Dublin Core Metadata** (Lines 647-680)
   - Academic/research citation
   - DC.title, DC.creator, DC.date, etc.

6. **Citation Metadata** (Lines 682-708)
   - Research paper citation
   - citation_title, citation_author, etc.

7. **Breadcrumb Schema** (Lines 864-956)
   - Navigation hierarchy
   - Google Search breadcrumbs

8. **Organization Schemas** (Lines 963-1053)
   - Website schema
   - Navigation element schema
   - Global organization info

**Reusability:** ✅ **85% reusable per function**

**What needs customization:**
- ACF field names
- Post types (if different CPT)
- Company info fields
- Image handling (if custom image sizes)

**What's universal:**
- Schema structure
- Google requirements
- Fallback logic
- Error handling

### Section 5: Open Graph & Twitter (Lines 710-813)

**Functions:**
- `ane_output_opengraph_tags()`
- `ane_output_twitter_card_tags()`

**Reusability:** ✅ **95% reusable**
- Only runs if Yoast not handling
- Standard OG/Twitter tags
- Minimal customization needed

### Section 6: Utility Functions (Lines 818-857)

**Functions:**
- `ane_add_robots_meta()` - Control indexing
- `ane_enhance_rss_feed()` - Add featured image to RSS

**Reusability:** ✅ **100% reusable**
- Universal WordPress functionality
- No theme-specific code

---

## Reusability Assessment

### ✅ Can Use As-Is (No Changes)

**Functions that work in ANY theme:**
1. Yoast detection functions
2. Robots meta function
3. RSS feed enhancement
4. Breadcrumb schema (uses Yoast breadcrumbs)
5. Website schema structure
6. Navigation schema structure

**Total:** ~300 lines (25%)

### 🔧 Needs Field Mapping Only

**Functions that need ACF field name updates:**
1. All company helper functions (13 functions)
2. Dublin Core metadata
3. Citation metadata
4. Open Graph tags
5. Twitter Card tags

**How to customize:**
- Option A: Search & replace ACF field names
- Option B: Create field mapping array
- Option C: Use wrapper functions

**Total:** ~400 lines (35%)

### 🔨 Needs Post Type Adjustment

**Functions that need CPT updates:**
1. Main orchestrator (`ane_add_seo_metadata`)
2. NewsArticle schema (if not using 'post')
3. Product schema (if not using 'product')
4. CollectionPage schema (adjust archive detection)

**Total:** ~200 lines (17%)

### 🎨 Needs Template-Specific Logic

**Functions that depend on template structure:**
1. Page schema (author detection, featured image handling)
2. Product schema (pricing structure, taxonomy)

**Total:** ~250 lines (22%)

---

## Migration Strategy

### Strategy 1: Direct Copy (Fastest)

**When to use:** Theme baru dengan struktur ACF sama

**Steps:**
1. Copy `inc/seo.php` to new theme
2. Update ACF field names via search-replace
3. Adjust post type conditionals in orchestrator
4. Test schemas dengan Google Rich Results Test

**Estimated time:** 30 minutes

### Strategy 2: Field Mapping (Clean)

**When to use:** Theme dengan ACF fields berbeda

**Steps:**
1. Copy `inc/seo.php` to new theme
2. Create `inc/seo-config.php` with field mapping:

```php
<?php
/**
 * SEO Field Mapping Configuration
 */

// Map old ACF fields to new theme fields
function mytheme_seo_field_map( $field ) {
    $map = array(
        'ane_com_nama'    => 'company_name',
        'ane_com_des'     => 'company_description',
        'ane_com_link'    => 'company_url',
        'ane_com_alamat'  => 'company_address',
        'ane_telepon'     => 'phone_number',
        'ane_kontak'      => 'contact_info',
        // ... add all fields
    );

    return $map[ $field ] ?? $field;
}

// Wrapper for get_field with mapping
function mytheme_get_seo_field( $field, $context = 'option' ) {
    $mapped_field = mytheme_seo_field_map( $field );
    return get_field( $mapped_field, $context );
}
```

3. Replace all `get_field()` calls dengan `mytheme_get_seo_field()`
4. Test

**Estimated time:** 1-2 hours

### Strategy 3: Modular Rewrite (Future-proof)

**When to use:** Theme dengan struktur sangat berbeda atau ingin flexibility

**Steps:**
1. Create `inc/seo/` directory:
   ```
   inc/seo/
   ├── class-seo-helpers.php      ← Helper functions as class methods
   ├── class-schema-generator.php ← Schema generation logic
   ├── class-metadata.php         ← Dublin Core, Citation, OG, Twitter
   └── seo-init.php               ← Orchestrator
   ```

2. Convert functions to OOP classes
3. Use dependency injection for field names
4. Configuration via `inc/seo-config.php`

**Estimated time:** 3-4 hours (one-time investment)

---

## Quick Migration Checklist

### Pre-Migration Analysis

- [ ] Check what post types theme uses
- [ ] List all ACF fields related to company info
- [ ] Check if using Yoast SEO (Free/Premium)
- [ ] Verify if custom taxonomies need schema
- [ ] Check image size names used in theme

### File Preparation

- [ ] Copy `inc/seo.php` to new theme
- [ ] Backup original file
- [ ] Create test environment

### Field Customization

- [ ] Update company name field: `ane_com_nama` → `your_field`
- [ ] Update company description: `ane_com_des` → `your_field`
- [ ] Update company URL: `ane_com_link` → `your_field`
- [ ] Update address: `ane_com_alamat` → `your_field`
- [ ] Update phone: `ane_telepon` → `your_field`
- [ ] Update contact repeater: `ane_kontak` → `your_field`
- [ ] Update logo: `ane_com_logo` → `your_field`

### Post Type Customization

- [ ] Update `is_singular('post')` if using different slug
- [ ] Update `is_singular('product')` if using different CPT
- [ ] Add new CPT conditionals if needed
- [ ] Remove unused CPT schemas

### Testing

- [ ] Test on single post: NewsArticle schema
- [ ] Test on page: WebPage schema
- [ ] Test on product: Product schema (if applicable)
- [ ] Test on archive: CollectionPage schema
- [ ] Test on homepage: CollectionPage schema
- [ ] Validate with Google Rich Results Test
- [ ] Validate with Schema Markup Validator
- [ ] Check in Yoast SEO → Search Appearance → Schema

### Final Verification

- [ ] No duplicate schemas (check with view-source)
- [ ] No console errors in structured data testing tool
- [ ] Breadcrumbs work correctly
- [ ] Organization info correct
- [ ] OG tags present (if Yoast disabled)
- [ ] Twitter cards present (if Yoast disabled)

---

## Field Dependencies Reference

### Required ACF Fields (Must Have)

These fields are CRITICAL for SEO system to work:

**Company Information:**
```
ane_com_nama        → Company Name (text)
ane_com_des         → Company Description (textarea)
ane_com_link        → Company URL (url)
ane_com_logo        → Company Logo (image)
```

**Contact Information:**
```
ane_kontak (group/repeater with sub-fields)
  ├── ane_email      → Email (email)
  ├── ane_handphone  → Mobile (text)
  └── ane_website    → Website (url)

OR separate fields:
ane_telepon         → Phone (text)
ane_com_alamat      → Address (textarea)
```

**Fallbacks:** If fields missing, uses WordPress defaults:
- Company Name → `get_bloginfo('name')`
- Description → `get_bloginfo('description')`
- URL → `home_url()`
- Email → `get_bloginfo('admin_email')`

### Optional ACF Fields (Enhance SEO)

**For Products:**
```
ane_harga_normal    → Regular Price (number)
ane_harga_diskon    → Sale Price (number)
ane_stok           → Stock Status (select: yes/no)
ane_product_sku    → SKU (text)
```

**For Better Schemas:**
```
ane_author_bio     → Author Biography (textarea)
ane_social_media   → Social Media Links (repeater)
```

---

## Common Customization Patterns

### Pattern 1: Add New Post Type Schema

**Example:** Add Service CPT

```php
// In ane_add_seo_metadata() function
elseif ( is_singular('service') ) {
    ane_output_service_schema();
}

// Add new function
function ane_output_service_schema() {
    if ( ! is_singular('service') ) {
        return;
    }

    $service_id = get_the_ID();
    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'Service',
        '@id'      => get_permalink() . '#service',
        'name'     => get_the_title(),
        'description' => wp_trim_words( get_the_content(), 30 ),
        'provider' => array(
            '@type' => 'Organization',
            'name'  => ane_get_company_name(),
        ),
        // Add more fields...
    );

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}
```

### Pattern 2: Change ACF Field Names

**Option A: Search & Replace**
```bash
# In terminal
sed -i "s/ane_com_nama/company_name/g" inc/seo.php
sed -i "s/ane_com_des/company_desc/g" inc/seo.php
# ... repeat for all fields
```

**Option B: Wrapper Function**
```php
// Add this before all helper functions
function mytheme_get_field( $old_field, $context = 'option' ) {
    $field_map = array(
        'ane_com_nama' => 'company_name',
        'ane_com_des'  => 'company_description',
        // ... map all fields
    );

    $new_field = $field_map[ $old_field ] ?? $old_field;
    return get_field( $new_field, $context );
}

// Then replace all get_field() calls:
// Old: get_field( 'ane_com_nama', 'option' )
// New: mytheme_get_field( 'ane_com_nama', 'option' )
```

### Pattern 3: Remove Unused Schemas

**Example:** Theme tidak punya Product CPT

```php
// In ane_add_seo_metadata()
// Comment out or remove:
/*
elseif ( is_singular('product') ) {
    ane_output_product_schema();
}
*/

// Also delete/comment function:
/*
function ane_output_product_schema() {
    // ... entire function
}
*/
```

### Pattern 4: Add Custom Image Sizes

**If theme uses different image size names:**

```php
// In ane_output_newsarticle_schema()
// Find this line:
$image_url = get_the_post_thumbnail_url( $post_id, 'large' );

// Change to your size:
$image_url = get_the_post_thumbnail_url( $post_id, 'mytheme-featured' );
```

---

## Testing & Validation

### Tools for Validation

**1. Google Rich Results Test**
URL: https://search.google.com/test/rich-results
- Paste URL or code
- Check for errors/warnings
- Preview how it appears in search

**2. Schema Markup Validator**
URL: https://validator.schema.org/
- More detailed than Google tool
- Shows complete JSON-LD structure
- Validates against Schema.org spec

**3. Facebook Sharing Debugger**
URL: https://developers.facebook.com/tools/debug/
- Test Open Graph tags
- Preview social share appearance

**4. Twitter Card Validator**
URL: https://cards-dev.twitter.com/validator
- Test Twitter Card tags
- Preview tweet appearance

**5. Yoast SEO Schema Tab**
- WordPress Admin → SEO → Search Appearance → Schema
- Shows what Yoast generates
- Helps avoid duplicates

### Common Validation Errors

**Error 1: Missing Required Field**
```
Error: The property "author" is required for type NewsArticle
```
**Fix:** Ensure author exists and is properly formatted

**Error 2: Duplicate Schema**
```
Warning: Multiple schemas of type "Organization" found
```
**Fix:** Check if Yoast also outputs Organization schema, remove duplicate

**Error 3: Invalid Date Format**
```
Error: Invalid value for "datePublished"
```
**Fix:** Use ISO 8601 format: `gmdate('c', strtotime($date))`

**Error 4: Missing Image**
```
Warning: Recommended property "image" is missing
```
**Fix:** Add fallback image if post has no featured image

---

## Performance Considerations

### Output Optimization

**Current implementation:** Direct echo in `wp_head`
**Performance impact:** Minimal (schema generated on-the-fly)

**For high-traffic sites, consider caching:**

```php
function ane_add_seo_metadata() {
    // Cache key unique per post/page
    $cache_key = 'seo_schema_' . get_queried_object_id();
    $cached = get_transient( $cache_key );

    if ( $cached ) {
        echo $cached;
        return;
    }

    // Start output buffering
    ob_start();

    // Generate schemas
    if ( is_singular('post') ) {
        ane_output_newsarticle_schema();
    }
    // ... rest of logic

    // Get output
    $output = ob_get_clean();

    // Cache for 24 hours
    set_transient( $cache_key, $output, DAY_IN_SECONDS );

    echo $output;
}
```

**Clear cache when post updated:**
```php
function ane_clear_seo_cache( $post_id ) {
    delete_transient( 'seo_schema_' . $post_id );
}
add_action( 'save_post', 'ane_clear_seo_cache' );
```

---

## Troubleshooting

### Issue 1: Schemas Not Showing

**Check:**
```php
// View source (Ctrl+U) and search for:
<script type="application/ld+json">

// If not found, check if function is hooked:
has_action( 'wp_head', 'ane_add_seo_metadata' );
```

### Issue 2: Wrong Company Info

**Check ACF field values:**
```php
// Temporarily add to template:
echo '<pre>';
var_dump( ane_get_company_info() );
echo '</pre>';
```

### Issue 3: Duplicate Schemas

**Yoast conflict check:**
```php
// Check if Yoast outputs same schema
// Disable SEO system temporarily:
remove_action( 'wp_head', 'ane_add_seo_metadata', 5 );

// View source and check remaining schemas
```

---

## Summary: Is inc/seo.php Reusable?

**Answer: YES, highly reusable** ✅

**Reusability Score: 85%**

**What makes it reusable:**
- Modular function structure
- Clear separation of concerns
- Fallback to WordPress defaults
- Works with/without Yoast SEO
- Universal schema.org standards
- Well-documented code

**What requires customization:**
- ACF field names (10-15 minutes search-replace)
- Post type conditionals (5 minutes)
- Optional: Image sizes, taxonomies

**Recommendation:**
1. **For similar themes:** Copy directly, update field names ✅
2. **For different structures:** Use field mapping pattern ✅
3. **For maximum flexibility:** Extract to OOP classes (future project) 🔄

**Time to implement in new theme:**
- Minimum: 30 minutes (direct copy + field update)
- Maximum: 2 hours (with mapping + testing)

**ROI:** Very high - saves weeks of SEO implementation work

---

## File Location in New Theme

**Recommended structure:**
```
inc/
├── seo.php                    ← Copy from Elemen Ane
├── seo-config.php             ← Optional: field mapping
└── ... (other inc files)
```

**Auto-load via functions.php:**
Already covered by glob pattern:
```php
$all_files = glob( get_template_directory() . '/inc/*.php' );
```

No additional require needed! ✅

---

## Credits

**Original Implementation:** Elemen Ane Theme v1.0.4+
**Schema Standards:** Schema.org, Google Search Central
**Compatible With:** Yoast SEO Free/Premium, RankMath, All in One SEO
**Last Updated:** 2025-12-31 (Product schema added in v1.0.6)
