<?php
/**
 * Custom Posts Widget (Webane_Posts_Widget).
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Webane_Posts_Widget.
 *
 * @return void
 */
function ane_posts_widgets_init() {
	register_widget( 'Webane_Posts_Widget' );
}
add_action( 'widgets_init', 'ane_posts_widgets_init' );

/**
 * Webane Posts Widget Class.
 */
class Webane_Posts_Widget extends WP_Widget {

	/**
	 * Supported post listing types.
	 *
	 * @var string[]
	 */
	private static $types = array(
		'Recent Post',
		'Popular Post',
		'Most Comments',
		'Post Format: Gallery',
		'Post Format: Video',
	);

	/**
	 * Supported orderby fields.
	 *
	 * @var string[]
	 */
	private static $orderbys = array( 'ID', 'title', 'date', 'rand' );

	/**
	 * Supported sort directions.
	 *
	 * @var string[]
	 */
	private static $orders = array( 'ASC', 'DESC' );

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'post-widget',
			'Webane: Posts',
			array(
				'description' => __( 'Display posts by recent post, category, tagged or most comment post as widget items.', 'elemenane' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array<string, mixed> $args     Widget arguments.
	 * @param array<string, mixed> $instance Saved values from database.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$before_widget = isset( $args['before_widget'] ) ? (string) $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? (string) $args['after_widget'] : '';

		$raw_title = isset( $instance['title'] ) ? (string) $instance['title'] : '';
		$title     = apply_filters( 'widget_title', $raw_title );
		$type      = isset( $instance['type'] ) ? (string) $instance['type'] : 'Recent Post';
		$category  = isset( $instance['category'] ) ? (string) $instance['category'] : '';
		$post_tag  = isset( $instance['post_tag'] ) ? (string) $instance['post_tag'] : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$orderby   = isset( $instance['orderby'] ) && in_array( $instance['orderby'], self::$orderbys, true ) ? (string) $instance['orderby'] : 'date';
		$order     = isset( $instance['order'] ) && in_array( $instance['order'], self::$orders, true ) ? (string) $instance['order'] : 'DESC';

		$query_args = array(
			'post_status'    => array( 'publish', 'inherit' ),
			'orderby'        => $orderby,
			'order'          => $order,
			'posts_per_page' => $number > 0 ? $number : 5,
		);

		if ( 'Most Comments' === $type ) {
			$query_args['orderby'] = 'comment_count';
			$query_args['order']   = 'DESC';
		} elseif ( 'Popular Post' === $type ) {
			$query_args['orderby']  = 'meta_value_num';
			$query_args['meta_key'] = 'musi_views';
		} elseif ( 'Post Format: Gallery' === $type ) {
			$query_args['post_format'] = 'post-format-gallery';
		} elseif ( 'Post Format: Video' === $type ) {
			$query_args['post_format'] = 'post-format-video';
		}

		if ( '' !== $post_tag ) {
			$query_args['tag'] = $post_tag;
		} elseif ( '' !== $category ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}

		$r = new WP_Query( $query_args );

		echo wp_kses_post( $before_widget );
		if ( ! empty( $title ) ) {
			echo "<h2 class='widget-title general-title'>" . esc_html( (string) $title ) . '</h2>';
		}
		echo "<div class='post-widget'>";

		if ( $r->have_posts() ) {
			while ( $r->have_posts() ) {
				$r->the_post();
				get_template_part( 'tp/content', 'list' );
			}
		} else {
			echo '<p>' . esc_html__( 'No post found', 'elemenane' ) . '</p>';
		}

		echo '</div>';
		echo wp_kses_post( $after_widget );

		wp_reset_postdata();
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array<string, mixed> $new_instance Values just sent to be saved.
	 * @param array<string, mixed> $old_instance Previously saved values from database.
	 * @return array<string, mixed> Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance     = is_array( $old_instance ) ? $old_instance : array();
		$new_instance = wp_parse_args(
			(array) $new_instance,
			array(
				'title'    => '',
				'type'     => '',
				'category' => '',
				'post_tag' => '',
				'number'   => 5,
				'offset'   => 0,
				'orderby'  => '',
				'order'    => '',
			)
		);

		$instance['title']    = sanitize_text_field( (string) $new_instance['title'] );
		$instance['type']     = in_array( $new_instance['type'], self::$types, true ) ? $new_instance['type'] : 'Recent Post';
		$instance['category'] = sanitize_title( (string) $new_instance['category'] );
		$instance['post_tag'] = sanitize_text_field( (string) $new_instance['post_tag'] );
		$instance['number']   = absint( $new_instance['number'] ) ? absint( $new_instance['number'] ) : 5;
		$instance['offset']   = absint( $new_instance['offset'] );
		$instance['orderby']  = in_array( $new_instance['orderby'], self::$orderbys, true ) ? $new_instance['orderby'] : 'date';
		$instance['order']    = in_array( $new_instance['order'], self::$orders, true ) ? $new_instance['order'] : 'DESC';

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array<string, mixed> $instance Previously saved values from database.
	 * @return string
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'    => '',
				'type'     => 'Recent Post',
				'category' => '',
				'post_tag' => '',
				'number'   => '5',
				'offset'   => '0',
				'orderby'  => 'date',
				'order'    => 'DESC',
			)
		);

		$title    = (string) $instance['title'];
		$type     = (string) $instance['type'];
		$category = (string) $instance['category'];
		$post_tag = (string) $instance['post_tag'];
		$number   = (string) $instance['number'];
		$orderby  = (string) $instance['orderby'];
		$order    = (string) $instance['order'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget Title', 'elemenane' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_html_e( 'Select a type', 'elemenane' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
				<?php foreach ( self::$types as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $type, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<hr />
		<p id="category<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Select a category:', 'elemenane' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<option value=""><?php esc_html_e( 'All Category', 'elemenane' ); ?></option>
				<?php
				$category_get = get_terms(
					array(
						'taxonomy'   => 'category',
						'hide_empty' => false,
					)
				);
				if ( ! is_wp_error( $category_get ) && ! empty( $category_get ) ) :
					foreach ( $category_get as $option ) :
						?>
						<option value="<?php echo esc_attr( $option->slug ); ?>" <?php selected( $category, $option->slug ); ?>><?php echo esc_html( $option->name ); ?></option>
						<?php
					endforeach;
				endif;
				?>
			</select>
		</p>
		<hr />
		<p id="tag<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>"><?php esc_html_e( 'Type tag', 'elemenane' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'post_tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_tag' ) ); ?>" type="text" value="<?php echo esc_attr( $post_tag ); ?>" />
			<?php esc_html_e( 'separate by comma', 'elemenane' ); ?>
		</p>
		<hr />
		<h4><?php esc_html_e( 'Display options', 'elemenane' ); ?></h4>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'elemenane' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" style="width:40px; margin-left:.2em; text-align:right;" />
		</p>
		<p id="orderby<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" style="<?php echo ( 'Most Comments' === $type || 'Popular Post' === $type ) ? 'display:none' : 'display:block'; ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order post by:', 'elemenane' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php foreach ( self::$orderbys as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" id="<?php echo esc_attr( $option ); ?>" <?php selected( $orderby, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p id="order<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'elemenane' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php foreach ( self::$orders as $option ) : ?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $order, $option ); ?>><?php echo esc_html( $option ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
		return '';
	}
}
