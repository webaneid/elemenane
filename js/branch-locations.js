/**
 * Branch Locations AJAX Handler
 *
 * Handles dynamic loading of cities based on province selection.
 *
 * @package elemenane
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Initialize location dropdowns.
	 */
	function initLocationDropdowns() {
		// Use jQuery change event on select2 hidden input.
		$(document).on('change', 'select[name="acf[field_ane_branch_province]"]', function() {
			var provinceId = $(this).val();
			var $citySelect = $('select[name="acf[field_ane_branch_city]"]');

			if (!$citySelect.length) {
				return;
			}

			// Clear city dropdown.
			$citySelect.html('<option value="">Pilih Kabupaten/Kota</option>');

			if (!provinceId) {
				// Just trigger change to update select2.
				$citySelect.trigger('change');
				return;
			}

			// Load cities via AJAX.
			$.ajax({
				url: aneBranchLocations.ajax_url,
				type: 'POST',
				data: {
					action: 'ane_load_cities',
					province_id: provinceId,
					nonce: aneBranchLocations.nonce
				},
				success: function(response) {
					if (response.success && response.data.cities) {
						// Build options.
						var options = '<option value="">Pilih Kabupaten/Kota</option>';
						$.each(response.data.cities, function(index, city) {
							options += '<option value="' + city.id + '">' + city.name + '</option>';
						});

						// Update city select and trigger change.
						$citySelect.html(options).trigger('change');
					}
				},
				error: function(xhr, status, error) {
					alert('Error loading cities. Please try again.');
				}
			});
		});
	}

	// Initialize when DOM is ready.
	$(document).ready(function() {
		initLocationDropdowns();
	});

})(jQuery);
