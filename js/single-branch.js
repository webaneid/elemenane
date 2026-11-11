/**
 * Single Branch Google Maps
 *
 * Displays Google Maps marker for single branch location.
 *
 * @package elemenane
 * @since 1.0.0
 */

(function($) {
	'use strict';

	/** Map pin in the theme's primary colour (Elementor-synced CSS token). */
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
	 * Initialize Google Maps for single branch.
	 */
	function initSingleBranchMap() {
		var mapElement = document.getElementById('ane-single-branch-map');

		if (!mapElement || typeof google === 'undefined' || !aneSingleBranchMap) {
			return;
		}

		var lat = aneSingleBranchMap.lat;
		var lng = aneSingleBranchMap.lng;
		var title = aneSingleBranchMap.title;

		// Initialize map.
		var map = new google.maps.Map(mapElement, {
			zoom: 15,
			center: {lat: lat, lng: lng},
			styles: [
				{
					featureType: 'poi',
					elementType: 'labels',
					stylers: [{visibility: 'off'}]
				}
			]
		});

		// Add marker.
		var marker = new google.maps.Marker({
			position: {lat: lat, lng: lng},
			map: map,
			title: title,
			animation: google.maps.Animation.DROP,
			icon: pinIcon()
		});

		// Add info window.
		var infoWindow = new google.maps.InfoWindow({
			content: '<div class="ane-map-info"><h4>' + $('<div>').text(title).html() + '</h4></div>'
		});

		// Show info window on marker click.
		marker.addListener('click', function() {
			infoWindow.open(map, marker);
		});

		// Auto-open info window.
		infoWindow.open(map, marker);
	}

	// Initialize when Google Maps is loaded.
	if (typeof google !== 'undefined') {
		$(document).ready(initSingleBranchMap);
	} else {
		// Wait for Google Maps to load.
		window.initAneSingleBranchMap = initSingleBranchMap;
	}

})(jQuery);
