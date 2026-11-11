/**
 * Share bar (inc/sosmed.php -> ane_share_this_post()). Vanilla JS.
 *
 * - Copy button: puts "Title \n\n Description \n\n URL" on the clipboard (data-copy) and confirms inline.
 * - Native share: shown only where navigator.share exists (mostly phones); shares title, description and URL.
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var l10n = window.elemenaneShare || {};

	function copyText(text) {
		if (navigator.clipboard && window.isSecureContext) {
			return navigator.clipboard.writeText(text);
		}
		// Fallback for http:// and old browsers.
		return new Promise(function (resolve, reject) {
			var area = document.createElement('textarea');
			area.value = text;
			area.setAttribute('readonly', '');
			area.style.cssText = 'position:fixed;top:0;left:0;opacity:0;';
			document.body.appendChild(area);
			area.select();
			try {
				if (document.execCommand('copy')) {
					resolve();
				} else {
					reject(new Error('copy failed'));
				}
			} catch (e) {
				reject(e);
			} finally {
				document.body.removeChild(area);
			}
		});
	}

	function init() {
		var bars = document.querySelectorAll('[data-ane-share]');
		for (var i = 0; i < bars.length; i++) {
			setup(bars[i]);
		}
	}

	function setup(bar) {
		var copyBtn = bar.querySelector('[data-share-copy]');
		var nativeBtn = bar.querySelector('[data-share-native]');
		var toast = bar.querySelector('.ane-share__toast');
		var timer = null;

		function say(message, ok) {
			if (!toast) {
				return;
			}
			toast.textContent = message;
			toast.classList.toggle('is-error', !ok);
			toast.classList.add('is-visible');
			clearTimeout(timer);
			timer = setTimeout(function () {
				toast.classList.remove('is-visible');
				if (copyBtn) { copyBtn.classList.remove('is-done'); }
			}, 3200);
		}

		if (copyBtn) {
			copyBtn.addEventListener('click', function () {
				copyText(bar.getAttribute('data-copy') || bar.getAttribute('data-url') || '').then(function () {
					copyBtn.classList.add('is-done');
					say(l10n.copied || 'Copied', true);
				}, function () {
					say(l10n.failed || 'Could not copy', false);
				});
			});
		}

		if (nativeBtn) {
			if (navigator.share) {
				bar.classList.add('has-native');
				nativeBtn.addEventListener('click', function () {
					navigator.share({
						title: bar.getAttribute('data-title') || document.title,
						text: bar.getAttribute('data-text') || '',
						url: bar.getAttribute('data-url') || window.location.href
					}).catch(function () {
						// Dismissed by the user: nothing to do.
					});
				});
			}
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
