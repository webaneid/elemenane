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
			icon: {
				url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
			}
		});

		// Add info window.
		var infoWindow = new google.maps.InfoWindow({
			content: '<div class="ane-map-info"><h4>' + title + '</h4></div>'
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
