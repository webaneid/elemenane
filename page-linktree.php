<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php
	/**
	 * Template Name: Link Tree
	 *
	 * Standalone link aggregator page for social media bio links.
	 * This template does not use header/footer and renders independently.
	 */
	wp_head();
	?>
</head>
<body <?php body_class( 'ane-linktree' ); ?>>
	<?php wp_body_open(); ?>

	<main id="main" class="site-main">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'tp/content', 'linktree' );
			endwhile;
		else :
			get_template_part( 'tp/content', 'none' );
		endif;
		?>
	</main>

	<?php wp_footer(); ?>
</body>
</html>
