<?php
/**
 * Single Branch Template
 *
 * Header, photo, address, Google map, gallery, contact card (branch contacts, or the company contacts from the
 * theme settings when the branch has none), opening hours and nearby branches (same cards as the archive).
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$elemenane_b      = elemenane_get_branch_data( get_the_ID() );
	$elemenane_photos = get_field( 'ane_branch_photos' );
	$elemenane_hours  = trim( (string) $elemenane_b['hours'] );
	$elemenane_where  = implode( ', ', array_filter( array( $elemenane_b['city_name'], $elemenane_b['province_name'] ) ) );
	$elemenane_dirs   = 'https://www.google.com/maps/dir/?api=1&destination=' . $elemenane_b['lat'] . ',' . $elemenane_b['lng'];

	if ( $elemenane_photos ) {
		ane_enqueue_lightbox();
	}
	if ( $elemenane_b['has_map'] ) {
		wp_add_inline_script(
			'ane-single-branch',
			'var aneSingleBranchMap = ' . wp_json_encode(
				array(
					'lat'   => $elemenane_b['lat'],
					'lng'   => $elemenane_b['lng'],
					'title' => $elemenane_b['title'],
				)
			) . ';',
			'before'
		);
	}

	// Nearby branches: same city first, then the same province.
	$elemenane_related = new WP_Query(
		array(
			'post_type'      => 'branch',
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'rand',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Small CPT, limited query.
				array(
					'key'   => 'ane_branch_city',
					'value' => $elemenane_b['city'],
				),
			),
		)
	);
	if ( $elemenane_related->post_count < 3 && '' !== $elemenane_b['province'] ) {
		$elemenane_related = new WP_Query(
			array(
				'post_type'      => 'branch',
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'orderby'        => 'rand',
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Small CPT, limited query.
					array(
						'key'   => 'ane_branch_province',
						'value' => $elemenane_b['province'],
					),
				),
			)
		);
	}
	?>
	<main id="main" <?php post_class( 'site-main ane-archive ane-archive--single-branch' ); ?> role="main">
		<?php get_template_part( 'breadcrumbs' ); ?>

		<div class="container">
			<header class="page-header ane-archive-head">
				<span class="ane-archive-head__kicker"><?php echo '' !== $elemenane_b['city_name'] ? esc_html( $elemenane_b['city_name'] ) : esc_html__( 'Cabang', 'elemenane' ); ?></span>
				<h1 class="ane-archive-head__title"><?php the_title(); ?></h1>
				<?php if ( '' !== $elemenane_where ) : ?>
					<p class="ane-archive-head__count"><?php elemenane_icon( 'lokasi' ); ?> <?php echo esc_html( $elemenane_where ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="ane-sb__hero">
					<?php
					the_post_thumbnail(
						'large',
						array(
							'loading' => 'eager',
							'fetchpriority' => 'high',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="ane-sb__layout">
				<div class="ane-sb__main">
					<?php if ( $elemenane_b['address'] || $elemenane_b['district'] ) : ?>
						<section class="ane-sb__section">
							<h2 class="ane-section-title"><?php esc_html_e( 'Alamat', 'elemenane' ); ?></h2>
							<?php if ( $elemenane_b['address'] ) : ?>
								<p class="ane-sb__address"><?php echo nl2br( esc_html( (string) $elemenane_b['address'] ) ); ?></p>
							<?php endif; ?>
							<?php if ( $elemenane_b['district'] ) : ?>
								<p class="ane-sb__district"><strong><?php esc_html_e( 'Kecamatan:', 'elemenane' ); ?></strong> <?php echo esc_html( (string) $elemenane_b['district'] ); ?></p>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<?php if ( $elemenane_b['has_map'] ) : ?>
						<section class="ane-sb__section">
							<h2 class="ane-section-title"><?php esc_html_e( 'Peta Lokasi', 'elemenane' ); ?></h2>
							<div id="ane-single-branch-map" class="ane-sb__map" role="region" aria-label="<?php esc_attr_e( 'Peta lokasi cabang', 'elemenane' ); ?>"></div>
							<a class="ane-sb__directions" href="<?php echo esc_url( $elemenane_dirs ); ?>" target="_blank" rel="noopener">
								<?php elemenane_icon( 'lokasi' ); ?>
								<?php esc_html_e( 'Petunjuk Arah', 'elemenane' ); ?>
							</a>
						</section>
					<?php endif; ?>

					<?php if ( $elemenane_photos ) : ?>
						<section class="ane-sb__section">
							<h2 class="ane-section-title"><?php esc_html_e( 'Galeri Foto', 'elemenane' ); ?></h2>
							<div class="ane-sb__gallery">
								<?php foreach ( $elemenane_photos as $elemenane_photo ) : ?>
									<a href="<?php echo esc_url( $elemenane_photo['url'] ); ?>" class="ane-sb__photo gallery-popup">
										<img src="<?php echo esc_url( $elemenane_photo['sizes']['medium'] ); ?>" alt="<?php echo esc_attr( $elemenane_photo['alt'] ); ?>" loading="lazy" decoding="async">
									</a>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>
				</div>

				<aside class="ane-sb__aside">
					<?php get_template_part( 'tp/branch', 'contact', array( 'branch' => $elemenane_b ) ); ?>

					<?php if ( '' !== $elemenane_hours ) : ?>
						<section class="ane-bcard" aria-labelledby="ane-hours-title">
							<h2 id="ane-hours-title" class="ane-section-title"><?php esc_html_e( 'Jam Operasional', 'elemenane' ); ?></h2>
							<div class="ane-bcard__hours"><?php elemenane_icon( 'jam' ); ?><p><?php echo nl2br( esc_html( $elemenane_hours ) ); ?></p></div>
						</section>
					<?php endif; ?>
				</aside>
			</div>

			<?php if ( $elemenane_related->have_posts() ) : ?>
				<section class="ane-sb__related" aria-labelledby="ane-nearby-title">
					<h2 id="ane-nearby-title" class="ane-section-title"><?php esc_html_e( 'Cabang Terdekat Lainnya', 'elemenane' ); ?></h2>
					<div class="ane-branch-grid">
						<?php
						while ( $elemenane_related->have_posts() ) :
							$elemenane_related->the_post();
							get_template_part(
								'tp/branch',
								'card',
								array(
									'branch' => elemenane_get_branch_data( get_the_ID() ),
									'detail' => 'link',
								)
							);
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</section>
			<?php endif; ?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
