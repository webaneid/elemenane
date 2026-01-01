/**
 * Single Product JavaScript
 *
 * Handles gallery lightbox, tabs, branch popup, and purchase interactions.
 *
 * @package elemenane
 * @since 1.0.0
 */

(function ($) {
	'use strict';

	/**
	 * Gallery Thumbnail Navigation
	 */
	function initGalleryThumbs() {
		$('.ane-gallery-thumb').on('click', function () {
			const $thumb = $(this);
			const fullUrl = $thumb.data('full');
			const largeUrl = $thumb.data('large');

			// Update active state
			$('.ane-gallery-thumb').removeClass('active');
			$thumb.addClass('active');

			// Update main image
			const $mainLink = $('.ane-gallery-popup');
			const $mainImg = $('.ane-gallery-main-img');

			$mainLink.attr('href', fullUrl);
			$mainImg.attr('src', largeUrl);
		});
	}

	/**
	 * Gallery Lightbox (Magnific Popup)
	 */
	function initGalleryLightbox() {
		if (typeof $.fn.magnificPopup === 'undefined') {
			return;
		}

		// Single image popup
		$('.ane-gallery-popup').magnificPopup({
			type: 'image',
			closeOnContentClick: true,
			closeBtnInside: false,
			fixedContentPos: true,
			mainClass: 'mfp-with-zoom',
			image: {
				verticalFit: true
			},
			zoom: {
				enabled: true,
				duration: 300
			}
		});

		// Gallery popup (if multiple images)
		if ($('.ane-gallery-thumb').length > 1) {
			$('.ane-gallery-popup').magnificPopup({
				type: 'image',
				gallery: {
					enabled: true
				},
				callbacks: {
					elementParse: function (item) {
						// Build gallery from all thumbnails
						const items = [];
						$('.ane-gallery-thumb').each(function () {
							items.push({
								src: $(this).data('full'),
								type: 'image'
							});
						});
						this.items = items;
					}
				}
			});
		}
	}

	/**
	 * Product Tabs
	 */
	function initProductTabs() {
		$('.ane-tab-btn').on('click', function (e) {
			e.preventDefault();

			const $btn = $(this);
			const tabId = $btn.data('tab');

			// Update active button
			$('.ane-tab-btn').removeClass('active');
			$btn.addClass('active');

			// Show corresponding tab content
			$('.ane-tab-pane').removeClass('active');
			$('#tab-' + tabId).addClass('active');
		});
	}

	/**
	 * Branch Modal
	 */
	function initBranchModal() {
		const $modal = $('#ane-branch-modal');
		const $trigger = $('#ane-branch-trigger');
		const $close = $('#ane-branch-modal-close');
		const $overlay = $('.ane-branch-modal-overlay');
		const $searchInput = $('#ane-branch-search');

		console.log('Branch modal init', {
			modal: $modal.length,
			trigger: $trigger.length,
			close: $close.length,
			overlay: $overlay.length,
			search: $searchInput.length
		});

		// Open modal
		$trigger.on('click', function (e) {
			e.preventDefault();
			console.log('Branch trigger clicked');
			$modal.addClass('active');
			$('body').addClass('ane-modal-open');

			// Focus on search input
			setTimeout(function() {
				$searchInput.focus();
			}, 100);
		});

		// Close modal
		function closeModal() {
			$modal.removeClass('active');
			$('body').removeClass('ane-modal-open');

			// Reset search
			$searchInput.val('');
			$('.ane-branch-item').show();
		}

		$close.on('click', closeModal);
		$overlay.on('click', closeModal);

		// ESC key to close
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' && $modal.hasClass('active')) {
				closeModal();
			}
		});

		// Search/Filter branches by city
		$searchInput.on('input', function() {
			const searchTerm = $(this).val().toLowerCase().trim();

			$('.ane-branch-item').each(function() {
				const $item = $(this);
				const branchData = $item.data('branch');
				const city = branchData.city ? branchData.city.toLowerCase() : '';
				const title = branchData.title ? branchData.title.toLowerCase() : '';

				// Search in city and title
				if (city.includes(searchTerm) || title.includes(searchTerm)) {
					$item.show();
				} else {
					$item.hide();
				}
			});

			// Show "no results" message if all items hidden
			const visibleItems = $('.ane-branch-item:visible').length;
			$('.ane-branch-no-results').remove();

			if (visibleItems === 0 && searchTerm !== '') {
				const noResultsMsg = typeof aneProductStrings !== 'undefined'
					? aneProductStrings.noBranchFound
					: 'No branches found for search';
				$('.ane-branch-list').append(
					'<div class="ane-branch-no-results">' + noResultsMsg + ' "' + escapeHtml(searchTerm) + '"</div>'
				);
			}
		});
	}

	/**
	 * Branch WhatsApp Link
	 */
	function initBranchWhatsApp() {
		$('.ane-branch-whatsapp').on('click', function (e) {
			e.preventDefault();

			const $btn = $(this);
			const whatsappNo = $btn.data('whatsapp');
			const productTitle = $('.ane-product-title').text();
			const productUrl = window.location.href;

			// Clean phone number
			let cleanNumber = whatsappNo.replace(/[^0-9]/g, '');

			// Add 62 if starts with 0
			if (cleanNumber.charAt(0) === '0') {
				cleanNumber = '62' + cleanNumber.substring(1);
			}

			// Build message
			const messageTemplate = typeof aneProductStrings !== 'undefined'
				? aneProductStrings.whatsappMessage
				: 'Hi, I am interested in the product *%s*. Can you help me with more information? %s';
			const message = messageTemplate.replace('%s', productTitle).replace('%s', productUrl);

			// Open WhatsApp
			const waUrl = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(message)}`;
			window.open(waUrl, '_blank');
		});
	}

	/**
	 * Branch Detail Popup (Reuse existing branch popup modal)
	 */
	function initBranchDetail() {
		$(document).on('click', '.ane-branch-detail', function () {
			const $item = $(this).closest('.ane-branch-item');
			const branchData = $item.data('branch');

			// Hide branch modal temporarily (but don't remove active class)
			const $modal = $('#ane-branch-modal');
			$modal.css('visibility', 'hidden');

			// Open branch detail popup (using Magnific Popup inline)
			if (typeof $.fn.magnificPopup !== 'undefined') {
				$.magnificPopup.open({
					items: {
						src: buildBranchDetailHTML(branchData),
						type: 'inline'
					},
					closeBtnInside: true,
					mainClass: 'ane-branch-detail-popup',
					callbacks: {
						open: function() {
							// Remove ane-modal-open temporarily so Magnific can handle overflow
							$('body').removeClass('ane-modal-open');
						},
						close: function() {
							// When detail popup closes, show branch modal again
							$modal.css('visibility', 'visible');
							$('body').addClass('ane-modal-open');
						}
					}
				});
			}
		});
	}

	/**
	 * Build Branch Detail HTML
	 */
	function buildBranchDetailHTML(branch) {
		// Get localized strings with fallbacks
		const addressLabel = typeof aneProductStrings !== 'undefined' ? aneProductStrings.address : 'Address';
		const phoneLabel = typeof aneProductStrings !== 'undefined' ? aneProductStrings.phone : 'Phone';
		const emailLabel = typeof aneProductStrings !== 'undefined' ? aneProductStrings.email : 'Email';

		let html = '<div class="ane-branch-detail-content">';
		html += '<h3>' + escapeHtml(branch.title) + '</h3>';

		if (branch.address) {
			html += '<p><strong>' + addressLabel + ':</strong><br>' + escapeHtml(branch.address) + '</p>';
		}

		if (branch.city) {
			html += '<p><strong>City:</strong> ' + escapeHtml(branch.city) + '</p>';
		}

		if (branch.phone) {
			html += '<p><strong>' + phoneLabel + ':</strong> ' + escapeHtml(branch.phone) + '</p>';
		}

		if (branch.email) {
			html += '<p><strong>' + emailLabel + ':</strong> ' + escapeHtml(branch.email) + '</p>';
		}

		// Check if we have any actions to display
		const hasWhatsApp = branch.whatsapp && branch.whatsapp.trim() !== '';
		const hasMapLocation = branch.lat && branch.lng;

		if (hasWhatsApp || hasMapLocation) {
			html += '<div class="ane-branch-actions">';

			// WhatsApp button - independent condition
			if (hasWhatsApp) {
				const cleanNumber = branch.whatsapp.replace(/[^0-9]/g, '');
				const productTitle = $('.ane-product-title').text();
				const productUrl = window.location.href;
				const messageTemplate = typeof aneProductStrings !== 'undefined'
					? aneProductStrings.whatsappMessage
					: 'Hi, I am interested in the product *%s*. Can you help me with more information? %s';
				const message = messageTemplate.replace('%s', productTitle).replace('%s', productUrl);
				const waUrl = `https://wa.me/${cleanNumber.charAt(0) === '0' ? '62' + cleanNumber.substring(1) : cleanNumber}?text=${encodeURIComponent(message)}`;

				html += '<a href="' + waUrl + '" target="_blank" class="ane-btn ane-btn-whatsapp">Chat WhatsApp</a>';
			}

			// Petunjuk Arah button - independent condition
			if (hasMapLocation) {
				const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${branch.lat},${branch.lng}`;
				html += '<a href="' + mapsUrl + '" target="_blank" class="ane-btn ane-btn-outline">Petunjuk Arah</a>';
			}

			html += '</div>';
		}

		html += '</div>';

		return html;
	}

	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function (m) {
			return map[m];
		});
	}

	/**
	 * Initialize on Document Ready
	 */
	$(document).ready(function () {
		console.log('Single product JS initialized');
		initGalleryThumbs();
		initGalleryLightbox();
		initProductTabs();
		initBranchModal();
		initBranchWhatsApp();
		initBranchDetail();
	});

})(jQuery);
