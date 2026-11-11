<?php
/**
 * Archive Header
 *
 * Title panel for category, tag, date, search and blog index archives.
 *
 * @package elemenane
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_type  = ! empty( $args['type'] ) ? (string) $args['type'] : elemenane_archive_type();
$elemenane_title = elemenane_archive_title( $elemenane_type );
$elemenane_found = elemenane_archive_found_posts();
$elemenane_desc  = in_array( $elemenane_type, array( 'category', 'tag', 'tax' ), true ) ? term_description() : '';

if ( 'home' === $elemenane_type && is_front_page() ) {
	return;
}
?>
<header class="page-header ane-archive-head">
	<span class="ane-archive-head__kicker"><?php echo esc_html( elemenane_archive_kicker( $elemenane_type ) ); ?></span>

	<h1 class="ane-archive-head__title">
		<?php if ( 'tag' === $elemenane_type ) : ?>
			<span class="ane-archive-head__hash" aria-hidden="true">#</span>
		<?php endif; ?>
		<?php if ( 'search' === $elemenane_type ) : ?>
			<span class="ane-archive-head__quote" aria-hidden="true">&ldquo;</span><?php echo esc_html( $elemenane_title ); ?><span class="ane-archive-head__quote" aria-hidden="true">&rdquo;</span>
		<?php else : ?>
			<?php echo esc_html( $elemenane_title ); ?>
		<?php endif; ?>
	</h1>

	<?php if ( '' !== trim( wp_strip_all_tags( $elemenane_desc ) ) ) : ?>
		<div class="ane-archive-head__desc"><?php echo wp_kses_post( $elemenane_desc ); ?></div>
	<?php endif; ?>

	<?php if ( 'home' !== $elemenane_type ) : ?>
		<p class="ane-archive-head__count">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: number of posts */
					1 === $elemenane_found ? __( '%s article', 'elemenane' ) : __( '%s articles', 'elemenane' ),
					number_format_i18n( $elemenane_found )
				)
			);
			?>
		</p>
	<?php endif; ?>

	<?php if ( 'search' === $elemenane_type ) : ?>
		<div class="ane-archive-head__search">
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</header>
