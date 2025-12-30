/**
 * Linktree Click Tracking
 *
 * @package elemenane
 * @since 1.0.6
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Track custom link clicks
		$('.entry-content .ane-links').parent('a').on('click', function(e) {
			var $link = $(this);
			var linkType = 'custom';
			var linkLabel = $link.find('.ane-label').text().trim();
			var linkUrl = $link.attr('href');

			// Determine link type from classes
			if ($link.find('.ane-links').hasClass('whatsapp')) {
				linkType = 'whatsapp';
			} else if ($link.find('.ane-links').hasClass('website')) {
				linkType = 'website';
			} else if ($link.find('.ane-links').hasClass('tiktok')) {
				linkType = 'tiktok';
			} else if ($link.find('.ane-links').hasClass('telegram')) {
				linkType = 'telegram';
			}

			trackClick(linkType, linkLabel, linkUrl);
		});

		// Track social media link clicks
		$('.entry-footer .ane-sosmed a').on('click', function(e) {
			var $link = $(this);
			var $li = $link.find('li');
			var linkType = 'social';
			var linkLabel = $li.find('span').text().trim();
			var linkUrl = $link.attr('href');

			// Determine platform from li class
			if ($li.hasClass('whatsapp')) {
				linkType = 'whatsapp';
			} else if ($li.hasClass('facebook')) {
				linkType = 'facebook';
			} else if ($li.hasClass('twitter')) {
				linkType = 'twitter';
			} else if ($li.hasClass('youtube')) {
				linkType = 'youtube';
			} else if ($li.hasClass('instagram')) {
				linkType = 'instagram';
			} else if ($li.hasClass('tiktok')) {
				linkType = 'tiktok';
			} else if ($li.hasClass('telegram')) {
				linkType = 'telegram';
			} else if ($li.hasClass('threads')) {
				linkType = 'threads';
			} else if ($li.hasClass('linkedin')) {
				linkType = 'linkedin';
			}

			trackClick(linkType, linkLabel, linkUrl);
		});

		/**
		 * Track click via AJAX
		 */
		function trackClick(linkType, linkLabel, linkUrl) {
			$.ajax({
				url: aneLinktreeTracking.ajaxUrl,
				type: 'POST',
				data: {
					action: 'ane_track_linktree_click',
					nonce: aneLinktreeTracking.nonce,
					link_type: linkType,
					link_label: linkLabel,
					link_url: linkUrl
				},
				success: function(response) {
					// Silent success
				},
				error: function(xhr, status, error) {
					// Silent error - don't interrupt user experience
				}
			});
		}
	});
})(jQuery);
