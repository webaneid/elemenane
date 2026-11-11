<?php
/**
 * Branch archive layout: header panel, location filter, Google map, card grid, load more, detail dialog.
 * Behaviour: js/branch-archive.js (jQuery + Google Maps).
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_page_size = 12; // Keep in sync with aneMapConfig.pageSize (inc/branch-acf.php).

$elemenane_query = new WP_Query(
	array(
		'post_type'      => 'branch',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

// City ID => name, read once (branches store the ID).
$elemenane_city_names = array();
$elemenane_locations  = json_decode( (string) file_get_contents( get_template_directory() . '/inc/indonesia-locations.json' ), true );
if ( is_array( $elemenane_locations ) && ! empty( $elemenane_locations['provinces'] ) ) {
	foreach ( $elemenane_locations['provinces'] as $elemenane_province ) {
		foreach ( (array) ( $elemenane_province['cities'] ?? array() ) as $elemenane_city ) {
			$elemenane_city_names[ (string) $elemenane_city['id'] ] = (string) $elemenane_city['name'];
		}
	}
}

// Branches with map data, passed to the script and used for the first page of cards.
$elemenane_branches = array();
while ( $elemenane_query->have_posts() ) {
	$elemenane_query->the_post();

	$elemenane_map = get_field( 'ane_branch_map' );
	if ( ! $elemenane_map ) {
		continue;
	}

	$elemenane_branches[] = array(
		'id'        => get_the_ID(),
		// Plain text: escaped at output (esc_html in PHP, escapeHtml in JS).
		'title'     => html_entity_decode( get_the_title(), ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'permalink' => get_permalink(),
		'province'  => get_field( 'ane_branch_province' ),
		'city'      => get_field( 'ane_branch_city' ),
		'city_name' => $elemenane_city_names[ (string) get_field( 'ane_branch_city' ) ] ?? '',
		'district'  => get_field( 'ane_branch_district' ),
		'address'   => get_field( 'ane_branch_address' ),
		'phone'     => get_field( 'ane_branch_phone' ),
		'email'     => get_field( 'ane_branch_email' ),
		'whatsapp'  => get_field( 'ane_branch_whatsapp' ),
		'hours'     => get_field( 'ane_branch_hours' ),
		'lat'       => (float) $elemenane_map['lat'],
		'lng'       => (float) $elemenane_map['lng'],
		'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
	);
}
wp_reset_postdata();

$elemenane_total = count( $elemenane_branches );
wp_add_inline_script( 'ane-branch-archive', 'var aneBranchesData = ' . wp_json_encode( $elemenane_branches ) . ';', 'before' );
?>
<main id="main" class="site-main ane-archive ane-archive--branch" role="main">
	<?php get_template_part( 'breadcrumbs' ); ?>

	<div class="container">
		<header class="page-header ane-archive-head">
			<span class="ane-archive-head__kicker"><?php esc_html_e( 'Cabang', 'elemenane' ); ?></span>
			<h1 class="ane-archive-head__title"><?php esc_html_e( 'Lokasi Cabang', 'elemenane' ); ?></h1>
			<div class="ane-archive-head__desc"><p><?php esc_html_e( 'Temukan cabang terdekat dari lokasi Anda', 'elemenane' ); ?></p></div>
			<p class="ane-archive-head__count">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: number of branches */
						_n( '%s cabang', '%s cabang', $elemenane_total, 'elemenane' ),
						number_format_i18n( $elemenane_total )
					)
				);
				?>
			</p>
		</header>

		<form class="ane-branch-filter" role="search" aria-label="<?php esc_attr_e( 'Filter cabang', 'elemenane' ); ?>" onsubmit="return false;">
			<div class="ane-branch-filter__field">
				<label for="ane-filter-province"><?php esc_html_e( 'Provinsi', 'elemenane' ); ?></label>
				<select id="ane-filter-province" class="ane-branch-filter__select">
					<option value=""><?php esc_html_e( 'Semua Provinsi', 'elemenane' ); ?></option>
				</select>
			</div>
			<div class="ane-branch-filter__field">
				<label for="ane-filter-city"><?php esc_html_e( 'Kota', 'elemenane' ); ?></label>
				<select id="ane-filter-city" class="ane-branch-filter__select">
					<option value=""><?php esc_html_e( 'Semua Kota', 'elemenane' ); ?></option>
				</select>
			</div>
			<button type="button" id="ane-reset-filter" class="ane-branch-filter__reset"><?php esc_html_e( 'Reset Filter', 'elemenane' ); ?></button>
		</form>

		<div id="ane-branch-map" class="ane-branch-map" role="region" aria-label="<?php esc_attr_e( 'Peta lokasi cabang', 'elemenane' ); ?>"></div>

		<div id="ane-branch-list" class="ane-branch-grid" aria-live="polite">
			<?php if ( ! empty( $elemenane_branches ) ) : ?>
				<?php foreach ( array_slice( $elemenane_branches, 0, $elemenane_page_size ) as $elemenane_branch ) : ?>
					<?php get_template_part( 'tp/branch', 'card', array( 'branch' => $elemenane_branch ) ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="ane-branch-grid__empty"><?php esc_html_e( 'Belum ada cabang yang terdaftar.', 'elemenane' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $elemenane_total > $elemenane_page_size ) : ?>
			<div class="ane-branch-load-more-wrap">
				<button type="button" id="ane-branch-load-more" class="ane-branch-load-more" data-loaded="<?php echo esc_attr( (string) $elemenane_page_size ); ?>" data-total="<?php echo esc_attr( (string) $elemenane_total ); ?>">
					<?php esc_html_e( 'Tampilkan Lebih Banyak', 'elemenane' ); ?>
					<span class="ane-branch-load-more__count">(<?php echo esc_html( (string) $elemenane_page_size ); ?> / <?php echo esc_html( (string) $elemenane_total ); ?>)</span>
				</button>
			</div>
		<?php endif; ?>
	</div>
</main>

<div id="ane-branch-modal" class="ane-branch-dialog" role="dialog" aria-modal="true" aria-labelledby="ane-branch-modal-title" hidden>
	<div class="ane-branch-dialog__overlay"></div>
	<div class="ane-branch-dialog__content">
		<button type="button" class="ane-branch-dialog__close" aria-label="<?php esc_attr_e( 'Tutup', 'elemenane' ); ?>"><?php elemenane_icon( 'tutup' ); ?></button>
		<div id="ane-branch-modal-body"></div>
	</div>
</div>
