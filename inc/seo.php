<?php
/**
 * SEO Enhancement Module
 *
 * Adds premium SEO features to complement Yoast SEO Free:
 * - NewsArticle schema markup
 * - Dublin Core metadata
 * - Citation metadata
 * - Enhanced Open Graph tags
 * - Twitter Card optimization
 *
 * @package elemenane
 * @since 1.0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * ============================================================================
 * HELPER FUNCTIONS - Company Information from ACF Options
 * ============================================================================
 */

/**
 * Get company name from ACF options
 *
 * @return string Company name
 */
function ane_get_company_name() : string {
	return get_field( 'ane_com_nama', 'option' ) ?: get_bloginfo( 'name' );
}

/**
 * Get company description from ACF options
 *
 * @return string Company description
 */
function ane_get_company_description() : string {
	return get_field( 'ane_com_des', 'option' ) ?: get_bloginfo( 'description' );
}

/**
 * Get company URL from ACF options
 *
 * @return string Company URL
 */
function ane_get_company_url() : string {
	return get_field( 'ane_com_link', 'option' ) ?: home_url();
}

/**
 * Get company address from ACF options
 *
 * @return string Company address
 */
function ane_get_company_address() : string {
	return get_field( 'ane_com_alamat', 'option' ) ?: '';
}

/**
 * Get company phone from ACF options
 *
 * @return string Company phone number
 */
function ane_get_company_phone() : string {
	return get_field( 'ane_telepon', 'option' ) ?: '';
}

/**
 * Get company mobile/handphone from ACF options
 *
 * @return string Company mobile number
 */
function ane_get_company_mobile() : string {
	$kontak = get_field( 'ane_kontak', 'option' );
	return $kontak['ane_handphone'] ?? '';
}

/**
 * Get company email from ACF options
 *
 * @return string Company email
 */
function ane_get_company_email() : string {
	$kontak = get_field( 'ane_kontak', 'option' );
	return $kontak['ane_email'] ?? get_bloginfo( 'admin_email' );
}

/**
 * Get company website from ACF options
 *
 * @return string Company website URL
 */
function ane_get_company_website() : string {
	$kontak = get_field( 'ane_kontak', 'option' );
	return $kontak['ane_website'] ?? home_url();
}

/**
 * Get company Google Maps data from ACF options
 *
 * @return array|false Google Maps data (address, lat, lng) or false
 */
function ane_get_company_location() {
	return get_field( 'ane_gmap', 'option' ) ?: false;
}

/**
 * Get company logo URL
 *
 * @param string $size Image size (default: 'full')
 * @return string Logo URL
 */
function ane_get_company_logo( $size = 'full' ) : string {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, $size );
		if ( $logo_url ) {
			return $logo_url;
		}
	}
	return get_template_directory_uri() . '/img/logo-webane.png';
}

/**
 * Get full company contact information as array
 *
 * @return array Complete company contact information
 */
function ane_get_company_info() : array {
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
 * SEO METADATA OUTPUT FUNCTIONS
 * ============================================================================
 */

/**
 * Add all SEO metadata to <head>
 * Only runs on single posts and archive pages
 * Checks for Yoast SEO to avoid conflicts
 */
function ane_add_seo_metadata() {
	// Only add on single posts, archives, and pages
	if ( ! is_single() && ! is_archive() && ! is_home() && ! is_page() ) {
		return;
	}

	// Add NewsArticle schema for single posts
	if ( is_single() && get_post_type() === 'post' ) {
		ane_output_newsarticle_schema();
	}

	// Add CollectionPage schema for archives
	if ( is_archive() || is_home() ) {
		ane_output_collectionpage_schema();
	}

	// Add Page schema for pages
	if ( is_page() ) {
		ane_output_page_schema();
	}

	// Add Dublin Core metadata
	ane_output_dublin_core_metadata();

	// Add Citation metadata
	ane_output_citation_metadata();

	// Add Open Graph tags (only if Yoast is not handling it)
	if ( ! ane_yoast_handles_opengraph() ) {
		ane_output_opengraph_tags();
	}

	// Add Twitter Card tags (only if Yoast is not handling it)
	if ( ! ane_yoast_handles_twitter() ) {
		ane_output_twitter_card_tags();
	}

	// Add Breadcrumb schema (for Google Sitelinks)
	ane_output_breadcrumb_schema();

	// Add WebSite schema with SearchAction (for Google Search Box)
	ane_output_website_schema();
}
add_action( 'wp_head', 'ane_add_seo_metadata', 5 );

/**
 * Check if Yoast SEO is handling Open Graph
 *
 * @return bool True if Yoast is active and OpenGraph is enabled
 */
function ane_yoast_handles_opengraph() : bool {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return false;
	}

	$options = get_option( 'wpseo_social' );
	return ! empty( $options['opengraph'] );
}

/**
 * Check if Yoast SEO is handling Twitter Cards
 *
 * @return bool True if Yoast is active and Twitter is enabled
 */
function ane_yoast_handles_twitter() : bool {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return false;
	}

	$options = get_option( 'wpseo_social' );
	return ! empty( $options['twitter'] );
}

/**
 * Output NewsArticle Schema (JSON-LD)
 * Google News and rich snippets optimization
 */
function ane_output_newsarticle_schema() {
	if ( ! is_single() || get_post_type() !== 'post' ) {
		return;
	}

	global $post;
	$post_id = get_the_ID();

	// Get post data
	$title           = get_the_title();
	$excerpt         = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
	$permalink       = get_permalink();
	$date_published  = get_the_date( 'c' );
	$date_modified   = get_the_modified_date( 'c' );
	$author_name     = get_the_author();
	$author_url      = get_author_posts_url( get_the_author_meta( 'ID' ) );

	// Get featured image
	$image_url    = get_the_post_thumbnail_url( $post_id, 'large' );
	$image_width  = 1200;
	$image_height = 675;

	if ( has_post_thumbnail() ) {
		$image_id     = get_post_thumbnail_id( $post_id );
		$image_meta   = wp_get_attachment_metadata( $image_id );
		$image_width  = $image_meta['width'] ?? 1200;
		$image_height = $image_meta['height'] ?? 675;
	} else {
		// Fallback to company logo
		$image_url = ane_get_company_logo( 'large' );
	}

	// Get company info from ACF options
	$company_name = ane_get_company_name();
	$company_url  = ane_get_company_url();
	$company_logo = ane_get_company_logo();

	// Get categories
	$categories      = get_the_category();
	$category_name   = ! empty( $categories ) ? $categories[0]->name : 'News';
	$category_url    = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '';

	// Build schema
	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'NewsArticle',
		'@id'              => $permalink . '#newsarticle',
		'headline'         => $title,
		'description'      => $excerpt,
		'url'              => $permalink,
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => $permalink,
		),
		'datePublished'    => $date_published,
		'dateModified'     => $date_modified,
		'author'           => array(
			'@type' => 'Person',
			'name'  => $author_name,
			'url'   => $author_url,
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => $company_name,
			'url'   => $company_url,
			'logo'  => array(
				'@type'  => 'ImageObject',
				'url'    => $company_logo,
				'width'  => 600,
				'height' => 60,
			),
		),
		'image'            => array(
			'@type'  => 'ImageObject',
			'url'    => $image_url,
			'width'  => $image_width,
			'height' => $image_height,
		),
	);

	// Add article section (category)
	if ( ! empty( $category_name ) ) {
		$schema['articleSection'] = $category_name;
	}

	// Add keywords from tags
	$tags = get_the_tags();
	if ( $tags ) {
		$keywords = array();
		foreach ( $tags as $tag ) {
			$keywords[] = $tag->name;
		}
		$schema['keywords'] = implode( ', ', $keywords );
	}

	// Output JSON-LD
	echo "\n<!-- NewsArticle Schema by Elemen Ane -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}

/**
 * Output CollectionPage Schema (JSON-LD)
 * For archive pages (category, tag, author, date, homepage)
 * Optimized for Google News and search engines
 */
function ane_output_collectionpage_schema() {
	if ( ! is_archive() && ! is_home() ) {
		return;
	}

	// Get company info
	$company_name = ane_get_company_name();
	$company_url  = ane_get_company_url();
	$company_logo = ane_get_company_logo();

	// Determine archive type and metadata
	$page_title       = '';
	$page_description = '';
	$page_url         = '';
	$breadcrumb_items = array();

	if ( is_home() ) {
		$page_title       = get_bloginfo( 'name' );
		$page_description = get_bloginfo( 'description' );
		$page_url         = home_url();
	} elseif ( is_category() ) {
		$category         = get_queried_object();
		$page_title       = $category->name;
		$page_description = $category->description ?: 'Artikel kategori ' . $category->name;
		$page_url         = get_category_link( $category->term_id );
	} elseif ( is_tag() ) {
		$tag              = get_queried_object();
		$page_title       = $tag->name;
		$page_description = $tag->description ?: 'Artikel dengan tag ' . $tag->name;
		$page_url         = get_tag_link( $tag->term_id );
	} elseif ( is_author() ) {
		$author           = get_queried_object();
		$page_title       = 'Artikel oleh ' . $author->display_name;
		$page_description = $author->description ?: 'Semua artikel oleh ' . $author->display_name;
		$page_url         = get_author_posts_url( $author->ID );
	} elseif ( is_date() ) {
		$page_title       = get_the_archive_title();
		$page_description = get_the_archive_description();
		$page_url         = get_pagenum_link();
	}

	// Build schema
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'CollectionPage',
		'@id'         => $page_url . '#collectionpage',
		'url'         => $page_url,
		'name'        => $page_title,
		'description' => $page_description,
		'publisher'   => array(
			'@type' => 'Organization',
			'name'  => $company_name,
			'url'   => $company_url,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => $company_logo,
			),
		),
		'isPartOf'    => array(
			'@type' => 'WebSite',
			'@id'   => home_url() . '#website',
			'url'   => home_url(),
			'name'  => $company_name,
		),
	);

	// Add article items for current page
	global $wp_query;
	if ( $wp_query->have_posts() ) {
		$items = array();
		$position = 1;

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$post_id = get_the_ID();

			$item = array(
				'@type'            => 'ListItem',
				'position'         => $position++,
				'item'             => array(
					'@type'         => 'NewsArticle',
					'@id'           => get_permalink() . '#newsarticle',
					'url'           => get_permalink(),
					'headline'      => get_the_title(),
					'datePublished' => get_the_date( 'c' ),
					'dateModified'  => get_the_modified_date( 'c' ),
					'author'        => array(
						'@type' => 'Person',
						'name'  => get_the_author(),
					),
				),
			);

			// Add featured image if exists
			if ( has_post_thumbnail( $post_id ) ) {
				$item['item']['image'] = array(
					'@type' => 'ImageObject',
					'url'   => get_the_post_thumbnail_url( $post_id, 'large' ),
				);
			}

			$items[] = $item;
		}

		wp_reset_postdata();

		// Add itemListElement
		if ( ! empty( $items ) ) {
			$schema['mainEntity'] = array(
				'@type'           => 'ItemList',
				'itemListElement' => $items,
				'numberOfItems'   => count( $items ),
			);
		}
	}

	// Output JSON-LD
	echo "\n<!-- CollectionPage Schema by Elemen Ane -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}

/**
 * Output Page Schema
 * Supports multiple schema.org types based on ACF field selection
 */
function ane_output_page_schema() {
	if ( ! is_page() ) {
		return;
	}

	$page_id = get_the_ID();

	// Get schema type from ACF field (default: WebPage)
	$schema_type = get_field( 'ane_page_schema_type', $page_id ) ?: 'WebPage';

	// Get page data
	$title       = get_the_title();
	$description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 55 );
	$permalink   = get_permalink();
	$date_published = get_the_date( 'c' );
	$date_modified  = get_the_modified_date( 'c' );

	// Get featured image
	$image_url = get_the_post_thumbnail_url( $page_id, 'large' );
	if ( ! $image_url ) {
		$image_url = ane_get_company_logo( 'large' );
	}

	// Get company info
	$company_name = ane_get_company_name();
	$company_url  = ane_get_company_url();
	$company_logo = ane_get_company_logo();

	// Base schema structure (common to all types)
	$schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => $schema_type,
		'@id'           => $permalink . '#' . strtolower( $schema_type ),
		'url'           => $permalink,
		'name'          => $title,
		'description'   => $description,
		'datePublished' => $date_published,
		'dateModified'  => $date_modified,
		'inLanguage'    => get_bloginfo( 'language' ),
		'isPartOf'      => array(
			'@type' => 'WebSite',
			'@id'   => home_url() . '#website',
			'url'   => home_url(),
			'name'  => $company_name,
		),
	);

	// Add image if available
	if ( $image_url ) {
		$schema['image'] = array(
			'@type' => 'ImageObject',
			'url'   => $image_url,
		);
	}

	// Add schema-specific properties based on type
	switch ( $schema_type ) {
		case 'AboutPage':
			// About pages can include organization info
			$schema['about'] = array(
				'@type' => 'Organization',
				'name'  => $company_name,
				'url'   => $company_url,
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => $company_logo,
				),
			);
			if ( ane_get_company_description() ) {
				$schema['about']['description'] = ane_get_company_description();
			}
			break;

		case 'ContactPage':
			// Contact pages include organization contact info
			$contact_info = array(
				'@type' => 'Organization',
				'name'  => $company_name,
				'url'   => $company_url,
			);

			// Add contact points
			$contact_points = array();

			if ( ane_get_company_phone() ) {
				$contact_points[] = array(
					'@type'       => 'ContactPoint',
					'telephone'   => ane_get_company_phone(),
					'contactType' => 'customer service',
				);
			}

			if ( ane_get_company_mobile() ) {
				$contact_points[] = array(
					'@type'       => 'ContactPoint',
					'telephone'   => ane_get_company_mobile(),
					'contactType' => 'customer service',
				);
			}

			if ( ane_get_company_email() ) {
				$contact_info['email'] = ane_get_company_email();
			}

			if ( ane_get_company_address() ) {
				$contact_info['address'] = array(
					'@type'         => 'PostalAddress',
					'streetAddress' => ane_get_company_address(),
				);
			}

			if ( ! empty( $contact_points ) ) {
				$contact_info['contactPoint'] = $contact_points;
			}

			$schema['mainEntity'] = $contact_info;
			break;

		case 'FAQPage':
			// FAQ pages should have Question/Answer items
			// Check if content has FAQ blocks or list items
			$content = get_the_content();
			$schema['mainEntity'] = array(); // Will be populated by user with FAQ blocks

			// Note: This requires proper FAQ structured content
			// Users should add FAQ blocks to populate this automatically
			break;

		case 'ProfilePage':
			// Profile pages represent a person or organization
			$schema['mainEntity'] = array(
				'@type' => 'Organization',
				'name'  => $company_name,
				'url'   => $company_url,
				'logo'  => array(
					'@type' => 'ImageObject',
					'url'   => $company_logo,
				),
			);
			break;

		case 'SearchResultsPage':
			// Search results page
			$schema['@type'] = 'SearchResultsPage';
			break;

		case 'CollectionPage':
			// Collection page for grouped content
			$schema['@type'] = 'CollectionPage';
			break;

		case 'ItemPage':
			// Generic item page
			$schema['@type'] = 'ItemPage';
			break;

		case 'WebPage':
		default:
			// Default WebPage - no additional properties needed
			break;
	}

	// Output JSON-LD
	echo "\n<!-- Page Schema ({$schema_type}) by Elemen Ane -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}

/**
 * Output Dublin Core Metadata
 * For academic/news citations and AI crawlers
 */
function ane_output_dublin_core_metadata() {
	if ( is_single() && get_post_type() === 'post' ) {
		$post_id        = get_the_ID();
		$title          = get_the_title();
		$author         = get_the_author();
		$date_published = get_the_date( 'Y-m-d' );
		$description    = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
		$permalink      = get_permalink();
		$company_name   = ane_get_company_name();

		// Get categories
		$categories    = get_the_category();
		$category_name = ! empty( $categories ) ? $categories[0]->name : '';

		echo "\n<!-- Dublin Core Metadata -->\n";
		echo '<meta name="DC.title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="DC.creator" content="' . esc_attr( $author ) . '">' . "\n";
		echo '<meta name="DC.date" content="' . esc_attr( $date_published ) . '">' . "\n";
		echo '<meta name="DC.description" content="' . esc_attr( $description ) . '">' . "\n";
		echo '<meta name="DC.identifier" content="' . esc_url( $permalink ) . '">' . "\n";
		echo '<meta name="DC.publisher" content="' . esc_attr( $company_name ) . '">' . "\n";
		echo '<meta name="DC.type" content="Text">' . "\n";
		echo '<meta name="DC.format" content="text/html">' . "\n";
		echo '<meta name="DC.language" content="' . esc_attr( get_bloginfo( 'language' ) ) . '">' . "\n";

		if ( ! empty( $category_name ) ) {
			echo '<meta name="DC.subject" content="' . esc_attr( $category_name ) . '">' . "\n";
		}
	}
}

/**
 * Output Citation Metadata
 * For proper attribution in AI models and academic citations
 */
function ane_output_citation_metadata() {
	if ( is_single() && get_post_type() === 'post' ) {
		$post_id        = get_the_ID();
		$title          = get_the_title();
		$author         = get_the_author();
		$date_published = get_the_date( 'Y-m-d' );
		$permalink      = get_permalink();
		$company_name   = ane_get_company_name();

		echo "\n<!-- Citation Metadata -->\n";
		echo '<meta name="citation_title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta name="citation_author" content="' . esc_attr( $author ) . '">' . "\n";
		echo '<meta name="citation_publication_date" content="' . esc_attr( $date_published ) . '">' . "\n";
		echo '<meta name="citation_journal_title" content="' . esc_attr( $company_name ) . '">' . "\n";
		echo '<meta name="citation_public_url" content="' . esc_url( $permalink ) . '">' . "\n";

		// Add PDF URL if exists (for academic papers)
		$pdf_url = get_post_meta( $post_id, 'citation_pdf_url', true );
		if ( ! empty( $pdf_url ) ) {
			echo '<meta name="citation_pdf_url" content="' . esc_url( $pdf_url ) . '">' . "\n";
		}
	}
}

/**
 * Output Open Graph Tags
 * For Facebook, LinkedIn, and other social platforms
 */
function ane_output_opengraph_tags() {
	$og_type        = 'website';
	$og_title       = get_bloginfo( 'name' );
	$og_description = get_bloginfo( 'description' );
	$og_url         = home_url();
	$og_image       = get_template_directory_uri() . '/img/logo-webane.png';
	$og_site_name   = get_bloginfo( 'name' );

	if ( is_single() ) {
		$post_id        = get_the_ID();
		$og_type        = 'article';
		$og_title       = get_the_title();
		$og_description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
		$og_url         = get_permalink();

		if ( has_post_thumbnail() ) {
			$og_image = get_the_post_thumbnail_url( $post_id, 'large' );
		}

		// Article specific tags
		$date_published = get_the_date( 'c' );
		$date_modified  = get_the_modified_date( 'c' );
		$author_name    = get_the_author();

		$categories = get_the_category();
		$section    = ! empty( $categories ) ? $categories[0]->name : '';

		echo "\n<!-- Open Graph Article Tags -->\n";
		echo '<meta property="article:published_time" content="' . esc_attr( $date_published ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( $date_modified ) . '">' . "\n";
		echo '<meta property="article:author" content="' . esc_attr( $author_name ) . '">' . "\n";

		if ( ! empty( $section ) ) {
			echo '<meta property="article:section" content="' . esc_attr( $section ) . '">' . "\n";
		}

		$tags = get_the_tags();
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '">' . "\n";
			}
		}
	} elseif ( is_archive() ) {
		if ( is_category() ) {
			$category       = get_queried_object();
			$og_title       = $category->name;
			$og_description = $category->description ?: 'Artikel kategori ' . $category->name;
			$og_url         = get_category_link( $category->term_id );
		} elseif ( is_tag() ) {
			$tag            = get_queried_object();
			$og_title       = $tag->name;
			$og_description = $tag->description ?: 'Artikel dengan tag ' . $tag->name;
			$og_url         = get_tag_link( $tag->term_id );
		} elseif ( is_author() ) {
			$author         = get_queried_object();
			$og_title       = 'Artikel oleh ' . $author->display_name;
			$og_description = $author->description ?: 'Semua artikel oleh ' . $author->display_name;
			$og_url         = get_author_posts_url( $author->ID );
		}
	}

	echo "\n<!-- Open Graph Tags -->\n";
	echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $og_site_name ) . '">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) . '">' . "\n";
}

/**
 * Output Twitter Card Tags
 * For Twitter sharing optimization
 */
function ane_output_twitter_card_tags() {
	$card_type      = 'summary_large_image';
	$title          = get_bloginfo( 'name' );
	$description    = get_bloginfo( 'description' );
	$image          = get_template_directory_uri() . '/img/logo-webane.png';
	$twitter_handle = get_option( 'ane_twitter_handle', '' );

	if ( is_single() ) {
		$post_id     = get_the_ID();
		$title       = get_the_title();
		$description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );

		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( $post_id, 'large' );
		}
	}

	echo "\n<!-- Twitter Card Tags -->\n";
	echo '<meta name="twitter:card" content="' . esc_attr( $card_type ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";

	if ( ! empty( $twitter_handle ) ) {
		echo '<meta name="twitter:site" content="@' . esc_attr( $twitter_handle ) . '">' . "\n";
		echo '<meta name="twitter:creator" content="@' . esc_attr( $twitter_handle ) . '">' . "\n";
	}
}

/**
 * Add robots meta tag with index,follow for freshness
 * Only if Yoast SEO is not active
 */
function ane_add_robots_meta() {
	// Skip if Yoast SEO is handling robots meta
	if ( defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	if ( is_single() && get_post_type() === 'post' ) {
		echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
	}
}
add_action( 'wp_head', 'ane_add_robots_meta', 1 );

/**
 * Enhance RSS feed with full content
 */
function ane_enhance_rss_feed( $content ) {
	if ( is_feed() ) {
		global $post;

		// Add featured image to RSS
		if ( has_post_thumbnail( $post->ID ) ) {
			$content = '<p>' . get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'max-width: 100%; height: auto;' ) ) . '</p>' . $content;
		}

		// Add full content instead of excerpt
		$content = $post->post_content;
		$content = apply_filters( 'the_content', $content );
	}

	return $content;
}
add_filter( 'the_excerpt_rss', 'ane_enhance_rss_feed' );
add_filter( 'the_content_feed', 'ane_enhance_rss_feed' );

/**
 * ============================================================================
 * GOOGLE SITELINKS OPTIMIZATION
 * ============================================================================
 * These schemas help Google understand site structure for enhanced sitelinks
 */

/**
 * Output Breadcrumb Schema (JSON-LD)
 * Helps Google understand page hierarchy for sitelinks
 * Critical for Google Sitelinks appearance
 */
function ane_output_breadcrumb_schema() {
	// Skip on homepage
	if ( is_front_page() ) {
		return;
	}

	$breadcrumbs = array();
	$position    = 1;

	// Add Home
	$breadcrumbs[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => 'Home',
		'item'     => home_url(),
	);

	// Single Post
	if ( is_single() && get_post_type() === 'post' ) {
		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$category = $categories[0];
			$breadcrumbs[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $category->name,
				'item'     => get_category_link( $category->term_id ),
			);
		}
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	// Category Archive
	if ( is_category() ) {
		$category = get_queried_object();
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $category->name,
			'item'     => get_category_link( $category->term_id ),
		);
	}

	// Tag Archive
	if ( is_tag() ) {
		$tag = get_queried_object();
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $tag->name,
			'item'     => get_tag_link( $tag->term_id ),
		);
	}

	// Author Archive
	if ( is_author() ) {
		$author = get_queried_object();
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => $author->display_name,
			'item'     => get_author_posts_url( $author->ID ),
		);
	}

	// Page
	if ( is_page() ) {
		$breadcrumbs[] = array(
			'@type'    => 'ListItem',
			'position' => $position++,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	// Build schema
	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $breadcrumbs,
	);

	// Output JSON-LD
	echo "\n<!-- Breadcrumb Schema for Google Sitelinks -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}

/**
 * Output WebSite Schema with SearchAction
 * Enables Google Search Box in search results (like NU Online)
 * Critical for brand recognition and sitelinks
 */
function ane_output_website_schema() {
	// Only output on homepage
	if ( ! is_front_page() ) {
		return;
	}

	$company_name = ane_get_company_name();
	$company_url  = ane_get_company_url();
	$company_logo = ane_get_company_logo();

	// Build WebSite schema with SearchAction
	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'@id'      => home_url() . '#website',
		'url'      => home_url(),
		'name'     => $company_name,
		'alternateName' => get_bloginfo( 'description' ),
		'publisher' => array(
			'@type' => 'Organization',
			'name'  => $company_name,
			'url'   => $company_url,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => $company_logo,
			),
		),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	// Output JSON-LD
	echo "\n<!-- WebSite Schema with SearchAction for Google Search Box -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}

/**
 * Output Site Navigation Schema
 * Helps Google understand main navigation for sitelinks
 * Call this in header.php near menu output
 */
function ane_output_navigation_schema() {
	$menu_locations = get_nav_menu_locations();

	if ( empty( $menu_locations['menuutama'] ) ) {
		return;
	}

	$menu = wp_get_nav_menu_object( $menu_locations['menuutama'] );
	if ( ! $menu ) {
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	if ( empty( $menu_items ) ) {
		return;
	}

	$navigation_elements = array();

	foreach ( $menu_items as $item ) {
		// Only top-level menu items (no parent)
		if ( $item->menu_item_parent == 0 ) {
			$navigation_elements[] = array(
				'@type' => 'SiteNavigationElement',
				'@id'   => $item->url . '#navigation',
				'name'  => $item->title,
				'url'   => $item->url,
			);
		}
	}

	if ( empty( $navigation_elements ) ) {
		return;
	}

	// Output JSON-LD
	echo "\n<!-- Site Navigation Schema for Google Sitelinks -->\n";
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $navigation_elements, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n</script>\n";
}
