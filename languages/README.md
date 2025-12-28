# Elemen Ane Theme - Translation Files

This directory contains translation files for the Elemen Ane theme.

## Available Languages

- **English (Default)** - Built into the theme
- **Indonesian (id_ID)** - `elemenane-id_ID.po` and `elemenane-id_ID.mo`

## How to Use Translation

### 1. Automatic (Recommended)

The theme will automatically load translations based on your WordPress site language setting:

1. Go to **WordPress Admin → Settings → General**
2. Set **Site Language** to **Bahasa Indonesia**
3. Save changes
4. The theme will automatically use Indonesian translations

### 2. Manual (via wp-config.php)

Add this to your `wp-config.php` file:

```php
define( 'WPLANG', 'id_ID' );
```

## Translation Files

### PO File (`.po`)
- Human-readable text file
- Can be edited with text editor or Poedit
- File: `elemenane-id_ID.po`

### MO File (`.mo`)
- Compiled binary file used by WordPress
- Generated from PO file
- File: `elemenane-id_ID.mo`

## How to Edit Translations

### Option 1: Using Poedit (Recommended)

1. Download and install [Poedit](https://poedit.net/)
2. Open `elemenane-id_ID.po` in Poedit
3. Edit translations
4. Save (Poedit will auto-compile to .mo file)

### Option 2: Text Editor

1. Open `elemenane-id_ID.po` in any text editor
2. Edit translations:
   ```
   msgid "In Stock"
   msgstr "Tersedia"
   ```
3. Save the file
4. Compile to MO using the compile script:
   ```bash
   cd languages
   php compile-mo.php
   ```

## Translation Coverage

### Product Archive & Taxonomy
- "Products" → "Produk"
- "No products found." → "Tidak ada produk ditemukan."
- Product count formatting

### Product Card (Loop)
- "In Stock" → "Tersedia"
- "Out of Stock" → "Stok Habis"
- "New" → "Baru"

### Single Product Page
- "Chat WhatsApp" → "Chat WhatsApp"
- "Buy at Branch" → "Beli di Cabang"
- "Or buy on marketplace:" → "Atau beli di marketplace:"
- "Description" → "Deskripsi"
- "Specifications" → "Spesifikasi"
- "Features" → "Fitur"
- "Select Branch" → "Pilih Cabang"
- "Search by city or branch name..." → "Cari berdasarkan kota atau nama cabang..."
- "WhatsApp" → "WhatsApp"
- "Detail" → "Detail"

### JavaScript Messages
- "No branches found for search" → "Tidak ada cabang ditemukan untuk pencarian"
- WhatsApp message template
- Address, Phone, Email labels

### ACF Fields
- "Product Gallery" → "Galeri Produk"
- "Product Images" → "Gambar Produk"
- Field instructions

## Creating New Language

To create translation for a new language (e.g., Japanese - ja):

1. Copy `elemenane-id_ID.po` to `elemenane-ja.po`
2. Edit the header:
   ```
   "Language: ja\n"
   ```
3. Translate all `msgstr` values to Japanese
4. Compile to MO:
   ```bash
   php compile-mo.php
   ```
5. Set WordPress language to Japanese

## Text Domain

All translatable strings use text domain: **`elemenane`**

Example in code:
```php
esc_html_e( 'In Stock', 'elemenane' );
__( 'Products', 'elemenane' );
```

## Notes

- Always edit the `.po` file, not the `.mo` file
- After editing `.po`, you must recompile to `.mo`
- WordPress will cache translations, you may need to clear cache
- The `compile-mo.php` script is included for convenience

## Support

For translation issues or adding new languages, please contact Webane Indonesia.
