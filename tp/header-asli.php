<header class="site-header ane-header">
    <div class="ane-mobile-menu">
        <div class="ane-atas">
            <div class="ane-logo">
                <?php
                    $logoimg = get_theme_mod( 'logo', get_template_directory_uri().'/img/logo-elemenane.png' );
                    if ( has_custom_logo() ) {
                    the_custom_logo();
                    }
                    else {
                        echo '<a href="' .get_home_url(). '" rel="home"><img src="' .$logoimg. '" title="' .get_bloginfo( 'name' ).'" alt="' .get_bloginfo( 'name' ).'"></a>';
                    }
                ?>
            </div>
            <div class="mobile-menu-area">
                <div class="mobile-menu">
                    <nav id="mobile-menu-active">
                        <?php
                                    wp_nav_menu(  array(
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
                    wp_nav_menu(  array(
                    'theme_location' => 'mobilemenu',
                    'container_id'    => 'main-menu',
                    'echo'            => true,
                    'fallback_cb'    => 'wp_page_menu',
                    'items_wrap'      => '<ul class="main-menu">%3$s</ul>',
                    'depth'          => 4,
                    )
                    );

                ?>
            </div>
            <div class="ane-tombol">
                <a class="nav-link search-button" href="#"><i class="ane-search"></i></a>
            </div>
        </div>

    </div>
    <div class="desktop-menu">
        <?php get_template_part('tp/content','top-header') ?>
        <div class="atas">
            <div class="container">
                <div class="atas-isi">
                    <div class="ane-logo">
                        <?php
                            $logoimg = get_theme_mod( 'logo', get_template_directory_uri().'/img/logo-elemenane.png' );
                            if ( has_custom_logo() ) {
                            the_custom_logo();
                            }
                            else {
                                echo '<a class="logo" href="' .get_home_url(). '" rel="home"><img src="' .$logoimg. '" title="' .get_bloginfo( 'name' ).'" alt="' .get_bloginfo( 'name' ).'"></a>';
                            }
                        ?>
                    </div>
                    <div class="ane-isi">
                        <?php
                            wp_nav_menu(  array(
                            'theme_location' => 'menuutama',
                            'container_id'    => 'main-menu',
                            'echo'            => true,
                            'fallback_cb'    => 'wp_page_menu',
                            'items_wrap'      => '<ul class="main-menu">%3$s</ul>',
                            'depth'          => 4,
                            )
                            );
                        ?>
                    </div>
                    <div class="ane-kanan">
                        <?php
                        // Get In Touch Button - ACF Link Field
                        $cta_link = get_field( 'ane_header_cta_link', 'option' );
                        if ( $cta_link ) :
                            $link_url    = $cta_link['url'];
                            $link_title  = $cta_link['title'] ? $cta_link['title'] : __( 'Get In Touch', 'elemenane' );
                            $link_target = $cta_link['target'] ? $cta_link['target'] : '_self';
                            ?>
                            <a class="btn-primary" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>">
                                <?php echo esc_html( $link_title ); ?>
                            </a>
                        <?php else : ?>
                            <a class="btn-primary" href="#contact"><?php echo esc_html__( 'Get In Touch', 'elemenane' ); ?></a>
                        <?php endif; ?>

                        <!-- Search Button -->
                        <a class="icon-button search-button" href="#" aria-label="<?php echo esc_attr__( 'Search', 'elemenane' ); ?>">
                            <i class="ane-search"></i>
                        </a>

                        <?php
                        // Product/Cart Link - Show only if enabled in ACF
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
                        // Phone Number - Show only if filled
                        $phone_number = get_field( 'ane_telepon', 'option' );
                        if ( $phone_number ) :
                            $phone_label = get_field( 'ane_telepon_label', 'option' );
                            if ( empty( $phone_label ) ) {
                                $phone_label = __( 'Sales Team', 'elemenane' );
                            }
                            ?>
                            <div class="header-phone">
                                <i class="ane-phone"></i>
                                <div class="phone-info">
                                    <span class="phone-label"><?php echo esc_html( $phone_label ); ?></span>
                                    <span class="phone-number"><?php echo esc_html( $phone_number ); ?></span>
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
                            wp_nav_menu(  array(
                            'theme_location' => 'menuutama',
                            'container_id'    => 'main-menu',
                            'echo'            => true,
                            'fallback_cb'    => 'wp_page_menu',
                            'items_wrap'      => '<ul class="main-menu">%3$s</ul>',
                            'depth'          => 4,
                            )
                            );

                        ?>
                    </div>
                    <div class="ane-kanan">
                        <a class="search-button" href="#"><i class="ane-search"></i></a>
                    </div>
                </div>
            </div>
        </div>

    </div>


</header>
<div class="search-bar">
    <div class="search-bar-isi">

        <a href="#" class="search-back">
            <i class="ane-tutup"></i>
        </a>
        <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <input type="text" class="form-control" placeholder="<?php echo esc_attr_x( 'Looking for Something?  &hellip;', 'placeholder', 'webane' ); ?>" value="<?php echo get_search_query(); ?>" name="s" >
            <button><i class="ane-search"></i></button>
        </form>

        <?php get_template_part('tp/banner/header') ?>

    </div>

</div>
<?php
// Output navigation schema for Google Sitelinks
if ( function_exists( 'ane_output_navigation_schema' ) ) {
	ane_output_navigation_schema();
}
?>