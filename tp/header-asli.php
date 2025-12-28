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
                        <?php get_template_part('tp/banner/header') ?>
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