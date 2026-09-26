/**
 * Owl Carousel setup. Loaded with Owl Carousel by ane_enqueue_carousel().
 *
 * @package elemenane
 */
jQuery(function ($) {
	// One item carousel
	if ($('.one-item-carousel').length) {
		$('.one-item-carousel').owlCarousel({
			loop: true,
			margin: 15,
			nav: true,
			smartSpeed: 500,
			autoplay: 5000,
			navText: ['<i class="ane-circle-left"></i>', '<i class="ane-circle-right"></i>'],
			responsive: {
				0: { items: 1 },
				600: { items: 1 },
				800: { items: 1 },
				1024: { items: 1 },
				1200: { items: 1 },
				1400: { items: 1 }
			}
		});
	}

	// Trending topics
	if ($('#home-sliding').length > 0) {
		$('#home-sliding').owlCarousel({
			nav: false,
			items: 4,
			autoplay: true,
			margin: 15,
			reponsiveClass: true,
			dots: false,
			autoplayHoverPause: true,
			loop: true,
			// Owl 2.2.1 throws "Invalid string length" for fractional items
			// with loop enabled, so small screens use whole items.
			responsive: {
				0: { items: 2 },
				480: { items: 2 },
				768: { items: 3 },
				1200: { items: 4 }
			}
		});
	}
});
