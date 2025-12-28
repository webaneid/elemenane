<?php
/**
 * Single Branch Template
 *
 * Displays individual branch details with Google Maps, contact info, and related branches.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	// Get ACF fields.
	$province    = get_field( 'ane_branch_province' );
	$city        = get_field( 'ane_branch_city' );
	$district    = get_field( 'ane_branch_district' );
	$address     = get_field( 'ane_branch_address' );
	$map_data    = get_field( 'ane_branch_map' );
	$phone       = get_field( 'ane_branch_phone' );
	$email       = get_field( 'ane_branch_email' );
	$whatsapp    = get_field( 'ane_branch_whatsapp' );
	$hours       = get_field( 'ane_branch_hours' );
	$photos      = get_field( 'ane_branch_photos' );

	// Get location names from JSON.
	$province_name = '';
	$city_name     = '';

	$json_file = get_template_directory() . '/inc/indonesia-locations.json';
	if ( file_exists( $json_file ) ) {
		$json_data = file_get_contents( $json_file );
		$data      = json_decode( $json_data, true );

		if ( $data && isset( $data['provinces'] ) ) {
			foreach ( $data['provinces'] as $prov ) {
				if ( $prov['id'] === $province ) {
					$province_name = $prov['name'];
					if ( isset( $prov['cities'] ) && is_array( $prov['cities'] ) ) {
						foreach ( $prov['cities'] as $ct ) {
							if ( $ct['id'] === $city ) {
								$city_name = $ct['name'];
								break;
							}
						}
					}
					break;
				}
			}
		}
	}
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ane-single-branch' ); ?>>
		<div class="ane-container">
			<!-- Branch Header -->
			<header class="ane-single-branch__header">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="ane-single-branch__hero">
						<?php the_post_thumbnail( 'large', array( 'class' => 'ane-single-branch__hero-img' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="ane-single-branch__title-area">
					<h1 class="ane-single-branch__title"><?php the_title(); ?></h1>
					<?php if ( $province_name || $city_name ) : ?>
						<p class="ane-single-branch__location-badge">
							<span class="dashicons dashicons-location"></span>
							<?php
							if ( $city_name ) {
								echo esc_html( $city_name );
							}
							if ( $province_name ) {
								echo $city_name ? ', ' : '';
								echo esc_html( $province_name );
							}
							?>
						</p>
					<?php endif; ?>
				</div>
			</header>

			<div class="ane-single-branch__content">
				<!-- Left Column: Main Info -->
				<div class="ane-single-branch__main">
					<!-- Address Info -->
					<?php if ( $address || $district ) : ?>
						<section class="ane-single-branch__section">
							<h2 class="ane-single-branch__section-title">
								<span class="dashicons dashicons-location-alt"></span>
								<?php esc_html_e( 'Alamat', 'elemenane' ); ?>
							</h2>
							<div class="ane-single-branch__section-content">
								<?php if ( $address ) : ?>
									<p><?php echo nl2br( esc_html( $address ) ); ?></p>
								<?php endif; ?>
								<?php if ( $district ) : ?>
									<p><strong><?php esc_html_e( 'Kecamatan:', 'elemenane' ); ?></strong> <?php echo esc_html( $district ); ?></p>
								<?php endif; ?>
							</div>
						</section>
					<?php endif; ?>

					<!-- Google Maps -->
					<?php if ( $map_data ) : ?>
						<section class="ane-single-branch__section">
							<h2 class="ane-single-branch__section-title">
								<span class="dashicons dashicons-location"></span>
								<?php esc_html_e( 'Peta Lokasi', 'elemenane' ); ?>
							</h2>
							<div class="ane-single-branch__map-container">
								<div id="ane-single-branch-map" class="ane-single-branch__map"
									data-lat="<?php echo esc_attr( $map_data['lat'] ); ?>"
									data-lng="<?php echo esc_attr( $map_data['lng'] ); ?>"
									data-title="<?php echo esc_attr( get_the_title() ); ?>">
								</div>
								<a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $map_data['lat'] . ',' . $map_data['lng'] ); ?>"
									target="_blank"
									class="ane-btn ane-btn--directions">
									<span class="dashicons dashicons-arrow-right-alt"></span>
									<?php esc_html_e( 'Petunjuk Arah', 'elemenane' ); ?>
								</a>
							</div>
						</section>
					<?php endif; ?>

					<!-- Photo Gallery -->
					<?php if ( $photos ) : ?>
						<section class="ane-single-branch__section">
							<h2 class="ane-single-branch__section-title">
								<span class="dashicons dashicons-format-gallery"></span>
								<?php esc_html_e( 'Galeri Foto', 'elemenane' ); ?>
							</h2>
							<div class="ane-single-branch__gallery">
								<?php foreach ( $photos as $photo ) : ?>
									<a href="<?php echo esc_url( $photo['url'] ); ?>" class="ane-single-branch__gallery-item" data-fancybox="branch-gallery">
										<img src="<?php echo esc_url( $photo['sizes']['medium'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ); ?>">
									</a>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>
				</div>

				<!-- Right Column: Sidebar -->
				<aside class="ane-single-branch__sidebar">
					<!-- Contact Info Card -->
					<div class="ane-single-branch__card">
						<h3 class="ane-single-branch__card-title">
							<span class="dashicons dashicons-phone"></span>
							<?php esc_html_e( 'Hubungi Kami', 'elemenane' ); ?>
						</h3>
						<div class="ane-single-branch__contact-list">
							<?php if ( $phone ) : ?>
								<div class="ane-single-branch__contact-item">
									<span class="dashicons dashicons-phone"></span>
									<div>
										<strong><?php esc_html_e( 'Telepon', 'elemenane' ); ?></strong>
										<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $whatsapp ) : ?>
								<div class="ane-single-branch__contact-item">
									<span class="dashicons dashicons-whatsapp"></span>
									<div>
										<strong><?php esc_html_e( 'WhatsApp', 'elemenane' ); ?></strong>
										<a href="https://wa.me/<?php echo esc_attr( $whatsapp ); ?>" target="_blank"><?php echo esc_html( $whatsapp ); ?></a>
									</div>
								</div>
							<?php endif; ?>

							<?php if ( $email ) : ?>
								<div class="ane-single-branch__contact-item">
									<span class="dashicons dashicons-email"></span>
									<div>
										<strong><?php esc_html_e( 'Email', 'elemenane' ); ?></strong>
										<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Operating Hours Card -->
					<?php if ( $hours ) : ?>
						<div class="ane-single-branch__card">
							<h3 class="ane-single-branch__card-title">
								<span class="dashicons dashicons-clock"></span>
								<?php esc_html_e( 'Jam Operasional', 'elemenane' ); ?>
							</h3>
							<div class="ane-single-branch__hours">
								<?php echo nl2br( esc_html( $hours ) ); ?>
							</div>
						</div>
					<?php endif; ?>
				</aside>
			</div>

			<!-- Related Branches -->
			<?php
			// Query related branches - same city first, then same province.
			$related_args = array(
				'post_type'      => 'branch',
				'posts_per_page' => 4,
				'post__not_in'   => array( get_the_ID() ),
				'meta_query'     => array(
					array(
						'key'     => 'ane_branch_city',
						'value'   => $city,
						'compare' => '=',
					),
				),
			);

			$related_query = new WP_Query( $related_args );

			// If not enough in same city, get from same province.
			if ( $related_query->post_count < 4 ) {
				$related_args = array(
					'post_type'      => 'branch',
					'posts_per_page' => 4,
					'post__not_in'   => array( get_the_ID() ),
					'meta_query'     => array(
						array(
							'key'     => 'ane_branch_province',
							'value'   => $province,
							'compare' => '=',
						),
					),
				);

				$related_query = new WP_Query( $related_args );
			}

			if ( $related_query->have_posts() ) :
				?>
				<section class="ane-single-branch__related">
					<h2 class="ane-single-branch__related-title">
						<?php esc_html_e( 'Cabang Terdekat Lainnya', 'elemenane' ); ?>
					</h2>
					<div class="ane-single-branch__related-grid">
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							$rel_map    = get_field( 'ane_branch_map' );
							$rel_phone  = get_field( 'ane_branch_phone' );
							$rel_city   = get_field( 'ane_branch_city' );
							$rel_province = get_field( 'ane_branch_province' );

							// Get city name.
							$rel_city_name = '';
							if ( $data && isset( $data['provinces'] ) ) {
								foreach ( $data['provinces'] as $prov ) {
									if ( $prov['id'] === $rel_province && isset( $prov['cities'] ) ) {
										foreach ( $prov['cities'] as $ct ) {
											if ( $ct['id'] === $rel_city ) {
												$rel_city_name = $ct['name'];
												break 2;
											}
										}
									}
								}
							}
							?>
							<article class="ane-branch-card-mini">
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="ane-branch-card-mini__image">
										<a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'medium' ); ?>
										</a>
									</div>
								<?php endif; ?>
								<div class="ane-branch-card-mini__content">
									<h3 class="ane-branch-card-mini__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
									<?php if ( $rel_city_name ) : ?>
										<p class="ane-branch-card-mini__location">
											<span class="dashicons dashicons-location"></span>
											<?php echo esc_html( $rel_city_name ); ?>
										</p>
									<?php endif; ?>
									<?php if ( $rel_phone ) : ?>
										<p class="ane-branch-card-mini__phone">
											<span class="dashicons dashicons-phone"></span>
											<?php echo esc_html( $rel_phone ); ?>
										</p>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>" class="ane-btn ane-btn--small">
										<?php esc_html_e( 'Lihat Detail', 'elemenane' ); ?>
									</a>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</section>
				<?php
				wp_reset_postdata();
			endif;
			?>

		</div>
	</article>

	<?php
	// Pass map data to JavaScript.
	if ( $map_data ) :
		?>
		<script>
			var aneSingleBranchMap = {
				lat: <?php echo floatval( $map_data['lat'] ); ?>,
				lng: <?php echo floatval( $map_data['lng'] ); ?>,
				title: <?php echo wp_json_encode( get_the_title() ); ?>
			};
		</script>
		<?php
	endif;

endwhile;

get_footer();
