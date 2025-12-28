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
		<?php if ( have_rows( 'ane_sosmed', 'option' ) ) : ?>
			<div class="ane-sosmed">
				<ul>
					<?php
					while ( have_rows( 'ane_sosmed', 'option' ) ) :
						the_row();

						// Define social media platforms with their data
						$social_platforms = array(
							'whatsapp'  => array(
								'value'  => get_sub_field( 'ane_whatsapp' ),
								'url'    => 'https://wa.me/' . get_sub_field( 'ane_whatsapp' ),
								'label'  => 'WhatsApp',
								'icon'   => 'ane-whatsapp',
								'target' => true,
							),
							'facebook'  => array(
								'value'  => get_sub_field( 'ane_facebook' ),
								'url'    => get_sub_field( 'ane_facebook' ),
								'label'  => 'Facebook',
								'icon'   => 'ane-facebook',
								'target' => true,
							),
							'twitter'   => array(
								'value'  => get_sub_field( 'ane_twitter' ),
								'url'    => 'https://twitter.com/' . get_sub_field( 'ane_twitter' ),
								'label'  => 'Twitter',
								'icon'   => 'ane-twitter',
								'target' => true,
							),
							'youtube'   => array(
								'value'  => get_sub_field( 'ane_youtube' ),
								'url'    => get_sub_field( 'ane_youtube' ),
								'label'  => 'Youtube',
								'icon'   => 'ane-youtube',
								'target' => true,
							),
							'instagram' => array(
								'value'  => get_sub_field( 'ane_instagram' ),
								'url'    => 'https://www.instagram.com/' . get_sub_field( 'ane_instagram' ),
								'label'  => 'Instagram',
								'icon'   => 'ane-instagram',
								'target' => true,
							),
							'linkedin'  => array(
								'value'  => get_sub_field( 'ane_linkedin' ),
								'url'    => get_sub_field( 'ane_linkedin' ),
								'label'  => 'LinkedIn',
								'icon'   => 'ane-linkedin',
								'target' => true,
							),
						);

						// Loop through platforms and render links
						foreach ( $social_platforms as $platform => $data ) :
							if ( ! empty( $data['value'] ) ) :
								?>
								<a href="<?php echo esc_url( $data['url'] ); ?>"
								   <?php echo $data['target'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
								   aria-label="<?php echo esc_attr( sprintf( __( 'Follow us on %s', 'elemenane' ), $data['label'] ) ); ?>">
									<li class="<?php echo esc_attr( $platform ); ?>">
										<i class="<?php echo esc_attr( $data['icon'] ); ?>"></i>
										<span><?php echo esc_html( $data['label'] ); ?></span>
									</li>
								</a>
								<?php
							endif;
						endforeach;

					endwhile;
					?>
				</ul>
			</div>
		<?php endif; ?>
	</footer>
</article>
