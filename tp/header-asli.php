<?php
/**
 * Native Theme Header Template.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="site-header ane-header">
	<div class="ane-mobile-menu">
		<div class="ane-atas">
			<div class="ane-logo">
				<?php
				$logoimg = get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-elemenane.png' );
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					printf(
						'<a href="%1$s" rel="home"><img src="%2$s" title="%3$s" alt="%3$s"></a>',
						esc_url( get_home_url() ),
						esc_url( (string) $logoimg ),
						esc_attr( get_bloginfo( 'name' ) )
					);
				}
				?>
			</div>
			<div class="mobile-menu-area">
				<div class="mobile-menu">
					<nav id="mobile-menu-active">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menuutama',
								'container'      => 'div',
								'fallback_cb'    => 'wp_page_menu',
								'depth'          => 4,
							)
						);
						?>
					</nav>
				</div>
			</div>
		</div>
		<div class="ane-bawah">
			<div class="ane-menu">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'mobilemenu',
						'container_id'   => 'main-menu',
						'echo'           => true,
						'fallback_cb'    => 'wp_page_menu',
						'items_wrap'     => '<ul class="main-menu">%3$s</ul>',
						'depth'          => 4,
					)
				);
				?>
			</div>
			<div class="ane-tombol">
				<a class="nav-link search-button" href="#" aria-label="<?php echo esc_attr__( 'Search', 'elemenane' ); ?>"><?php elemenane_icon( 'search' ); ?></a>
			</div>
		</div>
	</div>
	<div class="desktop-menu">
		<?php get_template_part( 'tp/content', 'top-header' ); ?>
		<div class="atas">
			<div class="container">
				<div class="atas-isi">
					<div class="ane-logo">
						<?php
						$logoimg = get_theme_mod( 'logo', get_template_directory_uri() . '/img/logo-elemenane.png' );
						if ( has_custom_logo() ) {
							the_custom_logo();
						} else {
							printf(
								'<a class="logo" href="%1$s" rel="home"><img src="%2$s" title="%3$s" alt="%3$s"></a>',
								esc_url( get_home_url() ),
								esc_url( (string) $logoimg ),
								esc_attr( get_bloginfo( 'name' ) )
							);
						}
						?>
					</div>
					<div class="ane-isi">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menuutama',
								'container_id'   => 'main-menu',
								'echo'           => true,
								'fallback_cb'    => 'wp_page_menu',
								'items_wrap'     => '<ul class="main-menu">%3$s</ul>',
								'depth'          => 4,
							)
						);
						?>
					</div>
					<div class="ane-kanan">
						<?php
						$cta_link = get_field( 'ane_header_cta_link', 'option' );
						if ( is_array( $cta_link ) && ! empty( $cta_link['url'] ) ) :
							$link_url    = $cta_link['url'];
							$link_title  = ! empty( $cta_link['title'] ) ? $cta_link['title'] : __( 'Get In Touch', 'elemenane' );
							$link_target = ! empty( $cta_link['target'] ) ? $cta_link['target'] : '_self';
							?>
							<a class="btn-primary" href="<?php echo esc_url( (string) $link_url ); ?>" target="<?php echo esc_attr( (string) $link_target ); ?>">
								<?php echo esc_html( (string) $link_title ); ?>
							</a>
						<?php else : ?>
							<a class="btn-primary" href="#contact"><?php echo esc_html__( 'Get In Touch', 'elemenane' ); ?></a>
						<?php endif; ?>

						<a class="icon-button search-button" href="#" aria-label="<?php echo esc_attr__( 'Search', 'elemenane' ); ?>">
							<?php elemenane_icon( 'search' ); ?>
						</a>

						<?php
						$show_product = get_field( 'ane_header_show_product', 'option' );
						if ( $show_product ) :
							$product_archive = get_post_type_archive_link( 'product' );
							if ( $product_archive ) :
								?>
								<a class="icon-button cart-button" href="<?php echo esc_url( $product_archive ); ?>" aria-label="<?php echo esc_attr__( 'Products', 'elemenane' ); ?>">
									<span class="solar--cart-bold"></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>

						<?php
						$phone_number = get_field( 'ane_telepon', 'option' );
						if ( $phone_number ) :
							$phone_label = get_field( 'ane_telepon_label', 'option' );
							if ( empty( $phone_label ) ) {
								$phone_label = __( 'Sales Team', 'elemenane' );
							}
							?>
							<div class="header-phone">
								<?php elemenane_icon( 'telepon' ); ?>
								<div class="phone-info">
									<span class="phone-label"><?php echo esc_html( (string) $phone_label ); ?></span>
									<span class="phone-number"><?php echo esc_html( (string) $phone_number ); ?></span>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="bawah">
			<div class="container">
				<div class="bawah-row">
					<div class="ane-kiri">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menuutama',
								'container_id'   => 'main-menu',
								'echo'           => true,
								'fallback_cb'    => 'wp_page_menu',
								'items_wrap'     => '<ul class="main-menu">%3$s</ul>',
								'depth'          => 4,
							)
						);
						?>
					</div>
					<div class="ane-kanan">
						<a class="search-button" href="#" aria-label="<?php echo esc_attr__( 'Search', 'elemenane' ); ?>"><?php elemenane_icon( 'search' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<div class="search-bar">
	<div class="search-bar-isi">
		<a href="#" class="search-back" aria-label="<?php echo esc_attr__( 'Close search', 'elemenane' ); ?>">
			<?php elemenane_icon( 'tutup' ); ?>
		</a>
		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="text" class="form-control" placeholder="<?php echo esc_attr_x( 'Looking for Something? &hellip;', 'placeholder', 'elemenane' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
			<button type="submit" aria-label="<?php echo esc_attr__( 'Search', 'elemenane' ); ?>"><?php elemenane_icon( 'search' ); ?></button>
		</form>

		<?php get_template_part( 'tp/banner/header' ); ?>
	</div>
</div>
<?php
if ( function_exists( 'ane_output_navigation_schema' ) ) {
	ane_output_navigation_schema();
}
