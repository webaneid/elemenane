<?php
/**
 * Site Footer
 *
 * Uses Elementor Theme Builder footer if available,
 * otherwise falls back to native theme footer.
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	// No Elementor footer found, use native theme footer
	get_template_part( 'tp/footer-asli' );
}
// If elementor_theme_do_location() returns true, Elementor already rendered the footer
?>

	<?php if (esc_attr( get_field('ane_cs_aktif','option') )): ?>
	<div class="floating-chat">
		<div class="chat">
			<div class="header">
				<?php
				$title = get_field('ane_cs_label', 'option');
				if (!empty($title)): ?>
				<span class="tombol"><?php echo esc_html($title); ?></span>
				<?php endif; ?>
				<div class="ane-tutupin">
				<i class="ane-close-x"></i>
				</div>
			</div>
			<div class="mesej">
				<div class="mesej-header">
				<?php $msg = get_field('ane_cs_welcome', 'option');
				if (!empty($msg)): ?>
					<p><?php echo esc_html($msg); ?></p>
				<?php endif; ?>
				</div>
			
				<div class="mesej-box">
					<input type="text" id="waMessage" class="wa-input" placeholder="Ketik pesan...">
					<button class="wa-button" id="sendWaButton">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f1f1f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tabler-icon tabler-icon-send">
						<path d="M10 14l11 -11"></path>
						<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
					</svg>
					</button>
				</div>
				
				<?php if (have_rows('ane_cs','option')): ?>
				<ul class="text-box">
				<div class="body-pilihan">
					<h4><?php esc_html_e( 'Atau Chat langsung dengan Admin kami', 'autoane' ); ?></h4>
				</div>

				<?php while (have_rows('ane_cs','option')) : the_row();
					$wa = get_sub_field('ane_whatsapp'); ?>
					<li>
						<?php
						$image = get_sub_field('ane_image');
						$nama = get_sub_field('ane_nama');
						$area = get_sub_field('ane_area');

						if ($image):
							$size = 'sarika-square-sm';
							$thumb = $image['sizes'][$size] ?? $image['url'];
							if (!empty($thumb)): ?>
							<img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($nama); ?>">
							<?php endif;
						endif; ?>
						<a href="https://wa.me/<?php echo esc_attr($wa); ?>" target="_blank">
							<span><?php echo esc_html($nama); ?></span><br/>
							<?php echo esc_html($area); ?>
						</a>
					</li>
				<?php endwhile; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<script>
	document.addEventListener("DOMContentLoaded", function () {
		function sendWhatsApp() {
			let message = document.getElementById("waMessage").value.trim();
			if (message === "") {
				alert("Silakan ketik pesan terlebih dahulu.");
				return;
			}
			
			// Ambil nomor WhatsApp dari ACF Repeater
			let numbers = <?php
				$numbers = [];
				if (have_rows('ane_cs', 'option')) {
				while (have_rows('ane_cs', 'option')) {
					the_row();
					$numbers[] = get_sub_field('ane_whatsapp');
				}
				}
				echo json_encode($numbers);
			?>;
			
			if (numbers.length === 0) {
				alert("Nomor WhatsApp tidak tersedia.");
				return;
			}

			// Ambil indeks terakhir dari localStorage
			let lastIndex = localStorage.getItem("waLastIndex");
			lastIndex = lastIndex ? parseInt(lastIndex) : 0;

			// Tentukan nomor WhatsApp yang akan digunakan
			let phone = numbers[lastIndex];

			// Perbarui indeks untuk pemanggilan berikutnya (round-robin)
			lastIndex = (lastIndex + 1) % numbers.length;
			localStorage.setItem("waLastIndex", lastIndex);

			// Buka WhatsApp dengan nomor yang dipilih
			let url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
			window.open(url, "_blank");
		}

		// Tambahkan event listener ke tombol
		document.querySelector(".wa-button").addEventListener("click", sendWhatsApp);
	});
	</script>

	<?php endif; ?>

</div> <!--- webane-wrapper -->

<?php wp_footer(); ?>
</body>
</html>
