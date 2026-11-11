<?php
/**
 * Archive: nothing found
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_is_search = is_search();
$elemenane_cats      = get_categories(
	array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
		'hide_empty' => true,
	)
);
?>
<section class="ane-empty">
	<span class="ane-empty__icon" aria-hidden="true">
		<?php elemenane_icon( $elemenane_is_search ? 'search' : 'dokumen' ); ?>
	</span>

	<h2 class="ane-empty__title">
		<?php
		echo $elemenane_is_search
			? esc_html__( 'No results found', 'elemenane' )
			: esc_html__( 'No posts yet', 'elemenane' );
		?>
	</h2>

	<p class="ane-empty__text">
		<?php
		echo $elemenane_is_search
			? esc_html__( 'Nothing matched your search. Check the spelling or try different, more general keywords.', 'elemenane' )
			: esc_html__( 'There is nothing to show here at the moment. Please check back later.', 'elemenane' );
		?>
	</p>

	<?php if ( $elemenane_is_search ) : ?>
		<div class="ane-empty__search"><?php get_search_form(); ?></div>
	<?php endif; ?>

	<?php if ( ! empty( $elemenane_cats ) ) : ?>
		<p class="ane-empty__label"><?php esc_html_e( 'Browse popular categories', 'elemenane' ); ?></p>
		<ul class="ane-empty__chips">
			<?php foreach ( $elemenane_cats as $elemenane_cat ) : ?>
				<li><a href="<?php echo esc_url( get_category_link( $elemenane_cat->term_id ) ); ?>"><?php echo esc_html( $elemenane_cat->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
