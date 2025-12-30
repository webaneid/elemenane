<?php
/**
 * Contact Page Content Template
 *
 * Displays contact information with company details and Google Maps.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-page ane-page-kontak' ); ?>>
	<header class="entry-header">
		<div class="ane-content">
			<div class="ane-title">
				<?php the_title( '<h1 class="post-title general-title">', '</h1>' ); ?>
			</div>
		</div>

		<div class="kontak-konten">
			<div class="row">
				<!-- Company Name & Address -->
				<div class="col-md-6 post-tengah">
					<div class="alamat">
						<?php
						$company_name    = get_field( 'ane_com_nama', 'option' );
						$company_address = get_field( 'ane_com_alamat', 'option' );

						if ( $company_name ) :
							?>
							<h3><?php echo esc_html( $company_name ); ?></h3>
						<?php endif; ?>

						<?php if ( $company_address ) : ?>
							<p><?php echo wp_kses_post( wpautop( $company_address ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Contact Information -->
				<div class="col-md-6 post-tengah">
					<?php if ( have_rows( 'ane_kontak', 'option' ) ) : ?>
						<div class="kontak text-right">
							<?php
							while ( have_rows( 'ane_kontak', 'option' ) ) :
								the_row();

								$phone    = get_sub_field( 'ane_telepon' );
								$mobile   = get_sub_field( 'ane_handphone' );
								$email    = get_sub_field( 'ane_email' );
								$website  = get_sub_field( 'ane_website' );

								if ( ! empty( $phone ) ) :
									?>
									<p>
										<?php esc_html_e( 'Phone:', 'elemenane' ); ?>
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
											<?php echo esc_html( $phone ); ?>
										</a>
									</p>
									<?php
								endif;

								if ( ! empty( $mobile ) ) :
									?>
									<p>
										<?php esc_html_e( 'Mobile:', 'elemenane' ); ?>
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $mobile ) ); ?>">
											<?php echo esc_html( $mobile ); ?>
										</a>
									</p>
									<?php
								endif;

								if ( ! empty( $email ) ) :
									?>
									<p>
										<?php esc_html_e( 'Email:', 'elemenane' ); ?>
										<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
											<?php echo esc_html( antispambot( $email ) ); ?>
										</a>
									</p>
									<?php
								endif;

								if ( ! empty( $website ) ) :
									?>
									<p>
										<?php esc_html_e( 'Website:', 'elemenane' ); ?>
										<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer">
											<?php echo esc_html( $website ); ?>
										</a>
									</p>
									<?php
								endif;

							endwhile;
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>

	<div class="ane-col-64">
		<!-- Page Content -->
		<div class="ane-kiri">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>

		<!-- Google Maps -->
			<?php
				$location = get_field('ane_gmap', 'option');
				if( !empty($location) ):
			?>
			<div class="ane-kanan">
				<div class="kontak-map">
                    <div class="acf-map">
                        <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                        <a class="directions" href="https://www.google.com/maps?saddr=My+Location&daddr=<?php echo $location['lat'] . ',' . $location['lng']; ?>"><?php _e('Get Directions to','roots'); ?> <?php echo $location['address']; ?></a> <!-- Output the title -->
                        </div>
                    </div>
            	</div>
			</div>
		<?php endif; ?>
	</div>
</article>