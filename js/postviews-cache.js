/**
 * Post Views Cache - counts a view via AJAX so cached pages still count.
 *
 * @package elemenane
 * @since 1.0.0
 */
(function () {
	'use strict';

	if (typeof postViewsCache === 'undefined') {
		return;
	}

	var params = new URLSearchParams({
		postviews_id: postViewsCache.post_id,
		action: 'postviews',
		nonce: postViewsCache.nonce
	});

	fetch(postViewsCache.admin_ajax_url + '?' + params.toString(), { credentials: 'same-origin', cache: 'no-store' })
		.then(function (response) {
			return response.json();
		})
		.then(function (response) {
			if (response.success && window.console) {
				console.log('Post views updated:', response.data);
			}
		})
		.catch(function (error) {
			if (window.console) {
				console.warn('Post views tracking failed:', error);
			}
		});
})();
