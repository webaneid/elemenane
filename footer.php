<?php
/**
 * Site Footer.
 *
 * Uses Elementor Theme Builder footer if available,
 * otherwise falls back to native theme footer.
 *
 * @package elemenane
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	get_template_part( 'tp/footer-default' );
}
?>

	<?php if ( get_field( 'ane_cs_aktif', 'option' ) ) : ?>
	<div class="floating-chat">
		<div class="chat">
			<div class="header">
				<?php
				$title = get_field( 'ane_cs_label', 'option' );
				if ( ! empty( $title ) ) :
					?>
					<span class="tombol"><?php echo esc_html( (string) $title ); ?></span>
				<?php endif; ?>
				<div class="ane-tutupin">
					<i class="ane-close-x"></i>
				</div>
			</div>
			<div class="mesej">
				<div class="mesej-header">
				<?php
				$msg = get_field( 'ane_cs_welcome', 'option' );
				if ( ! empty( $msg ) ) :
					?>
					<p><?php echo esc_html( (string) $msg ); ?></p>
				<?php endif; ?>
				</div>

				<div class="mesej-box">
					<input type="text" id="waMessage" class="wa-input" placeholder="<?php echo esc_attr__( 'Ketik pesan...', 'elemenane' ); ?>">
					<button class="wa-button" id="sendWaButton" aria-label="<?php echo esc_attr__( 'Send WhatsApp Message', 'elemenane' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f1f1f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tabler-icon tabler-icon-send" aria-hidden="true">
						<path d="M10 14l11 -11"></path>
						<path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
					</svg>
					</button>
				</div>

				<?php if ( have_rows( 'ane_cs', 'option' ) ) : ?>
				<ul class="text-box">
					<li class="body-pilihan">
						<h4><?php esc_html_e( 'Atau Chat langsung dengan Admin kami', 'elemenane' ); ?></h4>
					</li>

					<?php
					while ( have_rows( 'ane_cs', 'option' ) ) :
						the_row();
						$wa    = (string) get_sub_field( 'ane_whatsapp' );
						$image = get_sub_field( 'ane_image' );
						$nama  = (string) get_sub_field( 'ane_nama' );
						$area  = (string) get_sub_field( 'ane_area' );
						?>
						<li>
							<?php
							if ( is_array( $image ) ) :
								$size  = 'sarika-square-sm';
								$thumb = isset( $image['sizes'][ $size ] ) ? $image['sizes'][ $size ] : ( isset( $image['url'] ) ? $image['url'] : '' );
								if ( ! empty( $thumb ) ) :
									?>
									<img src="<?php echo esc_url( (string) $thumb ); ?>" alt="<?php echo esc_attr( $nama ); ?>" loading="lazy" decoding="async" width="48" height="48">
									<?php
								endif;
							endif;
							?>
							<a href="<?php echo esc_url( 'https://wa.me/' . rawurlencode( preg_replace( '/[^0-9]/', '', $wa ) ) ); ?>" target="_blank" rel="noopener noreferrer">
								<span><?php echo esc_html( $nama ); ?></span><br/>
								<?php echo esc_html( $area ); ?>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>

		<?php
		$numbers = array();
		if ( have_rows( 'ane_cs', 'option' ) ) {
			while ( have_rows( 'ane_cs', 'option' ) ) {
				the_row();
				$raw_wa = (string) get_sub_field( 'ane_whatsapp' );
				$clean  = preg_replace( '/[^0-9]/', '', $raw_wa );
				if ( '' !== $clean ) {
					$numbers[] = $clean;
				}
			}
		}
		?>
	<script>
	document.addEventListener("DOMContentLoaded", function () {
		var waBtn = document.querySelector(".wa-button");
		if (!waBtn) {
			return;
		}
		var numbers = <?php echo wp_json_encode( $numbers ); ?>;
		waBtn.addEventListener("click", function () {
			var input = document.getElementById("waMessage");
			var message = input ? input.value.trim() : "";
			if (message === "") {
				alert("Silakan ketik pesan terlebih dahulu.");
				return;
			}
			if (!Array.isArray(numbers) || numbers.length === 0) {
				alert("Nomor WhatsApp tidak tersedia.");
				return;
			}
			var lastIndex = parseInt(localStorage.getItem("waLastIndex") || "0", 10);
			if (isNaN(lastIndex) || lastIndex < 0) {
				lastIndex = 0;
			}
			var phone = numbers[lastIndex % numbers.length];
			localStorage.setItem("waLastIndex", String((lastIndex + 1) % numbers.length));
			window.open("https://wa.me/" + encodeURIComponent(phone) + "?text=" + encodeURIComponent(message), "_blank", "noopener,noreferrer");
		});
	});
	</script>

	<?php endif; ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
