<?php
/**
 * Single branch: "Hubungi Kami" card.
 *
 * Args: branch (array) from elemenane_get_branch_data().
 * Shows the branch's own phone / WhatsApp / email; when the branch has none, the company contacts from the theme
 * settings (ACF options) are shown instead, with a note saying so.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_b       = ! empty( $args['branch'] ) ? (array) $args['branch'] : array();
$elemenane_contact = elemenane_get_branch_contacts( $elemenane_b );
$elemenane_icons   = array(
	'phone'    => 'telepon',
	'whatsapp' => 'whatsapp',
	'email'    => 'email',
	'website'  => 'link',
);

if ( empty( $elemenane_contact['items'] ) ) {
	return;
}

$elemenane_wa = '';
foreach ( $elemenane_contact['items'] as $elemenane_item ) {
	if ( 'whatsapp' === $elemenane_item['type'] ) {
		$elemenane_wa = $elemenane_item['href'];
		break;
	}
}
?>
<section class="ane-bcard" aria-labelledby="ane-contact-title">
	<h2 id="ane-contact-title" class="ane-section-title"><?php esc_html_e( 'Hubungi Kami', 'elemenane' ); ?></h2>

	<?php if ( 'company' === $elemenane_contact['source'] ) : ?>
		<p class="ane-bcard__note"><?php esc_html_e( 'Cabang ini belum memiliki kontak sendiri. Silakan hubungi kantor pusat kami.', 'elemenane' ); ?></p>
	<?php endif; ?>

	<ul class="ane-bcard__list">
		<?php foreach ( $elemenane_contact['items'] as $elemenane_item ) : ?>
			<li>
				<?php elemenane_icon( $elemenane_icons[ $elemenane_item['type'] ] ?? 'telepon' ); ?>
				<div>
					<strong><?php echo esc_html( $elemenane_item['label'] ); ?></strong>
					<a href="<?php echo esc_attr( $elemenane_item['href'] ); ?>"<?php echo 0 === strpos( $elemenane_item['href'], 'http' ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo wp_kses( $elemenane_item['text'], array() ); ?></a>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php if ( '' !== $elemenane_wa ) : ?>
		<a class="ane-bcard__cta" href="<?php echo esc_url( $elemenane_wa ); ?>" target="_blank" rel="noopener">
			<?php elemenane_icon( 'whatsapp' ); ?>
			<?php esc_html_e( 'Chat WhatsApp', 'elemenane' ); ?>
		</a>
	<?php endif; ?>
</section>
