# Translation Guide - Making Your Theme Translation-Ready

Complete guide to implement proper translation system in WordPress themes using gettext functions.

## Table of Contents
1. [Overview](#overview)
2. [Setup Translation System](#setup-translation-system)
3. [Translation Functions](#translation-functions)
4. [Best Practices](#best-practices)
5. [Common Mistakes](#common-mistakes)
6. [Generating Translation Files](#generating-translation-files)
7. [Testing Translations](#testing-translations)
8. [Troubleshooting](#troubleshooting)

---

## Overview

**What is Translation-Ready?**

A translation-ready theme allows users to translate all visible text into any language without modifying the theme code. WordPress uses the **gettext** system for internationalization (i18n).

**Key Concepts:**
- **Text Domain**: Unique identifier for your theme's translations (e.g., `elemenane`)
- **POT File**: Template file containing all translatable strings
- **PO File**: Translation file for specific language (e.g., `elemenane-id_ID.po`)
- **MO File**: Compiled binary translation file (e.g., `elemenane-id_ID.mo`)

**Benefits:**
- Multi-language support
- Professional theme standards
- WordPress.org theme directory requirement
- Better user experience for non-English users

---

## Setup Translation System

### Step 1: Declare Text Domain in style.css

Add `Text Domain` header to your theme's `style.css`:

```css
/*!
Theme Name: Your Theme Name
Text Domain: yourtheme
Version: 1.0.0
*/
```

**Rules:**
- Text domain should be **lowercase**
- Use **hyphens** for multi-word domains (e.g., `my-theme`)
- **Must match** the slug used in translation functions
- Should be **unique** to avoid conflicts

**Example from Elemen Ane:**
```css
Text Domain: elemenane
```

### Step 2: Load Translations in functions.php

Add translation loading to your theme setup function:

**Method 1: Standard (WordPress default - with cache issues)**
```php
function yourtheme_setup() {
    load_theme_textdomain( 'yourtheme', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'yourtheme_setup' );
```

**Method 2: Force Load (Recommended - bypasses cache)**

This is the method used in Elemen Ane to prevent WordPress translation caching issues:

```php
function elemenane_theme_setup() {
    /**
     * Load theme translations.
     * Using load_textdomain() instead of load_theme_textdomain() to bypass WordPress
     * translation caching mechanism that can prevent new translations from loading.
     * determine_locale() gets fresh locale without cache interference.
     */
    $locale = determine_locale();
    $mofile = get_template_directory() . "/languages/elemenane-{$locale}.mo";

    if ( file_exists( $mofile ) ) {
        load_textdomain( 'elemenane', $mofile );
    }

    // Other theme setup code...
}
add_action( 'after_setup_theme', 'elemenane_theme_setup' );
```

**Why Force Load?**

WordPress caches translations in memory and database. When you update `.mo` files, changes might not appear immediately. The force load method:
- Uses `determine_locale()` to get current locale fresh
- Directly loads specific `.mo` file without cache
- Checks file existence before loading
- Ensures translations update immediately

**When to Use Each Method:**

| Method | Use When | Pros | Cons |
|--------|----------|------|------|
| `load_theme_textdomain()` | Production, stable translations | Standard WP way, automatic fallback | Cache issues, delayed updates |
| `load_textdomain()` force | Development, frequent translation updates | No cache, instant updates | Need to specify locale manually |

### Step 3: Create Languages Folder

Create `languages/` directory in your theme root:

```bash
mkdir -p languages
```

**Folder structure:**
```
your-theme/
├── functions.php
├── style.css
└── languages/
    ├── yourtheme-id_ID.po      ← Indonesian translation
    ├── yourtheme-id_ID.mo      ← Compiled Indonesian
    ├── yourtheme-es_ES.po      ← Spanish translation
    ├── yourtheme-es_ES.mo      ← Compiled Spanish
    └── yourtheme.pot           ← Template (optional)
```

**Naming Convention:**
- Format: `{textdomain}-{locale}.{extension}`
- Examples:
  - `elemenane-id_ID.mo` - Indonesian
  - `elemenane-en_US.mo` - US English
  - `elemenane-ar.mo` - Arabic
  - `elemenane-zh_CN.mo` - Chinese Simplified

**Common Locales:**

| Language | Locale Code |
|----------|-------------|
| Indonesian | `id_ID` |
| English (US) | `en_US` |
| English (UK) | `en_GB` |
| Spanish | `es_ES` |
| French | `fr_FR` |
| German | `de_DE` |
| Italian | `it_IT` |
| Portuguese (Brazil) | `pt_BR` |
| Arabic | `ar` |
| Chinese (Simplified) | `zh_CN` |
| Japanese | `ja` |
| Korean | `ko_KR` |

---

## Translation Functions

### Basic Translation Functions

WordPress provides several functions for translation. **Choose the right function** for your use case:

### 1. `__()` - Translate String

Returns translated string. Use when you need to **store** translation in a variable.

```php
// Basic usage
$text = __( 'Hello World', 'yourtheme' );
echo $text;

// Store in variable
$title = __( 'Welcome', 'yourtheme' );
$message = sprintf( __( 'Hi %s!', 'yourtheme' ), $name );
```

**When to use:**
- Need to manipulate string before output
- Concatenating with variables
- Using with `sprintf()`
- Storing in variable for later use

### 2. `_e()` - Echo Translated String

Translates and immediately echoes. Use for **direct output**.

```php
// Basic usage
_e( 'Hello World', 'yourtheme' );

// In templates
<h1><?php _e( 'Page Title', 'yourtheme' ); ?></h1>
```

**When to use:**
- Simple text output
- No need to store in variable
- Direct echo to screen

**⚠️ Warning:** Never use `_e()` inside functions that return values:

```php
// ❌ WRONG
function get_title() {
    return _e( 'Title', 'yourtheme' ); // This echoes, doesn't return!
}

// ✅ CORRECT
function get_title() {
    return __( 'Title', 'yourtheme' );
}
```

### 3. `esc_html__()` - Translate & Escape for HTML

Translates string and escapes HTML entities. **Most commonly used** for safe output.

```php
// Basic usage
echo esc_html__( 'Hello World', 'yourtheme' );

// In HTML
<p><?php echo esc_html__( 'Description', 'yourtheme' ); ?></p>

// Or shorter
<?php echo esc_html__( 'Text', 'yourtheme' ); ?>
```

**When to use:**
- **99% of text output** (safest option)
- Prevent XSS attacks
- User-generated content
- Any text that might contain special characters

**Security Note:**
```php
// ❌ UNSAFE (user could inject HTML)
echo __( 'User input', 'yourtheme' );

// ✅ SAFE (HTML entities escaped)
echo esc_html__( 'User input', 'yourtheme' );
```

### 4. `esc_html_e()` - Echo Translated & Escaped String

Combines `_e()` and `esc_html__()`. Translates, escapes, and echoes.

```php
// Basic usage
<h1><?php esc_html_e( 'Page Title', 'yourtheme' ); ?></h1>

// In attributes (though esc_attr_e is better)
<div title="<?php esc_html_e( 'Tooltip', 'yourtheme' ); ?>">
```

**When to use:**
- Quick output without storing
- HTML content that needs escaping
- Template files

### 5. `esc_attr__()` - Translate & Escape for Attributes

Translates and escapes for use in HTML attributes.

```php
// Basic usage
$placeholder = esc_attr__( 'Enter your name', 'yourtheme' );
echo '<input placeholder="' . $placeholder . '">';

// Direct usage
<input placeholder="<?php echo esc_attr__( 'Search...', 'yourtheme' ); ?>">
```

**When to use:**
- `placeholder` attributes
- `title` attributes
- `alt` attributes
- Any HTML attribute value

### 6. `esc_attr_e()` - Echo Translated & Escaped Attribute

Combines `_e()` and `esc_attr__()`.

```php
// Basic usage
<input placeholder="<?php esc_attr_e( 'Enter email', 'yourtheme' ); ?>">
<img alt="<?php esc_attr_e( 'Logo', 'yourtheme' ); ?>">
<a title="<?php esc_attr_e( 'Click here', 'yourtheme' ); ?>">
```

**When to use:**
- Direct attribute output
- No need to store in variable

### 7. `esc_url()` - Escape URLs (Not for Translation)

Not a translation function, but often used together:

```php
// Escape URL output
echo esc_url( 'https://example.com' );

// With translation
<a href="<?php echo esc_url( home_url() ); ?>">
    <?php esc_html_e( 'Home', 'yourtheme' ); ?>
</a>
```

### 8. `_n()` - Plural Forms

Handles singular/plural translations based on count.

```php
// Basic usage
$count = 5;
printf(
    _n(
        'You have %d item',      // Singular
        'You have %d items',     // Plural
        $count,                  // Count
        'yourtheme'              // Text domain
    ),
    $count
);

// Output: "You have 5 items"

// Another example
$comments = get_comments_number();
echo sprintf(
    _n(
        '%d comment',
        '%d comments',
        $comments,
        'yourtheme'
    ),
    number_format_i18n( $comments )
);
```

**When to use:**
- Item counts
- Comment counts
- User counts
- Any number-dependent text

**Important:** Some languages have complex plural rules (e.g., Russian has 3 forms, Arabic has 6 forms). `_n()` handles this automatically.

### 9. `_x()` - Translate with Context

Provides context for ambiguous strings.

```php
// Without context (ambiguous)
__( 'Post', 'yourtheme' ); // Post (verb) or Post (noun)?

// With context (clear)
_x( 'Post', 'noun', 'yourtheme' );        // "Tulisan" in Indonesian
_x( 'Post', 'verb', 'yourtheme' );        // "Kirim" in Indonesian

// Real example
_x( 'Read', 'past tense', 'yourtheme' );  // "Telah dibaca"
_x( 'Read', 'imperative', 'yourtheme' );  // "Baca"
```

**When to use:**
- Ambiguous words (read, post, close, etc.)
- Same English word, different meanings
- Helps translators understand context

### 10. `esc_html_x()` - Translate with Context & Escape

Combines `_x()` with `esc_html()`:

```php
echo esc_html_x( 'Post', 'noun', 'yourtheme' );
```

### Decision Tree: Which Function to Use?

```
Need translation?
├─ YES
│  ├─ For HTML content?
│  │  ├─ Store in variable?
│  │  │  ├─ YES → esc_html__()
│  │  │  └─ NO → esc_html_e()
│  │  └─ For HTML attribute?
│  │     ├─ Store in variable?
│  │     │  ├─ YES → esc_attr__()
│  │     │  └─ NO → esc_attr_e()
│  │     └─ Needs context?
│  │        └─ YES → esc_html_x() or esc_attr_x()
│  ├─ Plural form?
│  │  └─ YES → _n()
│  └─ Just need string (no escape)?
│     ├─ Store in variable → __()
│     └─ Direct echo → _e()
└─ NO
   └─ Just escape
      ├─ HTML → esc_html()
      ├─ Attribute → esc_attr()
      └─ URL → esc_url()
```

---

## Best Practices

### Rule 1: Always Use Text Domain

**❌ WRONG:**
```php
__( 'Hello' ); // Missing text domain!
```

**✅ CORRECT:**
```php
__( 'Hello', 'yourtheme' );
```

### Rule 2: Text Domain Must Match style.css

```css
/* style.css */
Text Domain: elemenane
```

```php
/* All PHP files */
__( 'Text', 'elemenane' ); // ✅ Matches!
__( 'Text', 'newsane' );   // ❌ Different!
```

### Rule 3: Use Static Strings (No Variables)

**❌ WRONG:**
```php
$text = 'Hello World';
__( $text, 'yourtheme' ); // Can't be extracted!

$key = 'title';
__( $key, 'yourtheme' ); // Won't work!
```

**✅ CORRECT:**
```php
__( 'Hello World', 'yourtheme' );

// For dynamic text, use arrays:
$titles = array(
    'page' => __( 'Page', 'yourtheme' ),
    'post' => __( 'Post', 'yourtheme' ),
);
echo $titles[ $key ];
```

### Rule 4: Escape All Output

**❌ WRONG:**
```php
echo __( 'Title', 'yourtheme' ); // Not escaped!
```

**✅ CORRECT:**
```php
echo esc_html__( 'Title', 'yourtheme' );
// or
<h1><?php esc_html_e( 'Title', 'yourtheme' ); ?></h1>
```

### Rule 5: Use sprintf() for Variable Substitution

**❌ WRONG:**
```php
echo 'Hello ' . $name; // Not translatable!
echo __( 'Hello', 'yourtheme' ) . ' ' . $name; // Word order!
```

**✅ CORRECT:**
```php
echo sprintf(
    /* translators: %s: user name */
    __( 'Hello %s', 'yourtheme' ),
    esc_html( $name )
);
```

**Why?** Different languages have different word orders:
- English: "Hello John"
- Indonesian: "Halo John"
- Japanese: "Johnさん、こんにちは" (Name first!)

### Rule 6: Add Translator Comments

Help translators understand context:

```php
/* translators: %s: number of items */
sprintf( __( 'You have %s items', 'yourtheme' ), $count );

/* translators: 1: post title, 2: author name */
sprintf(
    __( '%1$s by %2$s', 'yourtheme' ),
    $title,
    $author
);

/* translators: This appears in the user profile page */
esc_html_e( 'Update Profile', 'yourtheme' );
```

**Format:**
```php
/* translators: [explanation] */
```

Must be **directly above** the translation function (no empty lines).

### Rule 7: Extract Common Phrases to Functions

**❌ BAD (repeated translation):**
```php
// In 20 different files:
esc_html_e( 'Read More', 'yourtheme' );
esc_html_e( 'Read More', 'yourtheme' );
esc_html_e( 'Read More', 'yourtheme' );
```

**✅ GOOD (centralized):**
```php
// In functions.php
function yourtheme_read_more_text() {
    return esc_html__( 'Read More', 'yourtheme' );
}

// In templates
echo yourtheme_read_more_text();
```

### Rule 8: Never Translate Inside Functions That Return HTML

**❌ WRONG:**
```php
function get_button() {
    return '<button>' . __( 'Click', 'yourtheme' ) . '</button>';
    // Translator can't see HTML structure!
}
```

**✅ CORRECT:**
```php
function get_button() {
    return sprintf(
        '<button>%s</button>',
        esc_html__( 'Click', 'yourtheme' )
    );
}

// Or better, in template:
<button><?php esc_html_e( 'Click', 'yourtheme' ); ?></button>
```

### Rule 9: Translate Complete Sentences, Not Fragments

**❌ WRONG:**
```php
echo __( 'Posted on', 'yourtheme' ) . ' ' . $date . ' ' . __( 'by', 'yourtheme' ) . ' ' . $author;
// Translators can't change word order!
```

**✅ CORRECT:**
```php
echo sprintf(
    /* translators: 1: date, 2: author */
    __( 'Posted on %1$s by %2$s', 'yourtheme' ),
    $date,
    $author
);
```

### Rule 10: Use Numbered Placeholders for Multiple Variables

**❌ CONFUSING:**
```php
sprintf( __( '%s wrote %s on %s', 'yourtheme' ), $author, $title, $date );
// Which %s is which?
```

**✅ CLEAR:**
```php
sprintf(
    /* translators: 1: author, 2: post title, 3: date */
    __( '%1$s wrote %2$s on %3$s', 'yourtheme' ),
    $author,
    $title,
    $date
);
```

This allows translators to reorder:
- English: "John wrote Article on Monday"
- Other: "Monday, John wrote Article" → `%3$s, %1$s wrote %2$s`

---

## Common Mistakes

### Mistake 1: Concatenating Translations

**❌ WRONG:**
```php
echo __( 'Read', 'yourtheme' ) . ' ' . __( 'More', 'yourtheme' );
// Creates 2 separate translation strings!
// Some languages might need completely different structure
```

**✅ CORRECT:**
```php
echo esc_html__( 'Read More', 'yourtheme' );
```

### Mistake 2: Translating HTML

**❌ WRONG:**
```php
__( '<strong>Warning:</strong> This is important', 'yourtheme' );
// Translators see HTML tags, might break them
```

**✅ CORRECT:**
```php
echo sprintf(
    '<strong>%s</strong> %s',
    esc_html__( 'Warning:', 'yourtheme' ),
    esc_html__( 'This is important', 'yourtheme' )
);
```

### Mistake 3: Using `_e()` in Return Statements

**❌ WRONG:**
```php
function get_title() {
    return _e( 'Title', 'yourtheme' ); // This echoes, not returns!
}
```

**✅ CORRECT:**
```php
function get_title() {
    return __( 'Title', 'yourtheme' );
}
```

### Mistake 4: Missing Text Domain

**❌ WRONG:**
```php
__( 'Hello' ); // Which theme/plugin is this from?
```

**✅ CORRECT:**
```php
__( 'Hello', 'yourtheme' );
```

### Mistake 5: Translating User Input

**❌ WRONG:**
```php
$title = get_the_title();
echo __( $title, 'yourtheme' ); // User content shouldn't be translated!
```

**✅ CORRECT:**
```php
echo esc_html( get_the_title() ); // Just escape, don't translate
```

### Mistake 6: Empty String Translation

**❌ WRONG:**
```php
echo __( '', 'yourtheme' ); // Returns gettext headers!
```

**✅ CORRECT:**
```php
if ( ! empty( $text ) ) {
    echo esc_html__( $text, 'yourtheme' );
}
```

### Mistake 7: Translating URLs

**❌ WRONG:**
```php
__( 'https://example.com', 'yourtheme' ); // URLs don't need translation!
```

**✅ CORRECT:**
```php
echo esc_url( 'https://example.com' );
```

---

## Generating Translation Files

### Method 1: Using Poedit (Recommended for Beginners)

**Download:** https://poedit.net/

**Steps:**
1. **Create New Translation:**
   - File → New → "From POT/PO file" (if you have template)
   - OR File → New Catalog (create from scratch)

2. **Set Catalog Properties:**
   - Project name: "Your Theme Name"
   - Language: Select target language (e.g., Indonesian)
   - Plural forms: Auto-detected based on language

3. **Add Source Paths:**
   - Click "Sources paths" tab
   - Add: `.` (current directory - theme root)
   - Add: `inc` (if you have includes folder)
   - Add: `tp` (template parts)

4. **Set Keywords:**
   - Click "Sources keywords" tab
   - Add these keywords:
     ```
     __
     _e
     _x
     _n:1,2
     _nx:1,2,4c
     esc_html__
     esc_html_e
     esc_html_x
     esc_attr__
     esc_attr_e
     esc_attr_x
     ```

5. **Update from Sources:**
   - Click "Update" button
   - Poedit scans your theme files
   - All translatable strings appear

6. **Translate:**
   - Click each string in list
   - Enter translation in bottom panel
   - Save file as `yourtheme-id_ID.po`

7. **Compile:**
   - Poedit automatically creates `.mo` file
   - Save both files to `languages/` folder

### Method 2: Using WP-CLI (Recommended for Developers)

**Install WP-CLI:** https://wp-cli.org/

**Generate POT file:**
```bash
wp i18n make-pot . languages/elemenane.pot --domain=elemenane
```

**Options:**
```bash
# Exclude specific directories
wp i18n make-pot . languages/yourtheme.pot \
    --domain=yourtheme \
    --exclude=node_modules,vendor,tests

# Include specific headers
wp i18n make-pot . languages/yourtheme.pot \
    --headers='{"Report-Msgid-Bugs-To":"https://yoursite.com"}'
```

**Create PO file from POT:**
```bash
# Copy POT to PO
cp languages/yourtheme.pot languages/yourtheme-id_ID.po

# Edit with text editor or Poedit
```

**Compile MO file:**
```bash
msgfmt languages/yourtheme-id_ID.po -o languages/yourtheme-id_ID.mo
```

### Method 3: Using Loco Translate Plugin

**Install Plugin:** https://wordpress.org/plugins/loco-translate/

**Steps:**
1. Go to WordPress Admin → Loco Translate → Themes
2. Select your theme
3. Click "New language"
4. Choose language (e.g., Indonesian)
5. Translate strings in browser
6. Save (automatically creates PO and MO files)

**Pros:**
- No external software needed
- Translate directly in WordPress
- Live preview

**Cons:**
- Requires plugin installation
- Can be slow for large themes
- Database overhead

### Method 4: Using PHP Script (Elemen Ane Method)

The Elemen Ane theme includes `languages/compile-mo.php` for compiling `.mo` files:

```php
// In languages/compile-mo.php
<?php
/**
 * Compile MO files from PO files
 *
 * Run: php compile-mo.php
 */

$po_file = 'elemenane-id_ID.po';
$mo_file = 'elemenane-id_ID.mo';

if ( ! file_exists( $po_file ) ) {
    die( "Error: $po_file not found\n" );
}

// Use msgfmt command
exec( "msgfmt $po_file -o $mo_file", $output, $return );

if ( $return === 0 ) {
    echo "✓ Successfully compiled $mo_file\n";
} else {
    echo "✗ Error compiling MO file\n";
}
```

**Usage:**
```bash
cd wp-content/themes/yourtheme/languages
php compile-mo.php
```

---

## Testing Translations

### Method 1: Change WordPress Language

1. Go to **Settings → General**
2. Change **Site Language** to target language
3. Save changes
4. Visit frontend and admin to verify translations

### Method 2: Use Code Switcher Plugin

**Install:** https://wordpress.org/plugins/code-switcher/

Allows switching language per-page without changing site settings.

### Method 3: Force Locale in Code

Temporarily force locale for testing:

```php
// In functions.php (testing only, remove after)
add_filter( 'locale', function() {
    return 'id_ID'; // Force Indonesian
});
```

### Method 4: Check Translation File Syntax

**Using msgfmt:**
```bash
msgfmt -c -v languages/yourtheme-id_ID.po
```

This checks for syntax errors in PO file.

### Testing Checklist

- [ ] All visible text is translated
- [ ] No English text appears in target language
- [ ] Plurals work correctly (1 item vs 2 items)
- [ ] Date/time formats are correct
- [ ] Number formats are correct (1,000.50 vs 1.000,50)
- [ ] RTL languages display correctly (if applicable)
- [ ] Special characters display correctly (ñ, ç, ü, etc.)
- [ ] Text doesn't overflow containers
- [ ] Buttons and links are translated
- [ ] Admin area is translated
- [ ] Error messages are translated
- [ ] Email notifications are translated

---

## Troubleshooting

### Issue 1: Translations Not Showing

**Symptoms:** Changed `.po` file but translations don't appear on site.

**Solutions:**

1. **Recompile MO file:**
   ```bash
   msgfmt languages/yourtheme-id_ID.po -o languages/yourtheme-id_ID.mo
   ```

2. **Clear WordPress cache:**
   - Object cache
   - Page cache (if using caching plugin)
   - Browser cache (Ctrl+Shift+R)

3. **Check file permissions:**
   ```bash
   chmod 644 languages/*.mo
   chmod 644 languages/*.po
   ```

4. **Verify file naming:**
   - Must be: `{textdomain}-{locale}.mo`
   - Example: `elemenane-id_ID.mo`
   - NOT: `elemenane_id_ID.mo` or `elemenane-id-ID.mo`

5. **Use force load method** (see Setup section):
   ```php
   $locale = determine_locale();
   $mofile = get_template_directory() . "/languages/yourtheme-{$locale}.mo";
   if ( file_exists( $mofile ) ) {
       load_textdomain( 'yourtheme', $mofile );
   }
   ```

6. **Check WordPress language setting:**
   - Settings → General → Site Language
   - Must match your `.mo` file locale

### Issue 2: Some Strings Not Translating

**Symptoms:** Most text is translated but some strings remain in English.

**Solutions:**

1. **Check text domain:**
   ```php
   // ❌ Wrong domain
   __( 'Text', 'wrong-domain' );

   // ✅ Correct domain
   __( 'Text', 'yourtheme' );
   ```

2. **Regenerate POT file:**
   ```bash
   wp i18n make-pot . languages/yourtheme.pot
   ```
   Then update your PO file with new strings.

3. **Check for dynamic strings:**
   ```php
   // ❌ Can't be extracted
   $text = 'Hello';
   __( $text, 'yourtheme' );

   // ✅ Can be extracted
   __( 'Hello', 'yourtheme' );
   ```

4. **Verify string in PO file:**
   - Open PO file in text editor
   - Search for the English string
   - Check if translation exists and is not empty

5. **Check for string concatenation:**
   ```php
   // ❌ Creates separate strings
   __( 'Read', 'yourtheme' ) . ' ' . __( 'More', 'yourtheme' );

   // ✅ Single translatable string
   __( 'Read More', 'yourtheme' );
   ```

### Issue 3: Compiled MO File Errors

**Symptoms:** `msgfmt` command fails or shows errors.

**Solutions:**

1. **Check PO file syntax:**
   ```bash
   msgfmt -c languages/yourtheme-id_ID.po
   ```
   This shows specific line numbers with errors.

2. **Common PO file errors:**
   - Missing quotes: `msgid "Text`
   - Unclosed quotes: `msgid "Text`
   - Wrong encoding: Must be UTF-8
   - Placeholder mismatch: `%s` in msgid but `%d` in msgstr

3. **Fix encoding:**
   ```bash
   # Convert to UTF-8
   iconv -f ISO-8859-1 -t UTF-8 old.po > new.po
   ```

4. **Regenerate from POT:**
   - Delete corrupted PO file
   - Copy from POT template
   - Retranslate

### Issue 4: Wrong Text Domain

**Symptoms:** Translations work in one theme but not after theme switch.

**Solution:**

Use WordPress text domain in `style.css`:
```css
Text Domain: yourtheme
```

And in all PHP files:
```php
__( 'Text', 'yourtheme' ); // Same as style.css
```

**Check all files:**
```bash
# Find all translation function calls
grep -r "__(" . --include="*.php"
grep -r "_e(" . --include="*.php"
grep -r "esc_html__(" . --include="*.php"
```

### Issue 5: Plural Forms Not Working

**Symptoms:** Plural translations show wrong form (e.g., "1 items" instead of "1 item").

**Solutions:**

1. **Check PO file header:**
   ```
   "Plural-Forms: nplurals=1; plural=0;\n"
   ```

   **Common plural forms:**
   - English: `nplurals=2; plural=(n != 1);`
   - Indonesian: `nplurals=1; plural=0;` (no plurals)
   - French: `nplurals=2; plural=(n > 1);`
   - Polish: `nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);`

2. **Use correct function:**
   ```php
   // ✅ Correct
   _n( '%d item', '%d items', $count, 'yourtheme' );

   // ❌ Wrong (no plural support)
   __( '%d items', 'yourtheme' );
   ```

3. **Provide all plural forms in PO:**
   ```
   msgid "1 item"
   msgid_plural "%d items"
   msgstr[0] "1 item"
   msgstr[1] "%d items"
   ```

### Issue 6: Date/Time Not Localized

**Symptoms:** Dates show as "January 1, 2025" even in non-English locales.

**Solution:**

Use WordPress date functions, not PHP's `date()`:

```php
// ❌ Not localized
echo date( 'F j, Y' );

// ✅ Localized
echo date_i18n( 'F j, Y' );

// Or with WordPress format
echo get_the_date(); // Uses site format setting
```

### Issue 7: Number Formatting Wrong

**Symptoms:** Numbers show as "1,000.50" in locales that use "1.000,50".

**Solution:**

Use `number_format_i18n()`:

```php
// ❌ Not localized
echo number_format( 1234.56, 2 );

// ✅ Localized
echo number_format_i18n( 1234.56 );
```

### Issue 8: RTL Languages Display Broken

**Symptoms:** Arabic/Hebrew text displays left-to-right or UI is broken.

**Solutions:**

1. **Enable RTL support:**
   ```php
   // In functions.php
   add_theme_support( 'rtl' );
   ```

2. **Create RTL stylesheet:**
   - Create `style-rtl.css`
   - WordPress automatically loads it for RTL languages
   - Mirror CSS properties:
     ```css
     /* style.css */
     .element { float: left; margin-right: 10px; }

     /* style-rtl.css */
     .element { float: right; margin-left: 10px; }
     ```

3. **Use logical properties:**
   ```css
   /* ❌ Fixed direction */
   margin-left: 10px;

   /* ✅ Logical (auto-flips in RTL) */
   margin-inline-start: 10px;
   ```

---

## Quick Reference

### Common Functions Cheat Sheet

| Function | Use Case | Example |
|----------|----------|---------|
| `__()` | Get translated string | `$text = __('Hello', 'theme');` |
| `_e()` | Echo translated string | `_e('Hello', 'theme');` |
| `esc_html__()` | Get translated & escaped | `echo esc_html__('Title', 'theme');` |
| `esc_html_e()` | Echo translated & escaped | `esc_html_e('Title', 'theme');` |
| `esc_attr__()` | Get for HTML attribute | `$alt = esc_attr__('Logo', 'theme');` |
| `esc_attr_e()` | Echo for attribute | `<img alt="<?php esc_attr_e('Logo', 'theme'); ?>">` |
| `_n()` | Plural forms | `_n('1 item', '%d items', $n, 'theme');` |
| `_x()` | Translation with context | `_x('Post', 'noun', 'theme');` |
| `sprintf()` | Variable substitution | `sprintf(__('Hello %s', 'theme'), $name);` |

### File Naming Reference

| File Type | Naming Pattern | Example |
|-----------|----------------|---------|
| POT (Template) | `{theme}.pot` | `elemenane.pot` |
| PO (Translation) | `{theme}-{locale}.po` | `elemenane-id_ID.po` |
| MO (Compiled) | `{theme}-{locale}.mo` | `elemenane-id_ID.mo` |

### Text Domain Checklist

- [ ] Declared in `style.css` header
- [ ] Loaded in `functions.php` (after_setup_theme)
- [ ] Used consistently in all translation functions
- [ ] Matches folder name or theme slug
- [ ] Lowercase, no spaces (use hyphens)

---

## Resources

**Official Documentation:**
- WordPress I18n: https://developer.wordpress.org/apis/internationalization/
- Gettext Manual: https://www.gnu.org/software/gettext/manual/

**Tools:**
- Poedit: https://poedit.net/
- WP-CLI: https://wp-cli.org/
- Loco Translate: https://wordpress.org/plugins/loco-translate/

**Locale Codes:**
- Full list: https://make.wordpress.org/polyglots/teams/

**Testing:**
- WordPress Locale Switcher: https://wordpress.org/plugins/polylang/

---

## Credits

**Theme:** Elemen Ane by Webane Indonesia
**Translation System:** WordPress Gettext
**Version:** 1.0.7
**Last Updated:** 2025-12-31

---

## License

This translation guide is part of the Elemen Ane theme documentation.
GPL-compatible, free to use and adapt for your projects.
