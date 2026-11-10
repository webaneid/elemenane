<?php
/**
 * Default Site Footer.
 *
 * Content comes from the ACF option pages (company profile, contact, social)
 * and the `menufooter` menu location. Styles: scss/_footer.scss (.ane-footer__*).
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

$elemenane_social_title = get_field( 'ane_title_sosmed', 'option' );
$elemenane_name         = (string) get_field( 'ane_com_nama', 'option' );
$elemenane_tagline      = (string) get_field( 'ane_com_tag', 'option' );
$elemenane_about        = (string) get_field( 'ane_com_des', 'option' );
$elemenane_address      = (string) get_field( 'ane_com_alamat', 'option' );
$elemenane_phone        = (string) get_field( 'ane_telepon', 'option' );

/**
 * Contact items: icon, label (for screen readers), text, optional href.
 * Address and main phone appear once; the repeater adds one set per row.
 */
$elemenane_contacts = array();

if ( '' !== $elemenane_address ) {
	$elemenane_contacts[] = array(
		'icon'  => 'lokasi',
		'label' => __( 'Address', 'elemenane' ),
		'text'  => wp_kses_post( $elemenane_address ),
		'href'  => '',
	);
}

if ( '' !== $elemenane_phone ) {
	$elemenane_contacts[] = array(
		'icon'  => 'telepon',
		'label' => __( 'Phone', 'elemenane' ),
		'text'  => esc_html( $elemenane_phone ),
		'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $elemenane_phone ),
	);
}

if ( have_rows( 'ane_kontak', 'option' ) ) {
	while ( have_rows( 'ane_kontak', 'option' ) ) {
		the_row();

		$elemenane_mobile = (string) get_sub_field( 'ane_handphone' );
		$elemenane_fax    = (string) get_sub_field( 'ane_fax' );
		$elemenane_email  = (string) get_sub_field( 'ane_email' );
		$elemenane_web    = (string) get_sub_field( 'ane_website' );

		if ( '' !== $elemenane_mobile ) {
			$elemenane_contacts[] = array(
				'icon'  => 'handphone',
				'label' => __( 'Mobile', 'elemenane' ),
				'text'  => esc_html( $elemenane_mobile ),
				'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $elemenane_mobile ),
			);
		}
		if ( '' !== $elemenane_fax ) {
			$elemenane_contacts[] = array(
				'icon'  => 'telepon',
				'label' => __( 'Fax', 'elemenane' ),
				'text'  => esc_html( $elemenane_fax ),
				'href'  => '',
			);
		}
		if ( '' !== $elemenane_email ) {
			$elemenane_contacts[] = array(
				'icon'  => 'email',
				'label' => __( 'Email', 'elemenane' ),
				'text'  => esc_html( antispambot( $elemenane_email ) ),
				'href'  => 'mailto:' . antispambot( $elemenane_email ),
			);
		}
		if ( '' !== $elemenane_web ) {
			$elemenane_web_url    = preg_match( '#^https?://#i', $elemenane_web ) ? $elemenane_web : 'https://' . $elemenane_web;
			$elemenane_contacts[] = array(
				'icon'  => 'laptop',
				'label' => __( 'Website', 'elemenane' ),
				'text'  => esc_html( $elemenane_web ),
				'href'  => $elemenane_web_url,
			);
		}
	}
}

// Social links come from tp/content-sosmed.php; only show the band when at least one is set.
ob_start();
get_template_part( 'tp/content', 'sosmed' );
$elemenane_social_html = (string) ob_get_clean();
$elemenane_has_social  = false !== strpos( $elemenane_social_html, '<li' );
?>
<footer class="site-footer ane-footer" id="footer">

	<?php if ( $elemenane_has_social ) : ?>
		<div class="ane-footer__social">
			<div class="ane-footer__inner ane-footer__social-inner">
				<p class="ane-footer__social-title">
					<?php
					if ( ! empty( $elemenane_social_title ) ) {
						echo wp_kses_post( (string) $elemenane_social_title );
					} else {
						echo esc_html__( 'Support Our', 'elemenane' ) . ' <span>' . esc_html__( 'Social Media', 'elemenane' ) . '</span>';
					}
					?>
				</p>
				<div class="ane-footer__social-links">
					<?php echo wp_kses( $elemenane_social_html, elemenane_kses_post_with_svg() ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="ane-footer__main">
		<div class="ane-footer__inner ane-footer__grid">

			<section class="ane-footer__col ane-footer__brand">
				<?php if ( '' !== $elemenane_name ) : ?>
					<h2 class="ane-footer__brand-name"><?php echo esc_html( $elemenane_name ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $elemenane_tagline ) : ?>
					<p class="ane-footer__tagline"><?php echo esc_html( $elemenane_tagline ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $elemenane_about ) : ?>
					<div class="ane-footer__about"><?php echo wp_kses_post( wpautop( $elemenane_about ) ); ?></div>
				<?php endif; ?>
			</section>

			<nav class="ane-footer__col ane-footer__links" aria-labelledby="ane-footer-links-title">
				<h2 class="ane-footer__title" id="ane-footer-links-title"><?php esc_html_e( 'Useful Links', 'elemenane' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menufooter',
						'container'      => '',
						'menu_class'     => 'ane-footer__menu',
						'fallback_cb'    => 'wp_page_menu',
						'depth'          => 2,
					)
				);
				?>
			</nav>

			<?php if ( ! empty( $elemenane_contacts ) ) : ?>
				<section class="ane-footer__col ane-footer__contact" aria-labelledby="ane-footer-contact-title">
					<h2 class="ane-footer__title" id="ane-footer-contact-title"><?php esc_html_e( 'Contact Us', 'elemenane' ); ?></h2>
					<ul class="ane-footer__contact-list">
						<?php foreach ( $elemenane_contacts as $elemenane_contact ) : ?>
							<li class="ane-footer__contact-item">
								<span class="ane-footer__contact-icon"><?php elemenane_icon( $elemenane_contact['icon'] ); ?></span>
								<span class="screen-reader-text"><?php echo esc_html( $elemenane_contact['label'] ); ?>:</span>
								<?php if ( '' !== $elemenane_contact['href'] ) : ?>
									<a class="ane-footer__contact-text" href="<?php echo esc_url( $elemenane_contact['href'], array( 'http', 'https', 'tel', 'mailto' ) ); ?>"><?php echo wp_kses_post( $elemenane_contact['text'] ); ?></a>
								<?php else : ?>
									<span class="ane-footer__contact-text"><?php echo wp_kses_post( $elemenane_contact['text'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

		</div>
	</div>

	<div class="ane-footer__bottom">
		<div class="ane-footer__inner">
			<p class="ane-footer__copy">
				Copyright <?php echo wp_kses_post( ane_copyright_year() ); ?> <?php echo esc_html( $elemenane_name ); ?>
			</p>
		</div>
	</div>
</footer>
