/**
 * Linktree Click Tracking
 *
 * The server only accepts links shown on the page and derives the link type
 * and label itself (see ane_track_linktree_click()), so only the URL is sent.
 *
 * @package elemenane
 * @since 1.0.6
 */
(function () {
	'use strict';

	if (typeof aneLinktreeTracking === 'undefined') {
		return;
	}

	function trackClick(linkUrl) {
		var body = new URLSearchParams({
			action: 'ane_track_linktree_click',
			nonce: aneLinktreeTracking.nonce,
			page_id: aneLinktreeTracking.pageId,
			link_url: linkUrl
		});

		// keepalive lets the request finish even if the browser leaves the page.
		fetch(aneLinktreeTracking.ajaxUrl, {
			method: 'POST',
			body: body,
			credentials: 'same-origin',
			keepalive: true
		}).catch(function () {});
	}

	document.addEventListener('click', function (e) {
		var link = e.target.closest('a');
		if (!link) {
			return;
		}

		// Custom links and social media links.
		var isCustom = link.querySelector('.ane-links') && link.closest('.entry-content');
		var isSocial = link.closest('.entry-footer .ane-sosmed');

		if (isCustom || isSocial) {
			trackClick(link.getAttribute('href'));
		}
	});
})();
