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
	var cfg = window.aneMapConfig || {};
	var icons = cfg.icons || {};
	var t = cfg.i18n || {};
	var PAGE_SIZE = cfg.pageSize || 12;
	var lastFocus = null;

	function icon(name) {
		return icons[name] || '';
	}

	/** Map pin in the theme's primary colour (read from the Elementor-synced CSS token). */
	function pinIcon() {
		var primary = (getComputedStyle(document.documentElement).getPropertyValue('--ane-warna-utama') || '').trim() || '#014C78';
		var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 32 42"><path fill="' + primary + '" stroke="#ffffff" stroke-width="2" d="M16 1C8 1 2 7 2 15c0 10 14 25 14 25s14-15 14-25C30 7 24 1 16 1z"/><circle cx="16" cy="15" r="5.5" fill="#ffffff"/></svg>';
		return {
			url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
			scaledSize: new google.maps.Size(32, 42),
			anchor: new google.maps.Point(16, 40)
		};
	}

	/**
	 * Initialize Google Maps with all branch markers.
	 */
	function initMap() {
		if (typeof google === 'undefined' || !aneBranchesData || aneBranchesData.length === 0) {
			$('#ane-branch-map').html('<p class="ane-branch-map__empty">' + escapeHtml(t.mapFailed || 'Map unavailable.') + '</p>');
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
			icon: pinIcon()
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
		content += '<h4>' + escapeHtml(branch.title) + '</h4>';
		content += '<p>' + escapeHtml(branch.address) + '</p>';
		if (branch.phone) {
			content += '<p><strong>' + escapeHtml(t.phone || 'Phone') + ':</strong> ' + escapeHtml(branch.phone) + '</p>';
		}
		content += '<a href="#" class="ane-map-info__detail" data-branch-id="' + escapeHtml(branch.id) + '">' + escapeHtml(t.seeDetail || 'Details') + '</a>';
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
		$('.ane-branch-grid__empty-filter').remove();

		if (branches.length === 0) {
			$list.append('<p class="ane-branch-grid__empty-filter">' + escapeHtml(t.noneInArea || 'No branches found.') + '</p>');
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
		var hours = branch.hours ? String(branch.hours).split('\n')[0].trim() : '';
		var wa = branch.whatsapp ? String(branch.whatsapp).replace(/\D/g, '') : '';
		var directions = 'https://www.google.com/maps/dir/?api=1&destination=' + Number(branch.lat) + ',' + Number(branch.lng);

		// Keep identical to tp/branch-card.php.
		var card = '<article class="ane-branch-card" data-branch-id="' + escapeHtml(branch.id) + '" data-province="' + escapeHtml(branch.province) + '" data-city="' + escapeHtml(branch.city) + '">';

		card += '<div class="ane-branch-card__media">';
		if (branch.thumbnail) {
			card += '<img src="' + escapeHtml(branch.thumbnail) + '" alt="" loading="lazy" decoding="async">';
		} else {
			card += '<span class="ane-branch-card__placeholder" aria-hidden="true">' + icon('lokasi') + '</span>';
		}
		if (branch.city_name) {
			card += '<span class="ane-branch-card__city">' + escapeHtml(branch.city_name) + '</span>';
		}
		card += '</div>';

		card += '<div class="ane-branch-card__body">';
		card += '<h3 class="ane-branch-card__title"><a href="' + escapeHtml(branch.permalink) + '">' + escapeHtml(branch.title) + '</a></h3>';
		card += '<ul class="ane-branch-card__meta">';
		if (branch.address) {
			card += '<li class="ane-branch-card__address">' + icon('lokasi') + '<span>' + escapeHtml(branch.address) + '</span></li>';
		}
		if (branch.phone) {
			card += '<li class="ane-branch-card__phone">' + icon('telepon') + '<span>' + escapeHtml(branch.phone) + '</span></li>';
		}
		if (hours) {
			card += '<li class="ane-branch-card__hours">' + icon('jam') + '<span>' + escapeHtml(hours) + '</span></li>';
		}
		card += '</ul>';

		card += '<div class="ane-branch-card__actions">';
		card += '<a href="#" class="ane-branch-card__detail" data-branch-id="' + escapeHtml(branch.id) + '">' + escapeHtml(t.detail || 'Details') + '</a>';
		card += '<a href="' + directions + '" target="_blank" rel="noopener" class="ane-branch-card__directions">' + escapeHtml(t.directions || 'Directions') + '</a>';
		if (wa) {
			card += '<a href="https://wa.me/' + wa + '" target="_blank" rel="noopener" class="ane-branch-card__wa" aria-label="' + escapeHtml(t.whatsapp || 'WhatsApp') + '">' + icon('whatsapp') + '</a>';
		}
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
		var nextBatch = filteredBranches.slice(currentlyLoaded, currentlyLoaded + PAGE_SIZE);

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
			$('#ane-filter-city').html('<option value="">' + escapeHtml(t.allCities || 'All cities') + '</option>').prop('disabled', true);
			return;
		}

		// Load cities from JSON
		$.getJSON(aneMapConfig.location_json_url, function(data) {
			var province = data.provinces.find(function(p) {
				return p.id === selectedProvince;
			});

			if (province && province.cities) {
				var cityOptions = '<option value="">' + escapeHtml(t.allCities || 'All cities') + '</option>';
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

		var content = '<div class="ane-branch-dialog__inner">';
		content += '<h2 id="ane-branch-modal-title">' + escapeHtml(branch.title) + '</h2>';
		content += '<ul class="ane-branch-dialog__info">';
		content += '<li>' + icon('lokasi') + '<div><strong>' + escapeHtml(t.address || 'Address') + '</strong><span>' + escapeHtml(branch.address) + '</span></div></li>';
		if (branch.phone) {
			content += '<li>' + icon('telepon') + '<div><strong>' + escapeHtml(t.phone || 'Phone') + '</strong><span>' + escapeHtml(branch.phone) + '</span></div></li>';
		}
		if (branch.email) {
			content += '<li>' + icon('email') + '<div><strong>' + escapeHtml(t.email || 'Email') + '</strong><span>' + escapeHtml(branch.email) + '</span></div></li>';
		}
		if (branch.whatsapp) {
			// wa.me only accepts digits.
			var waNumber = String(branch.whatsapp).replace(/\D/g, '');
			content += '<li>' + icon('whatsapp') + '<div><strong>WhatsApp</strong><span><a href="https://wa.me/' + waNumber + '" target="_blank" rel="noopener">' + escapeHtml(branch.whatsapp) + '</a></span></div></li>';
		}
		if (branch.hours) {
			content += '<li>' + icon('jam') + '<div><strong>' + escapeHtml(t.hours || 'Hours') + '</strong><span>' + escapeHtml(branch.hours).replace(/\n/g, '<br>') + '</span></div></li>';
		}
		content += '</ul>';
		content += '<div class="ane-branch-dialog__actions">';
		content += '<a href="https://www.google.com/maps/dir/?api=1&destination=' + Number(branch.lat) + ',' + Number(branch.lng) + '" target="_blank" rel="noopener" class="ane-btn ane-btn--primary">' + escapeHtml(t.directions || 'Directions') + '</a>';
		content += '<a href="' + escapeHtml(branch.permalink) + '" class="ane-btn ane-btn--secondary">' + escapeHtml(t.detailPage || 'Detail page') + '</a>';
		content += '</div>';
		content += '</div>';

		lastFocus = document.activeElement;
		$('#ane-branch-modal-body').html(content);
		var modal = document.getElementById('ane-branch-modal');
		modal.hidden = false;
		window.requestAnimationFrame(function () {
			modal.classList.add('is-open');
			$(modal).find('.ane-branch-dialog__close').trigger('focus');
		});
		$('body').addClass('ane-modal-open');
	}

	/**
	 * Close modal.
	 */
	function closeBranchModal() {
		var modal = document.getElementById('ane-branch-modal');
		if (!modal || modal.hidden) {
			return;
		}
		modal.classList.remove('is-open');
		$('body').removeClass('ane-modal-open');
		setTimeout(function () { modal.hidden = true; }, 200);
		if (lastFocus && lastFocus.focus) {
			lastFocus.focus();
		}
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
			$('#ane-filter-city').html('<option value="">' + escapeHtml(t.allCities || 'All cities') + '</option>').prop('disabled', true);
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
		$('.ane-branch-dialog__close, .ane-branch-dialog__overlay').on('click', function() {
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
