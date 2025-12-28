<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Get base URL for assets (CDN disabled, using local only)
 *
 * @return string Sanitized base URL for assets
 */
function get_base_url_assets() {
    // Always use local assets
    $base_url = get_template_directory_uri();

    /**
     * Filter the base URL for assets
     *
     * @param string $base_url The base URL
     */
    $base_url = apply_filters( 'ane_assets_base_url', $base_url );

    return esc_url( $base_url );
}

/**
 * Get asset URL
 *
 * @param string $path Asset path relative to base URL
 * @return string Full asset URL
 */
function ane_get_asset_url( $path ) {
    return esc_url( trailingslashit( get_base_url_assets() ) . ltrim( $path, '/' ) );
}

function ane_enqueue_scripts(){
	wp_enqueue_style( 'FontAne', get_base_url_assets() . '/css/FontAne.css', array(), '1.0.0');
	wp_enqueue_style( 'magnific-popup','https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css' );
	wp_enqueue_style( 'OwlCarouse','https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css' );
    //wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');

	wp_enqueue_style( 'main-css', get_base_url_assets() . '/css/main.min.css', array(), '1.3.6');

	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery' ,'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js');
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'magnific', 'https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js' );
	wp_enqueue_script( 'OwlCarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js' );
	wp_enqueue_script( 'bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js');

	wp_enqueue_script( 'main-js', get_base_url_assets() . '/js/newsane.js', array(), '1.0.0');
}
add_action( 'wp_enqueue_scripts', 'ane_enqueue_scripts' );

/**
 * Filters the excerpt length
 * 
 * @param int $length Default excerpt length
 * @return int Sanitized excerpt length
 */
function ane_excerpt_length( $length ) {
    // Ensure we have a positive integer
    $safe_length = absint( 15 );
    
    // Set minimum and maximum bounds
    $min_length = 1;
    $max_length = 15;
    
    // Clamp the value between min and max
    return max( $min_length, min( $safe_length, $max_length ) );
}

// Add the filter with proper priority
add_filter( 'excerpt_length', 'ane_excerpt_length', 10 );

function ane_change_excerpt( $text )
{
    $pos = strrpos( $text, '[');
    if ($pos === false)
    {
        return $text;
    }

    return rtrim (substr($text, 0, $pos) );
}
add_filter('get_the_excerpt', 'ane_change_excerpt');

// Hide sticky posts
/**
 * Hides the Sticky Checkbox on the Post Edit/ Add New Post screen
 *
 * @return void
 */
function ane_hide_sticky_checkbox(): void {
    ?>
    <script>

            function hideAdminStickyCheckbox() {

                // Grab all labels
                let labels = jQuery('.components-panel label.components-checkbox-control__label');

                labels.each(function(){
                    let label = jQuery(this);

                    if ( label.html() == 'Stick to the top of the blog') {
                        // We found the label

                        // Hide with jQuery
                        label.closest('.components-panel__row').hide();

                        // Stop
                        return;
                    }
                });
            }

            // Run when the DOM changes for that panel
            jQuery('body').on('DOMNodeInserted', '.edit-post-sidebar .edit-post-post-status', hideAdminStickyCheckbox);

    </script>
    <?php
}
add_action('admin_footer-post.php', 'ane_hide_sticky_checkbox', 999, 0);
add_action('admin_footer-post-new.php', 'ane_hide_sticky_checkbox', 999, 0);

/**
 * Adds lightbox functionality to image links in content
 * 
 * @param string $content The post content
 * @return string Modified content with gallery popup classes
 */
function ane_popup_gallery( $content ) {
    if ( empty( $content ) || ! is_string( $content ) ) {
        return $content;
    }

    global $post;
    if ( ! $post instanceof WP_Post ) {
        return $content;
    }

    // Build a pattern to match any image link
    $pattern = '/<a(.*?)href=([\'"])(.*?\.(bmp|gif|jpeg|jpg|png|webp|svg))([\'"])(.*?)>/i';

    // Sanitize the title
    $safe_title = esc_attr( $post->post_title );
    
    // Create replacement with sanitized attributes
    $replacement = sprintf(
        '<a$1href=$2$3$5 class="gallery-popup" title="%s"$6>',
        $safe_title
    );

    // Perform the replacement
    $content = preg_replace( $pattern, $replacement, $content );

    return $content;
}

// Add the filter with standard priority
add_filter( 'the_content', 'ane_popup_gallery', 10 );


// Caption shortcode removed - WordPress handles this natively

// 03. embeded video
function ane_get_embedded_media($type = array())
{
    $content = do_shortcode(apply_filters('the_content', get_the_content()));
    $embed = get_media_embedded_in_content($content, $type);
    if (in_array('audio', $type)):
        $output = str_replace('?visual=true', '?visual=false', $embed[0]); else:
        $output = $embed[0];
    endif;

    return $output;
}
// 04. Youtube Thumbnail Image
function ane_get_youtube_thumbnail($url, $size = ''){
    $thumbBaseUri = 'https://img.youtube.com/vi/';
    $id = '';
    $size = $size ? $size.'default.jpg': 'default.jpg';
    if(preg_match('/iframe/', $url)){
        $id = preg_replace('/[\s\S]*\/embed\/|"[\s\S]*|\?[\s\S]*/', '', $url);
    }
    elseif(preg_match('/youtu\.be/', $url) || $embed = preg_match('/embed/', $url)){
        $id = preg_replace("/^((?![^\/]+$).)*/", '', $url);
    }
    else{
        $id = preg_replace("/^((?![^\?v]+$).)*=|&[^\&]*/", '', $url);
    }

    return "$thumbBaseUri$id/$size";
}
// meta post
/**
 * Displays post meta information including author, date, and view count
 *
 * @return string Sanitized HTML for post meta information
 */
function ane_post_meta_v1() {
    // Get post object
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return '';
    }

    // Get author information safely
    $author_id = absint( $post->post_author );
    $display_name = get_the_author_meta( 'display_name', $author_id );
    
    if ( empty( $display_name ) ) {
        $display_name = get_the_author_meta( 'nickname', $author_id );
    }
    
    // Sanitize display name
    $display_name = esc_html( $display_name );
    
    // Get and format date
    $waktu = esc_html( get_the_date( 'j F Y' ) );
    
    // Get author email and avatar
    $author_email = get_the_author_meta( 'user_email', $author_id );
    $avatar = get_avatar( $author_email, 90, '', $display_name, array( 'class' => 'author-avatar' ) );
    
    // Get post views (assuming ane_get_views() is a custom function)
    $views = isset( $post->ID ) ? esc_html( ane_get_views( $post->ID ) ) : '0';
    
    // Build HTML with proper escaping
    $output = sprintf(
        '<div class="pmi-v1">
            <ul>
                <li class="author">%s%s</li>
                <li><i class="ane-kalender"></i> %s</li>
                <li><i class="ane-mata"></i>%s</li>
            </ul>
        </div>',
        $avatar,
        $display_name,
        $waktu,
        $views
    );
    
    return wp_kses_post( $output );
}

/**
 * Displays post meta information including author, date, time, reading time, and view count
 *
 * @return string Sanitized HTML for post meta information
 */
function ane_post_meta_v2() {
    // Get post object
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return '';
    }

    // Get and sanitize date and time
    $waktu = esc_html( get_the_date( 'l, j F Y' ) );
    $jam = esc_html( get_the_time( 'G:i' ) );
    
    // Get author name using custom function
    $author = esc_html( ane_author_name() );
    
    // Get reading time and views
    $reading_time = esc_html( ane_reading_time() );
    $views = isset( $post->ID ) ? esc_html( ane_get_views( $post->ID ) ) : '0';
    
    // Prepare translatable strings
    $by_text = esc_html__( 'By: ', 'elemenane' );
    $at_text = esc_html__( ' on: ', 'elemenane' );
    $reading_text = esc_html__( 'Reading time', 'elemenane' );
    $views_text = esc_html__( 'views', 'elemenane' );
    
    // Build HTML with proper escaping
    $output = sprintf(
        '<div class="post-meta-info">
            <ul>
                <li>%s%s%s%s - %s</li>
                <li>%s: %s</li>
                <li>%s: %s</li>
            </ul>
        </div>',
        $by_text,
        $author,
        $at_text,
        $waktu,
        $jam,
        $reading_text,
        $reading_time,
        $views_text,
        $views
    );
    
    return wp_kses_post( $output );
}

/**
 * Displays the time ago meta information for a post
 *
 * @return string Sanitized HTML for time ago display
 */
function ane_content_meta_date() {
    // Get post timestamp
    $timestamp = get_the_time( 'U' );
    if ( ! $timestamp ) {
        return '';
    }

    // Sanitize timestamp
    $timestamp = absint( $timestamp );
    
    // Get time ago string (assuming times_ago() is a custom function)
    $time_ago = ane_times_ago( $timestamp );
    if ( empty( $time_ago ) ) {
        return '';
    }

    // Build HTML with proper escaping
    $output = sprintf(
        '<div class="times-ago">
            <i class="ane-jam" aria-hidden="true"></i>
            %s
        </div>',
        esc_html( $time_ago )
    );
    
    return wp_kses_post( $output );
}
//reading time
/**
 * Calculates and returns the estimated reading time for a post
 *
 * @return string Sanitized reading time with unit
 */
function ane_reading_time() {
    // Get post object
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return esc_html__( '0 Minutes', 'elemenane' );
    }

    // Get post content safely
    $content = get_post_field( 'post_content', $post->ID, 'raw' );
    if ( empty( $content ) ) {
        return esc_html__( '1 Minute', 'elemenane' );
    }

    // Calculate word count
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    
    // Constants for calculation
    $words_per_minute = 200;
    $minimum_reading_time = 1;
    
    // Calculate reading time and ensure it's at least 1 minute
    $reading_time = max(
        $minimum_reading_time,
        absint( ceil( $word_count / $words_per_minute ) )
    );

    // Format the output
    /* translators: %d: number of minutes */
    $total_reading_time = sprintf(
        esc_html( _n( '%d Minute', '%d Minutes', $reading_time, 'elemenane' ) ),
        $reading_time
    );
    
    return $total_reading_time;
}

/**
 * Displays or returns pagination for posts
 *
 * @param WP_Query|null $wp_query WordPress Query object
 * @param bool         $echo     Whether to echo or return the pagination
 * @return string|null           Pagination HTML or null if no pages
 */
function ane_post_pagination( \WP_Query $wp_query = null, bool $echo = true ) {
    // Use global query if no query object provided
    if ( null === $wp_query ) {
        global $wp_query;
    }

    // Verify we have a valid WP_Query object
    if ( ! $wp_query instanceof \WP_Query ) {
        return null;
    }

    // Build pagination arguments
    $args = array(
        'base'         => str_replace( 
            999999999, 
            '%#%', 
            esc_url( get_pagenum_link( 999999999 ) ) 
        ),
        'format'       => '?paged=%#%',
        'current'      => max( 1, absint( get_query_var( 'paged' ) ) ),
        'total'        => absint( $wp_query->max_num_pages ),
        'type'         => 'array',
        'show_all'     => false,
        'end_size'     => 3,
        'mid_size'     => 1,
        'prev_next'    => true,
        'prev_text'    => sprintf(
            '<i class="ane ane-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">%s</span>',
            esc_html__( 'Previous Page', 'elemenane' )
        ),
        'next_text'    => sprintf(
            '<i class="ane ane-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">%s</span>',
            esc_html__( 'Next Page', 'elemenane' )
        ),
        'add_args'     => false,
        'add_fragment' => ''
    );

    // Get pagination links
    $pages = paginate_links( $args );

    // Return if no pages
    if ( ! is_array( $pages ) || empty( $pages ) ) {
        return null;
    }

    // Build pagination HTML
    $pagination = sprintf(
        '<nav class="pagination-area" aria-label="%s"><ul>',
        esc_attr__( 'Posts Navigation', 'elemenane' )
    );

    foreach ( $pages as $page ) {
        $pagination .= sprintf(
            '<li>%s</li>',
            wp_kses(
                $page,
                array(
                    'a' => array(
                        'href' => array(),
                        'class' => array(),
                        'aria-current' => array()
                    ),
                    'span' => array(
                        'class' => array(),
                        'aria-hidden' => array()
                    ),
                    'i' => array(
                        'class' => array(),
                        'aria-hidden' => array()
                    )
                )
            )
        );
    }

    $pagination .= '</ul></nav>';

    if ( $echo ) {
        echo wp_kses_post( $pagination );
        return null;
    }

    return $pagination;
}

/**
 * Returns the URL for the default thumbnail image
 *
 * @return string Sanitized URL for the default thumbnail
 */
function ane_dummy_thumbnail() {
    // Get the default fallback image URL
    $default_image = get_base_url_assets() . '/img/no-image.jpg';
    
    // Get the custom thumbnail from theme mod
    $dummy_thumb = get_theme_mod( 'dummythumb', $default_image );
    
    // Validate and sanitize URL
    if ( ! empty( $dummy_thumb ) && filter_var( $dummy_thumb, FILTER_VALIDATE_URL ) ) {
        return esc_url( $dummy_thumb );
    }
    
    // Return sanitized default if custom thumbnail is invalid
    return esc_url( $default_image );
}

if ( ! defined( 'ANE_DEFAULT_SQUARE_THUMBNAIL' ) ) {
    define( 'ANE_DEFAULT_SQUARE_THUMBNAIL', get_base_url_assets() . '/img/no-image-kotak.jpg' );
}

/**
 * Returns the URL for the default square thumbnail image
 *
 * @return string Sanitized URL for the default square thumbnail
 */
function ane_dummy_square() {
    // Get the custom thumbnail from theme mod
    $dummy_thumb = get_theme_mod( 'dummythumb', ANE_DEFAULT_SQUARE_THUMBNAIL );
    
    // Validate and sanitize URL
    if ( ! empty( $dummy_thumb ) && filter_var( $dummy_thumb, FILTER_VALIDATE_URL ) ) {
        return esc_url( $dummy_thumb );
    }
    
    // Return sanitized default if custom thumbnail is invalid
    return esc_url( ANE_DEFAULT_SQUARE_THUMBNAIL );
}

// next and prev posts

/**
 * Generates HTML for previous and next post navigation
 *
 * @return string Sanitized HTML for post navigation
 */
function ane_prev_next_post() {
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return '';
    }

    ob_start();
    ?>
    <div class="ane-col-55">
        <?php
        // Previous Post
        $prev_post = get_previous_post();
        if ( ! empty( $prev_post ) ) {
            ane_render_nav_post( $prev_post, 'prev' );
        }

        // Next Post
        $next_post = get_next_post();
        if ( ! empty( $next_post ) ) {
            ane_render_nav_post( $next_post, 'next' );
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders individual navigation post
 *
 * @param WP_Post $post Post object
 * @param string  $type Navigation type ('prev' or 'next')
 */
function ane_render_nav_post( $post, $type = 'prev' ) {
    if ( ! $post instanceof WP_Post ) {
        return;
    }

    $is_next = $type === 'next';
    $post_id = absint( $post->ID );
    $title = esc_html( get_the_title( $post_id ) );
    $permalink = esc_url( get_permalink( $post_id ) );
    $post_classes = get_post_class( '', $post_id );
    
    // Get thumbnail
    if ( has_post_thumbnail( $post ) ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'kotak' );
        $image_url = esc_url( $thumb[0] );
    } else {
        $image_url = esc_url( ane_dummy_square() );
    }

    // Navigation classes and text
    $nav_classes = array(
        'ane-kgv',
        'ane-konten-nav',
        $is_next ? 'nav-next' : '',
        esc_attr( implode( ' ', $post_classes ) )
    );
    
    $nav_header_class = $is_next ? 'nav-header-prev text-right' : 'nav-header-prev';
    $nav_text = $is_next ? 
        sprintf( 'Next Post <i class="ane-chevron-right" aria-hidden="true"></i>' ) :
        sprintf( '<i class="ane-chevron-left" aria-hidden="true"></i> Prev Post' );
    ?>
    <div class="ane-isi">
        <div class="<?php echo esc_attr( $nav_header_class ); ?>">
            <span><?php echo wp_kses_post( $nav_text ); ?></span>
        </div>
        <article id="post-<?php echo $post_id; ?>" class="<?php echo esc_attr( implode( ' ', $nav_classes ) ); ?>">
            <div class="entry-header">
                <a href="<?php echo $permalink; ?>">
                    <div class="ane-image">
                        <img src="<?php echo $image_url; ?>" 
                             alt="<?php echo $title; ?>" 
                             title="<?php echo $title; ?>"
                             loading="lazy">
                    </div>
                </a>
            </div>
            <div class="entry-content">
                <a href="<?php echo $permalink; ?>">
                    <?php echo $title; ?>
                </a>
            </div>
        </article>
    </div>
    <?php
}
/**
 * Converts timestamp to human-readable time difference
 *
 * @param int  $timestamp Timestamp to compare against
 * @param bool $show_time Whether to show the time with date
 * @return string Formatted time difference string
 */
function ane_times_ago( $timestamp = 1, $show_time = false ) {
    // Validate inputs
    $timestamp = absint( $timestamp );
    $current_time = current_time( 'timestamp', false );
    
    // Ensure valid timestamp
    if ( $timestamp <= 0 ) {
        $timestamp = 1;
    }
    
    // Calculate time difference
    $seconds = ( $current_time <= $timestamp ) ? 1 : $current_time - $timestamp;
    
    // Time intervals in seconds
    $intervals = array(
        'year'   => 31536000,
        'month'  => 2628000,
        'week'   => 604800,
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1
    );
    
    // If more than 5 years old, return formatted date
    if ( $seconds > ( $intervals['year'] * 5 ) ) {
        $format = get_option( 'date_format' );
        if ( $show_time ) {
            $format .= ' ' . get_option( 'time_format' );
        }
        return date_i18n( $format, strtotime( get_the_time( 'Y-m-d' ) ) );
    }
    
    // Calculate the appropriate time period
    foreach ( $intervals as $interval => $seconds_in_interval ) {
        $count = floor( $seconds / $seconds_in_interval );
        
        if ( $count > 0 ) {
            // Handle translation
            switch ( $interval ) {
                case 'year':
                    /* translators: %s: number of years */
                    return sprintf( 
                        _n( 
                            '%s year ago', 
                            '%s years ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'month':
                    /* translators: %s: number of months */
                    return sprintf( 
                        _n( 
                            '%s month ago', 
                            '%s months ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'week':
                    /* translators: %s: number of weeks */
                    return sprintf( 
                        _n( 
                            '%s week ago', 
                            '%s weeks ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'day':
                    /* translators: %s: number of days */
                    return sprintf( 
                        _n( 
                            '%s day ago', 
                            '%s days ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'hour':
                    /* translators: %s: number of hours */
                    return sprintf( 
                        _n( 
                            '%s hour ago', 
                            '%s hours ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'minute':
                    /* translators: %s: number of minutes */
                    return sprintf( 
                        _n( 
                            '%s minute ago', 
                            '%s minutes ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
                
                case 'second':
                    /* translators: %s: number of seconds */
                    return sprintf( 
                        _n( 
                            '%s sec ago', 
                            '%s secs ago', 
                            $count, 
                            'elemenane' 
                        ), 
                        number_format_i18n( $count ) 
                    );
            }
        }
    }
    
    return '';
}

/**
 * Displays the time ago information with an icon
 *
 * @return string Sanitized HTML for time ago display
 */
function ane_load_times_ago() {
    // Get post timestamp
    $timestamp = get_the_time( 'U' );
    if ( ! $timestamp ) {
        return '';
    }

    ob_start();
    ?>
    <div class="meta-time">
        <i class="ane-jam" aria-hidden="true"></i>
        <span class="time-ago"><?php echo esc_html( ane_times_ago( absint( $timestamp ) ) ); ?></span>
    </div>
    <?php
    return ob_get_clean();
}


// --== adding author bio ==-

/**
 * Adds author info box after post content
 *
 * @param string $content Post content
 * @return string Modified content with author box
 */
function ane_author_info_box( $content ) {
    // Only show on single posts
    if ( ! is_single() ) {
        return $content;
    }

    // Get post object
    $post = get_post();
    if ( ! $post instanceof WP_Post || ! isset( $post->post_author ) ) {
        return $content;
    }

    // Get author data
    $author_id = absint( $post->post_author );
    $author_data = array(
        'display_name'     => get_the_author_meta( 'display_name', $author_id ),
        'nickname'         => get_the_author_meta( 'nickname', $author_id ),
        'description'      => get_the_author_meta( 'user_description', $author_id ),
        'website'         => esc_url( get_the_author_meta( 'url', $author_id ) ),
        'email'           => get_the_author_meta( 'user_email', $author_id ),
        'posts_url'       => esc_url( get_author_posts_url( $author_id ) ),
    );

    // Use nickname if display name is empty
    $display_name = ! empty( $author_data['display_name'] ) 
        ? $author_data['display_name'] 
        : $author_data['nickname'];

    // Only proceed if we have an author description
    if ( empty( $author_data['description'] ) ) {
        return $content;
    }

    // Build author box HTML
    ob_start();
    ?>
    <footer class="ane-author">
        <div class="ane-author-isi">
            <div class="author-gambar">
                <?php 
                echo get_avatar( 
                    $author_data['email'], 
                    90, 
                    '', 
                    esc_attr( $display_name ),
                    array( 'class' => 'author-avatar' ) 
                ); 
                ?>
            </div>
            <div class="author-deskripsi">
                <h2>
                    <?php 
                    printf(
                        '%s %s',
                        esc_html__( 'By:', 'elemenane' ),
                        esc_html( $display_name )
                    );
                    ?>
                </h2>
                <p><?php echo wp_kses_post( nl2br( $author_data['description'] ) ); ?></p>
                <div class="author_links">
                    <a href="<?php echo $author_data['posts_url']; ?>">
                        <?php esc_html_e( 'View All Posts', 'elemenane' ); ?>
                    </a>
                    <?php if ( ! empty( $author_data['website'] ) ) : ?>
                        <a href="<?php echo $author_data['website']; ?>" 
                           target="_blank" 
                           rel="nofollow noopener">
                            <?php esc_html_e( 'Website', 'elemenane' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <?php
    $author_box = ob_get_clean();

    return $content . wp_kses_post( $author_box );
}

/**
 * Add author box to content and remove kses filter from user description
 */
function ane_setup_author_box() {
    add_filter( 'the_content', 'ane_author_info_box', 20 );
    remove_filter( 'pre_user_description', 'wp_filter_kses' );
    add_filter( 'pre_user_description', 'wp_kses_post' );
}
add_action( 'init', 'ane_setup_author_box' );

/**
 * Define allowed HTML tags for author description
 */
function ane_get_allowed_author_html() {
    return array(
        'a'      => array(
            'href'   => array(),
            'title'  => array(),
            'rel'    => array(),
            'target' => array()
        ),
        'p'      => array(),
        'br'     => array(),
        'em'     => array(),
        'strong' => array(),
        'ul'     => array(),
        'li'     => array(),
    );
}

/**
 * Filter author description HTML
 */
function ane_filter_author_description( $description ) {
    return wp_kses( $description, ane_get_allowed_author_html() );
}
add_filter( 'pre_user_description', 'ane_filter_author_description' );

//authorname

function ane_author_name() {
    $post = get_post(); // Safely retrieve the current post object.

    if ( ! $post || !isset($post->post_author)) {
        return esc_html__( 'Unknown Author', 'elemenane' ); // Fallback if no post or author is found.
    }

    $author_id = intval($post->post_author); // Ensure author ID is an integer.

    // Get display name or fallback to nickname.
    $display_name = get_the_author_meta('display_name', $author_id);
    if (empty($display_name)) {
        $display_name = get_the_author_meta('nickname', $author_id);
    }

    return $display_name ?: esc_html__( 'Unknown Author', 'elemenane' ); // Final fallback.
}



/**
 * Filter the avatar to use custom field image if available
 *
 * @param string $avatar      HTML for the user's avatar
 * @param mixed  $id_or_email User ID, email address, or WP_User object
 * @param int    $size        Avatar square size in pixels
 * @param string $default     URL for the default avatar image
 * @param string $alt         Alternative text for the avatar image
 * 
 * @return string Modified avatar HTML
 */
function ane_acf_profile_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    // Sanitize inputs
    $size = absint( $size );
    $alt  = esc_attr( $alt );
    
    // Get user object
    $user = ane_get_user_by_id_or_email( $id_or_email );
    if ( ! $user instanceof WP_User ) {
        return $avatar;
    }

    // Get custom avatar ID
    $image_id = get_user_meta( $user->ID, 'gravatar_ane', true );
    if ( empty( $image_id ) ) {
        return $avatar;
    }

    // Validate image ID
    $image_id = absint( $image_id );
    if ( ! wp_attachment_is_image( $image_id ) ) {
        return $avatar;
    }

    // Get image URL
    $image = wp_get_attachment_image_src( $image_id, 'kotak' );
    if ( ! $image ) {
        return $avatar;
    }

    // Build avatar attributes
    $attrs = array(
        'alt'    => $alt,
        'src'    => esc_url( $image[0] ),
        'class'  => sprintf( 'avatar avatar-%d', $size ),
        'height' => $size,
        'width'  => $size,
        'loading' => 'lazy',
    );

    // Build avatar HTML
    $avatar = sprintf(
        '<img%s/>',
        ane_build_html_attributes( $attrs )
    );

    return $avatar;
}

/**
 * Get user object from ID or email
 *
 * @param mixed $id_or_email User ID, email address, or WP_User object
 * @return WP_User|false User object or false on failure
 */
function ane_get_user_by_id_or_email( $id_or_email ) {
    if ( $id_or_email instanceof WP_User ) {
        return $id_or_email;
    }

    if ( $id_or_email instanceof WP_Post ) {
        return get_user_by( 'id', (int) $id_or_email->post_author );
    }

    if ( $id_or_email instanceof WP_Comment ) {
        if ( ! empty( $id_or_email->user_id ) ) {
            return get_user_by( 'id', (int) $id_or_email->user_id );
        }
        return get_user_by( 'email', $id_or_email->comment_author_email );
    }

    if ( is_numeric( $id_or_email ) ) {
        return get_user_by( 'id', (int) $id_or_email );
    }

    if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
        return get_user_by( 'email', $id_or_email );
    }

    return false;
}

/**
 * Build HTML attributes string
 *
 * @param array $attrs Array of attribute key-value pairs
 * @return string Formatted HTML attributes
 */
function ane_build_html_attributes( $attrs ) {
    $html = '';
    
    foreach ( $attrs as $key => $value ) {
        if ( ! empty( $value ) ) {
            $html .= sprintf(
                ' %s="%s"',
                esc_attr( $key ),
                esc_attr( $value )
            );
        }
    }
    
    return $html;
}

// Add the filter
add_filter( 'get_avatar', 'ane_acf_profile_avatar', 10, 5 );

/**
 * Filter allowed mime types for avatar uploads
 */
function ane_allowed_avatar_mime_types( $mimes ) {
    // Add allowed image mime types
    return array(
        'jpg|jpeg' => 'image/jpeg',
        'png'      => 'image/png',
        'gif'      => 'image/gif',
        'webp'     => 'image/webp',
        'svg'      => 'image/svg+xml',
        'bmp'      => 'image/bmp',
    );
}
add_filter( 'upload_mimes', 'ane_allowed_avatar_mime_types' );

//=====================
// RELATED FILE
//======================
/**
 * Display related posts based on post tags
 *
 * @return void
 */
function ane_related_posts() {
    // Get current post
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return;
    }

    // Get post tags
    $tags = wp_get_post_tags( $post->ID );
    if ( empty( $tags ) ) {
        return;
    }

    // Build tag IDs array
    $tag_ids = wp_list_pluck( $tags, 'term_id' );

    // Query arguments
    $args = array(
        'tag__in'          => array_map( 'absint', $tag_ids ),
        'post__not_in'     => array( $post->ID ),
        'posts_per_page'   => 5,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_status'      => 'publish',
        'ignore_sticky_posts' => 1, // Modern replacement for caller_get_posts
    );

    // Run the query
    $related_query = new WP_Query( $args );

    // Only proceed if we have posts
    if ( ! $related_query->have_posts() ) {
        return;
    }

    // Start output buffering
    ob_start();
    ?>
    <div class="ane-related">
        <h1><?php esc_html_e( 'Related Content', 'elemenane' ); ?></h1>
        <div class="isi">
            <?php
            while ( $related_query->have_posts() ) :
                $related_query->the_post();

                // Load appropriate template based on device
                if ( wp_is_mobile() ) {
                    get_template_part( 'tp/content', 'list' );
                } else {
                    get_template_part( 'tp/content', get_post_format() );
                }
            endwhile;
            ?>
        </div>
    </div>
    <?php
    
    // Reset post data
    wp_reset_postdata();
    
    // Output the buffer
    echo wp_kses_post( ob_get_clean() );
}


//************************
// post view
//***********************

function ane_get_views($postID = 0) {
    if ( ! $postID ) {
        $postID = get_the_ID();
    }

    if ( ! $postID ) {
        return '0 views';
    }

    $count_key = 'musi_views';
    $count = intval(get_post_meta($postID, $count_key, true));

    if ( ! $count ) {
        $count = 0;
        update_post_meta($postID, $count_key, $count); // Initialize the count if missing
    }

    return sprintf(_n('%s view', '%s views', $count, 'elemenane'), $count);
}

// Increment the view count for a post
function ane_set_views($postID = 0) {
    if ( ! $postID ) {
        $postID = get_the_ID();
    }

    if ( ! $postID ) {
        return false;
    }

    $count_key = 'musi_views';
    $count = intval(get_post_meta($postID, $count_key, true)) + 1;

    update_post_meta($postID, $count_key, $count);

    return true;
}

// Remove adjacent posts links to prevent prefetching from adding extra views
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

// Add a "Views" column to the admin posts table
add_filter('manage_posts_columns', function($columns) {
    $columns['musi_post_views'] = __('Views');
    return $columns;
});

// Display the view count in the custom column
add_action('manage_posts_custom_column', function($column_name, $postID) {
    if ($column_name === 'musi_post_views') {
        echo ane_get_views($postID);
    }
}, 10, 2);

/**
 * Enqueue post views tracking script for cached pages
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', function() {
	if ( is_single() && defined( 'WP_CACHE' ) && WP_CACHE ) {
		wp_enqueue_script(
			'ane-postviews-cache',
			get_base_url_assets() . '/js/postviews-cache.js',
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'ane-postviews-cache',
			'postViewsCache',
			array(
				'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
				'post_id'        => get_the_ID(),
				'nonce'          => wp_create_nonce( 'ane-postviews-nonce' ),
			)
		);
	}
} );

// Handle AJAX requests to increment views
add_action('wp_ajax_postviews', 'ane_increment_views');
add_action('wp_ajax_nopriv_postviews', 'ane_increment_views');

/**
 * AJAX handler to increment post views
 *
 * @since 1.0.0
 */
function ane_increment_views() {
	// Verify nonce for security
	check_ajax_referer( 'ane-postviews-nonce', 'nonce' );

	// Get and validate post ID
	$post_id = isset( $_GET['postviews_id'] ) ? absint( $_GET['postviews_id'] ) : 0;

	if ( $post_id > 0 ) {
		ane_set_views( $post_id );
		wp_send_json_success( array( 'views' => ane_get_views( $post_id ) ) );
	} else {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ) );
	}
}


// copyright tahun
/**
 * Get copyright year range for the website
 *
 * @return string Formatted copyright year range
 */
function ane_copyright_year() {
    // Try to get from cache first
    $copyright_dates = wp_cache_get( 'ane_copyright_years', 'ane' );
    
    if ( false === $copyright_dates ) {
        global $wpdb;
        
        // Prepare and execute secure query
        $query = $wpdb->prepare(
            "SELECT
                YEAR(MIN(post_date_gmt)) AS firstdate,
                YEAR(MAX(post_date_gmt)) AS lastdate
            FROM {$wpdb->posts}
            WHERE post_status = %s
            AND post_type IN (%s, %s)",
            'publish',
            'post',
            'page'
        );
        
        // Get results
        $copyright_dates = $wpdb->get_results( $query );
        
        // Cache the results for 24 hours
        wp_cache_set( 'ane_copyright_years', $copyright_dates, 'ane', DAY_IN_SECONDS );
    }
    
    // Return early if no results
    if ( empty( $copyright_dates ) || ! isset( $copyright_dates[0] ) ) {
        return sprintf(
            '&copy; %s',
            esc_html( date( 'Y' ) )
        );
    }
    
    // Format the copyright string
    $first_year = absint( $copyright_dates[0]->firstdate );
    $last_year = absint( $copyright_dates[0]->lastdate );
    
    if ( $first_year === $last_year ) {
        $copyright = sprintf(
            '&copy; %s',
            esc_html( $first_year )
        );
    } else {
        $copyright = sprintf(
            '&copy; %s-%s',
            esc_html( $first_year ),
            esc_html( $last_year )
        );
    }
    
    /**
     * Filter the copyright text
     *
     * @param string $copyright The formatted copyright text
     * @param int    $first_year The first year
     * @param int    $last_year  The last year
     */
    return apply_filters( 'ane_copyright_text', $copyright, $first_year, $last_year );
}

/**
 * Clear copyright cache when publishing posts
 */
function ane_clear_copyright_cache( $post_id, $post ) {
    if ( 'publish' === $post->post_status ) {
        wp_cache_delete( 'ane_copyright_years', 'ane' );
    }
}
add_action( 'save_post', 'ane_clear_copyright_cache', 10, 2 );

 


/**
 * Get designer credit with schema markup
 */
function ane_get_designer_credit_with_schema() {
    $designer = array(
        'name'    => 'Webane Indonesia',
        'url'     => 'https://webane.com/',
        'title'   => __( 'Web Design Webane Indonesia', 'elemenane' ),
    );

    ob_start();
    ?>
    <span class="designer-credit" itemscope itemtype="https://schema.org/Organization">
        <?php echo esc_html__( 'Created with love by', 'elemenane' ); ?> 
        <a href="<?php echo esc_url( $designer['url'] ); ?>" 
           class="designer-link"
           target="_blank" 
           rel="nofollow noopener" 
           title="<?php echo esc_attr( $designer['title'] ); ?>"
           itemprop="url">
            <span itemprop="name"><?php echo esc_html( $designer['name'] ); ?></span>
        </a>
	</span>
    <?php
    return wp_kses_post( ob_get_clean() );
}