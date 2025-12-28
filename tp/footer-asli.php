<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
	$nama = get_field('nama_organisasi', 'option');
	
?>

<footer class="site-footer ane-footer" id="footer" >

	<div class="sosmed-footer">
		<?php $titlesosmed = get_field('ane_title_sosmed','option'); ?>
		<div class="kiri">
			<?php if( !empty( $whatsapp ) ): ?>
			<h2><?php echo $titlesosmed; ?></h2>
			<?php else: ?>
				<h2><?php echo esc_html__('Support Our','elemenane')."<span>".esc_html__('Social Media','elemenane')."</span>"; ?></h2>
			<?php endif; ?>
		</div>
		<div class="kanan">
			<?php get_template_part('tp/content','sosmed'); ?>
		</div>
	</div>

	<div class="atas">
		<div class="container">
			<div class="ane-col-3">
				<div class="ane-isi">
					<div class="isi">
						<?php
					        $judul = get_field('ane_com_nama','option');
					        $sologan = get_field('ane_com_tag','option');
					        $about = get_field('ane_com_des','option');
					    ?>
						<div class="footer-content">
							<h3><?php echo $judul ?></h3>
							<h4><?php echo $sologan ?></h4>
							<p><?php echo $about ?></p>
						</div>
					</div>
				</div> <!--- end col-md-4 -->
				<div class="ane-isi">
					<div class="isi-v2">
						<h3><?php _e('Useful Links','newsane') ?></h3>
						<?php
								wp_nav_menu(  array(
								'theme_location' => 'menufooter',
								'container'      => 'div',
								'fallback_cb'    => 'wp_page_menu',
								'depth'          => 4,
								)
								);
						?>
					</div>
				</div><!--- end col-md-4 -->
				<div class="ane-isi">
					<div class="isi-v2">
						<h3><?php _e('Contact Us','newsane') ?></h3>
						<?php $Phone = get_field('ane_telepon','option');
						if( have_rows('ane_kontak','option') ): ?>
							<div class="footer-kontak">
								<ul>
									<?php
									while( have_rows('ane_kontak','option') ): the_row();

									
									$fax = get_sub_field('ane_fax');
									$mobile = get_sub_field('ane_handphone');
									$Email = get_sub_field('ane_email');
									$Web = get_sub_field('ane_website');

										if( !empty( $alamat ) ):
									?>
									<li><i class="ane-lokasi"></i> <?php
									echo $alamat; ?></li>
									<?php
										endif;
										if( !empty( $Phone ) ):
									?>
									<li><i class="ane-telepon"></i> <?= $Phone; ?></li>
									<?php
										endif;
										if( !empty( $mobile ) ):
									?>
									<li><i class="ane-handphone"></i> <?= $mobile; ?></li>
									<?php endif;
										if( !empty( $Email ) ):
									?>
									<li><i class="ane-email"></i> <?= $Email; ?></li>
									<?php endif;
										$url = home_url( $path = '/' );
										if( !empty( $Web ) ):
									?>
									<li><a href="<?= $url ?>" alt="<?= $Web; ?>" title="<?= $Web; ?>"> <i class="ane-laptop"></i> <?= $Web; ?></a></li>

									<?php endif; ?>
								</ul>
							<?php endwhile; ?>
							</div>

					<?php endif;?>
					</div>

				</div>
			</div>
		</div>

	</div>
	<div class="bawah">
		<div class="container">
			Copyright <?php echo ane_copyright_year() . ' ' . $judul; ?>
		</div>
	</div>

</footer>