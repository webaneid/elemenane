<?php
/**
 * Contact page layout.
 *
 * Sources (ACF options): ane_com_nama / ane_com_tag / ane_com_des / ane_com_alamat, ane_telepon, repeater ane_kontak
 * (handphone, fax, email, website), WhatsApp, ane_gmap, and the social links. The page content is shown as the
 * message form (Contact Form 7 or any shortcode / blocks).
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_name     = trim( (string) get_field( 'ane_com_nama', 'option' ) );
$elemenane_lead     = trim( (string) get_field( 'ane_com_tag', 'option' ) );
$elemenane_about    = trim( (string) get_field( 'ane_com_des', 'option' ) );
$elemenane_address  = trim( (string) get_field( 'ane_com_alamat', 'option' ) );
$elemenane_contacts = elemenane_get_company_contacts( true );
$elemenane_location = get_field( 'ane_gmap', 'option' );
$elemenane_has_map  = is_array( $elemenane_location ) && isset( $elemenane_location['lat'], $elemenane_location['lng'] );
$elemenane_icons    = array(
	'phone'    => 'telepon',
	'fax'      => 'telepon',
	'whatsapp' => 'whatsapp',
	'email'    => 'email',
	'website'  => 'link',
);

$elemenane_wa = '';
$elemenane_tel = '';
foreach ( $elemenane_contacts as $elemenane_item ) {
	if ( '' === $elemenane_wa && 'whatsapp' === $elemenane_item['type'] ) {
		$elemenane_wa = $elemenane_item['href'];
	}
	if ( '' === $elemenane_tel && 'phone' === $elemenane_item['type'] ) {
		$elemenane_tel = $elemenane_item['href'];
	}
}

$elemenane_branches_url = '';
if ( function_exists( 'ane_is_branch_cpt_enabled' ) && ane_is_branch_cpt_enabled() && (int) wp_count_posts( 'branch' )->publish > 0 ) {
	$elemenane_branches_url = (string) get_post_type_archive_link( 'branch' );
}

while ( have_posts() ) :
	the_post();

	$elemenane_form = trim( (string) get_the_content() );
	?>
	<main id="main" <?php post_class( 'site-main ane-archive ane-archive--contact' ); ?> role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<header class="page-header ane-archive-head">
				<span class="ane-archive-head__kicker"><?php echo '' !== $elemenane_name ? esc_html( $elemenane_name ) : esc_html__( 'Kontak', 'elemenane' ); ?></span>
				<h1 class="ane-archive-head__title"><?php the_title(); ?></h1>
				<?php if ( '' !== $elemenane_lead || '' !== $elemenane_about ) : ?>
					<div class="ane-archive-head__desc"><p><?php echo esc_html( '' !== $elemenane_about ? wp_trim_words( $elemenane_about, 32, '...' ) : $elemenane_lead ); ?></p></div>
				<?php endif; ?>

				<?php if ( '' !== $elemenane_wa || '' !== $elemenane_tel ) : ?>
					<div class="ane-contact__quick">
						<?php if ( '' !== $elemenane_wa ) : ?>
							<a class="ane-contact__btn ane-contact__btn--wa" href="<?php echo esc_url( $elemenane_wa ); ?>" target="_blank" rel="noopener"><?php elemenane_icon( 'whatsapp' ); ?><?php esc_html_e( 'Chat WhatsApp', 'elemenane' ); ?></a>
						<?php endif; ?>
						<?php if ( '' !== $elemenane_tel ) : ?>
							<a class="ane-contact__btn" href="<?php echo esc_attr( $elemenane_tel ); ?>"><?php elemenane_icon( 'telepon' ); ?><?php esc_html_e( 'Telepon Sekarang', 'elemenane' ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			<div class="ane-contact__grid<?php echo '' === $elemenane_form ? ' is-single' : ''; ?>">
				<section class="ane-contact__info" aria-labelledby="ane-contact-info-title">
					<h2 id="ane-contact-info-title" class="ane-section-title"><?php esc_html_e( 'Informasi Kontak', 'elemenane' ); ?></h2>

					<ul class="ane-contact__cards">
						<?php if ( '' !== $elemenane_address ) : ?>
							<li class="ane-ccard ane-ccard--wide">
								<?php elemenane_icon( 'lokasi' ); ?>
								<div>
									<strong><?php esc_html_e( 'Alamat', 'elemenane' ); ?></strong>
									<span><?php echo wp_kses_post( nl2br( $elemenane_address ) ); ?></span>
								</div>
							</li>
						<?php endif; ?>

						<?php foreach ( $elemenane_contacts as $elemenane_item ) : ?>
							<li class="ane-ccard">
								<?php elemenane_icon( $elemenane_icons[ $elemenane_item['type'] ] ?? 'telepon' ); ?>
								<div>
									<strong><?php echo esc_html( $elemenane_item['label'] ); ?></strong>
									<?php if ( '' !== $elemenane_item['href'] ) : ?>
										<a href="<?php echo esc_attr( $elemenane_item['href'] ); ?>"<?php echo 0 === strpos( $elemenane_item['href'], 'http' ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo wp_kses( $elemenane_item['text'], array() ); ?></a>
									<?php else : ?>
										<span><?php echo esc_html( $elemenane_item['text'] ); ?></span>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>

					<?php
					ob_start();
					get_template_part( 'tp/content', 'sosmed' );
					$elemenane_social = trim( (string) ob_get_clean() );
					if ( false !== strpos( $elemenane_social, '<li' ) ) :
						?>
						<div class="ane-contact__social">
							<span class="ane-contact__label"><?php esc_html_e( 'Ikuti Kami', 'elemenane' ); ?></span>
							<?php echo wp_kses( $elemenane_social, elemenane_kses_post_with_svg() ); ?>
						</div>
					<?php endif; ?>
				</section>

				<?php if ( '' !== $elemenane_form ) : ?>
					<section class="ane-contact__form" aria-labelledby="ane-contact-form-title">
						<h2 id="ane-contact-form-title" class="ane-section-title"><?php esc_html_e( 'Kirim Pesan', 'elemenane' ); ?></h2>
						<div class="ane-content"><?php the_content(); ?></div>
					</section>
				<?php endif; ?>
			</div>

			<?php if ( $elemenane_has_map ) : ?>
				<?php
				$elemenane_lat = (string) $elemenane_location['lat'];
				$elemenane_lng = (string) $elemenane_location['lng'];
				?>
				<section class="ane-contact__map" aria-labelledby="ane-contact-map-title">
					<h2 id="ane-contact-map-title" class="ane-section-title"><?php esc_html_e( 'Lokasi Kami', 'elemenane' ); ?></h2>
					<div class="acf-map" role="region" aria-label="<?php esc_attr_e( 'Peta lokasi', 'elemenane' ); ?>">
						<div class="marker" data-lat="<?php echo esc_attr( $elemenane_lat ); ?>" data-lng="<?php echo esc_attr( $elemenane_lng ); ?>">
							<a class="directions" href="<?php echo esc_url( 'https://www.google.com/maps?saddr=My+Location&daddr=' . rawurlencode( $elemenane_lat . ',' . $elemenane_lng ) ); ?>"><?php esc_html_e( 'Get Directions to', 'elemenane' ); ?> <?php echo esc_html( isset( $elemenane_location['address'] ) ? (string) $elemenane_location['address'] : '' ); ?></a>
						</div>
					</div>
					<a class="ane-contact__btn ane-contact__btn--solid" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $elemenane_lat . ',' . $elemenane_lng ) ); ?>" target="_blank" rel="noopener"><?php elemenane_icon( 'lokasi' ); ?><?php esc_html_e( 'Petunjuk Arah', 'elemenane' ); ?></a>
				</section>
			<?php endif; ?>

			<?php if ( '' !== $elemenane_branches_url ) : ?>
				<aside class="ane-contact__branches">
					<div>
						<strong><?php esc_html_e( 'Cari cabang terdekat', 'elemenane' ); ?></strong>
						<span><?php esc_html_e( 'Temukan cabang terdekat dari lokasi Anda', 'elemenane' ); ?></span>
					</div>
					<a class="ane-contact__btn ane-contact__btn--solid" href="<?php echo esc_url( $elemenane_branches_url ); ?>"><?php esc_html_e( 'Lokasi Cabang', 'elemenane' ); ?><?php elemenane_icon( 'chevron-right' ); ?></a>
				</aside>
			<?php endif; ?>
		</div>
	</main>
	<?php
endwhile;
