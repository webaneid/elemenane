<?php
/**
 * Product archive filters: category, sort order and price range.
 *
 * Filtering runs server-side from query string parameters, so it works without
 * JavaScript and filtered pages can be shared. js/product-filter.js only turns
 * the category select into an autocomplete and submits on change.
 *
 * Parameters:
 * - kategori     Category slug. Not `product_cat`: that query var would
 *                override the category from a /product-category/ permalink.
 * - urutkan      terbaru | terlama | nama-az | nama-za | harga-terendah | harga-tertinggi
 * - harga_min    Minimum price.
 * - harga_max    Maximum price.
 *
 * @package elemenane
 * @since 1.1.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Whether the current request is a product archive the filters apply to.
 *
 * @param WP_Query|null $query Query to check. Defaults to the main query.
 * @return bool
 */
function ane_is_product_listing( $query = null ) {
	$query = $query ? $query : $GLOBALS['wp_query'];

	return $query instanceof WP_Query
		&& ( $query->is_post_type_archive( 'product' ) || $query->is_tax( array( 'product_cat', 'product_tag' ) ) );
}

/**
 * Sort options.
 *
 * @return array Slug => label.
 */
function ane_product_sort_options() {
	return array(
		'terbaru'         => __( 'Newest', 'elemenane' ),
		'terlama'         => __( 'Oldest', 'elemenane' ),
		'nama-az'         => __( 'Name: A to Z', 'elemenane' ),
		'nama-za'         => __( 'Name: Z to A', 'elemenane' ),
		'harga-terendah'  => __( 'Price: low to high', 'elemenane' ),
		'harga-tertinggi' => __( 'Price: high to low', 'elemenane' ),
	);
}

/**
 * Filter values requested in the URL, sanitized.
 *
 * @return array { sort: string, min: float, max: float }
 */
function ane_product_filter_request() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only GET filters.
	$sort = isset( $_GET['urutkan'] ) ? sanitize_key( wp_unslash( $_GET['urutkan'] ) ) : '';
	$min  = isset( $_GET['harga_min'] ) ? (float) preg_replace( '/[^\d.]/', '', wp_unslash( $_GET['harga_min'] ) ) : 0;
	$max  = isset( $_GET['harga_max'] ) ? (float) preg_replace( '/[^\d.]/', '', wp_unslash( $_GET['harga_max'] ) ) : 0;
	// phpcs:enable

	if ( ! array_key_exists( $sort, ane_product_sort_options() ) ) {
		$sort = 'terbaru';
	}

	// Swap a reversed range instead of returning nothing.
	if ( $min > 0 && $max > 0 && $min > $max ) {
		list( $min, $max ) = array( $max, $min );
	}

	return array(
		'sort' => $sort,
		'min'  => max( 0, $min ),
		'max'  => max( 0, $max ),
	);
}

/**
 * IDs of published products in the current archive before filters: all
 * products, or the products in the current category/tag.
 *
 * @return int[]
 */
function ane_product_filter_context_ids() {
	$term = is_tax( array( 'product_cat', 'product_tag' ) ) ? get_queried_object() : null;
	$key  = 'ctx_' . ( $term ? $term->taxonomy . '_' . $term->term_id : 'all' ) . '_' . wp_cache_get_last_changed( 'posts' ) . '_' . wp_cache_get_last_changed( 'terms' );

	$ids = wp_cache_get( $key, 'ane_product_filter' );
	if ( false !== $ids ) {
		return $ids;
	}

	$args = array(
		'post_type'              => 'product',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	if ( $term ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => $term->taxonomy,
				'terms'    => $term->term_id,
			),
		);
	}

	$ids = get_posts( $args );
	wp_cache_set( $key, $ids, 'ane_product_filter' );

	return $ids;
}

/**
 * Product categories that have products in the current archive.
 *
 * @return WP_Term[]
 */
function ane_product_filter_categories() {
	$ids = ane_product_filter_context_ids();
	if ( ! $ids ) {
		return array();
	}

	// On a tag page (or the main archive) only offer categories that contain
	// products listed here. A category page offers all categories to switch to.
	if ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
			)
		);
	} else {
		$terms = wp_get_object_terms( $ids, 'product_cat' );
	}

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Lowest and highest price among products in the current archive.
 *
 * @return array|null { min: float, max: float }, or null when no product has a price.
 */
function ane_product_filter_price_range() {
	global $wpdb;

	$ids = ane_product_filter_context_ids();
	if ( ! $ids ) {
		return null;
	}

	$ids = array_map( 'absint', $ids );
	$row = $wpdb->get_row(
		"SELECT MIN( CAST( meta_value AS DECIMAL(15,2) ) ) AS min_price, MAX( CAST( meta_value AS DECIMAL(15,2) ) ) AS max_price
		FROM {$wpdb->postmeta}
		WHERE meta_key = '_price' AND meta_value + 0 > 0
		AND post_id IN (" . implode( ',', $ids ) . ')' // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- integer IDs.
	);

	if ( ! $row || null === $row->max_price ) {
		return null;
	}

	return array(
		'min' => (float) $row->min_price,
		'max' => (float) $row->max_price,
	);
}

/**
 * Category slug requested with the `kategori` parameter.
 *
 * @return string
 */
function ane_product_filter_category_slug() {
	return isset( $_GET['kategori'] ) ? sanitize_title( wp_unslash( $_GET['kategori'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Whether any filter or non-default sort is active.
 *
 * @return bool
 */
function ane_product_filters_active() {
	$request = ane_product_filter_request();

	return 'terbaru' !== $request['sort'] || $request['min'] > 0 || $request['max'] > 0
		|| ( is_tax( 'product_tag' ) && ane_product_filter_category_slug() );
}

/**
 * Apply sort order to the main product archive query.
 *
 * @param WP_Query $query Query.
 */
function ane_product_filter_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! ane_is_product_listing( $query ) ) {
		return;
	}

	$request = ane_product_filter_request();

	// On tag archives the category narrows the tag listing. Other archives
	// redirect to the category URL instead (ane_product_filter_redirect()).
	$category = ane_product_filter_category_slug();
	if ( $category && $query->is_tax( 'product_tag' ) ) {
		$tax_query   = (array) $query->get( 'tax_query' );
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $category,
		);
		$query->set( 'tax_query', $tax_query );
	}

	switch ( $request['sort'] ) {
		case 'terlama':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
			break;
		case 'nama-az':
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
			break;
		case 'nama-za':
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'DESC' );
			break;
		case 'terbaru':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
			break;
	}

	// Price sorting and the price range are applied in ane_product_filter_clauses().
	$query->set( 'ane_price_sort', in_array( $request['sort'], array( 'harga-terendah', 'harga-tertinggi' ), true ) ? $request['sort'] : '' );
	$query->set( 'ane_price_min', $request['min'] );
	$query->set( 'ane_price_max', $request['max'] );
}
add_action( 'pre_get_posts', 'ane_product_filter_pre_get_posts' );

/**
 * Join the active price for price sorting and the price range.
 *
 * Products without a price sort last and are excluded by a price range.
 *
 * @param array    $clauses SQL clauses.
 * @param WP_Query $query   Query.
 * @return array
 */
function ane_product_filter_clauses( $clauses, $query ) {
	$sort = $query->get( 'ane_price_sort' );
	$min  = (float) $query->get( 'ane_price_min' );
	$max  = (float) $query->get( 'ane_price_max' );

	if ( ! $sort && ! $min && ! $max ) {
		return $clauses;
	}

	global $wpdb;

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS ane_price ON ( ane_price.post_id = {$wpdb->posts}.ID AND ane_price.meta_key = '_price' )";
	$price            = 'CAST( ane_price.meta_value AS DECIMAL(15,2) )';

	if ( $min > 0 ) {
		$clauses['where'] .= $wpdb->prepare( " AND {$price} >= %f", $min ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	if ( $max > 0 ) {
		$clauses['where'] .= $wpdb->prepare( " AND {$price} <= %f AND {$price} > 0", $max ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	if ( $sort ) {
		$direction          = 'harga-tertinggi' === $sort ? 'DESC' : 'ASC';
		$clauses['orderby'] = "( ane_price.meta_value IS NULL OR {$price} <= 0 ) ASC, {$price} {$direction}, {$wpdb->posts}.post_date DESC";
	}

	return $clauses;
}
add_filter( 'posts_clauses', 'ane_product_filter_clauses', 10, 2 );

/**
 * Send category choices to the category's own archive URL.
 *
 * On the main product archive and on category archives, choosing a category
 * goes to /product-category/{slug}/ (keeping sort and price). On tag archives
 * the category narrows the tag listing instead (see pre_get_posts above).
 */
function ane_product_filter_redirect() {
	if ( ! isset( $_GET['kategori'] ) || is_tax( 'product_tag' ) || ! ( is_post_type_archive( 'product' ) || is_tax( 'product_cat' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only GET filters.
	$slug = ane_product_filter_category_slug();
	$keep = array();
	$sort = isset( $_GET['urutkan'] ) ? sanitize_key( wp_unslash( $_GET['urutkan'] ) ) : '';
	if ( $sort && 'terbaru' !== $sort ) {
		$keep['urutkan'] = $sort;
	}
	foreach ( array( 'harga_min', 'harga_max' ) as $key ) {
		if ( ! empty( $_GET[ $key ] ) && absint( $_GET[ $key ] ) > 0 ) {
			$keep[ $key ] = absint( $_GET[ $key ] );
		}
	}
	// phpcs:enable

	$term = $slug ? get_term_by( 'slug', $slug, 'product_cat' ) : null;
	$url  = $term ? get_term_link( $term ) : get_post_type_archive_link( 'product' );

	if ( ! is_wp_error( $url ) && $url ) {
		wp_safe_redirect( add_query_arg( $keep, $url ) );
		exit;
	}
}
add_action( 'template_redirect', 'ane_product_filter_redirect' );

/**
 * Keep filtered and re-sorted listings out of search results; they repeat
 * the unfiltered archive.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function ane_product_filter_robots( $robots ) {
	if ( ane_is_product_listing() && ane_product_filters_active() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'ane_product_filter_robots' );

/**
 * Same as ane_product_filter_robots() for Yoast SEO, which prints its own
 * robots meta tag.
 *
 * @param string $robots Robots value.
 * @return string
 */
function ane_product_filter_wpseo_robots( $robots ) {
	if ( ane_is_product_listing() && ane_product_filters_active() ) {
		return 'noindex, follow';
	}

	return $robots;
}
add_filter( 'wpseo_robots', 'ane_product_filter_wpseo_robots' );

/**
 * Render the filter bar. Called from the product archive templates.
 */
function ane_product_filters() {
	if ( ! ane_is_product_listing() ) {
		return;
	}

	$request    = ane_product_filter_request();
	$categories = ane_product_filter_categories();
	$range      = ane_product_filter_price_range();
	$options    = ane_product_sort_options();

	if ( ! $range ) {
		unset( $options['harga-terendah'], $options['harga-tertinggi'] );
		if ( in_array( $request['sort'], array( 'harga-terendah', 'harga-tertinggi' ), true ) ) {
			$request['sort'] = 'terbaru';
		}
	}

	$current_cat = '';
	if ( is_tax( 'product_cat' ) ) {
		$current_cat = get_queried_object()->slug;
	} else {
		$current_cat = ane_product_filter_category_slug();
	}

	// The form always submits to page 1 of the current archive.
	$action = is_tax() ? get_term_link( get_queried_object() ) : get_post_type_archive_link( 'product' );

	wp_enqueue_script( 'ane-product-filter' );
	?>
	<form class="ane-product-filter" method="get" action="<?php echo esc_url( $action ); ?>" role="search" aria-label="<?php esc_attr_e( 'Filter products', 'elemenane' ); ?>">
		<?php if ( count( $categories ) > 1 ) : ?>
			<div class="ane-filter-field ane-filter-field--category">
				<label for="ane-filter-cat"><?php esc_html_e( 'Category', 'elemenane' ); ?></label>
				<select id="ane-filter-cat" name="kategori" data-ane-autocomplete data-placeholder="<?php esc_attr_e( 'Search category…', 'elemenane' ); ?>" data-empty="<?php esc_attr_e( 'No matching category', 'elemenane' ); ?>">
					<option value=""><?php esc_html_e( 'All categories', 'elemenane' ); ?></option>
					<?php foreach ( $categories as $category ) : ?>
						<option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $current_cat, $category->slug ); ?>>
							<?php echo esc_html( $category->name ); ?> (<?php echo esc_html( number_format_i18n( $category->count ) ); ?>)
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<div class="ane-filter-field ane-filter-field--sort">
			<label for="ane-filter-sort"><?php esc_html_e( 'Sort by', 'elemenane' ); ?></label>
			<select id="ane-filter-sort" name="urutkan" data-ane-autosubmit>
				<?php foreach ( $options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $request['sort'], $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<?php if ( $range ) : ?>
			<fieldset class="ane-filter-field ane-filter-field--price">
				<legend><?php esc_html_e( 'Price (Rp)', 'elemenane' ); ?></legend>
				<div class="ane-filter-price-inputs">
					<input type="number" name="harga_min" min="0" step="1" inputmode="numeric"
						aria-label="<?php esc_attr_e( 'Minimum price', 'elemenane' ); ?>"
						placeholder="<?php echo esc_attr( number_format_i18n( floor( $range['min'] ) ) ); ?>"
						value="<?php echo $request['min'] > 0 ? esc_attr( (int) $request['min'] ) : ''; ?>">
					<span aria-hidden="true">–</span>
					<input type="number" name="harga_max" min="0" step="1" inputmode="numeric"
						aria-label="<?php esc_attr_e( 'Maximum price', 'elemenane' ); ?>"
						placeholder="<?php echo esc_attr( number_format_i18n( ceil( $range['max'] ) ) ); ?>"
						value="<?php echo $request['max'] > 0 ? esc_attr( (int) $request['max'] ) : ''; ?>">
				</div>
			</fieldset>
		<?php endif; ?>

		<div class="ane-filter-actions">
			<button type="submit" class="ane-filter-submit"><?php esc_html_e( 'Apply', 'elemenane' ); ?></button>
			<?php if ( ane_product_filters_active() ) : ?>
				<a class="ane-filter-reset" href="<?php echo esc_url( $action ); ?>"><?php esc_html_e( 'Reset', 'elemenane' ); ?></a>
			<?php endif; ?>
		</div>
	</form>
	<?php
}

/**
 * Register the filter script. It is enqueued by ane_product_filters() only
 * when the filter bar is printed.
 */
function ane_product_filter_register_script() {
	wp_register_script( 'ane-product-filter', get_base_url_assets() . '/js/product-filter.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'ane_product_filter_register_script' );
