<?php
/**
 * Hybrid Premium SEO Engine (Yoast Booster & Standalone Engine), Schema.org JSON-LD @graph
 * for Google Sitelinks & Rich Results (NewsArticle, Product, LocalBusiness Branch, FAQPage,
 * Speakable), Multi-Layer Tag-to-Keywords, Smart 301 Slug Redirects, and Generative Engine
 * Optimization (GEO /llms.txt & /llms-full.txt).
 *
 * @package ElemenAne
 * @since   1.0.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check whether Yoast SEO plugin is active.
 *
 * @return bool
 */
function elemenane_is_yoast_active(): bool {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'WPSEO_Options' );
}

/**
 * Normalize phone/WhatsApp number to international format (+62...).
 *
 * @param string $raw Raw phone string.
 * @return string Normalized phone or empty string.
 */
function elemenane_seo_normalize_phone( string $raw ): string {
	$digits = (string) preg_replace( '/[^0-9]/', '', $raw );
	if ( '' === $digits ) {
		return '';
	}
	if ( '0' === substr( $digits, 0, 1 ) ) {
		$digits = '62' . substr( $digits, 1 );
	}
	return '+' . $digits;
}

/**
 * Collect and cache the complete company/organization profile from ACF Options & WordPress
 * settings (static per-request cache for 0 redundant database queries).
 *
 * @return array<string, mixed>
 */
function elemenane_get_company_profile(): array {
	static $profile = null;
	if ( is_array( $profile ) ) {
		return $profile;
	}

	$site_name = trim( wp_strip_all_tags( (string) get_bloginfo( 'name' ) ) );
	$tagline   = trim( wp_strip_all_tags( (string) get_bloginfo( 'description' ) ) );
	$home_url  = home_url( '/' );

	$org_name = '';
	$slogan   = $tagline;
	$about    = '';
	$org_url  = $home_url;
	$address  = '';
	$phone    = '';
	$mobile   = '';
	$email    = '';
	$website  = $home_url;
	$lat      = '';
	$lng      = '';
	$location = false;
	$socials  = array();
	$cs_list  = array();

	if ( function_exists( 'get_field' ) ) {
		$com_name = trim( wp_strip_all_tags( (string) get_field( 'ane_com_nama', 'option' ) ) );
		if ( '' !== $com_name ) {
			$org_name = $com_name;
		}

		$com_tag = trim( wp_strip_all_tags( (string) get_field( 'ane_com_tag', 'option' ) ) );
		if ( '' !== $com_tag ) {
			$slogan = $com_tag;
		}

		$com_des = trim( wp_strip_all_tags( (string) get_field( 'ane_com_des', 'option' ) ) );
		if ( '' !== $com_des ) {
			$about = $com_des;
		}

		$com_link = trim( (string) get_field( 'ane_com_link', 'option' ) );
		if ( '' !== $com_link ) {
			$org_url = esc_url_raw( $com_link );
		}

		$address = trim( wp_strip_all_tags( (string) get_field( 'ane_com_alamat', 'option' ) ) );
		$phone   = trim( wp_strip_all_tags( (string) get_field( 'ane_telepon', 'option' ) ) );

		$kontak_rows = get_field( 'ane_kontak', 'option' );
		if ( is_array( $kontak_rows ) ) {
			if ( isset( $kontak_rows['ane_handphone'] ) || isset( $kontak_rows['ane_email'] ) ) {
				$kontak_rows = array( $kontak_rows );
			}
			foreach ( $kontak_rows as $k_row ) {
				if ( ! is_array( $k_row ) ) {
					continue;
				}
				if ( '' === $mobile && ! empty( $k_row['ane_handphone'] ) ) {
					$mobile = trim( wp_strip_all_tags( (string) $k_row['ane_handphone'] ) );
				}
				if ( '' === $email && ! empty( $k_row['ane_email'] ) ) {
					$email = sanitize_email( (string) $k_row['ane_email'] );
				}
				if ( $home_url === $website && ! empty( $k_row['ane_website'] ) ) {
					$raw_web = trim( (string) $k_row['ane_website'] );
					if ( '' !== $raw_web ) {
						$website = preg_match( '#^https?://#i', $raw_web ) ? esc_url_raw( $raw_web ) : esc_url_raw( 'https://' . $raw_web );
					}
				}
			}
		}

		$gmap = get_field( 'ane_gmap', 'option' );
		if ( is_array( $gmap ) && ! empty( $gmap['lat'] ) && ! empty( $gmap['lng'] ) ) {
			$lat      = (string) $gmap['lat'];
			$lng      = (string) $gmap['lng'];
			$location = $gmap;
		}

		$social_keys = array(
			'ane_facebook',
			'ane_twitter',
			'ane_instagram',
			'ane_youtube',
			'ane_tiktok',
			'ane_telegram',
			'ane_threads',
			'ane_linkedin',
			'ane_whatsapp',
		);
		foreach ( $social_keys as $s_key ) {
			$s_val = trim( (string) get_field( $s_key, 'option' ) );
			if ( '' !== $s_val && 0 === strpos( $s_val, 'http' ) ) {
				$socials[] = esc_url_raw( $s_val );
			}
		}

		$cs_rows = get_field( 'ane_cs', 'option' );
		if ( is_array( $cs_rows ) ) {
			foreach ( $cs_rows as $cs_item ) {
				if ( ! is_array( $cs_item ) ) {
					continue;
				}
				$cs_name = isset( $cs_item['ane_nama'] ) ? trim( wp_strip_all_tags( (string) $cs_item['ane_nama'] ) ) : '';
				$cs_area = isset( $cs_item['ane_area'] ) ? trim( wp_strip_all_tags( (string) $cs_item['ane_area'] ) ) : '';
				$cs_wa   = isset( $cs_item['ane_whatsapp'] ) ? elemenane_seo_normalize_phone( (string) $cs_item['ane_whatsapp'] ) : '';
				if ( '' !== $cs_wa ) {
					$cs_list[] = array(
						'name'  => $cs_name,
						'role'  => $cs_area,
						'phone' => $cs_wa,
					);
				}
			}
		}
	}

	if ( '' === $org_name ) {
		$org_name = $site_name;
	}
	if ( '' === $email ) {
		$email = sanitize_email( (string) get_bloginfo( 'admin_email' ) );
	}

	$logo_url    = get_template_directory_uri() . '/img/logo-elemenane.svg';
	$custom_logo = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo > 0 ) {
		$custom_logo_url = wp_get_attachment_image_url( $custom_logo, 'full' );
		if ( is_string( $custom_logo_url ) && '' !== $custom_logo_url ) {
			$logo_url = $custom_logo_url;
		}
	}

	$profile = array(
		'site_name' => $site_name,
		'org_name'  => $org_name,
		'slogan'    => $slogan,
		'about'     => $about,
		'org_url'   => $org_url,
		'address'   => $address,
		'phone'     => $phone,
		'mobile'    => $mobile,
		'email'     => $email,
		'website'   => $website,
		'lat'       => $lat,
		'lng'       => $lng,
		'location'  => $location,
		'logo_url'  => $logo_url,
		'home_url'  => $home_url,
		'socials'   => array_values( array_unique( array_filter( $socials ) ) ),
		'cs_list'   => $cs_list,
	);

	return $profile;
}

/**
 * ============================================================================
 * BACKWARD-COMPATIBLE COMPANY HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Get company name from ACF options.
 *
 * @return string
 */
function ane_get_company_name(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['org_name'];
}

/**
 * Get company description from ACF options.
 *
 * @return string
 */
function ane_get_company_description(): string {
	$profile = elemenane_get_company_profile();
	return '' !== (string) $profile['about'] ? (string) $profile['about'] : (string) $profile['slogan'];
}

/**
 * Get company URL from ACF options.
 *
 * @return string
 */
function ane_get_company_url(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['org_url'];
}

/**
 * Get company address from ACF options.
 *
 * @return string
 */
function ane_get_company_address(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['address'];
}

/**
 * Get company phone from ACF options.
 *
 * @return string
 */
function ane_get_company_phone(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['phone'];
}

/**
 * Get company mobile/handphone from ACF options.
 *
 * @return string
 */
function ane_get_company_mobile(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['mobile'];
}

/**
 * Get company email from ACF options.
 *
 * @return string
 */
function ane_get_company_email(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['email'];
}

/**
 * Get company website from ACF options.
 *
 * @return string
 */
function ane_get_company_website(): string {
	$profile = elemenane_get_company_profile();
	return (string) $profile['website'];
}

/**
 * Get company Google Maps data from ACF options.
 *
 * @return array<string, mixed>|false
 */
function ane_get_company_location() {
	$profile = elemenane_get_company_profile();
	return is_array( $profile['location'] ) ? $profile['location'] : false;
}

/**
 * Get company logo URL.
 *
 * @param string $size Image size (default: 'full').
 * @return string
 */
function ane_get_company_logo( $size = 'full' ): string {
	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id > 0 ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, $size );
		if ( is_string( $logo_url ) && '' !== $logo_url ) {
			return $logo_url;
		}
	}
	return get_template_directory_uri() . '/img/logo-elemenane.svg';
}

/**
 * Get full company contact information as array.
 *
 * @return array<string, mixed>
 */
function ane_get_company_info(): array {
	return array(
		'name'        => ane_get_company_name(),
		'description' => ane_get_company_description(),
		'url'         => ane_get_company_url(),
		'address'     => ane_get_company_address(),
		'phone'       => ane_get_company_phone(),
		'mobile'      => ane_get_company_mobile(),
		'email'       => ane_get_company_email(),
		'website'     => ane_get_company_website(),
		'location'    => ane_get_company_location(),
		'logo'        => ane_get_company_logo(),
	);
}

/**
 * ============================================================================
 * SMART DESCRIPTION, IMAGE & MULTI-LAYER KEYWORDS ENGINE
 * ============================================================================
 */

/**
 * Trim text cleanly at a word boundary up to a maximum character length.
 *
 * @param string $text       Source text.
 * @param int    $max_length Maximum character length.
 * @return string
 */
function elemenane_seo_trim_description( string $text, int $max_length = 158 ): string {
	$clean = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( strip_shortcodes( $text ) ) ) );
	if ( '' === $clean ) {
		return '';
	}
	if ( strlen( $clean ) <= $max_length ) {
		return $clean;
	}
	$cut   = substr( $clean, 0, $max_length );
	$space = strrpos( $cut, ' ' );
	if ( false !== $space && $space > 80 ) {
		$cut = substr( $cut, 0, $space );
	}
	return rtrim( $cut, '.,;:- ' ) . '...';
}

/**
 * Generate a context-aware smart Meta Description for a given WP_Post object.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function elemenane_build_post_smart_description( $post ): string {
	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$profile   = elemenane_get_company_profile();
	$org_name  = (string) $profile['org_name'];
	$slogan    = (string) $profile['slogan'];
	$address   = (string) $profile['address'];
	$post_id   = (int) $post->ID;
	$post_type = (string) $post->post_type;
	$title     = trim( wp_strip_all_tags( get_the_title( $post ) ) );
	$raw_body  = '' !== trim( $post->post_excerpt ) ? $post->post_excerpt : $post->post_content;

	if ( 'product' === $post_type ) {
		$cat_terms = get_the_terms( $post_id, 'product_cat' );
		$cat_names = is_array( $cat_terms ) ? wp_list_pluck( $cat_terms, 'name' ) : array();
		$cat_str   = ! empty( $cat_names ) ? ' (' . implode( ', ', $cat_names ) . ')' : '';

		$price_str = '';
		if ( function_exists( 'ane_get_product_price_data' ) && function_exists( 'ane_format_rupiah' ) ) {
			$prices = ane_get_product_price_data( $post_id );
			if ( ! empty( $prices['price'] ) && $prices['price'] > 0 ) {
				$price_str = ' — Harga: ' . ane_format_rupiah( (float) $prices['price'] );
			}
		}

		$desc  = $title . $cat_str . $price_str . ' di ' . $org_name . '. ';
		$desc .= wp_strip_all_tags( strip_shortcodes( $raw_body ) );
		return elemenane_seo_trim_description( $desc, 158 );
	}

	if ( 'branch' === $post_type ) {
		$b_meta = function_exists( 'elemenane_get_branch_meta' ) ? elemenane_get_branch_meta( $post_id ) : array();
		$city   = ! empty( $b_meta['city_name'] ) ? (string) $b_meta['city_name'] : '';
		$prov   = ! empty( $b_meta['province_name'] ) ? (string) $b_meta['province_name'] : '';
		$b_addr = ! empty( $b_meta['address'] ) ? trim( wp_strip_all_tags( (string) $b_meta['address'] ) ) : '';
		$loc    = implode( ', ', array_filter( array( $city, $prov ) ) );

		$desc = $title;
		if ( '' !== $loc ) {
			$desc .= ' (' . $loc . ')';
		}
		$desc .= ' — Cabang resmi ' . $org_name . '.';
		if ( '' !== $b_addr ) {
			$desc .= ' Alamat: ' . $b_addr . '.';
		}
		$desc .= ' ' . wp_strip_all_tags( strip_shortcodes( $raw_body ) );
		return elemenane_seo_trim_description( $desc, 158 );
	}

	if ( 'service' === $post_type ) {
		$desc = 'Layanan ' . $title . ' dari ' . $org_name . '. ' . wp_strip_all_tags( strip_shortcodes( $raw_body ) );
		return elemenane_seo_trim_description( $desc, 158 );
	}

	if ( 'page-kontak.php' === get_page_template_slug( $post_id ) ) {
		$desc = 'Hubungi ' . $org_name;
		if ( '' !== $address ) {
			$desc .= ' — Alamat: ' . $address . '.';
		}
		if ( '' !== (string) $profile['phone'] || '' !== (string) $profile['mobile'] ) {
			$desc .= ' Informasi layanan telepon, WhatsApp Customer Care, dan peta lokasi resmi.';
		}
		return elemenane_seo_trim_description( $desc, 158 );
	}

	$trimmed = elemenane_seo_trim_description( $raw_body, 158 );
	if ( '' !== $trimmed ) {
		return $trimmed;
	}

	return elemenane_seo_trim_description( $title . ' — ' . $org_name . ( '' !== $slogan ? ' (' . $slogan . ')' : '' ), 158 );
}

/**
 * Generate a smart Meta Description for the current request when Yoast meta is blank.
 *
 * @return string
 */
function elemenane_generate_smart_description(): string {
	$profile  = elemenane_get_company_profile();
	$org_name = (string) $profile['org_name'];
	$slogan   = (string) $profile['slogan'];
	$about    = (string) $profile['about'];
	$address  = (string) $profile['address'];

	if ( is_front_page() || is_home() ) {
		$parts = array();
		if ( '' !== $org_name ) {
			$parts[] = $org_name;
		}
		if ( '' !== $slogan ) {
			$parts[] = $slogan;
		}
		if ( '' !== $about ) {
			$parts[] = $about;
		} elseif ( '' !== $address ) {
			$parts[] = 'Alamat: ' . $address;
		}
		return elemenane_seo_trim_description( implode( ' — ', $parts ), 158 );
	}

	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		$post    = get_post( $post_id );
		if ( ! $post instanceof WP_Post ) {
			return '';
		}
		return elemenane_build_post_smart_description( $post );
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			if ( '' !== trim( $term->description ) ) {
				return elemenane_seo_trim_description( $term->description, 158 );
			}
			return elemenane_seo_trim_description( 'Kumpulan informasi dan katalog ' . $term->name . ' di ' . $org_name . '.', 158 );
		}
	}

	if ( is_post_type_archive() ) {
		$pt_obj = get_queried_object();
		if ( is_object( $pt_obj ) && isset( $pt_obj->labels->name ) ) {
			return elemenane_seo_trim_description( 'Daftar ' . $pt_obj->labels->name . ' resmi di ' . $org_name . '.', 158 );
		}
	}

	if ( is_author() ) {
		$author = get_queried_object();
		if ( $author instanceof WP_User ) {
			$a_desc = trim( (string) $author->description );
			if ( '' !== $a_desc ) {
				return elemenane_seo_trim_description( $a_desc, 158 );
			}
			return elemenane_seo_trim_description( 'Semua artikel oleh ' . $author->display_name . ' di ' . $org_name . '.', 158 );
		}
	}

	return elemenane_seo_trim_description( $org_name . ( '' !== $slogan ? ' — ' . $slogan : '' ), 158 );
}

/**
 * Retrieve the best image URL for OpenGraph / Twitter Card / Schema on the current page.
 *
 * @return string
 */
function elemenane_get_seo_image_url(): string {
	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		if ( $post_id > 0 && has_post_thumbnail( $post_id ) ) {
			$img = wp_get_attachment_image_src( (int) get_post_thumbnail_id( $post_id ), 'large' );
			if ( is_array( $img ) && ! empty( $img[0] ) ) {
				return (string) $img[0];
			}
		}
	}

	$profile = elemenane_get_company_profile();
	return (string) $profile['logo_url'];
}

/**
 * Collect multi-layer keywords from Yoast Focus Keyphrase, Post Tags (`post_tag`),
 * Categories (`category`), Product Taxonomies (`product_cat`, `product_tag`), and Branch Location.
 *
 * @param int $post_id Optional post ID (0 to auto-detect from current query).
 * @return array<int, string>
 */
function elemenane_get_current_keywords( int $post_id = 0 ): array {
	$profile  = elemenane_get_company_profile();
	$keywords = array();

	if ( $post_id <= 0 && is_singular() ) {
		$post_id = (int) get_queried_object_id();
	}

	if ( $post_id > 0 ) {
		// 1. Focus keyphrase from Yoast SEO (if filled).
		$yoast_kw = trim( wp_strip_all_tags( (string) get_post_meta( $post_id, '_yoast_wpseo_focuskw', true ) ) );
		if ( '' !== $yoast_kw ) {
			$keywords[] = $yoast_kw;
		}

		// 2. Post Tags (post_tag).
		$tags = get_the_tags( $post_id );
		if ( is_array( $tags ) ) {
			foreach ( $tags as $tag ) {
				if ( $tag instanceof WP_Term && '' !== trim( $tag->name ) ) {
					$keywords[] = trim( $tag->name );
				}
			}
		}

		// 3. Post Categories (category).
		$cats = get_the_category( $post_id );
		if ( is_array( $cats ) ) {
			foreach ( $cats as $cat ) {
				if ( $cat instanceof WP_Term && 'Uncategorized' !== $cat->name && 'Tak Berkategori' !== $cat->name ) {
					$keywords[] = trim( $cat->name );
				}
			}
		}

		// 4. CPT-specific terms & metadata (product, branch, service).
		$post_type = get_post_type( $post_id );
		if ( 'product' === $post_type ) {
			$p_cats = get_the_terms( $post_id, 'product_cat' );
			if ( is_array( $p_cats ) ) {
				foreach ( $p_cats as $pc ) {
					if ( $pc instanceof WP_Term ) {
						$keywords[] = trim( $pc->name );
					}
				}
			}
			$p_tags = get_the_terms( $post_id, 'product_tag' );
			if ( is_array( $p_tags ) ) {
				foreach ( $p_tags as $pt ) {
					if ( $pt instanceof WP_Term ) {
						$keywords[] = trim( $pt->name );
					}
				}
			}
		} elseif ( 'branch' === $post_type && function_exists( 'elemenane_get_branch_meta' ) ) {
			$b_meta = elemenane_get_branch_meta( $post_id );
			if ( ! empty( $b_meta['city_name'] ) ) {
				$keywords[] = (string) $b_meta['city_name'];
			}
			if ( ! empty( $b_meta['province_name'] ) ) {
				$keywords[] = (string) $b_meta['province_name'];
			}
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$keywords[] = trim( $term->name );
		}
	}

	if ( '' !== (string) $profile['org_name'] ) {
		$keywords[] = (string) $profile['org_name'];
	}
	if ( '' !== (string) $profile['site_name'] && $profile['site_name'] !== $profile['org_name'] ) {
		$keywords[] = (string) $profile['site_name'];
	}

	$clean_list = array();
	$seen       = array();
	foreach ( $keywords as $kw ) {
		$kw_clean = trim( wp_strip_all_tags( (string) $kw ) );
		$kw_lower = strtolower( $kw_clean );
		if ( '' !== $kw_clean && ! isset( $seen[ $kw_lower ] ) ) {
			$seen[ $kw_lower ] = true;
			$clean_list[]      = $kw_clean;
		}
	}

	return $clean_list;
}

/**
 * Yoast SEO Filter: Auto-fill Meta Description, OG Description, & Twitter Description
 * when left empty by the editor.
 *
 * @param string $desc Description from Yoast.
 * @return string
 */
function elemenane_yoast_fallback_description( $desc ): string {
	if ( is_string( $desc ) && '' !== trim( $desc ) ) {
		return $desc;
	}
	return elemenane_generate_smart_description();
}
add_filter( 'wpseo_metadesc', 'elemenane_yoast_fallback_description', 20 );
add_filter( 'wpseo_opengraph_desc', 'elemenane_yoast_fallback_description', 20 );
add_filter( 'wpseo_twitter_description', 'elemenane_yoast_fallback_description', 20 );

/**
 * Yoast SEO Filter: Auto-fallback OpenGraph & Twitter image to featured image / company logo.
 *
 * @param string $image Image URL from Yoast.
 * @return string
 */
function elemenane_yoast_fallback_og_image( $image ): string {
	if ( is_string( $image ) && '' !== trim( $image ) ) {
		return $image;
	}
	return elemenane_get_seo_image_url();
}
add_filter( 'wpseo_opengraph_image', 'elemenane_yoast_fallback_og_image', 20 );
add_filter( 'wpseo_twitter_image', 'elemenane_yoast_fallback_og_image', 20 );

/**
 * Ensure robots directives allow full snippets & large image previews
 * for Google Discover & AI Overviews (GEO).
 *
 * @param array<string, bool|string> $robots WordPress robots directives.
 * @return array<string, bool|string>
 */
function elemenane_seo_robots_directives( $robots ): array {
	if ( ! is_array( $robots ) ) {
		$robots = array();
	}
	if ( empty( $robots['noindex'] ) ) {
		$robots['max-snippet']       = '-1';
		$robots['max-image-preview'] = 'large';
		$robots['max-video-preview'] = '-1';
	}
	return $robots;
}
add_filter( 'wp_robots', 'elemenane_seo_robots_directives', 20 );

/**
 * ============================================================================
 * UNIFIED SCHEMA.ORG JSON-LD @GRAPH & YOAST GRAPH ENRICHMENT
 * ============================================================================
 */

/**
 * Retrieve top-level navigation items from `menuutama` for `SiteNavigationElement`
 * (Google Sitelinks signal) and `/llms.txt`.
 *
 * @return array<int, array{name: string, url: string}>
 */
function elemenane_get_sitelinks_navigation(): array {
	$items     = array();
	$locations = get_nav_menu_locations();

	if ( is_array( $locations ) && ! empty( $locations['menuutama'] ) ) {
		$menu_items = wp_get_nav_menu_items( (int) $locations['menuutama'] );
		if ( is_array( $menu_items ) ) {
			foreach ( $menu_items as $item ) {
				if ( ! is_object( $item ) || empty( $item->url ) || empty( $item->title ) ) {
					continue;
				}
				if ( isset( $item->menu_item_parent ) && 0 !== (int) $item->menu_item_parent ) {
					continue;
				}
				$url   = trim( (string) $item->url );
				$title = trim( wp_strip_all_tags( (string) $item->title ) );
				if ( '' === $title || '' === $url || '#' === $url ) {
					continue;
				}
				$items[] = array(
					'name' => $title,
					'url'  => $url,
				);
			}
		}
	}

	if ( empty( $items ) ) {
		$pages = get_pages(
			array(
				'parent'      => 0,
				'number'      => 8,
				'sort_column' => 'menu_order,post_title',
			)
		);
		if ( is_array( $pages ) ) {
			foreach ( $pages as $p ) {
				if ( $p instanceof WP_Post ) {
					$items[] = array(
						'name' => trim( wp_strip_all_tags( get_the_title( $p ) ) ),
						'url'  => (string) get_permalink( $p ),
					);
				}
			}
		}
	}

	return $items;
}

/**
 * Build the `BreadcrumbList` Schema.org node for the active page.
 *
 * @return array<string, mixed>|null
 */
function elemenane_get_breadcrumb_schema_node(): ?array {
	if ( is_front_page() || is_home() ) {
		return null;
	}

	$home_url  = home_url( '/' );
	$positions = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'elemenane' ),
			'item'     => $home_url,
		),
	);
	$pos_index = 2;

	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		$post    = get_post( $post_id );
		if ( $post instanceof WP_Post ) {
			if ( 'post' === $post->post_type ) {
				$cats = get_the_category( $post_id );
				if ( is_array( $cats ) && ! empty( $cats[0] ) && $cats[0] instanceof WP_Term ) {
					$cat_link = get_category_link( $cats[0]->term_id );
					if ( is_string( $cat_link ) && '' !== $cat_link ) {
						$positions[] = array(
							'@type'    => 'ListItem',
							'position' => $pos_index++,
							'name'     => $cats[0]->name,
							'item'     => $cat_link,
						);
					}
				}
			} elseif ( 'product' === $post->post_type ) {
				$archive_link = get_post_type_archive_link( 'product' );
				if ( is_string( $archive_link ) && '' !== $archive_link ) {
					$positions[] = array(
						'@type'    => 'ListItem',
						'position' => $pos_index++,
						'name'     => __( 'Products', 'elemenane' ),
						'item'     => $archive_link,
					);
				}
				$p_cats = get_the_terms( $post_id, 'product_cat' );
				if ( is_array( $p_cats ) && ! empty( $p_cats[0] ) && $p_cats[0] instanceof WP_Term ) {
					$t_link = get_term_link( $p_cats[0] );
					if ( is_string( $t_link ) && '' !== $t_link ) {
						$positions[] = array(
							'@type'    => 'ListItem',
							'position' => $pos_index++,
							'name'     => $p_cats[0]->name,
							'item'     => $t_link,
						);
					}
				}
			} elseif ( ! in_array( $post->post_type, array( 'page', 'attachment' ), true ) ) {
				$pt_obj       = get_post_type_object( $post->post_type );
				$archive_link = get_post_type_archive_link( $post->post_type );
				if ( is_object( $pt_obj ) && isset( $pt_obj->labels->name ) && is_string( $archive_link ) && '' !== $archive_link ) {
					$positions[] = array(
						'@type'    => 'ListItem',
						'position' => $pos_index++,
						'name'     => (string) $pt_obj->labels->name,
						'item'     => $archive_link,
					);
				}
			} elseif ( 'page' === $post->post_type && $post->post_parent > 0 ) {
				$ancestors = array_reverse( get_post_ancestors( $post ) );
				foreach ( $ancestors as $ancestor_id ) {
					$positions[] = array(
						'@type'    => 'ListItem',
						'position' => $pos_index++,
						'name'     => wp_strip_all_tags( get_the_title( $ancestor_id ) ),
						'item'     => (string) get_permalink( $ancestor_id ),
					);
				}
			}

			$positions[] = array(
				'@type'    => 'ListItem',
				'position' => $pos_index,
				'name'     => wp_strip_all_tags( get_the_title( $post ) ),
				'item'     => (string) get_permalink( $post ),
			);
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$term_link = get_term_link( $term );
			if ( is_string( $term_link ) ) {
				$positions[] = array(
					'@type'    => 'ListItem',
					'position' => $pos_index,
					'name'     => $term->name,
					'item'     => $term_link,
				);
			}
		}
	} elseif ( is_post_type_archive() ) {
		$pt_obj = get_queried_object();
		if ( is_object( $pt_obj ) && isset( $pt_obj->name, $pt_obj->labels->name ) ) {
			$archive_link = get_post_type_archive_link( (string) $pt_obj->name );
			if ( is_string( $archive_link ) ) {
				$positions[] = array(
					'@type'    => 'ListItem',
					'position' => $pos_index,
					'name'     => (string) $pt_obj->labels->name,
					'item'     => $archive_link,
				);
			}
		}
	} elseif ( is_author() ) {
		$author = get_queried_object();
		if ( $author instanceof WP_User ) {
			$positions[] = array(
				'@type'    => 'ListItem',
				'position' => $pos_index,
				'name'     => $author->display_name,
				'item'     => get_author_posts_url( $author->ID ),
			);
		}
	}

	if ( count( $positions ) < 2 ) {
		return null;
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) . '#breadcrumb',
		'itemListElement' => $positions,
	);
}

/**
 * Automatically extract Q&A pairs (FAQ) from post/page content
 * (supports `.schema-faq-question`/`.schema-faq-answer` and HTML5 `<details><summary>`)
 * into a `FAQPage` JSON-LD node.
 *
 * @param int $post_id Post/Page ID.
 * @return array<string, mixed>|null
 */
function elemenane_extract_faq_schema_node( int $post_id ): ?array {
	$post = get_post( $post_id );
	if ( ! $post instanceof WP_Post || '' === trim( $post->post_content ) ) {
		return null;
	}

	$content   = $post->post_content;
	$questions = array();
	$seen_q    = array();

	// 1. Yoast / Gutenberg FAQ block (.schema-faq-question & .schema-faq-answer).
	if ( false !== strpos( $content, 'schema-faq-question' ) ) {
		$pattern_yoast = '/<[^>]*class="[^"]*schema-faq-question[^"]*"[^>]*>(.*?)<\/[^>]+>\s*<[^>]*class="[^"]*schema-faq-answer[^"]*"[^>]*>(.*?)<\/[^>]+>/is';
		if ( preg_match_all( $pattern_yoast, $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $m ) {
				$q_text = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $m[1] ) ) );
				$a_text = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $m[2] ) ) );
				$q_key  = strtolower( $q_text );
				if ( '' !== $q_text && '' !== $a_text && ! isset( $seen_q[ $q_key ] ) ) {
					$seen_q[ $q_key ] = true;
					$questions[]      = array(
						'@type'          => 'Question',
						'name'           => $q_text,
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $a_text,
						),
					);
				}
			}
		}
	}

	// 2. HTML5 <details><summary>Question</summary>Answer</details>.
	if ( false !== strpos( $content, '<details' ) && false !== strpos( $content, '<summary' ) ) {
		$pattern_details = '/<details[^>]*>\s*<summary[^>]*>(.*?)<\/summary>(.*?)<\/details>/is';
		if ( preg_match_all( $pattern_details, $content, $matches_d, PREG_SET_ORDER ) ) {
			foreach ( $matches_d as $md ) {
				$q_text = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $md[1] ) ) );
				$a_text = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( $md[2] ) ) );
				$q_key  = strtolower( $q_text );
				if ( '' !== $q_text && '' !== $a_text && ! isset( $seen_q[ $q_key ] ) ) {
					$seen_q[ $q_key ] = true;
					$questions[]      = array(
						'@type'          => 'Question',
						'name'           => $q_text,
						'acceptedAnswer' => array(
							'@type' => 'Answer',
							'text'  => $a_text,
						),
					);
				}
			}
		}
	}

	if ( empty( $questions ) ) {
		return null;
	}

	$permalink = (string) get_permalink( $post );

	return array(
		'@type'      => 'FAQPage',
		'@id'        => $permalink . '#faq',
		'url'        => $permalink,
		'mainEntity' => $questions,
	);
}

/**
 * Build the `Product` Schema.org node for a single product post.
 *
 * @param int $product_id Product post ID.
 * @return array<string, mixed>|null
 */
function elemenane_build_product_schema_node( int $product_id ): ?array {
	$post = get_post( $product_id );
	if ( ! $post instanceof WP_Post || 'product' !== $post->post_type ) {
		return null;
	}

	$profile      = elemenane_get_company_profile();
	$home_url     = (string) $profile['home_url'];
	$org_id       = $home_url . '#organization';
	$company_name = (string) $profile['org_name'];
	$company_url  = (string) $profile['org_url'];

	$card_data = function_exists( 'elemenane_get_product_card_data' ) ? elemenane_get_product_card_data( $product_id ) : null;
	$title     = wp_strip_all_tags( get_the_title( $post ) );
	$desc      = elemenane_build_post_smart_description( $post );
	$permalink = (string) get_permalink( $post );
	$image_url = (string) get_the_post_thumbnail_url( $product_id, 'large' );
	if ( '' === $image_url ) {
		$image_url = (string) $profile['logo_url'];
	}

	$node = array(
		'@type'       => 'Product',
		'@id'         => $permalink . '#product',
		'name'        => $title,
		'description' => $desc,
		'url'         => $permalink,
		'image'       => $image_url,
		'brand'       => array(
			'@type' => 'Organization',
			'@id'   => $org_id,
			'name'  => $company_name,
			'url'   => $company_url,
		),
	);

	$sku = is_array( $card_data ) && ! empty( $card_data['sku'] )
		? (string) $card_data['sku']
		: (string) get_post_meta( $product_id, '_sku', true );
	if ( '' === $sku && function_exists( 'get_field' ) ) {
		$sku = (string) get_field( 'ane_product_sku', $product_id );
	}
	if ( '' !== $sku ) {
		$node['sku'] = $sku;
		$node['mpn'] = $sku;
	}

	if ( is_array( $card_data ) && $card_data['category'] instanceof WP_Term ) {
		$node['category'] = $card_data['category']->name;
	} else {
		$cats = get_the_terms( $product_id, 'product_cat' );
		if ( is_array( $cats ) && ! empty( $cats[0] ) && $cats[0] instanceof WP_Term ) {
			$node['category'] = $cats[0]->name;
		}
	}

	$price = is_array( $card_data ) ? (float) $card_data['price'] : 0.0;
	if ( $price <= 0 && function_exists( 'ane_get_product_price_data' ) ) {
		$p_data = ane_get_product_price_data( $product_id );
		$price  = (float) ( $p_data['price'] ?? 0.0 );
	}
	$stock_key    = is_array( $card_data ) ? (string) $card_data['stock'] : ( function_exists( 'ane_get_product_stock_status' ) ? ane_get_product_stock_status( $product_id ) : 'instock' );
	$availability = ( 'outofstock' === $stock_key ) ? 'OutOfStock' : 'InStock';

	if ( $price > 0 ) {
		$node['offers'] = array(
			'@type'           => 'Offer',
			'price'           => $price,
			'priceCurrency'   => 'IDR',
			'priceValidUntil' => gmdate( 'Y-m-d', strtotime( '+1 year' ) ),
			'availability'    => 'https://schema.org/' . $availability,
			'url'             => $permalink,
			'seller'          => array(
				'@type' => 'Organization',
				'@id'   => $org_id,
				'name'  => $company_name,
			),
		);
	}

	if ( is_array( $card_data ) && $card_data['rating_count'] > 0 && $card_data['rating'] > 0 ) {
		$node['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => round( (float) $card_data['rating'], 2 ),
			'reviewCount' => (int) $card_data['rating_count'],
		);
	}

	return $node;
}

/**
 * Build the `LocalBusiness` Schema.org node for a single branch post.
 *
 * @param int $branch_id Branch post ID.
 * @return array<string, mixed>|null
 */
function elemenane_build_branch_schema_node( int $branch_id ): ?array {
	$post = get_post( $branch_id );
	if ( ! $post instanceof WP_Post || 'branch' !== $post->post_type ) {
		return null;
	}

	$profile   = elemenane_get_company_profile();
	$home_url  = (string) $profile['home_url'];
	$org_id    = $home_url . '#organization';
	$permalink = (string) get_permalink( $post );
	$title     = wp_strip_all_tags( get_the_title( $post ) );
	$desc      = elemenane_build_post_smart_description( $post );
	$image_url = (string) get_the_post_thumbnail_url( $branch_id, 'large' );
	if ( '' === $image_url ) {
		$image_url = (string) $profile['logo_url'];
	}

	$b_meta = function_exists( 'elemenane_get_branch_meta' ) ? elemenane_get_branch_meta( $branch_id ) : array();

	$node = array(
		'@type'              => 'LocalBusiness',
		'@id'                => $permalink . '#branch',
		'name'               => $title,
		'description'        => $desc,
		'url'                => $permalink,
		'image'              => $image_url,
		'parentOrganization' => array( '@id' => $org_id ),
	);

	$address_parts = array();
	if ( ! empty( $b_meta['address'] ) ) {
		$address_parts['streetAddress'] = trim( wp_strip_all_tags( (string) $b_meta['address'] ) );
	}
	if ( ! empty( $b_meta['city_name'] ) ) {
		$address_parts['addressLocality'] = (string) $b_meta['city_name'];
	}
	if ( ! empty( $b_meta['province_name'] ) ) {
		$address_parts['addressRegion'] = (string) $b_meta['province_name'];
	}
	if ( ! empty( $address_parts ) ) {
		$node['address'] = array_merge(
			array(
				'@type'          => 'PostalAddress',
				'addressCountry' => 'ID',
			),
			$address_parts
		);
	}

	if ( ! empty( $b_meta['has_map'] ) && ! empty( $b_meta['lat'] ) && ! empty( $b_meta['lng'] ) ) {
		$node['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $b_meta['lat'],
			'longitude' => (float) $b_meta['lng'],
		);
	}

	if ( ! empty( $b_meta['phone'] ) ) {
		$node['telephone'] = trim( wp_strip_all_tags( (string) $b_meta['phone'] ) );
	} elseif ( ! empty( $b_meta['whatsapp'] ) ) {
		$node['telephone'] = elemenane_seo_normalize_phone( (string) $b_meta['whatsapp'] );
	}

	if ( ! empty( $b_meta['email'] ) ) {
		$node['email'] = sanitize_email( (string) $b_meta['email'] );
	}

	return $node;
}

/**
 * Build the complete JSON-LD `@graph` for Premium SEO, Google Sitelinks, & Knowledge Graph GEO.
 *
 * @return array<string, mixed>
 */
function elemenane_build_schema_graph(): array {
	$profile  = elemenane_get_company_profile();
	$home_url = (string) $profile['home_url'];
	$org_id   = $home_url . '#organization';
	$web_id   = $home_url . '#website';

	// 1. Node Organization (Knowledge Graph & Local SEO).
	$org_node = array(
		'@type' => 'Organization',
		'@id'   => $org_id,
		'name'  => (string) $profile['org_name'],
		'url'   => (string) $profile['org_url'],
		'logo'  => array(
			'@type' => 'ImageObject',
			'@id'   => $home_url . '#logo',
			'url'   => (string) $profile['logo_url'],
		),
		'image' => (string) $profile['logo_url'],
	);

	if ( '' !== (string) $profile['site_name'] && $profile['site_name'] !== $profile['org_name'] ) {
		$org_node['alternateName'] = (string) $profile['site_name'];
	}
	if ( '' !== (string) $profile['about'] ) {
		$org_node['description'] = (string) $profile['about'];
	} elseif ( '' !== (string) $profile['slogan'] ) {
		$org_node['description'] = (string) $profile['slogan'];
	}
	if ( '' !== (string) $profile['address'] ) {
		$org_node['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => (string) $profile['address'],
			'addressCountry' => 'ID',
		);
	}
	if ( '' !== (string) $profile['lat'] && '' !== (string) $profile['lng'] ) {
		$org_node['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $profile['lat'],
			'longitude' => (float) $profile['lng'],
		);
	}
	if ( '' !== (string) $profile['phone'] ) {
		$org_node['telephone'] = (string) $profile['phone'];
	} elseif ( '' !== (string) $profile['mobile'] ) {
		$org_node['telephone'] = elemenane_seo_normalize_phone( (string) $profile['mobile'] );
	}
	if ( '' !== (string) $profile['email'] ) {
		$org_node['email'] = (string) $profile['email'];
	}
	if ( ! empty( $profile['socials'] ) ) {
		$org_node['sameAs'] = $profile['socials'];
	}

	$contact_points = array();
	if ( '' !== (string) $profile['phone'] ) {
		$contact_points[] = array(
			'@type'             => 'ContactPoint',
			'telephone'         => (string) $profile['phone'],
			'contactType'       => 'customer service',
			'availableLanguage' => array( 'Indonesian' ),
		);
	}
	if ( ! empty( $profile['cs_list'] ) && is_array( $profile['cs_list'] ) ) {
		foreach ( $profile['cs_list'] as $cs ) {
			$contact_points[] = array(
				'@type'             => 'ContactPoint',
				'telephone'         => (string) $cs['phone'],
				'contactType'       => '' !== (string) $cs['role'] ? (string) $cs['role'] : 'customer support',
				'name'              => (string) $cs['name'],
				'availableLanguage' => array( 'Indonesian' ),
			);
		}
	}
	if ( ! empty( $contact_points ) ) {
		$org_node['contactPoint'] = $contact_points;
	}

	$graph = array( $org_node );

	// 2. Node WebSite + Sitelinks Searchbox.
	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => $web_id,
		'url'             => $home_url,
		'name'            => (string) $profile['org_name'],
		'alternateName'   => (string) $profile['site_name'],
		'description'     => (string) $profile['slogan'],
		'publisher'       => array( '@id' => $org_id ),
		'inLanguage'      => get_bloginfo( 'language' ),
		'potentialAction' => array(
			array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => $home_url . '?s={search_term_string}',
				),
				'query-input' => 'required name=search_term_string',
			),
		),
	);

	// 3. Node SiteNavigationElement from Main Menu (`menuutama`).
	$nav_items = elemenane_get_sitelinks_navigation();
	if ( ! empty( $nav_items ) ) {
		$nav_elements = array();
		$pos          = 1;
		foreach ( $nav_items as $nav ) {
			$nav_elements[] = array(
				'@type'    => 'SiteNavigationElement',
				'position' => $pos++,
				'name'     => $nav['name'],
				'url'      => $nav['url'],
			);
		}
		$graph[] = array(
			'@type'           => 'ItemList',
			'@id'             => $home_url . '#site-navigation',
			'name'            => __( 'Main Navigation', 'elemenane' ),
			'itemListElement' => $nav_elements,
		);
	}

	// 4. Node BreadcrumbList JSON-LD.
	$breadcrumb_node = elemenane_get_breadcrumb_schema_node();
	if ( is_array( $breadcrumb_node ) ) {
		$graph[] = $breadcrumb_node;
	}

	// 5. Entity-Specific Nodes (NewsArticle, Product, LocalBusiness Branch, WebPage/AboutPage/ContactPage, FAQPage).
	if ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
		$post    = get_post( $post_id );
		if ( $post instanceof WP_Post ) {
			$permalink = (string) get_permalink( $post );
			$title     = wp_strip_all_tags( get_the_title( $post ) );
			$desc      = elemenane_generate_smart_description();
			$img_url   = elemenane_get_seo_image_url();
			$keywords  = elemenane_get_current_keywords( $post_id );

			if ( 'post' === $post->post_type ) {
				$author_id   = (int) $post->post_author;
				$author_name = get_the_author_meta( 'display_name', $author_id );
				$cats        = get_the_category( $post_id );
				$sections    = is_array( $cats ) ? wp_list_pluck( $cats, 'name' ) : array();

				$article_node = array(
					'@type'            => array( 'NewsArticle', 'BlogPosting' ),
					'@id'              => $permalink . '#article',
					'mainEntityOfPage' => $permalink,
					'headline'         => $title,
					'description'      => $desc,
					'abstract'         => $desc,
					'image'            => array( $img_url ),
					'datePublished'    => (string) get_the_date( DATE_W3C, $post ),
					'dateModified'     => (string) get_the_modified_date( DATE_W3C, $post ),
					'author'           => array(
						'@type' => 'Person',
						'name'  => '' !== (string) $author_name ? (string) $author_name : (string) $profile['org_name'],
						'url'   => get_author_posts_url( $author_id ),
					),
					'publisher'        => array( '@id' => $org_id ),
					'isPartOf'         => array( '@id' => $web_id ),
					'inLanguage'       => get_bloginfo( 'language' ),
					'speakable'        => array(
						'@type'       => 'SpeakableSpecification',
						'cssSelector' => array( '.ane-single-head__title', '.post-title', '.ane-content > p:first-of-type' ),
					),
				);

				if ( ! empty( $keywords ) ) {
					$article_node['keywords'] = implode( ', ', $keywords );
				}
				if ( ! empty( $sections ) ) {
					$article_node['articleSection'] = array_values( $sections );
				}

				$graph[] = $article_node;
			} elseif ( 'product' === $post->post_type ) {
				$product_node = elemenane_build_product_schema_node( $post_id );
				if ( is_array( $product_node ) ) {
					$graph[] = $product_node;
				}
			} elseif ( 'branch' === $post->post_type ) {
				$branch_node = elemenane_build_branch_schema_node( $post_id );
				if ( is_array( $branch_node ) ) {
					$graph[] = $branch_node;
				}
			} elseif ( 'page' === $post->post_type ) {
				$schema_type = function_exists( 'get_field' ) ? (string) get_field( 'ane_page_schema_type', $post_id ) : '';
				if ( '' === $schema_type ) {
					$schema_type = 'WebPage';
				}
				if ( 'FAQPage' !== $schema_type ) {
					$page_node = array(
						'@type'         => $schema_type,
						'@id'           => $permalink . '#' . strtolower( $schema_type ),
						'url'           => $permalink,
						'name'          => $title,
						'description'   => $desc,
						'datePublished' => (string) get_the_date( DATE_W3C, $post ),
						'dateModified'  => (string) get_the_modified_date( DATE_W3C, $post ),
						'inLanguage'    => get_bloginfo( 'language' ),
						'isPartOf'      => array( '@id' => $web_id ),
					);
					if ( in_array( $schema_type, array( 'AboutPage', 'ContactPage', 'ProfilePage' ), true ) ) {
						$page_node['mainEntity'] = array( '@id' => $org_id );
					}
					$graph[] = $page_node;
				}
			}

			$faq_node = elemenane_extract_faq_schema_node( $post_id );
			if ( is_array( $faq_node ) ) {
				$graph[] = $faq_node;
			}
		}
	} elseif ( is_archive() || is_home() ) {
		$archive_url = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		$graph[]     = array(
			'@type'       => 'CollectionPage',
			'@id'         => $archive_url . '#collectionpage',
			'url'         => $archive_url,
			'name'        => wp_get_document_title(),
			'description' => elemenane_generate_smart_description(),
			'publisher'   => array( '@id' => $org_id ),
			'isPartOf'    => array( '@id' => $web_id ),
		);
	}

	return array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
}

/**
 * Enrich Yoast SEO's `@graph` (`wpseo_schema_graph`) with Elemen Ane's unique nodes
 * (`Organization` contactPoint/geo, `SiteNavigationElement`, `Product`, `LocalBusiness` branch,
 * `FAQPage`, plus GEO `abstract`, `keywords`, and `speakable` on `Article`/`NewsArticle`)
 * into a single unified `@graph` with 0 duplicate `WebSite` or `BreadcrumbList` blocks.
 *
 * @param array<int, array<string, mixed>> $graph   Yoast Schema nodes.
 * @param mixed                            $context Yoast indexable context.
 * @return array<int, array<string, mixed>>
 */
function elemenane_enrich_yoast_schema_graph( $graph, $context = null ) {
	unset( $context );
	if ( ! is_array( $graph ) ) {
		return $graph;
	}

	$GLOBALS['elemenane_yoast_schema_merged'] = true;

	$custom_schema = elemenane_build_schema_graph();
	$custom_nodes  = isset( $custom_schema['@graph'] ) && is_array( $custom_schema['@graph'] ) ? $custom_schema['@graph'] : array();
	$keywords      = elemenane_get_current_keywords();
	$smart_desc    = elemenane_generate_smart_description();

	// 1. Enrich Yoast's Article / NewsArticle / BlogPosting node with keywords, abstract, & speakable.
	foreach ( $graph as $idx => $node ) {
		if ( ! is_array( $node ) || empty( $node['@type'] ) ) {
			continue;
		}
		$types = (array) $node['@type'];
		if ( array_intersect( $types, array( 'Article', 'NewsArticle', 'BlogPosting' ) ) ) {
			if ( empty( $node['keywords'] ) && ! empty( $keywords ) ) {
				$graph[ $idx ]['keywords'] = implode( ', ', $keywords );
			}
			if ( empty( $node['abstract'] ) && '' !== $smart_desc ) {
				$graph[ $idx ]['abstract'] = $smart_desc;
			}
			if ( empty( $node['speakable'] ) ) {
				$graph[ $idx ]['speakable'] = array(
					'@type'       => 'SpeakableSpecification',
					'cssSelector' => array( '.ane-single-head__title', '.post-title', '.ane-content > p:first-of-type' ),
				);
			}
		}
	}

	// 2. Inject unique Elemen Ane nodes (Organization if missing, SiteNavigationElement, Product, LocalBusiness, FAQPage).
	$existing_ids = array();
	foreach ( $graph as $node ) {
		if ( is_array( $node ) && ! empty( $node['@id'] ) ) {
			$existing_ids[ (string) $node['@id'] ] = true;
		}
	}

	foreach ( $custom_nodes as $cnode ) {
		if ( ! is_array( $cnode ) || empty( $cnode['@type'] ) ) {
			continue;
		}
		$ctypes = (array) $cnode['@type'];
		if ( array_intersect( $ctypes, array( 'WebSite', 'BreadcrumbList', 'NewsArticle', 'CollectionPage', 'WebPage' ) ) ) {
			continue;
		}
		// Skip our Product node only if WooCommerce SEO already added a Product node.
		if ( in_array( 'Product', $ctypes, true ) && ane_is_woocommerce_active() && class_exists( 'WPSEO_WooCommerce_SEO' ) ) {
			continue;
		}
		$cid = isset( $cnode['@id'] ) ? (string) $cnode['@id'] : '';
		if ( '' !== $cid && isset( $existing_ids[ $cid ] ) ) {
			continue;
		}
		$graph[] = $cnode;
	}

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'elemenane_enrich_yoast_schema_graph', 20, 2 );

/**
 * ============================================================================
 * SEO & GEO HEAD RENDERING (ZERO DUPLICATION WITH YOAST)
 * ============================================================================
 */

/**
 * Check if Yoast SEO is handling Open Graph.
 *
 * @return bool
 */
function ane_yoast_handles_opengraph(): bool {
	if ( ! elemenane_is_yoast_active() ) {
		return false;
	}

	$options = get_option( 'wpseo_social' );
	return ! is_array( $options ) || ! isset( $options['opengraph'] ) || ! empty( $options['opengraph'] );
}

/**
 * Check if Yoast SEO is handling Twitter Cards.
 *
 * @return bool
 */
function ane_yoast_handles_twitter(): bool {
	if ( ! elemenane_is_yoast_active() ) {
		return false;
	}

	$options = get_option( 'wpseo_social' );
	return ! is_array( $options ) || ! isset( $options['twitter'] ) || ! empty( $options['twitter'] );
}

/**
 * Render all SEO & GEO metadata in `<head>`:
 * - Fallback meta description, canonical, OpenGraph, and Twitter Cards when Yoast is inactive
 * - Multi-layer `<meta name="keywords">` from Tags/Categories/Focus KW
 * - Dublin Core & Citation metadata on single posts for AI & academic attribution
 * - `<link rel="alternate">` discovery links for `/llms.txt` and `/llms-full.txt`
 * - Unified JSON-LD `@graph` when not already merged into Yoast's `wpseo_schema_graph`
 *
 * @return void
 */
function ane_add_seo_metadata() {
	if ( is_admin() || is_feed() || is_robots() ) {
		return;
	}

	$yoast_active = elemenane_is_yoast_active();
	$profile      = elemenane_get_company_profile();
	$description  = elemenane_generate_smart_description();
	$keywords     = elemenane_get_current_keywords();
	$img_url      = elemenane_get_seo_image_url();
	$llms_url     = home_url( '/llms.txt' );
	$llms_full    = home_url( '/llms-full.txt' );

	echo "\n<!-- Elemen Ane Premium SEO & GEO Engine -->\n";

	// 1. If Yoast is NOT active, output meta description, canonical, OpenGraph, and Twitter Cards.
	if ( ! $yoast_active ) {
		if ( '' !== $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		}

		$canonical = wp_get_canonical_url();
		if ( ! is_string( $canonical ) || '' === $canonical ) {
			$canonical = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
		}
		echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";

		$og_type  = is_singular( 'post' ) ? 'article' : 'website';
		$og_title = wp_get_document_title();

		echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
		if ( '' !== $description ) {
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_url( $canonical ) . '" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( (string) $profile['org_name'] ) . '" />' . "\n";
		if ( '' !== $img_url ) {
			echo '<meta property="og:image" content="' . esc_url( $img_url ) . '" />' . "\n";
		}
		if ( is_singular( 'post' ) ) {
			echo '<meta property="article:published_time" content="' . esc_attr( (string) get_the_date( DATE_W3C ) ) . '" />' . "\n";
			echo '<meta property="article:modified_time" content="' . esc_attr( (string) get_the_modified_date( DATE_W3C ) ) . '" />' . "\n";
		}

		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
		if ( '' !== $description ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
		}
		if ( '' !== $img_url ) {
			echo '<meta name="twitter:image" content="' . esc_url( $img_url ) . '" />' . "\n";
		}
	}

	// 2. Output <meta name="keywords"> from Tags + Categories + Yoast Focus KW.
	if ( ! empty( $keywords ) ) {
		echo '<meta name="keywords" content="' . esc_attr( implode( ', ', $keywords ) ) . '" />' . "\n";
	}

	// 3. If Yoast is not active on a single post, output <meta property="article:tag"> for each tag.
	if ( ! $yoast_active && is_singular( 'post' ) ) {
		$post_tags = get_the_tags();
		if ( is_array( $post_tags ) ) {
			foreach ( $post_tags as $ptag ) {
				if ( $ptag instanceof WP_Term ) {
					echo '<meta property="article:tag" content="' . esc_attr( $ptag->name ) . '" />' . "\n";
				}
			}
		}
	}

	// 4. Dublin Core & Citation metadata on single posts (AI attribution & academic citations).
	if ( is_singular( 'post' ) ) {
		ane_output_dublin_core_metadata();
		ane_output_citation_metadata();
	}

	// 5. GEO Discovery links (/llms.txt & /llms-full.txt) for AI Search Crawlers.
	echo '<link rel="alternate" type="text/plain" title="LLMs.txt - AI Knowledge Summary" href="' . esc_url( $llms_url ) . '" />' . "\n";
	echo '<link rel="alternate" type="text/plain" title="LLMs-Full.txt - Full AI Knowledge Base" href="' . esc_url( $llms_full ) . '" />' . "\n";

	// 6. Output unified JSON-LD Schema @graph when not already merged into Yoast's wpseo_schema_graph.
	if ( empty( $GLOBALS['elemenane_yoast_schema_merged'] ) ) {
		$schema_graph = elemenane_build_schema_graph();
		$json_ld      = wp_json_encode( $schema_graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG );
		if ( is_string( $json_ld ) && '' !== $json_ld ) {
			echo '<script type="application/ld+json" class="elemenane-schema-graph">' . $json_ld . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$GLOBALS['elemenane_schema_graph_printed'] = true;
		}
	}
}
add_action( 'wp_head', 'ane_add_seo_metadata', 5 );

/**
 * Output Dublin Core Metadata on single posts.
 *
 * @return void
 */
function ane_output_dublin_core_metadata() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$title          = wp_strip_all_tags( get_the_title() );
	$author         = get_the_author();
	$date_published = (string) get_the_date( 'Y-m-d' );
	$description    = elemenane_generate_smart_description();
	$permalink      = (string) get_permalink();
	$company_name   = ane_get_company_name();
	$categories     = get_the_category();
	$category_name  = ( is_array( $categories ) && ! empty( $categories[0] ) ) ? $categories[0]->name : '';

	echo '<meta name="DC.title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta name="DC.creator" content="' . esc_attr( $author ) . '" />' . "\n";
	echo '<meta name="DC.date" content="' . esc_attr( $date_published ) . '" />' . "\n";
	echo '<meta name="DC.description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta name="DC.identifier" content="' . esc_url( $permalink ) . '" />' . "\n";
	echo '<meta name="DC.publisher" content="' . esc_attr( $company_name ) . '" />' . "\n";
	echo '<meta name="DC.type" content="Text" />' . "\n";
	echo '<meta name="DC.format" content="text/html" />' . "\n";
	echo '<meta name="DC.language" content="' . esc_attr( get_bloginfo( 'language' ) ) . '" />' . "\n";

	if ( '' !== $category_name ) {
		echo '<meta name="DC.subject" content="' . esc_attr( $category_name ) . '" />' . "\n";
	}
}

/**
 * Output Citation Metadata on single posts.
 *
 * @return void
 */
function ane_output_citation_metadata() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id        = (int) get_the_ID();
	$title          = wp_strip_all_tags( get_the_title() );
	$author         = get_the_author();
	$date_published = (string) get_the_date( 'Y-m-d' );
	$permalink      = (string) get_permalink();
	$company_name   = ane_get_company_name();

	echo '<meta name="citation_title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta name="citation_author" content="' . esc_attr( $author ) . '" />' . "\n";
	echo '<meta name="citation_publication_date" content="' . esc_attr( $date_published ) . '" />' . "\n";
	echo '<meta name="citation_journal_title" content="' . esc_attr( $company_name ) . '" />' . "\n";
	echo '<meta name="citation_public_url" content="' . esc_url( $permalink ) . '" />' . "\n";

	$pdf_url = (string) get_post_meta( $post_id, 'citation_pdf_url', true );
	if ( '' !== $pdf_url ) {
		echo '<meta name="citation_pdf_url" content="' . esc_url( $pdf_url ) . '" />' . "\n";
	}
}

/**
 * Backward-compatible wrappers for individual schema/meta printers.
 * When `ane_add_seo_metadata()` is active, the unified `@graph` already outputs all nodes.
 */

/**
 * Legacy wrapper: NewsArticle schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_newsarticle_schema() {}

/**
 * Legacy wrapper: CollectionPage schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_collectionpage_schema() {}

/**
 * Legacy wrapper: Page schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_page_schema() {}

/**
 * Legacy wrapper: OpenGraph tags are now rendered by `ane_add_seo_metadata()`.
 *
 * @return void
 */
function ane_output_opengraph_tags() {}

/**
 * Legacy wrapper: Twitter Card tags are now rendered by `ane_add_seo_metadata()`.
 *
 * @return void
 */
function ane_output_twitter_card_tags() {}

/**
 * Legacy wrapper: BreadcrumbList schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_breadcrumb_schema() {}

/**
 * Legacy wrapper: WebSite schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_website_schema() {}

/**
 * Legacy wrapper called in `tp/header-default.php`: SiteNavigationElement is now part
 * of `elemenane_build_schema_graph()`, avoiding duplicate JSON-LD output in `<body>`.
 *
 * @return void
 */
function ane_output_navigation_schema() {}

/**
 * Legacy wrapper: Product schema is now part of `elemenane_build_schema_graph()`.
 *
 * @return void
 */
function ane_output_product_schema() {}

/**
 * Enhance RSS feed with featured image and full content.
 *
 * @param string $content Feed content.
 * @return string
 */
function ane_enhance_rss_feed( $content ) {
	if ( is_feed() ) {
		global $post;
		if ( $post instanceof WP_Post ) {
			$prefix = '';
			if ( has_post_thumbnail( $post->ID ) ) {
				$prefix = '<p>' . get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'max-width: 100%; height: auto;' ) ) . '</p>';
			}
			$content = $prefix . apply_filters( 'the_content', $post->post_content );
		}
	}

	return $content;
}
add_filter( 'the_excerpt_rss', 'ane_enhance_rss_feed' );
add_filter( 'the_content_feed', 'ane_enhance_rss_feed' );

/**
 * Automatically populate empty `alt` attributes on attachment images with post title + company name.
 *
 * @param array<string, string> $attr       Image HTML attributes.
 * @param WP_Post               $attachment Attachment post object.
 * @return array<string, string>
 */
function elemenane_auto_image_alt_attributes( $attr, $attachment ) {
	if ( ! is_array( $attr ) ) {
		return $attr;
	}

	$current_alt = isset( $attr['alt'] ) ? trim( (string) $attr['alt'] ) : '';
	if ( '' === $current_alt ) {
		$profile  = elemenane_get_company_profile();
		$fallback = '';
		if ( is_singular() ) {
			$fallback = trim( wp_strip_all_tags( get_the_title() ) );
		}
		if ( '' === $fallback && $attachment instanceof WP_Post && '' !== trim( $attachment->post_title ) ) {
			$fallback = trim( wp_strip_all_tags( $attachment->post_title ) );
		}
		if ( '' !== $fallback && '' !== (string) $profile['org_name'] ) {
			$fallback .= ' - ' . (string) $profile['org_name'];
		} elseif ( '' === $fallback ) {
			$fallback = (string) $profile['org_name'];
		}
		$attr['alt'] = $fallback;
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'elemenane_auto_image_alt_attributes', 10, 2 );

/**
 * Clean non-essential tags from `<head>` to keep markup lightweight and focused on SEO/GEO.
 *
 * @return void
 */
function elemenane_clean_wp_head_noise(): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
}
add_action( 'init', 'elemenane_clean_wp_head_noise' );

/**
 * ============================================================================
 * GENERATIVE ENGINE OPTIMIZATION (GEO): /llms.txt, /llms-full.txt & AI robots.txt
 * ============================================================================
 */

/**
 * Build the Markdown content for `/llms.txt` (summary) or `/llms-full.txt` (full knowledge base)
 * for Generative Engine Optimization (GEO) & LLM RAG systems (ChatGPT Search, Google AI Overviews,
 * Perplexity, Claude, Gemini).
 *
 * @param string $mode Output mode: `'summary'` (`/llms.txt`) or `'full'` (`/llms-full.txt`).
 * @return string
 */
function elemenane_generate_llms_txt_content( string $mode = 'summary' ): string {
	$is_full   = ( 'full' === $mode );
	$cache_key = $is_full ? 'elemenane_llms_full_txt_cache' : 'elemenane_llms_txt_cache';
	$cached    = get_transient( $cache_key );
	if ( false !== $cached && is_string( $cached ) && '' !== $cached ) {
		return $cached;
	}

	$profile = elemenane_get_company_profile();
	$lines   = array();

	$title_suffix = $is_full ? ' — Basis Pengetahuan Lengkap (LLMs Full)' : '';
	$lines[]      = '# ' . ( '' !== (string) $profile['org_name'] ? (string) $profile['org_name'] : (string) $profile['site_name'] ) . $title_suffix;
	$lines[]      = '';

	if ( '' !== (string) $profile['slogan'] ) {
		$lines[] = '> ' . (string) $profile['slogan'];
		$lines[] = '';
	}
	if ( '' !== (string) $profile['about'] ) {
		$lines[] = (string) $profile['about'];
		$lines[] = '';
	}

	$lines[] = '## Identitas & Kontak Resmi';
	$lines[] = '- Website Resmi: ' . (string) $profile['home_url'];
	$lines[] = '- Manifest Ringkas: ' . home_url( '/llms.txt' );
	$lines[] = '- Manifest Lengkap: ' . home_url( '/llms-full.txt' );
	if ( '' !== (string) $profile['address'] ) {
		$lines[] = '- Alamat: ' . (string) $profile['address'];
	}
	if ( '' !== (string) $profile['lat'] && '' !== (string) $profile['lng'] ) {
		$lines[] = '- Koordinat Peta (GPS): ' . (string) $profile['lat'] . ', ' . (string) $profile['lng'] . ' (https://www.google.com/maps/search/?api=1&query=' . rawurlencode( (string) $profile['lat'] . ',' . (string) $profile['lng'] ) . ')';
	}
	if ( '' !== (string) $profile['phone'] ) {
		$lines[] = '- Telepon Kantor: ' . (string) $profile['phone'];
	}
	if ( '' !== (string) $profile['mobile'] ) {
		$lines[] = '- Handphone / WhatsApp: ' . (string) $profile['mobile'];
	}
	if ( '' !== (string) $profile['email'] ) {
		$lines[] = '- Email Resmi: ' . (string) $profile['email'];
	}
	if ( ! empty( $profile['socials'] ) && is_array( $profile['socials'] ) ) {
		$lines[] = '- Media Sosial Resmi: ' . implode( ', ', $profile['socials'] );
	}
	if ( ! empty( $profile['cs_list'] ) && is_array( $profile['cs_list'] ) ) {
		foreach ( $profile['cs_list'] as $cs ) {
			$role_str = '' !== (string) $cs['role'] ? ' (' . (string) $cs['role'] . ')' : '';
			$lines[]  = '- Layanan Informasi / Customer Care: ' . (string) $cs['name'] . $role_str . ' — WhatsApp ' . (string) $cs['phone'];
		}
	}
	$lines[] = '';

	// Main Navigation (Sitelinks).
	$nav_items = elemenane_get_sitelinks_navigation();
	if ( ! empty( $nav_items ) ) {
		$lines[] = '## Halaman Utama (Sitelinks)';
		foreach ( $nav_items as $nav ) {
			$lines[] = '- [' . $nav['name'] . '](' . $nav['url'] . ')';
		}
		$lines[] = '';
	}

	// Static Pages (in full mode).
	if ( $is_full ) {
		$static_pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 20,
				'post_status'    => 'publish',
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			)
		);
		if ( is_array( $static_pages ) && ! empty( $static_pages ) ) {
			$lines[] = '## Profil & Halaman Informasi';
			foreach ( $static_pages as $sp ) {
				if ( $sp instanceof WP_Post ) {
					$sp_title = trim( wp_strip_all_tags( get_the_title( $sp ) ) );
					$sp_desc  = elemenane_seo_trim_description( '' !== $sp->post_excerpt ? $sp->post_excerpt : $sp->post_content, 260 );
					$lines[]  = '- [' . $sp_title . '](' . (string) get_permalink( $sp ) . ')' . ( '' !== $sp_desc ? ': ' . $sp_desc : '' );
				}
			}
			$lines[] = '';
		}
	}

	// Products (`product` CPT).
	if ( post_type_exists( 'product' ) ) {
		$product_posts = get_posts(
			array(
				'post_type'      => 'product',
				'posts_per_page' => $is_full ? 40 : 15,
				'post_status'    => 'publish',
			)
		);
		if ( is_array( $product_posts ) && ! empty( $product_posts ) ) {
			$lines[] = '## Katalog Produk';
			foreach ( $product_posts as $pr ) {
				if ( $pr instanceof WP_Post ) {
					$pr_title = trim( wp_strip_all_tags( get_the_title( $pr ) ) );
					$pr_desc  = elemenane_build_post_smart_description( $pr );
					$lines[]  = '- [' . $pr_title . '](' . (string) get_permalink( $pr ) . ')' . ( '' !== $pr_desc ? ': ' . $pr_desc : '' );
				}
			}
			$lines[] = '';
		}
	}

	// Branches (`branch` CPT).
	if ( post_type_exists( 'branch' ) ) {
		$branch_posts = get_posts(
			array(
				'post_type'      => 'branch',
				'posts_per_page' => $is_full ? 50 : 20,
				'post_status'    => 'publish',
			)
		);
		if ( is_array( $branch_posts ) && ! empty( $branch_posts ) ) {
			$lines[] = '## Jaringan Cabang & Lokasi';
			foreach ( $branch_posts as $br ) {
				if ( $br instanceof WP_Post ) {
					$br_title = trim( wp_strip_all_tags( get_the_title( $br ) ) );
					$br_desc  = elemenane_build_post_smart_description( $br );
					$lines[]  = '- [' . $br_title . '](' . (string) get_permalink( $br ) . ')' . ( '' !== $br_desc ? ': ' . $br_desc : '' );
				}
			}
			$lines[] = '';
		}
	}

	// Services (`service` CPT).
	if ( post_type_exists( 'service' ) ) {
		$service_posts = get_posts(
			array(
				'post_type'      => 'service',
				'posts_per_page' => $is_full ? 30 : 15,
				'post_status'    => 'publish',
			)
		);
		if ( is_array( $service_posts ) && ! empty( $service_posts ) ) {
			$lines[] = '## Layanan';
			foreach ( $service_posts as $sv ) {
				if ( $sv instanceof WP_Post ) {
					$sv_title = trim( wp_strip_all_tags( get_the_title( $sv ) ) );
					$sv_desc  = elemenane_seo_trim_description( '' !== $sv->post_excerpt ? $sv->post_excerpt : $sv->post_content, $is_full ? 240 : 110 );
					$lines[]  = '- [' . $sv_title . '](' . (string) get_permalink( $sv ) . ')' . ( '' !== $sv_desc ? ': ' . $sv_desc : '' );
				}
			}
			$lines[] = '';
		}
	}

	// Latest News & Articles (`post`).
	$recent_posts = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $is_full ? 30 : 10,
			'post_status'    => 'publish',
		)
	);
	if ( is_array( $recent_posts ) && ! empty( $recent_posts ) ) {
		$lines[] = '## Berita & Artikel Terbaru';
		foreach ( $recent_posts as $rp ) {
			if ( $rp instanceof WP_Post ) {
				$rp_title = trim( wp_strip_all_tags( get_the_title( $rp ) ) );
				$rp_date  = (string) get_the_date( 'Y-m-d', $rp );
				$rp_desc  = elemenane_seo_trim_description( '' !== $rp->post_excerpt ? $rp->post_excerpt : $rp->post_content, $is_full ? 280 : 110 );
				$lines[]  = '- [' . $rp_title . '](' . (string) get_permalink( $rp ) . ') (' . $rp_date . ')' . ( '' !== $rp_desc ? ': ' . $rp_desc : '' );
			}
		}
		$lines[] = '';
	}

	$output = implode( "\n", $lines );
	set_transient( $cache_key, $output, 6 * HOUR_IN_SECONDS );

	return $output;
}

/**
 * Flush `/llms.txt` and `/llms-full.txt` transient caches when a post or ACF option is saved.
 *
 * @return void
 */
function elemenane_flush_llms_txt_cache(): void {
	delete_transient( 'elemenane_llms_txt_cache' );
	delete_transient( 'elemenane_llms_full_txt_cache' );
}
add_action( 'save_post', 'elemenane_flush_llms_txt_cache' );
add_action( 'acf/save_post', 'elemenane_flush_llms_txt_cache' );

/**
 * Serve `/llms.txt`, `/llms-full.txt`, or `?llms_txt=1|full` dynamically without
 * requiring `.htaccess` changes or manual rewrite rule flushes.
 *
 * @return void
 */
function elemenane_serve_llms_txt_endpoint(): void {
	$req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$req_path = (string) wp_parse_url( $req_uri, PHP_URL_PATH );
	$q_param  = isset( $_GET['llms_txt'] ) ? sanitize_text_field( wp_unslash( $_GET['llms_txt'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$mode = '';
	if ( '/llms-full.txt' === substr( $req_path, -14 ) || 'full' === $q_param ) {
		$mode = 'full';
	} elseif ( '/llms.txt' === substr( $req_path, -9 ) || '1' === $q_param ) {
		$mode = 'summary';
	}

	if ( '' === $mode ) {
		return;
	}

	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: index, follow' );

	echo elemenane_generate_llms_txt_content( $mode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'init', 'elemenane_serve_llms_txt_endpoint', 1 );

/**
 * Configure explicit AI Search Crawler permissions (ChatGPT Search, Perplexity, Claude,
 * Google AI Overviews, Apple Intelligence) in `robots.txt` and block spam dataset scrapers.
 *
 * @param string $output    Default WordPress / Yoast `robots.txt` content.
 * @param string $is_public Search engine visibility ('1' or '0').
 * @return string
 */
function elemenane_seo_robots_txt( $output, $is_public ): string {
	if ( '0' === (string) $is_public ) {
		return (string) $output;
	}

	$ai_rules  = "\n# Elemen Ane GEO & AI Search Crawler Permissions\n";
	$ai_rules .= '# LLMs Manifest: ' . home_url( '/llms.txt' ) . "\n";
	$ai_rules .= '# LLMs Full Manifest: ' . home_url( '/llms-full.txt' ) . "\n";

	$allowed_bots = array(
		'OAI-SearchBot',
		'GPTBot',
		'ChatGPT-User',
		'PerplexityBot',
		'ClaudeBot',
		'Google-Extended',
		'Applebot-Extended',
	);
	foreach ( $allowed_bots as $bot ) {
		if ( false === stripos( (string) $output, 'User-agent: ' . $bot ) ) {
			$ai_rules .= 'User-agent: ' . $bot . "\nAllow: /\n\n";
		}
	}

	$blocked_bots = array(
		'CCBot',
		'Bytespider',
	);
	foreach ( $blocked_bots as $bad_bot ) {
		if ( false === stripos( (string) $output, 'User-agent: ' . $bad_bot ) ) {
			$ai_rules .= 'User-agent: ' . $bad_bot . "\nDisallow: /\n\n";
		}
	}

	if ( false === stripos( (string) $output, 'Sitemap:' ) ) {
		$sitemap_url = elemenane_is_yoast_active() ? home_url( '/sitemap_index.xml' ) : home_url( '/wp-sitemap.xml' );
		$ai_rules   .= 'Sitemap: ' . $sitemap_url . "\n";
	}

	return rtrim( (string) $output ) . "\n" . $ai_rules;
}
add_filter( 'robots_txt', 'elemenane_seo_robots_txt', 20, 2 );

/**
 * ============================================================================
 * AUTO-SYNC YOAST FOCUS KEYPHRASE & META DESCRIPTION ON SAVE
 * ============================================================================
 */

/**
 * Auto-fill `_yoast_wpseo_focuskw` and `_yoast_wpseo_metadesc` when a post/CPT
 * is published or updated and the editor left them blank.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function elemenane_sync_yoast_meta_on_save( $post_id, $post ): void {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
		return;
	}

	$allowed_types = array( 'post', 'page', 'product', 'branch', 'service' );
	if ( ! in_array( $post->post_type, $allowed_types, true ) ) {
		return;
	}

	// 1. Auto-fill Yoast Focus Keyphrase if empty.
	$focus_kw = trim( (string) get_post_meta( $post_id, '_yoast_wpseo_focuskw', true ) );
	if ( '' === $focus_kw ) {
		$keywords = elemenane_get_current_keywords( (int) $post_id );
		if ( ! empty( $keywords[0] ) ) {
			$focus_kw = sanitize_text_field( $keywords[0] );
		} else {
			$focus_kw = sanitize_text_field( wp_strip_all_tags( get_the_title( $post ) ) );
		}
		if ( '' !== $focus_kw ) {
			update_post_meta( $post_id, '_yoast_wpseo_focuskw', $focus_kw );
		}
	}

	// 2. Auto-fill Yoast Meta Description if empty.
	$meta_desc = trim( (string) get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) );
	if ( '' === $meta_desc ) {
		$smart_desc = elemenane_build_post_smart_description( $post );
		if ( '' !== $smart_desc ) {
			update_post_meta( $post_id, '_yoast_wpseo_metadesc', sanitize_text_field( $smart_desc ) );
		}
	}
}
add_action( 'wp_after_insert_post', 'elemenane_sync_yoast_meta_on_save', 20, 2 );

/**
 * ============================================================================
 * SMART 301 AUTO-REDIRECT ON SLUG CHANGE (0 OVERHEAD ON 200 OK PAGES)
 * ============================================================================
 */

/**
 * Record slug/URL changes on published posts, pages, products, branches, or services
 * so old URLs automatically 301-redirect to the new URL (capped at 200 entries with
 * `autoload = false` so normal page loads have 0 extra memory overhead).
 *
 * @param int     $post_id     Post ID.
 * @param WP_Post $post_after  Post object after update.
 * @param WP_Post $post_before Post object before update.
 * @return void
 */
function elemenane_log_slug_change_redirect( $post_id, $post_after, $post_before ): void {
	if ( ! $post_after instanceof WP_Post || ! $post_before instanceof WP_Post ) {
		return;
	}
	if ( 'publish' !== $post_after->post_status || 'publish' !== $post_before->post_status ) {
		return;
	}
	if ( $post_before->post_name === $post_after->post_name || '' === $post_before->post_name || '' === $post_after->post_name ) {
		return;
	}

	$allowed_types = array( 'post', 'page', 'product', 'branch', 'service' );
	if ( ! in_array( $post_after->post_type, $allowed_types, true ) ) {
		return;
	}

	$new_permalink = (string) get_permalink( $post_id );
	$new_path      = (string) wp_parse_url( $new_permalink, PHP_URL_PATH );
	if ( '' === $new_path ) {
		return;
	}

	$search_slug  = '/' . rawurlencode( $post_after->post_name ) . '/';
	$replace_slug = '/' . rawurlencode( $post_before->post_name ) . '/';
	if ( false !== strpos( trailingslashit( $new_path ), $search_slug ) ) {
		$old_path = str_replace( $search_slug, $replace_slug, trailingslashit( $new_path ) );
	} else {
		$old_path = trailingslashit( '/' . rawurlencode( $post_before->post_name ) );
	}

	$old_path = '/' . ltrim( trailingslashit( $old_path ), '/' );
	$new_norm = '/' . ltrim( trailingslashit( $new_path ), '/' );
	if ( $old_path === $new_norm ) {
		return;
	}

	$redirects = get_option( 'elemenane_seo_redirects', array() );
	if ( ! is_array( $redirects ) ) {
		$redirects = array();
	}

	unset( $redirects[ $old_path ], $redirects[ $new_norm ] );
	$redirects[ $old_path ] = (int) $post_id;

	if ( count( $redirects ) > 200 ) {
		$redirects = array_slice( $redirects, -200, 200, true );
	}

	update_option( 'elemenane_seo_redirects', $redirects, false );
}
add_action( 'post_updated', 'elemenane_log_slug_change_redirect', 10, 3 );

/**
 * Execute HTTP 301 redirect for changed slugs ONLY when a request results in 404 (`is_404()`),
 * ensuring 0 overhead on normal 200 OK requests.
 *
 * @return void
 */
function elemenane_execute_404_slug_redirect(): void {
	if ( ! is_404() || is_admin() ) {
		return;
	}

	$req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$req_path = (string) wp_parse_url( $req_uri, PHP_URL_PATH );
	if ( '' === $req_path ) {
		return;
	}

	$norm_path = '/' . ltrim( trailingslashit( $req_path ), '/' );
	$redirects = get_option( 'elemenane_seo_redirects', array() );
	if ( ! is_array( $redirects ) || empty( $redirects[ $norm_path ] ) ) {
		return;
	}

	$target_id   = (int) $redirects[ $norm_path ];
	$target_post = $target_id > 0 ? get_post( $target_id ) : null;
	if ( ! $target_post instanceof WP_Post || 'publish' !== $target_post->post_status ) {
		return;
	}

	$target_url  = (string) get_permalink( $target_post );
	$target_path = '/' . ltrim( trailingslashit( (string) wp_parse_url( $target_url, PHP_URL_PATH ) ), '/' );
	if ( '' === $target_url || $target_path === $norm_path ) {
		return;
	}

	wp_safe_redirect( $target_url, 301 );
	exit;
}
add_action( 'template_redirect', 'elemenane_execute_404_slug_redirect', 1 );
