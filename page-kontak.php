<?php
/**
 * Template Name: Contact Us
 *
 * Custom contact page template with company information and Google Maps integration.
 * Displays contact details from ACF options and an interactive map.
 */
get_header();
?>
<main id="main" class="site-main" role="main">
	<?php get_template_part( 'breadcrumbs' ); ?>

	<div class="container">
		<div class="ane-col-64">
			<div class="ane-kiri">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						get_template_part( 'tp/content', 'page-kontak' );
					endwhile;
				else :
					get_template_part( 'tp/content', 'none' );
				endif;
				?>
			</div>
		</div>
	</div>
</main>
<?php get_footer();
