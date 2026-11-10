/**
 * Theme front-end scripts (no jQuery).
 *
 * Carousel and lightbox setup live in carousel-init.js / lightbox-init.js and
 * load only with their libraries.
 *
 * @package elemenane
 */
(function () {
	'use strict';

	/**
	 * Slide an element open or closed, like jQuery's slideDown()/slideUp().
	 *
	 * @param {HTMLElement} el       Element.
	 * @param {boolean}     open     True to open, false to close.
	 * @param {number}      duration Milliseconds.
	 */
	function slide(el, open, duration) {
		if (!el) {
			return;
		}
		if (el._aneSlide) {
			el._aneSlide.cancel();
		}

		var isHidden = getComputedStyle(el).display === 'none';
		if (open === !isHidden && !el._aneSlide) {
			return;
		}

		if (open) {
			el.style.display = '';
			if (getComputedStyle(el).display === 'none') {
				el.style.display = 'block';
			}
		}

		// Jump straight to the end state when animating isn't possible or wanted
		// (hidden tab, no Web Animations support, or reduced motion preference).
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (document.hidden || typeof el.animate !== 'function' || reduceMotion) {
			if (!open) {
				el.style.display = 'none';
			}
			return;
		}

		var cs = getComputedStyle(el);
		var full = {
			height: el.scrollHeight - parseFloat(cs.paddingTop) - parseFloat(cs.paddingBottom) + 'px',
			paddingTop: cs.paddingTop,
			paddingBottom: cs.paddingBottom,
			marginTop: cs.marginTop,
			marginBottom: cs.marginBottom
		};
		var zero = { height: '0px', paddingTop: '0px', paddingBottom: '0px', marginTop: '0px', marginBottom: '0px' };

		var overflow = el.style.overflow;
		el.style.overflow = 'hidden';

		var anim = el.animate(open ? [zero, full] : [full, zero], { duration: duration, easing: 'ease-in-out' });
		el._aneSlide = anim;

		anim.onfinish = function () {
			el._aneSlide = null;
			el.style.overflow = overflow;
			if (!open) {
				el.style.display = 'none';
			}
		};
		anim.oncancel = function () {
			el._aneSlide = null;
			el.style.overflow = overflow;
		};
	}

	function init() {
		// Comment toggle
		document.querySelectorAll('.commrnt-toggle a').forEach(function (link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();
				document.querySelectorAll('.blog-comment-content-wrap').forEach(function (wrap) {
					slide(wrap, getComputedStyle(wrap).display === 'none', 1000);
				});
			});
		});

		// Floating chat
		var floatingChat = document.querySelector('.floating-chat');
		if (floatingChat) {
			var chatBox = floatingChat.querySelector('.chat');
			var closeButton = floatingChat.querySelector('.ane-tutupin');

			// Show the chat button after 1 second.
			setTimeout(function () {
				floatingChat.classList.add('enter');
			}, 1000);

			floatingChat.addEventListener('click', function () {
				floatingChat.classList.add('expand');
				if (chatBox) {
					chatBox.classList.add('enter');
				}
			});

			if (closeButton) {
				closeButton.addEventListener('click', function (event) {
					event.stopPropagation();
					floatingChat.classList.remove('expand');
					if (chatBox) {
						chatBox.classList.remove('enter');
					}
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
