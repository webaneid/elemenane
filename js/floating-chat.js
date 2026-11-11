/**
 * Floating WhatsApp chat (tp/floating-chat.php). Vanilla JS.
 *
 * - FAB toggles a panel (aria-expanded, Esc to close, focus returned to the FAB).
 * - Form sends the typed message to the next admin number (round-robin, remembered in localStorage).
 * - Label next to the FAB collapses after a few seconds.
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var l10n = window.elemenaneChat || {};
	var STORE_KEY = 'waLastIndex';

	function init() {
		var root = document.querySelector('[data-ane-chat]');
		if (!root) {
			return;
		}
		var fab = root.querySelector('[data-chat-toggle]');
		var panel = root.querySelector('.ane-chat__panel');
		var closeBtn = root.querySelector('[data-chat-close]');
		var form = root.querySelector('[data-chat-form]');
		var input = root.querySelector('.ane-chat__input');
		var error = root.querySelector('[data-chat-error]');
		var numbers = [];
		var hideTimer = null;

		try {
			numbers = JSON.parse(root.getAttribute('data-numbers') || '[]');
		} catch (e) {
			numbers = [];
		}
		if (!fab || !panel) {
			return;
		}

		// Collapse the label after the entrance so it does not cover content.
		setTimeout(function () { root.classList.add('is-compact'); }, 9000);

		function showError(message) {
			if (!error) {
				return;
			}
			error.textContent = message;
			error.hidden = false;
		}
		function clearError() {
			if (error) {
				error.hidden = true;
				error.textContent = '';
			}
		}

		function open() {
			clearTimeout(hideTimer);
			panel.hidden = false;
			// Next frame so the transition runs.
			window.requestAnimationFrame(function () {
				root.classList.add('is-open');
			});
			fab.setAttribute('aria-expanded', 'true');
			if (input && window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
				setTimeout(function () { input.focus(); }, 220);
			}
		}

		function close(returnFocus) {
			root.classList.remove('is-open');
			fab.setAttribute('aria-expanded', 'false');
			clearError();
			hideTimer = setTimeout(function () { panel.hidden = true; }, 220);
			if (returnFocus && panel.contains(document.activeElement)) {
				fab.focus();
			}
		}

		fab.addEventListener('click', function () {
			if (root.classList.contains('is-open')) {
				close(true);
			} else {
				open();
			}
		});
		if (closeBtn) {
			closeBtn.addEventListener('click', function () { close(true); });
		}
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && root.classList.contains('is-open')) {
				close(true);
			}
		});

		if (input) {
			input.addEventListener('input', clearError);
		}

		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var message = input ? input.value.trim() : '';
				if (message === '') {
					showError(l10n.empty || 'Please type a message first.');
					if (input) { input.focus(); }
					return;
				}
				if (!numbers.length) {
					showError(l10n.noNumber || 'No WhatsApp number available.');
					return;
				}

				var index = 0;
				try {
					index = parseInt(window.localStorage.getItem(STORE_KEY) || '0', 10);
				} catch (err) {
					index = 0;
				}
				if (isNaN(index) || index < 0) {
					index = 0;
				}
				var phone = numbers[index % numbers.length];
				try {
					window.localStorage.setItem(STORE_KEY, String((index + 1) % numbers.length));
				} catch (err2) {
					// Storage unavailable (private mode): the next message simply starts from the first admin.
				}

				window.open('https://wa.me/' + encodeURIComponent(phone) + '?text=' + encodeURIComponent(message), '_blank', 'noopener,noreferrer');
				if (input) { input.value = ''; }
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
