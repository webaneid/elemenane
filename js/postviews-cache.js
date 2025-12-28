/**
 * Post Views Cache - AJAX handler for cached pages
 *
 * @package elemenane
 * @since 1.0.0
 */
(function($) {
	'use strict';

	$(document).ready(function() {
		// Verify required data exists
		if ( typeof postViewsCache === 'undefined' ) {
			return;
		}

		// Send AJAX request to increment post views
		$.ajax({
			type: 'GET',
			url: postViewsCache.admin_ajax_url,
			data: {
				postviews_id: postViewsCache.post_id,
				action: 'postviews',
				nonce: postViewsCache.nonce
			},
			cache: false,
			success: function(response) {
				// Optional: Handle success response
				if ( response.success && window.console ) {
					console.log('Post views updated:', response.data);
				}
			},
			error: function(xhr, status, error) {
				// Optional: Handle error
				if ( window.console ) {
					console.warn('Post views tracking failed:', error);
				}
			}
		});
	});

})(jQuery);