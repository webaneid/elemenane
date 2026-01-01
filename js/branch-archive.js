/**
 * Branch Archive Interactive Map & Filter
 *
 * Handles Google Maps with markers, location filtering, and modal popup.
 *
 * @package elemenane
 * @since 1.0.0
 */

(function($) {
	'use strict';

	var map;
	var markers = [];
	var infoWindow;
	var currentlyLoaded = 12; // Number of branches currently displayed
	var filteredBranches = []; // Store filtered branches for pagination

	/**
	 * Initialize Google Maps with all branch markers.
	 */
	function initMap() {
		if (typeof google === 'undefined' || !aneBranchesData || aneBranchesData.length === 0) {
			$('#ane-branch-map').html('<p style="text-align: center; padding: 40px;">Peta tidak dapat dimuat atau tidak ada cabang.</p>');
			return;
		}

		// Default center: Indonesia
		var centerLat = -2.5489;
		var centerLng = 118.0149;

		// If branches exist, center on first branch
		if (aneBranchesData.length > 0) {
			centerLat = aneBranchesData[0].lat;
			centerLng = aneBranchesData[0].lng;
		}

		// Initialize map
		map = new google.maps.Map(document.getElementById('ane-branch-map'), {
			zoom: 5,
			center: {lat: centerLat, lng: centerLng},
			styles: [
				{
					featureType: 'poi',
					elementType: 'labels',
					stylers: [{visibility: 'off'}]
				}
			]
		});

		infoWindow = new google.maps.InfoWindow();

		// Add markers for all branches
		aneBranchesData.forEach(function(branch, index) {
			addMarker(branch, index);
		});

		// Auto-fit bounds to show all markers
		if (markers.length > 0) {
			var bounds = new google.maps.LatLngBounds();
			markers.forEach(function(marker) {
				bounds.extend(marker.getPosition());
			});
			map.fitBounds(bounds);

			// Prevent too much zoom on single marker or close markers
			google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
				var maxZoom = 15; // Max zoom level
				if (map.getZoom() > maxZoom) {
					map.setZoom(maxZoom);
				}
			});
		}
	}

	/**
	 * Add marker to map.
	 */
	function addMarker(branch, index) {
		var marker = new google.maps.Marker({
			position: {lat: branch.lat, lng: branch.lng},
			map: map,
			title: branch.title,
			animation: google.maps.Animation.DROP,
			icon: {
				url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
			}
		});

		// Store branch data in marker
		marker.branchData = branch;

		// Click marker to show info window
		marker.addListener('click', function() {
			showInfoWindow(marker);
			highlightCard(branch.id);
		});

		markers.push(marker);
	}

	/**
	 * Show info window on marker.
	 */
	function showInfoWindow(marker) {
		var branch = marker.branchData;
		var content = '<div class="ane-map-info">';
		content += '<h4>' + branch.title + '</h4>';
		content += '<p>' + branch.address + '</p>';
		if (branch.phone) {
			content += '<p><strong>Telepon:</strong> ' + branch.phone + '</p>';
		}
		content += '<a href="#" class="ane-map-info__detail" data-branch-id="' + branch.id + '">Lihat Detail</a>';
		content += '</div>';

		infoWindow.setContent(content);
		infoWindow.open(map, marker);
	}

	/**
	 * Highlight card when marker clicked.
	 */
	function highlightCard(branchId) {
		$('.ane-branch-card').removeClass('ane-branch-card--active');
		$('.ane-branch-card[data-branch-id="' + branchId + '"]').addClass('ane-branch-card--active');

		// Scroll to card
		var $card = $('.ane-branch-card[data-branch-id="' + branchId + '"]');
		if ($card.length) {
			$('html, body').animate({
				scrollTop: $card.offset().top - 100
			}, 500);
		}
	}

	/**
	 * Populate filter dropdowns.
	 */
	function populateFilters() {
		var provinces = {};
		var cities = {};

		aneBranchesData.forEach(function(branch) {
			if (branch.province) {
				provinces[branch.province] = true;
			}
			if (branch.city) {
				cities[branch.city] = true;
			}
		});

		// Load JSON for province/city names
		$.getJSON(aneMapConfig.location_json_url, function(data) {
			// Populate provinces
			var provinceOptions = '<option value="">Semua Provinsi</option>';
			data.provinces.forEach(function(province) {
				if (provinces[province.id]) {
					provinceOptions += '<option value="' + province.id + '">' + province.name + '</option>';
				}
			});
			$('#ane-filter-province').html(provinceOptions);
		});
	}

	/**
	 * Filter branches by province/city.
	 */
	function filterBranches() {
		var selectedProvince = $('#ane-filter-province').val();
		var selectedCity = $('#ane-filter-city').val();

		filteredBranches = aneBranchesData.filter(function(branch) {
			var matchProvince = !selectedProvince || branch.province === selectedProvince;
			var matchCity = !selectedCity || branch.city === selectedCity;
			return matchProvince && matchCity;
		});

		// Update map markers
		updateMapMarkers(filteredBranches);

		// Reset pagination and show first 12 branches
		currentlyLoaded = 12;
		renderBranchCards(filteredBranches.slice(0, currentlyLoaded));

		// Update Load More button
		updateLoadMoreButton();
	}

	/**
	 * Render branch cards (replace existing cards).
	 */
	function renderBranchCards(branches) {
		var $list = $('#ane-branch-list');

		// Remove existing cards and empty messages
		$('.ane-branch-card').remove();
		$('.ane-branch-list__empty-filter').remove();

		if (branches.length === 0) {
			$list.append('<p class="ane-branch-list__empty-filter">Tidak ada cabang di lokasi yang dipilih.</p>');
			return;
		}

		// Render cards
		branches.forEach(function(branch) {
			var card = buildBranchCard(branch);
			$list.append(card);
		});
	}

	/**
	 * Append more branch cards (for Load More).
	 */
	function appendBranchCards(branches) {
		var $list = $('#ane-branch-list');

		branches.forEach(function(branch) {
			var card = buildBranchCard(branch);
			$list.append(card);
		});
	}

	/**
	 * Build branch card HTML.
	 */
	function buildBranchCard(branch) {
		var card = '<article class="ane-branch-card" data-branch-id="' + branch.id + '" data-province="' + branch.province + '" data-city="' + branch.city + '">';

		if (branch.thumbnail) {
			card += '<div class="ane-branch-card__image">';
			card += '<img src="' + branch.thumbnail + '" alt="' + escapeHtml(branch.title) + '">';
			card += '</div>';
		}

		card += '<div class="ane-branch-card__content">';
		card += '<h3 class="ane-branch-card__title">' + escapeHtml(branch.title) + '</h3>';
		card += '<p class="ane-branch-card__address">';
		card += '<span class="dashicons dashicons-location"></span>';
		card += escapeHtml(trimWords(branch.address, 10));
		card += '</p>';

		if (branch.phone) {
			card += '<p class="ane-branch-card__phone">';
			card += '<span class="dashicons dashicons-phone"></span>';
			card += escapeHtml(branch.phone);
			card += '</p>';
		}

		card += '<div class="ane-branch-card__actions">';
		card += '<a href="#" class="ane-branch-card__detail" data-branch-id="' + branch.id + '">Detail</a>';
		card += '<a href="https://www.google.com/maps/dir/?api=1&destination=' + branch.lat + ',' + branch.lng + '" target="_blank" class="ane-branch-card__directions">Petunjuk Arah</a>';
		card += '</div>';
		card += '</div>';
		card += '</article>';

		return card;
	}

	/**
	 * Update Load More button state.
	 */
	function updateLoadMoreButton() {
		var $btn = $('#ane-branch-load-more');
		var $wrap = $('.ane-branch-load-more-wrap');
		var total = filteredBranches.length;

		if (currentlyLoaded >= total) {
			$wrap.hide();
		} else {
			$wrap.show();
			$btn.find('.ane-branch-load-more__count').text('(' + currentlyLoaded + ' / ' + total + ')');
		}
	}

	/**
	 * Load more branches.
	 */
	function loadMoreBranches() {
		var nextBatch = filteredBranches.slice(currentlyLoaded, currentlyLoaded + 12);

		if (nextBatch.length > 0) {
			appendBranchCards(nextBatch);
			currentlyLoaded += nextBatch.length;
			updateLoadMoreButton();
		}
	}

	/**
	 * Trim words (simple implementation).
	 */
	function trimWords(text, limit) {
		if (!text) return '';
		var words = text.split(' ');
		if (words.length > limit) {
			return words.slice(0, limit).join(' ') + '...';
		}
		return text;
	}

	/**
	 * Escape HTML.
	 */
	function escapeHtml(text) {
		if (!text) return '';
		var map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return String(text).replace(/[&<>"']/g, function(m) {
			return map[m];
		});
	}

	/**
	 * Update map markers based on filter.
	 */
	function updateMapMarkers(filteredBranches) {
		// Hide all markers
		markers.forEach(function(marker) {
			marker.setMap(null);
		});

		// Show filtered markers
		var bounds = new google.maps.LatLngBounds();
		var hasVisibleMarkers = false;

		markers.forEach(function(marker) {
			var isVisible = filteredBranches.some(function(b) {
				return b.id === marker.branchData.id;
			});

			if (isVisible) {
				marker.setMap(map);
				bounds.extend(marker.getPosition());
				hasVisibleMarkers = true;
			}
		});

		// Fit bounds to visible markers
		if (hasVisibleMarkers) {
			map.fitBounds(bounds);
		}
	}

	/**
	 * Update city filter based on selected province.
	 */
	function updateCityFilter() {
		var selectedProvince = $('#ane-filter-province').val();

		if (!selectedProvince) {
			$('#ane-filter-city').html('<option value="">Semua Kota</option>').prop('disabled', true);
			return;
		}

		// Load cities from JSON
		$.getJSON(aneMapConfig.location_json_url, function(data) {
			var province = data.provinces.find(function(p) {
				return p.id === selectedProvince;
			});

			if (province && province.cities) {
				var cityOptions = '<option value="">Semua Kota</option>';
				province.cities.forEach(function(city) {
					// Only show cities that have branches
					var hasBranch = aneBranchesData.some(function(b) {
						return b.city === city.id;
					});
					if (hasBranch) {
						cityOptions += '<option value="' + city.id + '">' + city.name + '</option>';
					}
				});
				$('#ane-filter-city').html(cityOptions).prop('disabled', false);
			}
		});
	}

	/**
	 * Show branch detail modal.
	 */
	function showBranchModal(branchId) {
		var branch = aneBranchesData.find(function(b) {
			return b.id === branchId;
		});

		if (!branch) return;

		var content = '<div class="ane-branch-modal__inner">';
		content += '<h2>' + branch.title + '</h2>';
		content += '<div class="ane-branch-modal__info">';
		content += '<p><strong>Alamat:</strong><br>' + branch.address + '</p>';
		if (branch.phone) {
			content += '<p><strong>Telepon:</strong> ' + branch.phone + '</p>';
		}
		if (branch.email) {
			content += '<p><strong>Email:</strong> ' + branch.email + '</p>';
		}
		if (branch.whatsapp) {
			content += '<p><strong>WhatsApp:</strong> <a href="https://wa.me/' + branch.whatsapp + '" target="_blank">' + branch.whatsapp + '</a></p>';
		}
		if (branch.hours) {
			content += '<p><strong>Jam Operasional:</strong><br>' + branch.hours.replace(/\n/g, '<br>') + '</p>';
		}
		content += '</div>';
		content += '<div class="ane-branch-modal__actions">';
		content += '<a href="https://www.google.com/maps/dir/?api=1&destination=' + branch.lat + ',' + branch.lng + '" target="_blank" class="ane-btn ane-btn--primary">Petunjuk Arah</a>';
		content += '<a href="' + branch.permalink + '" class="ane-btn ane-btn--secondary">Halaman Detail</a>';
		content += '</div>';
		content += '</div>';

		$('#ane-branch-modal-body').html(content);
		$('#ane-branch-modal').fadeIn(300);
	}

	/**
	 * Close modal.
	 */
	function closeBranchModal() {
		$('#ane-branch-modal').fadeOut(300);
	}

	/**
	 * Initialize all functionality.
	 */
	function init() {
		// Initialize map
		if (typeof google !== 'undefined') {
			initMap();
		}

		// Initialize filtered branches with all branches
		filteredBranches = aneBranchesData;

		// Populate filters
		populateFilters();

		// Filter change events
		$('#ane-filter-province').on('change', function() {
			updateCityFilter();
			filterBranches();
		});

		$('#ane-filter-city').on('change', function() {
			filterBranches();
		});

		$('#ane-reset-filter').on('click', function() {
			$('#ane-filter-province').val('');
			$('#ane-filter-city').html('<option value="">Semua Kota</option>').prop('disabled', true);
			filterBranches();
		});

		// Load More button
		$('#ane-branch-load-more').on('click', function(e) {
			e.preventDefault();
			loadMoreBranches();
		});

		// Card detail button
		$(document).on('click', '.ane-branch-card__detail', function(e) {
			e.preventDefault();
			var branchId = $(this).data('branch-id');
			showBranchModal(branchId);
		});

		// Map info window detail link
		$(document).on('click', '.ane-map-info__detail', function(e) {
			e.preventDefault();
			var branchId = $(this).data('branch-id');
			showBranchModal(branchId);
		});

		// Modal close
		$('.ane-branch-modal__close, .ane-branch-modal__overlay').on('click', function() {
			closeBranchModal();
		});

		// Close modal on ESC key
		$(document).on('keyup', function(e) {
			if (e.key === 'Escape') {
				closeBranchModal();
			}
		});
	}

	// Initialize when Google Maps is loaded
	if (typeof google !== 'undefined') {
		$(document).ready(init);
	} else {
		// Wait for Google Maps to load
		window.initAneBranchMap = init;
	}

})(jQuery);
