<?php
/**
 * Landing section: latest articles with pagination and the home sidebar.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_custom = get_field( 'ane_title' );
$elemenane_title  = ! empty( $elemenane_custom ) ? $elemenane_custom : __( 'Latest Content', 'elemenane' );

// A Page uses `page`, archives use `paged`.
$elemenane_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$elemenane_q = new WP_Query(
	array(
		'post_type'              => 'post',
		'posts_per_page'         => 10,
		'paged'                  => $elemenane_paged,
		'post_status'            => 'publish',
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => true,
	)
);

$elemenane_has_sidebar = is_active_sidebar( 'home-sidebar' );
?>
<section class="ane-block ane-block--latest">
	<div class="container">
		<?php get_template_part( 'tp/home-head', null, array( 'title' => $elemenane_title ) ); ?>

		<div class="ane-split<?php echo $elemenane_has_sidebar ? ' has-aside' : ''; ?>">
			<div class="ane-split__main">
				<?php if ( $elemenane_q->have_posts() ) : ?>
					<?php get_template_part( 'tp/archive', 'loop', array( 'query' => $elemenane_q ) ); ?>
					<?php ane_post_pagination( $elemenane_q ); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Sorry, no posts matched your criteria.', 'elemenane' ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $elemenane_has_sidebar ) : ?>
				<aside class="ane-split__aside ane-kanan" aria-label="<?php esc_attr_e( 'Sidebar', 'elemenane' ); ?>">
					<div class="right-sidebar">
						<?php dynamic_sidebar( 'home-sidebar' ); ?>
					</div>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
