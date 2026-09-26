/**
 * Image lightbox for links to images in post content (.gallery-popup, added by
 * ane_popup_gallery()). Loaded with Magnific Popup by ane_enqueue_lightbox().
 *
 * @package elemenane
 */
jQuery(function ($) {
	if ($('.gallery-popup').length > 0) {
		$('.gallery-popup').magnificPopup({
			type: 'image',
			image: {
				markup: '<div class="mfp-figure">' +
					'<div class="mfp-close"></div>' +
					'<div class="mfp-img"></div>' +
					'<div class="mfp-bottom-bar">' +
					'<div class="mfp-title"></div>' +
					'<div class="mfp-description" style="text-align: left;font-size: 12px;line-height: 16px;color: #f3f3f3;word-break: break-word;padding-right: 36px;"></div>' +
					'<div class="mfp-counter"></div>' +
					'</div>' +
					'</div>',
				titleSrc: function (item) {
					// alt text is plain text; escape it before it becomes HTML.
					return '<strong>' + $('<div>').text(item.el.find('img').attr('alt') || '').html() + '</strong>';
				}
			},
			gallery: { enabled: true },
			mainClass: 'mfp-with-zoom',
			zoom: {
				enabled: true,
				duration: 300,
				easing: 'ease-in-out',
				opener: function (openerElement) {
					return openerElement.is('img') ? openerElement : openerElement.find('img');
				}
			}
		});
	}
});
