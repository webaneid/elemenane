<?php
/**
 * Linktree Content Template
 *
 * Displays logo, custom links, and social media profiles for link aggregator pages.
 *
 * @package elemenane
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'linktree-content' ); ?>>
	<header class="entry-header">
		<!-- Logo Section -->
		<div class="ane-logo">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				$default_logo = get_template_directory_uri() . '/img/logo-newsane.png';
				$logo_url     = get_theme_mod( 'logo', $default_logo );
				$site_name    = get_bloginfo( 'name' );
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img src="<?php echo esc_url( $logo_url ); ?>"
					     alt="<?php echo esc_attr( $site_name ); ?>"
					     title="<?php echo esc_attr( $site_name ); ?>">
				</a>
				<?php
			}
			?>
		</div>

		<!-- Title & Tagline Section -->
		<div class="ane-title">
			<?php
			$company_name = get_field( 'ane_com_nama', 'option' );
			$tagline      = get_field( 'ane_com_tag', 'option' );

			if ( ! empty( $company_name ) ) :
				?>
				<h2><?php echo esc_html( $company_name ); ?></h2>
			<?php endif; ?>

			<?php if ( ! empty( $tagline ) ) : ?>
				<h3><?php echo esc_html( $tagline ); ?></h3>
			<?php endif; ?>
		</div>
	</header>

	<!-- Custom Links Section -->
	<div class="entry-content">
		<?php
		if ( have_rows( 'ane_linktree' ) ) :
			while ( have_rows( 'ane_linktree' ) ) :
				the_row();

				$link_type  = get_sub_field( 'ane_jenislink' );
				$link_url   = get_sub_field( 'ane_url' );
				$link_label = get_sub_field( 'ane_label' );

				if ( ! empty( $link_url ) && ! empty( $link_label ) ) :
					?>
					<a href="<?php echo esc_url( $link_url ); ?>"
					   target="_blank"
					   rel="noopener noreferrer"
					   title="<?php echo esc_attr( $link_label ); ?>">
						<div class="ane-links <?php echo esc_attr( $link_type ); ?>">
							<div class="icon-ane"></div>
							<div class="ane-label">
								<?php echo esc_html( $link_label ); ?>
							</div>
						</div>
					</a>
					<?php
				endif;
			endwhile;
		endif;
		?>
	</div>

	<!-- Social Media Section -->
	<footer class="entry-footer">
		<h2><?php esc_html_e( 'Our Social Media', 'elemenane' ); ?></h2>
		<div class="ane-sosmed">
			<ul>
				<?php
				// Define social media platforms with their data from ACF options
				$social_platforms = array(
					'whatsapp'  => array(
						'url'   => get_field( 'ane_whatsapp', 'option' ),
						'label' => 'WhatsApp',
						'icon'  => 'ane-whatsapp',
					),
					'facebook'  => array(
						'url'   => get_field( 'ane_facebook', 'option' ),
						'label' => 'Facebook',
						'icon'  => 'ane-facebook',
					),
					'twitter'   => array(
						'url'   => get_field( 'ane_twitter', 'option' ),
						'label' => 'Twitter',
						'icon'  => 'ane-twitter',
					),
					'youtube'   => array(
						'url'   => get_field( 'ane_youtube', 'option' ),
						'label' => 'Youtube',
						'icon'  => 'ane-youtube',
					),
					'instagram' => array(
						'url'   => get_field( 'ane_instagram', 'option' ),
						'label' => 'Instagram',
						'icon'  => 'ane-instagram',
					),
					'tiktok'    => array(
						'url'   => get_field( 'ane_tiktok', 'option' ),
						'label' => 'TikTok',
						'icon'  => 'ane-tiktok',
					),
					'telegram'  => array(
						'url'   => get_field( 'ane_telegram', 'option' ),
						'label' => 'Telegram',
						'icon'  => 'ane-telegram',
					),
					'threads'   => array(
						'url'   => get_field( 'ane_threads', 'option' ),
						'label' => 'Threads',
						'icon'  => 'ane-threads',
					),
					'linkedin'  => array(
						'url'   => get_field( 'ane_linkedin', 'option' ),
						'label' => 'LinkedIn',
						'icon'  => 'ane-linkedin',
					),
				);

				// Loop through platforms and render links
				foreach ( $social_platforms as $platform => $data ) :
					if ( ! empty( $data['url'] ) ) :
						?>
						<a href="<?php echo esc_url( $data['url'] ); ?>"
						   target="_blank" rel="noopener noreferrer"
						   aria-label="<?php echo esc_attr( sprintf( __( 'Follow us on %s', 'elemenane' ), $data['label'] ) ); ?>">
							<li class="<?php echo esc_attr( $platform ); ?>">
								<i class="<?php echo esc_attr( $data['icon'] ); ?>"></i>
								<span><?php echo esc_html( $data['label'] ); ?></span>
							</li>
						</a>
						<?php
					endif;
				endforeach;
				?>
			</ul>
		</div>
	</footer>
</article>
