<?php
/**
 * Social Media Links Template
 *
 * Displays social media profile links from ACF options.
 *
 * @package elemenane
 * @since 1.0.0
 */

// Get social media URLs from ACF options
$social_links = array(
	'whatsapp'  => array(
		'url'   => get_field( 'ane_whatsapp', 'option' ),
		'label' => __( 'WhatsApp', 'elemenane' ),
	),
	'facebook'  => array(
		'url'   => get_field( 'ane_facebook', 'option' ),
		'label' => __( 'Facebook', 'elemenane' ),
	),
	'twitter'   => array(
		'url'   => get_field( 'ane_twitter', 'option' ),
		'label' => __( 'Twitter', 'elemenane' ),
	),
	'instagram' => array(
		'url'   => get_field( 'ane_instagram', 'option' ),
		'label' => __( 'Instagram', 'elemenane' ),
	),
	'youtube'   => array(
		'url'   => get_field( 'ane_youtube', 'option' ),
		'label' => __( 'YouTube', 'elemenane' ),
	),
	'tiktok'    => array(
		'url'   => get_field( 'ane_tiktok', 'option' ),
		'label' => __( 'TikTok', 'elemenane' ),
	),
	'telegram'  => array(
		'url'   => get_field( 'ane_telegram', 'option' ),
		'label' => __( 'Telegram', 'elemenane' ),
	),
	'threads'   => array(
		'url'   => get_field( 'ane_threads', 'option' ),
		'label' => __( 'Threads', 'elemenane' ),
	),
	'linkedin'  => array(
		'url'   => get_field( 'ane_linkedin', 'option' ),
		'label' => __( 'LinkedIn', 'elemenane' ),
	),
);
?>

<div class="ane-sosmed">
	<ul>
		<?php
		foreach ( $social_links as $platform => $data ) :
			if ( ! empty( $data['url'] ) ) :
				?>
				<li class="<?php echo esc_attr( $platform ); ?>">
					<a href="<?php echo esc_url( $data['url'] ); ?>"
					   target="_blank"
					   rel="noopener noreferrer"
					   aria-label="<?php echo esc_attr( sprintf( __( 'Follow us on %s', 'elemenane' ), $data['label'] ) ); ?>">
						<i class="ane-<?php echo esc_attr( $platform ); ?>"></i>
					</a>
				</li>
				<?php
			endif;
		endforeach;
		?>
	</ul>
</div>
