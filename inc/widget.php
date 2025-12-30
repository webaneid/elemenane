<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
function ane_posts_widgets_init() {
	register_widget('Webane_Posts_Widget');
}
add_action( 'widgets_init', 'ane_posts_widgets_init' );

class Webane_Posts_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'post-widget', // Base ID
			'Webane: Posts', // Name
			array( 'description' => 'Display posts by recent post, category, tagged or most comment post as widget items, order by date, id. you can customize', ) // Args
		);

		//global $pagenow;
	}

    function widget($args, $instance) {
        extract( $args );
		extract( $instance );
		$title = apply_filters( 'widget_title', empty( $title ) ? '' : $title );
		$args = array();
		$args['post_status'] = array('publish', 'inherit');
		$args['orderby'] = $orderby;
		$args['order'] = $order;
		$args['posts_per_page'] = $number;

		if($type == "Most Comments") :
			$args['orderby'] = 'comment_count';
			$args['order'] = 'DESC';
			$args['meta_query'] = null;
		elseif($type == 'Popular Post' ) :
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'musi_views';
		elseif($type == 'Post Format: Gallery') :
			$args['post_format'] = $args2['post_format'] = 'post-format-gallery';
		elseif($type == 'Post Format: Video') :
			$args['post_format'] = $args2['post_format'] = 'post-format-video';
		endif;
		if($post_tag):
			$args['tag'] = $post_tag;
		elseif($category !== "") :
			$args['tax_query'] = $args2['tax_query'] = array(array('taxonomy' => 'category','field' => 'slug','terms' => $category,));
		endif;

		//print_r($args);
		// Get the posts for this instance
		$r = new WP_Query( $args );

		// Showing
		if ( !defined('ABSPATH') )
			die('-1');

		echo $before_widget;
		if ( !empty($title) ) echo "<h2 class='widget-title general-title'>".$title."</h2>";
			echo "<div class='post-widget'>";
			$count = 1;
			if( $r->have_posts() ):

		?>
				<?php while ( $r->have_posts() ) :
					$r->the_post();
					$id_cek = get_the_ID();
					$title = get_the_title();
					$permalink = get_permalink();

						if( wp_is_mobile() ) {
							get_template_part( 'tp/content','list' );
						} else {
							get_template_part( 'tp/content','list' );
						}
				$count++; endwhile; ?>
		<?php
		else: ?>
				<p>No post found</p>
		<?php
		endif; // End have_posts()
		?>

		<?php
		echo "</div>";
		echo $after_widget;

		// Be sure to reset any post_data before proceeding
		wp_reset_postdata();

    }

    /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
    function update($new_instance, $old_instance) {
		$instance 				= $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['type']		= $new_instance['type'];
		$instance['category']	= $new_instance['category'];
		$instance['post_tag']	= strip_tags( $new_instance['post_tag'] );
		$instance['number']		= strip_tags( $new_instance['number'] );
		$instance['offset']		= strip_tags( $new_instance['offset'] );
		$instance['orderby']	= $new_instance['orderby'];
		$instance['order']		= $new_instance['order'];
        return $instance;
    }

    /**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
    function form($instance) {

		$types = array( 'Recent Post', 'Popular Post','Most Comments','Post Format: Gallery','Post Format: Video');
		$orderbys = array( 'ID', 'title', 'date', 'rand');
		$orders = array( 'ASC', 'DESC', );

		$instance = wp_parse_args( (array) $instance, array(
			'title'		=> '',
			'type'		=> 'Recent Post',
			'category'	=> '',
			'number'	=> '5',
			'offset'	=> '0',
			'orderby'	=> 'date',
			'order'		=> 'DESC',
			'date'		=> 'all',
			'thumbnail' => 'small',
			'tdc' => true,
			'author' => false,
		) );

		extract( $instance );

        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo ('Widget Title'); ?></label>
                <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
			<p>
				<label for="<?php echo $this->get_field_id('type'); ?>"><?php echo ('Select a type'); ?></label>
				<select onchange="typechange()" name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
				<?php
				foreach ($types as $option) {
					echo '<option value="' . $option . '"', $type == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>
			</p>
            <hr />
			<p id="category<?php echo $this->get_field_id('category'); ?>" >
				<label for="<?php echo $this->get_field_id('category'); ?>"><?php echo ('Select a category:'); ?></label>
				<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>">
					<option value="">All Category</option>
					<?php
							$args = array (
								'hide_empty' => 0,);
							$category_get = get_terms( 'category', $args );

							if( !empty($category_get) ) :
								$output = '';
								foreach ( $category_get as $option ) :
									$output .= '<option value="' . $option->slug . '"' . ( $category == $option->slug ? ' selected="selected"' : '' ) . '>' . $option->name . '</option>';
								endforeach;
								echo( $output );
							endif;
					?>
				</select>
			</p>
            <hr />
            <p id="tag<?php echo $this->get_field_id('post_tag'); ?>">
			<label for="<?php echo $this->get_field_id('post_tag'); ?>"><?php echo ('Type tag'); ?></label>
			<input id="<?php echo $this->get_field_id('post_tag'); ?>" name="<?php echo $this->get_field_name('post_tag'); ?>" type="text" value="<?php echo $post_tag; ?>" />
            separate by comma
            </p>
            <hr />

            <h4 style="">Display options</h4>
            <p>
              <label for="<?php echo $this->get_field_id('number'); ?>"><?php echo ('Number of posts to show:'); ?></label>
              <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" style="width:40px; margin-left:.2em; text-align:right;" />
            </p>
            <p id="orderby<?php echo $this->get_field_id('orderby'); ?>" style="<?php if($type == 'Most Comments' or $type == 'Popular Post') echo "display:none"; else echo "display:block";?>">
                <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php echo ('Order post by:'); ?></label>
                <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
                    <?php
                    foreach ($orderbys as $option) :
                        echo '<option value="' . $option . '" id="' . $option . '"', $orderby == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    endforeach;
                    ?>
                </select>
            </p>
            <p id="order<?php echo $this->get_field_id('order'); ?>" >
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php echo ('Order:'); ?></label>
                <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
                    <?php
                    foreach ($orders as $option) :
                        echo '<option value="' . $option . '"', $order == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    endforeach;
                    ?>
                </select>
            </p>

        <?php
    }
}