<?php
/**
 * Branch Archive Template
 *
 * Displays all branches with Google Maps and location filter.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Get all branches with location data.
$branches_query = new WP_Query(
	array(
		'post_type'      => 'branch',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

// Prepare branches data for JavaScript.
$branches_data = array();
if ( $branches_query->have_posts() ) {
	while ( $branches_query->have_posts() ) {
		$branches_query->the_post();

		$map_data = get_field( 'ane_branch_map' );

		if ( $map_data ) {
			$branches_data[] = array(
				'id'        => get_the_ID(),
				'title'     => get_the_title(),
				'permalink' => get_permalink(),
				'province'  => get_field( 'ane_branch_province' ),
				'city'      => get_field( 'ane_branch_city' ),
				'district'  => get_field( 'ane_branch_district' ),
				'address'   => get_field( 'ane_branch_address' ),
				'phone'     => get_field( 'ane_branch_phone' ),
				'email'     => get_field( 'ane_branch_email' ),
				'whatsapp'  => get_field( 'ane_branch_whatsapp' ),
				'hours'     => get_field( 'ane_branch_hours' ),
				'lat'       => floatval( $map_data['lat'] ),
				'lng'       => floatval( $map_data['lng'] ),
				'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
			);
		}
	}
	wp_reset_postdata();
}
?>

<div class="ane-branch-archive">
	<div class="ane-container">
		<header class="ane-branch-archive__header">
			<h1><?php esc_html_e( 'Lokasi Cabang', 'elemenane' ); ?></h1>
			<p><?php esc_html_e( 'Temukan cabang terdekat dari lokasi Anda', 'elemenane' ); ?></p>
		</header>

		<!-- Filter Section -->
		<div class="ane-branch-filter">
			<select id="ane-filter-province" class="ane-branch-filter__select">
				<option value=""><?php esc_html_e( 'Semua Provinsi', 'elemenane' ); ?></option>
			</select>
			<select id="ane-filter-city" class="ane-branch-filter__select">
				<option value=""><?php esc_html_e( 'Semua Kota', 'elemenane' ); ?></option>
			</select>
			<button id="ane-reset-filter" class="ane-branch-filter__reset">
				<?php esc_html_e( 'Reset Filter', 'elemenane' ); ?>
			</button>
		</div>

		<!-- Google Maps -->
		<div id="ane-branch-map" class="ane-branch-map"></div>

		<!-- Branch Cards -->
		<div id="ane-branch-list" class="ane-branch-list">
			<?php if ( ! empty( $branches_data ) ) : ?>
				<?php foreach ( $branches_data as $branch ) : ?>
					<article class="ane-branch-card" data-branch-id="<?php echo esc_attr( $branch['id'] ); ?>" data-province="<?php echo esc_attr( $branch['province'] ); ?>" data-city="<?php echo esc_attr( $branch['city'] ); ?>">
						<?php if ( $branch['thumbnail'] ) : ?>
							<div class="ane-branch-card__image">
								<img src="<?php echo esc_url( $branch['thumbnail'] ); ?>" alt="<?php echo esc_attr( $branch['title'] ); ?>">
							</div>
						<?php endif; ?>
						<div class="ane-branch-card__content">
							<h3 class="ane-branch-card__title"><?php echo esc_html( $branch['title'] ); ?></h3>
							<p class="ane-branch-card__address">
								<span class="dashicons dashicons-location"></span>
								<?php echo esc_html( wp_trim_words( $branch['address'], 10 ) ); ?>
							</p>
							<?php if ( $branch['phone'] ) : ?>
								<p class="ane-branch-card__phone">
									<span class="dashicons dashicons-phone"></span>
									<?php echo esc_html( $branch['phone'] ); ?>
								</p>
							<?php endif; ?>
							<div class="ane-branch-card__actions">
								<a href="#" class="ane-branch-card__detail" data-branch-id="<?php echo esc_attr( $branch['id'] ); ?>">
									<?php esc_html_e( 'Detail', 'elemenane' ); ?>
								</a>
								<a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo esc_attr( $branch['lat'] . ',' . $branch['lng'] ); ?>" target="_blank" class="ane-branch-card__directions">
									<?php esc_html_e( 'Petunjuk Arah', 'elemenane' ); ?>
								</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="ane-branch-list__empty"><?php esc_html_e( 'Belum ada cabang yang terdaftar.', 'elemenane' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Modal for Branch Detail -->
<div id="ane-branch-modal" class="ane-branch-modal" style="display: none;">
	<div class="ane-branch-modal__overlay"></div>
	<div class="ane-branch-modal__content">
		<button class="ane-branch-modal__close">&times;</button>
		<div id="ane-branch-modal-body"></div>
	</div>
</div>

<script>
	var aneBranchesData = <?php echo wp_json_encode( $branches_data ); ?>;
</script>

<?php
get_footer();
