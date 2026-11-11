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
			const thumbImg = $thumb.find('img').get(0);
			const mainImg = $('.ane-gallery-main-img').get(0);

			// Update active state
			$('.ane-gallery-thumb').removeClass('active');
			$thumb.addClass('active');

			$('.ane-gallery-popup').attr('href', $thumb.data('full'));

			if (!thumbImg || !mainImg) {
				return;
			}

			// The thumbnail uses the same image size as the main image, so copy
			// its attributes. srcset must change too: when present, browsers
			// ignore a new src.
			['src', 'srcset', 'sizes', 'alt', 'width', 'height'].forEach(function (attr) {
				if (thumbImg.hasAttribute(attr)) {
					mainImg.setAttribute(attr, thumbImg.getAttribute(attr));
				} else {
					mainImg.removeAttribute(attr);
				}
			});
		});
	}

	/**
	 * Gallery Lightbox (js/lightbox.js)
	 *
	 * Opens at the image currently shown in the main slot, with all gallery
	 * images available as next/previous.
	 */
	function initGalleryLightbox() {
		$('.ane-gallery-popup').on('click', function (e) {
			if (!window.aneLightbox) {
				return; // Falls back to opening the image URL.
			}
			e.preventDefault();

			const $thumbs = $('.ane-gallery-thumb');
			let items;
			let index = 0;

			if ($thumbs.length > 1) {
				items = $thumbs.map(function () {
					return { src: $(this).data('full'), title: $(this).find('img').attr('alt') || '' };
				}).get();
				index = Math.max(0, $thumbs.index($thumbs.filter('.active')));
			} else {
				items = [{ src: $(this).attr('href'), title: '' }];
			}

			window.aneLightbox.open(items, index);
		});
	}

	/**
	 * Product Tabs (WAI-ARIA tabs: click, arrow keys, #reviews hash and [data-open-tab] links)
	 */
	function initProductTabs() {
		const $tabs = $('.ane-tab-btn');
		if (!$tabs.length) {
			return;
		}

		function activate(key, focus) {
			const $btn = $tabs.filter('[data-tab="' + key + '"]');
			if (!$btn.length) {
				return false;
			}
			$tabs.removeClass('active').attr({ 'aria-selected': 'false', tabindex: '-1' });
			$btn.addClass('active').attr({ 'aria-selected': 'true', tabindex: '0' });
			$('.ane-tab-pane').removeClass('active').attr('hidden', '');
			$('#tab-' + key).addClass('active').removeAttr('hidden');
			if (focus) {
				$btn.trigger('focus');
			}
			return true;
		}

		$tabs.on('click', function (e) {
			e.preventDefault();
			activate($(this).data('tab'), false);
		});

		$tabs.on('keydown', function (e) {
			const keys = $tabs.map(function () { return $(this).data('tab'); }).get();
			let i = keys.indexOf($(this).data('tab'));
			if (e.key === 'ArrowRight') { i = (i + 1) % keys.length; }
			else if (e.key === 'ArrowLeft') { i = (i - 1 + keys.length) % keys.length; }
			else if (e.key === 'Home') { i = 0; }
			else if (e.key === 'End') { i = keys.length - 1; }
			else { return; }
			e.preventDefault();
			activate(keys[i], true);
		});

		// "(12 reviews)" link, WooCommerce's rating link and #reviews / #comments in the URL.
		$(document).on('click', '[data-open-tab], a.woocommerce-review-link', function (e) {
			const key = $(this).data('tab-target') || $(this).data('open-tab') || 'reviews';
			if (activate(key, false)) {
				e.preventDefault();
				document.querySelector('.ane-product-tabs').scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		});
		if (/^#(reviews|comments|comment-)/.test(window.location.hash)) {
			activate('reviews', false);
		}
	}

	/**
	 * WooCommerce variable products: show the chosen variation's image in the main slot.
	 * WooCommerce triggers found_variation / reset_data on form.variations_form (jQuery events).
	 */
	function initVariationImages() {
		const mainImg = $('.ane-gallery-main-img').get(0);
		if (!mainImg) {
			return;
		}
		const attrs = ['src', 'srcset', 'sizes', 'alt', 'width', 'height'];
		const original = {};
		attrs.forEach(function (attr) { original[attr] = mainImg.getAttribute(attr); });
		const originalFull = $('.ane-gallery-popup').attr('href');

		function apply(source, full) {
			attrs.forEach(function (attr) {
				if (source[attr]) {
					mainImg.setAttribute(attr, source[attr]);
				} else {
					mainImg.removeAttribute(attr);
				}
			});
			if (full) {
				$('.ane-gallery-popup').attr('href', full);
			}
		}

		$(document).on('found_variation', 'form.variations_form', function (event, variation) {
			if (variation && variation.image && variation.image.src) {
				apply({
					src: variation.image.src,
					srcset: variation.image.srcset,
					sizes: variation.image.sizes,
					alt: variation.image.alt,
					width: variation.image.src_w,
					height: variation.image.src_h
				}, variation.image.full_src);
				$('.ane-gallery-thumb').removeClass('active');
			}
		});
		$(document).on('reset_data', 'form.variations_form', function () {
			apply(original, originalFull);
			$('.ane-gallery-thumb').first().addClass('active');
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


		// Open modal
		$trigger.on('click', function (e) {
			e.preventDefault();
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
	 * Branch Detail Popup (reuses the lightbox dialog)
	 */
	function initBranchDetail() {
		$(document).on('click', '.ane-branch-detail', function () {
			const $item = $(this).closest('.ane-branch-item');
			const branchData = $item.data('branch');

			// Hide branch modal temporarily (but don't remove active class)
			const $modal = $('#ane-branch-modal');
			$modal.css('visibility', 'hidden');

			// Open the branch detail card in the lightbox dialog; show the branch list again when it closes.
			if (window.aneLightbox) {
				window.aneLightbox.openHtml(buildBranchDetailHTML(branchData), {
					className: 'ane-branch-detail-popup',
					onClose: function () {
						$modal.css('visibility', 'visible');
					}
				});
			} else {
				$modal.css('visibility', 'visible');
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
		initGalleryThumbs();
		initGalleryLightbox();
		initProductTabs();
		initVariationImages();
		initBranchModal();
		initBranchWhatsApp();
		initBranchDetail();
	});

})(jQuery);
