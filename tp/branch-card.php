<?php
/**
 * Branch card.
 *
 * Args (get_template_part): branch (array) with keys id, title, permalink, province, city, city_name, address, phone,
 * whatsapp, hours, lat, lng, thumbnail.
 * detail (string, arg): 'link' makes Detail a plain link to the branch page (no dialog script on that page).
 * js/branch-archive.js builds the same markup for filtered / "load more"
 * results (buildBranchCard()): keep the two in sync.
 *
 * @package elemenane
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_b = ! empty( $args['branch'] ) ? (array) $args['branch'] : array();
if ( empty( $elemenane_b['id'] ) ) {
	return;
}

$elemenane_hours = trim( (string) ( $elemenane_b['hours'] ?? '' ) );
$elemenane_hours = '' !== $elemenane_hours ? trim( (string) strtok( $elemenane_hours, "\n" ) ) : '';
$elemenane_wa    = preg_replace( '/[^0-9]/', '', (string) ( $elemenane_b['whatsapp'] ?? '' ) );
$elemenane_lat   = (float) ( $elemenane_b['lat'] ?? 0 );
$elemenane_link  = ! empty( $args['detail'] ) && 'link' === $args['detail'];
$elemenane_lng   = (float) ( $elemenane_b['lng'] ?? 0 );
?>
<article class="ane-branch-card" data-branch-id="<?php echo esc_attr( (string) $elemenane_b['id'] ); ?>" data-province="<?php echo esc_attr( (string) ( $elemenane_b['province'] ?? '' ) ); ?>" data-city="<?php echo esc_attr( (string) ( $elemenane_b['city'] ?? '' ) ); ?>">
	<div class="ane-branch-card__media">
		<?php if ( ! empty( $elemenane_b['thumbnail'] ) ) : ?>
			<img src="<?php echo esc_url( $elemenane_b['thumbnail'] ); ?>" alt="" loading="lazy" decoding="async">
		<?php else : ?>
			<span class="ane-branch-card__placeholder" aria-hidden="true"><?php elemenane_icon( 'lokasi' ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $elemenane_b['city_name'] ) ) : ?>
			<span class="ane-branch-card__city"><?php echo esc_html( (string) $elemenane_b['city_name'] ); ?></span>
		<?php endif; ?>
	</div>

	<div class="ane-branch-card__body">
		<h3 class="ane-branch-card__title"><a href="<?php echo esc_url( (string) ( $elemenane_b['permalink'] ?? '' ) ); ?>"><?php echo esc_html( (string) $elemenane_b['title'] ); ?></a></h3>

		<ul class="ane-branch-card__meta">
			<?php if ( ! empty( $elemenane_b['address'] ) ) : ?>
				<li class="ane-branch-card__address"><?php elemenane_icon( 'lokasi' ); ?><span><?php echo esc_html( (string) $elemenane_b['address'] ); ?></span></li>
			<?php endif; ?>
			<?php if ( ! empty( $elemenane_b['phone'] ) ) : ?>
				<li class="ane-branch-card__phone"><?php elemenane_icon( 'telepon' ); ?><span><?php echo esc_html( (string) $elemenane_b['phone'] ); ?></span></li>
			<?php endif; ?>
			<?php if ( '' !== $elemenane_hours ) : ?>
				<li class="ane-branch-card__hours"><?php elemenane_icon( 'jam' ); ?><span><?php echo esc_html( $elemenane_hours ); ?></span></li>
			<?php endif; ?>
		</ul>

		<div class="ane-branch-card__actions">
			<a href="<?php echo $elemenane_link ? esc_url( (string) ( $elemenane_b['permalink'] ?? '#' ) ) : '#'; ?>" class="ane-branch-card__detail"<?php echo $elemenane_link ? '' : ' data-branch-id="' . esc_attr( (string) $elemenane_b['id'] ) . '"'; ?>><?php esc_html_e( 'Detail', 'elemenane' ); ?></a>
			<?php if ( $elemenane_lat || $elemenane_lng ) : ?>
			<a href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . $elemenane_lat . ',' . $elemenane_lng ); ?>" target="_blank" rel="noopener" class="ane-branch-card__directions"><?php esc_html_e( 'Petunjuk Arah', 'elemenane' ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $elemenane_wa ) : ?>
				<a href="<?php echo esc_url( 'https://wa.me/' . $elemenane_wa ); ?>" target="_blank" rel="noopener" class="ane-branch-card__wa" aria-label="<?php esc_attr_e( 'Chat WhatsApp', 'elemenane' ); ?>"><?php elemenane_icon( 'whatsapp' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
