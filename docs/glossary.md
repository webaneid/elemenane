# Glosarium — Elemen Ane (`elemenane`)

| Istilah / Singkatan | Penjelasan |
| :--- | :--- |
| **ACF Pro** | Advanced Custom Fields Pro — plugin wajib untuk mengelola Theme Options (`ane-setup`, `ane-general-setting`, `ane-customer-care`, `ane-seo-news`) dan field custom post type. |
| **CPT** | Custom Post Type di WordPress. Di `elemenane` mencakup `product` (Katalog Produk), `service` (Layanan), dan `branch` (Cabang/Lokasi). |
| **CWV** | Core Web Vitals — metrik performa utama Google PageSpeed Insights yang meliputi **LCP** (Largest Contentful Paint), **CLS** (Cumulative Layout Shift), **FCP** (First Contentful Paint), dan **INP** (Interaction to Next Paint). |
| **DPR** | Device Pixel Ratio — rasio piksel fisik terhadap piksel CSS pada layar perangkat (umumnya `2x`–`3x` pada smartphone modern). |
| **Elementor Kit** | Pengaturan desain global Elementor (`Global Colors` & `Global Fonts`) yang oleh `inc/elementor.php` disinkronkan ke variabel CSS `:root` (`--e-global-color-*`). |
| **FontAne** | Icon font warisan (`css/FontAne.css` & `fonts/FontAne.*`) yang dimigrasikan ke sistem Inline SVG (`elemenane_icon()`) untuk menghilangkan request font render-blocking. |
| **LCP** | Largest Contentful Paint — waktu yang dibutuhkan elemen konten terbesar di viewport awal (biasanya gambar hero/featured) untuk ter-render penuh. |
| **Linktree Page** | Template halaman khusus (`page-linktree.php`) untuk menampilkan halaman profil bio link beserta pelacakan klik di tabel `{$wpdb->prefix}ane_linktree_clicks`. |
| **WPCS** | WordPress Coding Standards — aturan standar penulisan kode dan keamanan PHP untuk ekosistem WordPress yang diperiksa melalui `phpcs`. |
| **XOR-Hex Key** | Teknik obfuscation kunci API default menggunakan operasi bitwise XOR dan representasi heksadesimal agar kunci tidak tersimpan sebagai plaintext di repositori kode. |
