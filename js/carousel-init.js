/**
 * Owl Carousel setup. Loaded with Owl Carousel by ane_enqueue_carousel().
 *
 * @package elemenane
 */
// Inline SVG chevrons (same glyphs as elemenane_icon( 'chevron-left' / 'chevron-right' )).
var ICON_PREV = '<svg class="ane-icon ane-icon--chevron-left" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false"><path d="M761 1000 810 950Q822 939 828 924Q835 909 835 892Q835 892 835 892Q835 891 835 891Q835 890 835 890Q835 889 835 888Q835 872 828 857Q822 842 810 831L490 511L810 191Q820 181 826 167Q832 153 832 137Q832 136 832 135Q832 134 832 133Q832 133 832 132Q832 132 832 132Q832 115 826 100Q819 85 808 74L761 25Q750 14 736 7Q721 0 704 0Q703 0 702 0Q700 0 698 0Q681 0 666 6Q651 13 640 25L214 453Q204 464 198 478Q192 492 192 507Q192 509 192 510Q192 511 192 512Q192 512 192 512Q192 513 192 513Q192 530 198 545Q205 560 217 571V572L640 1000Q651 1011 666 1018Q681 1024 698 1024Q700 1024 702 1024Q703 1024 704 1024Q721 1024 736 1018Q750 1011 761 1000Z"/></svg>';
var ICON_NEXT = '<svg class="ane-icon ane-icon--chevron-right" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false"><path d="M263 24 214 74Q204 85 198 99Q192 113 192 130Q192 130 192 131Q192 132 192 133Q192 134 192 135Q192 136 192 138Q192 153 198 167Q204 181 214 192L534 512L214 832Q202 843 196 858Q189 872 189 889Q189 890 189 890Q189 891 189 892Q189 892 189 892Q189 892 189 893Q189 910 196 925Q202 940 214 951L263 1000Q274 1011 288 1018Q303 1024 320 1024Q321 1024 322 1024Q324 1024 326 1024Q343 1024 358 1018Q373 1011 384 999L812 571Q821 561 826 548Q832 534 832 518Q832 517 832 515Q832 513 832 512Q832 512 832 512Q832 511 832 511Q832 494 826 479Q819 464 807 453V452L384 24Q373 13 358 6Q343 0 326 0Q324 0 322 0Q321 0 320 0Q303 0 288 6Q274 13 263 24Z"/></svg>';

// Give Owl's nav arrows and dots accessible names (strings come from PHP for i18n).
function aneCarouselA11y(event) {
	var l10n = window.elemenaneCarousel || {};
	var $el = jQuery(event.target);
	$el.find('.owl-prev').attr({ role: 'button', tabindex: 0, 'aria-label': l10n.prev || 'Previous slide' });
	$el.find('.owl-next').attr({ role: 'button', tabindex: 0, 'aria-label': l10n.next || 'Next slide' });
	$el.find('.owl-dot').each(function (i) {
		jQuery(this).attr({ role: 'button', tabindex: 0, 'aria-label': (l10n.goto || 'Go to slide %d').replace('%d', i + 1) });
	});
}

jQuery(function ($) {
	// One item carousel
	if ($('.one-item-carousel').length) {
		$('.one-item-carousel').owlCarousel({
			loop: true,
			margin: 15,
			nav: true,
			smartSpeed: 500,
			autoplay: 5000,
			navText: [ICON_PREV, ICON_NEXT],
			onInitialized: aneCarouselA11y,
			onRefreshed: aneCarouselA11y,
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
