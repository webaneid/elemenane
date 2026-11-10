<?php
/**
 * Native Theme Footer Template.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

$titlesosmed = get_field( 'ane_title_sosmed', 'option' );
$judul       = get_field( 'ane_com_nama', 'option' );
$sologan     = get_field( 'ane_com_tag', 'option' );
$about       = get_field( 'ane_com_des', 'option' );
$alamat      = get_field( 'ane_com_alamat', 'option' );
$phone       = get_field( 'ane_telepon', 'option' );
?>
<footer class="site-footer ane-footer" id="footer">
	<div class="sosmed-footer">
		<div class="kiri">
			<?php if ( ! empty( $titlesosmed ) ) : ?>
				<h2><?php echo wp_kses_post( (string) $titlesosmed ); ?></h2>
			<?php else : ?>
				<h2><?php echo esc_html__( 'Support Our', 'elemenane' ) . '<span>' . esc_html__( 'Social Media', 'elemenane' ) . '</span>'; ?></h2>
			<?php endif; ?>
		</div>
		<div class="kanan">
			<?php get_template_part( 'tp/content', 'sosmed' ); ?>
		</div>
	</div>

	<div class="atas">
		<div class="container">
			<div class="ane-col-3">
				<div class="ane-isi">
					<div class="isi">
						<div class="footer-content">
							<?php if ( ! empty( $judul ) ) : ?>
								<h3><?php echo esc_html( (string) $judul ); ?></h3>
							<?php endif; ?>
							<?php if ( ! empty( $sologan ) ) : ?>
								<h4><?php echo esc_html( (string) $sologan ); ?></h4>
							<?php endif; ?>
							<?php if ( ! empty( $about ) ) : ?>
								<p><?php echo wp_kses_post( (string) $about ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="ane-isi">
					<div class="isi-v2">
						<h3><?php esc_html_e( 'Useful Links', 'elemenane' ); ?></h3>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menufooter',
								'container'      => 'div',
								'fallback_cb'    => 'wp_page_menu',
								'depth'          => 4,
							)
						);
						?>
					</div>
				</div>
				<div class="ane-isi">
					<div class="isi-v2">
						<h3><?php esc_html_e( 'Contact Us', 'elemenane' ); ?></h3>
						<?php if ( have_rows( 'ane_kontak', 'option' ) ) : ?>
							<div class="footer-kontak">
								<ul>
									<?php
									while ( have_rows( 'ane_kontak', 'option' ) ) :
										the_row();

										$fax    = get_sub_field( 'ane_fax' );
										$mobile = get_sub_field( 'ane_handphone' );
										$email  = get_sub_field( 'ane_email' );
										$web    = get_sub_field( 'ane_website' );
										$url    = home_url( '/' );

										if ( ! empty( $alamat ) ) :
											?>
											<li><?php elemenane_icon( 'lokasi' ); ?> <?php echo wp_kses_post( (string) $alamat ); ?></li>
											<?php
										endif;
										if ( ! empty( $phone ) ) :
											?>
											<li><?php elemenane_icon( 'telepon' ); ?> <?php echo esc_html( (string) $phone ); ?></li>
											<?php
										endif;
										if ( ! empty( $fax ) ) :
											?>
											<li><?php elemenane_icon( 'telepon' ); ?> <?php echo esc_html( (string) $fax ); ?></li>
											<?php
										endif;
										if ( ! empty( $mobile ) ) :
											?>
											<li><?php elemenane_icon( 'handphone' ); ?> <?php echo esc_html( (string) $mobile ); ?></li>
											<?php
										endif;
										if ( ! empty( $email ) ) :
											?>
											<li><?php elemenane_icon( 'email' ); ?> <?php echo esc_html( antispambot( (string) $email ) ); ?></li>
											<?php
										endif;
										if ( ! empty( $web ) ) :
											?>
											<li><a href="<?php echo esc_url( $url ); ?>" title="<?php echo esc_attr( (string) $web ); ?>"><?php elemenane_icon( 'laptop' ); ?> <?php echo esc_html( (string) $web ); ?></a></li>
											<?php
										endif;
									endwhile;
									?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="bawah">
		<div class="container">
			Copyright <?php echo wp_kses_post( ane_copyright_year() ) . ' ' . esc_html( (string) $judul ); ?>
		</div>
	</div>
</footer>
