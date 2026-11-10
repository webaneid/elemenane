<?php
/**
 * Archive Layout
 *
 * One layout for every post archive: category, tag, date, search, author and the blog index.
 * Called from archive.php, category.php, tag.php, search.php, author.php and index.php.
 *
 * @package elemenane
 * @since   1.1.9
 */

defined( 'ABSPATH' ) || exit;

$elemenane_type        = elemenane_archive_type();
$elemenane_has_sidebar = is_active_sidebar( 'default-sidebar' );
?>
<main id="main" class="site-main ane-archive ane-archive--<?php echo esc_attr( $elemenane_type ); ?>" role="main">
	<?php get_template_part( 'breadcrumbs' ); ?>

	<div class="container">
		<div class="ane-archive__layout<?php echo $elemenane_has_sidebar ? ' has-sidebar' : ''; ?>">
			<div class="ane-archive__main">
				<?php
				if ( 'author' === $elemenane_type ) {
					get_template_part( 'tp/archive', 'author' );
				} else {
					get_template_part( 'tp/archive', 'header', array( 'type' => $elemenane_type ) );
				}

				if ( have_posts() ) {
					get_template_part( 'tp/archive', 'loop' );
					ane_post_pagination();
				} else {
					get_template_part( 'tp/archive', 'none' );
				}
				?>
			</div>
			<?php get_sidebar(); ?>
		</div>
	</div>
</main>
