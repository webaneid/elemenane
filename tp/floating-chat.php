<?php
/**
 * Floating chat (WhatsApp) widget.
 *
 * Rendered from footer.php when ACF option `ane_cs_aktif` is on. Behaviour: js/floating-chat.js.
 * ACF: ane_cs_label, ane_cs_welcome, repeater ane_cs (ane_whatsapp, ane_image, ane_nama, ane_area).
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! get_field( 'ane_cs_aktif', 'option' ) ) {
	return;
}

$elemenane_title   = (string) get_field( 'ane_cs_label', 'option' );
$elemenane_welcome = (string) get_field( 'ane_cs_welcome', 'option' );
if ( '' === $elemenane_title ) {
	$elemenane_title = __( 'Chat with us', 'elemenane' );
}

$elemenane_agents  = array();
$elemenane_numbers = array();
if ( have_rows( 'ane_cs', 'option' ) ) {
	while ( have_rows( 'ane_cs', 'option' ) ) {
		the_row();
		$elemenane_phone = preg_replace( '/[^0-9]/', '', (string) get_sub_field( 'ane_whatsapp' ) );
		if ( '' === $elemenane_phone ) {
			continue;
		}
		$elemenane_image = get_sub_field( 'ane_image' );
		$elemenane_thumb = '';
		if ( is_array( $elemenane_image ) ) {
			$elemenane_thumb = isset( $elemenane_image['sizes']['sarika-square-sm'] ) ? (string) $elemenane_image['sizes']['sarika-square-sm'] : (string) ( $elemenane_image['url'] ?? '' );
		}
		$elemenane_agents[]  = array(
			'phone' => $elemenane_phone,
			'name'  => (string) get_sub_field( 'ane_nama' ),
			'area'  => (string) get_sub_field( 'ane_area' ),
			'thumb' => $elemenane_thumb,
		);
		$elemenane_numbers[] = $elemenane_phone;
	}
}
?>
<div class="ane-chat" data-ane-chat data-numbers="<?php echo esc_attr( (string) wp_json_encode( $elemenane_numbers ) ); ?>">
	<section id="ane-chat-panel" class="ane-chat__panel" role="dialog" aria-modal="false" aria-labelledby="ane-chat-title" hidden>
		<header class="ane-chat__head">
			<span class="ane-chat__avatar" aria-hidden="true"><?php elemenane_icon( 'whatsapp' ); ?></span>
			<div class="ane-chat__heading">
				<h2 id="ane-chat-title" class="ane-chat__title"><?php echo esc_html( $elemenane_title ); ?></h2>
				<p class="ane-chat__status"><?php esc_html_e( 'via WhatsApp', 'elemenane' ); ?></p>
			</div>
			<button type="button" class="ane-chat__close" data-chat-close aria-label="<?php esc_attr_e( 'Close chat', 'elemenane' ); ?>"><?php elemenane_icon( 'tutup' ); ?></button>
		</header>

		<div class="ane-chat__body">
			<?php if ( '' !== $elemenane_welcome ) : ?>
				<p class="ane-chat__bubble"><?php echo esc_html( $elemenane_welcome ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $elemenane_agents ) ) : ?>
				<p class="ane-chat__label"><?php esc_html_e( 'Or chat directly with our admins', 'elemenane' ); ?></p>
				<ul class="ane-chat__agents">
					<?php foreach ( $elemenane_agents as $elemenane_agent ) : ?>
						<li>
							<a class="ane-chat__agent" href="<?php echo esc_url( 'https://wa.me/' . $elemenane_agent['phone'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php if ( '' !== $elemenane_agent['thumb'] ) : ?>
									<img class="ane-chat__photo" src="<?php echo esc_url( $elemenane_agent['thumb'] ); ?>" alt="" width="44" height="44" loading="lazy" decoding="async">
								<?php else : ?>
									<span class="ane-chat__photo ane-chat__photo--empty" aria-hidden="true"><?php elemenane_icon( 'whatsapp' ); ?></span>
								<?php endif; ?>
								<span class="ane-chat__who">
									<strong><?php echo esc_html( $elemenane_agent['name'] ); ?></strong>
									<span><?php echo esc_html( $elemenane_agent['area'] ); ?></span>
								</span>
								<span class="ane-chat__go" aria-hidden="true"><?php elemenane_icon( 'chevron-right' ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<form class="ane-chat__form" data-chat-form novalidate>
			<label class="screen-reader-text" for="ane-chat-input"><?php esc_html_e( 'Your message', 'elemenane' ); ?></label>
			<input id="ane-chat-input" class="ane-chat__input" type="text" name="message" autocomplete="off" placeholder="<?php esc_attr_e( 'Type a message...', 'elemenane' ); ?>">
			<button type="submit" class="ane-chat__send" aria-label="<?php esc_attr_e( 'Send WhatsApp Message', 'elemenane' ); ?>"><?php elemenane_icon( 'send' ); ?></button>
			<p class="ane-chat__error" data-chat-error role="alert" hidden></p>
		</form>
	</section>

	<button type="button" class="ane-chat__fab" data-chat-toggle aria-expanded="false" aria-controls="ane-chat-panel" aria-label="<?php echo esc_attr( $elemenane_title ); ?>">
		<span class="ane-chat__fab-icon ane-chat__fab-icon--open" aria-hidden="true"><?php elemenane_icon( 'whatsapp' ); ?></span>
		<span class="ane-chat__fab-icon ane-chat__fab-icon--close" aria-hidden="true"><?php elemenane_icon( 'tutup' ); ?></span>
		<span class="ane-chat__fab-text"><?php echo esc_html( $elemenane_title ); ?></span>
	</button>
</div>
