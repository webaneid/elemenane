<?php
/**
 * Landing: hero.
 *
 * Large scroll-snap slider with the featured posts (ACF `ane_news_utama`, or the latest posts as fallback)
 * next to a "Latest" panel. Controlled by js/home.js; works without JS as a swipeable strip.
 *
 * @package elemenane
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$elemenane_hero_ids = elemenane_home_hero_ids( 5 );
if ( empty( $elemenane_hero_ids ) ) {
	return;
}

$elemenane_total  = count( $elemenane_hero_ids );
$elemenane_hero_q = new WP_Query(
	array(
		'post_type'              => 'post',
		'post__in'               => $elemenane_hero_ids,
		'orderby'                => 'post__in',
		'posts_per_page'         => $elemenane_total,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => true,
	)
);

$elemenane_side_q = new WP_Query(
	array(
		'post_type'              => 'post',
		'posts_per_page'         => 4,
		'post_status'            => 'publish',
		'post__not_in'           => $elemenane_hero_ids,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
	)
);
?>
<section class="ane-hero" aria-label="<?php esc_attr_e( 'Featured articles', 'elemenane' ); ?>">
	<div class="container">
		<div class="ane-hero__grid">
			<div class="ane-hero__stage" data-ane-hero>
				<div class="ane-hero__track" tabindex="0" role="group" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured articles', 'elemenane' ); ?>">
					<?php
					$elemenane_i = 0;
					while ( $elemenane_hero_q->have_posts() ) :
						$elemenane_hero_q->the_post();
						++$elemenane_i;
						$elemenane_cats = get_the_category();
						$elemenane_img  = array(
							'class'    => 'ane-hero__img',
							'alt'      => '',
							'sizes'    => '(max-width: 992px) 100vw, 760px',
							'decoding' => 'async',
							'loading'  => 1 === $elemenane_i ? 'eager' : 'lazy',
						);
						if ( 1 === $elemenane_i ) {
							$elemenane_img['fetchpriority'] = 'high';
						}
						?>
						<article class="ane-hero__slide<?php echo 1 === $elemenane_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( /* translators: 1: current slide, 2: total slides */ __( 'Slide %1$d of %2$d', 'elemenane' ), $elemenane_i, $elemenane_total ) ); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								echo get_the_post_thumbnail( get_the_ID(), 'large', $elemenane_img ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core-generated, escaped image markup.
							} else {
								printf( '<img class="ane-hero__img" src="%s" alt="" loading="%s" decoding="async">', esc_url( ane_dummy_thumbnail() ), 1 === $elemenane_i ? 'eager' : 'lazy' );
							}
							?>
							<span class="ane-hero__shade" aria-hidden="true"></span>
							<div class="ane-hero__body">
								<?php if ( ! empty( $elemenane_cats ) ) : ?>
									<a class="ane-hero__cat" href="<?php echo esc_url( get_category_link( $elemenane_cats[0]->term_id ) ); ?>"><?php echo esc_html( $elemenane_cats[0]->name ); ?></a>
								<?php endif; ?>
								<h2 class="ane-hero__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="ane-hero__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
								<div class="ane-hero__meta">
									<span><?php echo esc_html( get_the_author() ); ?></span>
									<?php echo wp_kses( ane_load_times_ago(), elemenane_kses_post_with_svg() ); ?>
								</div>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<?php if ( $elemenane_total > 1 ) : ?>
					<div class="ane-hero__controls">
						<div class="ane-hero__dots">
							<?php for ( $elemenane_d = 1; $elemenane_d <= $elemenane_total; $elemenane_d++ ) : ?>
								<button type="button" class="ane-hero__dot<?php echo 1 === $elemenane_d ? ' is-active' : ''; ?>" data-hero-dot="<?php echo esc_attr( (string) ( $elemenane_d - 1 ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Go to slide %d', 'elemenane' ), $elemenane_d ) ); ?>"><span></span></button>
							<?php endfor; ?>
						</div>
						<div class="ane-hero__arrows">
							<button type="button" class="ane-hero__arrow" data-hero-prev aria-label="<?php esc_attr_e( 'Previous slide', 'elemenane' ); ?>"><?php elemenane_icon( 'chevron-left' ); ?></button>
							<button type="button" class="ane-hero__arrow" data-hero-next aria-label="<?php esc_attr_e( 'Next slide', 'elemenane' ); ?>"><?php elemenane_icon( 'chevron-right' ); ?></button>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $elemenane_side_q->have_posts() ) : ?>
				<aside class="ane-hero__side" aria-label="<?php esc_attr_e( 'Latest', 'elemenane' ); ?>">
					<h2 class="ane-section-title"><?php esc_html_e( 'Latest', 'elemenane' ); ?></h2>
					<div class="ane-hero__list">
						<?php
						while ( $elemenane_side_q->have_posts() ) :
							$elemenane_side_q->the_post();
							get_template_part(
								'tp/archive',
								'card',
								array(
									'variant' => 'row',
									'heading' => 'h3',
								)
							);
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
