<?php
/**
 * Branch data helpers: normalised branch array, location names and contact details with company fallback.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Province and city names from inc/indonesia-locations.json (branches store IDs). Read once per request.
 *
 * @return array{provinces:array<string,string>,cities:array<string,string>}
 */
function elemenane_get_location_names() {
	static $names = null;

	if ( null !== $names ) {
		return $names;
	}

	$names = array(
		'provinces' => array(),
		'cities'    => array(),
	);

	$file = get_template_directory() . '/inc/indonesia-locations.json';
	if ( ! file_exists( $file ) ) {
		return $names;
	}

	$data = json_decode( (string) file_get_contents( $file ), true );
	if ( ! is_array( $data ) || empty( $data['provinces'] ) ) {
		return $names;
	}

	foreach ( $data['provinces'] as $province ) {
		$names['provinces'][ (string) $province['id'] ] = (string) $province['name'];
		foreach ( (array) ( $province['cities'] ?? array() ) as $city ) {
			$names['cities'][ (string) $city['id'] ] = (string) $city['name'];
		}
	}

	return $names;
}

/**
 * Normalise a phone number for wa.me: digits only, leading 0 becomes 62.
 *
 * @param string $number Raw number.
 * @return string Digits ('' when empty).
 */
function elemenane_normalize_whatsapp( $number ) {
	$digits = preg_replace( '/[^0-9]/', '', (string) $number );

	if ( '' !== $digits && '0' === $digits[0] ) {
		$digits = '62' . substr( $digits, 1 );
	}

	return (string) $digits;
}

/**
 * One branch as a plain array (same shape the archive script and tp/branch-card.php use).
 *
 * @param int|null $post_id Branch post ID (defaults to the current post).
 * @return array
 */
function elemenane_get_branch_data( $post_id = null ) {
	$post_id = $post_id ? absint( $post_id ) : (int) get_the_ID();
	$names   = elemenane_get_location_names();
	$map     = get_field( 'ane_branch_map', $post_id );
	$city    = (string) get_field( 'ane_branch_city', $post_id );
	$prov    = (string) get_field( 'ane_branch_province', $post_id );

	return array(
		'id'            => $post_id,
		// Plain text: escaped at output (esc_html in PHP, escapeHtml in JS).
		'title'         => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'permalink'     => get_permalink( $post_id ),
		'province'      => $prov,
		'province_name' => $names['provinces'][ $prov ] ?? '',
		'city'          => $city,
		'city_name'     => $names['cities'][ $city ] ?? '',
		'district'      => get_field( 'ane_branch_district', $post_id ),
		'address'       => get_field( 'ane_branch_address', $post_id ),
		'phone'         => get_field( 'ane_branch_phone', $post_id ),
		'email'         => get_field( 'ane_branch_email', $post_id ),
		'whatsapp'      => get_field( 'ane_branch_whatsapp', $post_id ),
		'hours'         => get_field( 'ane_branch_hours', $post_id ),
		'lat'           => is_array( $map ) ? (float) $map['lat'] : 0.0,
		'lng'           => is_array( $map ) ? (float) $map['lng'] : 0.0,
		'has_map'       => is_array( $map ) && ! empty( $map['lat'] ),
		'thumbnail'     => get_the_post_thumbnail_url( $post_id, 'medium' ),
	);
}

/**
 * Company contact details from the theme settings (ACF options): main phone, mobile numbers, WhatsApp, email, website.
 *
 * @return array<int,array{type:string,label:string,text:string,href:string}>
 */
function elemenane_get_company_contacts() {
	$items   = array();
	$seen    = array();
	$phone   = trim( (string) get_field( 'ane_telepon', 'option' ) );
	$mobiles = array();
	$email   = '';
	$web     = '';

	if ( have_rows( 'ane_kontak', 'option' ) ) {
		while ( have_rows( 'ane_kontak', 'option' ) ) {
			the_row();
			$mobile = trim( (string) get_sub_field( 'ane_handphone' ) );
			if ( '' !== $mobile ) {
				$mobiles[] = $mobile;
			}
			if ( '' === $email ) {
				$email = trim( (string) get_sub_field( 'ane_email' ) );
			}
			if ( '' === $web ) {
				$web = trim( (string) get_sub_field( 'ane_website' ) );
			}
		}
	}

	if ( '' !== $phone ) {
		$items[]                                 = array(
			'type'  => 'phone',
			'label' => __( 'Telepon', 'elemenane' ),
			'text'  => $phone,
			'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ),
		);
		$seen[ preg_replace( '/\D/', '', $phone ) ] = true;
	}
	foreach ( $mobiles as $mobile ) {
		$digits = preg_replace( '/\D/', '', $mobile );
		if ( isset( $seen[ $digits ] ) ) {
			continue;
		}
		$seen[ $digits ] = true;
		$items[]         = array(
			'type'  => 'phone',
			'label' => '' === $phone && empty( $items ) ? __( 'Telepon', 'elemenane' ) : __( 'Handphone', 'elemenane' ),
			'text'  => $mobile,
			'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $mobile ),
		);
	}

	// WhatsApp: dedicated number, then the first customer-care number, then the WhatsApp social link.
	$wa = elemenane_normalize_whatsapp( (string) get_field( 'ane_whatsapp_no', 'option' ) );
	if ( '' === $wa && have_rows( 'ane_cs', 'option' ) ) {
		while ( have_rows( 'ane_cs', 'option' ) ) {
			the_row();
			$wa = elemenane_normalize_whatsapp( (string) get_sub_field( 'ane_whatsapp' ) );
			if ( '' !== $wa ) {
				break;
			}
		}
	}
	if ( '' === $wa && preg_match( '#wa\.me/(\d+)#', (string) get_field( 'ane_whatsapp', 'option' ), $match ) ) {
		$wa = $match[1];
	}
	if ( '' !== $wa ) {
		$items[] = array(
			'type'  => 'whatsapp',
			'label' => 'WhatsApp',
			'text'  => '+' . $wa,
			'href'  => 'https://wa.me/' . $wa,
		);
	}

	if ( '' !== $email ) {
		$items[] = array(
			'type'  => 'email',
			'label' => __( 'Email', 'elemenane' ),
			'text'  => antispambot( $email ),
			'href'  => 'mailto:' . antispambot( $email ),
		);
	}
	if ( '' !== $web ) {
		$items[] = array(
			'type'  => 'website',
			'label' => __( 'Website', 'elemenane' ),
			'text'  => preg_replace( '#^https?://#', '', $web ),
			'href'  => esc_url_raw( $web ),
		);
	}

	return $items;
}

/**
 * Contact details to show for a branch.
 *
 * The branch's own phone / WhatsApp / email when it has any; otherwise the company's contact settings, flagged
 * with source "company" so the template can say these are the head-office contacts.
 *
 * @param array $branch Branch array from elemenane_get_branch_data().
 * @return array{source:string,items:array}
 */
function elemenane_get_branch_contacts( $branch ) {
	$items = array();

	if ( ! empty( $branch['phone'] ) ) {
		$items[] = array(
			'type'  => 'phone',
			'label' => __( 'Telepon', 'elemenane' ),
			'text'  => (string) $branch['phone'],
			'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $branch['phone'] ),
		);
	}
	$wa = elemenane_normalize_whatsapp( (string) ( $branch['whatsapp'] ?? '' ) );
	if ( '' !== $wa ) {
		$items[] = array(
			'type'  => 'whatsapp',
			'label' => 'WhatsApp',
			'text'  => (string) $branch['whatsapp'],
			'href'  => 'https://wa.me/' . $wa,
		);
	}
	if ( ! empty( $branch['email'] ) ) {
		$items[] = array(
			'type'  => 'email',
			'label' => __( 'Email', 'elemenane' ),
			'text'  => antispambot( (string) $branch['email'] ),
			'href'  => 'mailto:' . antispambot( (string) $branch['email'] ),
		);
	}

	if ( ! empty( $items ) ) {
		return array(
			'source' => 'branch',
			'items'  => $items,
		);
	}

	$company = elemenane_get_company_contacts();

	return array(
		'source' => empty( $company ) ? 'none' : 'company',
		'items'  => $company,
	);
}
