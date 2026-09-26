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

	/**
	 * Make an element visible the way jQuery's show() does.
	 *
	 * @param {HTMLElement} el Element.
	 * @param {string}      fallbackDisplay Display value if CSS keeps it hidden.
	 */
	function show(el, fallbackDisplay) {
		el.style.display = '';
		if (getComputedStyle(el).display === 'none') {
			el.style.display = fallbackDisplay;
		}
	}

	/**
	 * Mobile menu. Same markup and behaviour as jQuery meanMenu 2.0.8, which the
	 * header styles are written for.
	 *
	 * @param {Object} opts Options.
	 */
	function mobileMenu(opts) {
		var menu = document.querySelector(opts.target);
		if (!menu) {
			return;
		}

		var screenWidth = opts.screenWidth;
		var containerSelector = opts.container;
		var closeText = 'X';
		var closeSize = '18px';
		var openMarkup = '<span></span><span></span><span></span>';
		var expandText = '+';
		var contractText = '-';

		var isMobile = /iPhone|iPod|iPad|Android|Blackberry|Windows Phone/i.test(navigator.userAgent);
		var menuOn = false;
		var menuExist = false;
		var reveal = null;

		function currentWidth() {
			return window.innerWidth || document.documentElement.clientWidth;
		}

		function revealInner() {
			reveal.innerHTML = reveal.classList.contains('meanclose') ? closeText : openMarkup;
		}

		// Restore the original navigation.
		function original() {
			document.querySelectorAll('.mean-bar, .mean-push').forEach(function (el) {
				el.remove();
			});
			document.querySelectorAll(containerSelector).forEach(function (el) {
				el.classList.remove('mean-container');
			});
			menu.style.display = 'block';
			menuOn = false;
			menuExist = false;
		}

		function build() {
			if (currentWidth() > screenWidth) {
				original();
				return;
			}

			menuExist = true;
			document.querySelectorAll(containerSelector).forEach(function (el) {
				el.classList.add('mean-container');
			});

			document.querySelectorAll('.mean-container').forEach(function (container) {
				container.insertAdjacentHTML('afterbegin', '<div class="mean-bar"><a href="#nav" class="meanmenu-reveal" style="background:;color:;">Show Navigation</a><nav class="mean-nav"></nav></div>');
			});

			// Copy the menu into the mobile nav.
			document.querySelectorAll('.mean-nav').forEach(function (nav) {
				nav.innerHTML = menu.innerHTML;
			});

			var push = document.createElement('div');
			push.className = 'mean-push';
			menu.parentNode.insertBefore(push, menu);

			menu.style.display = 'none';
			reveal = document.querySelector('.meanmenu-reveal');
			show(reveal, 'inline');
			reveal.innerHTML = openMarkup;

			document.querySelectorAll('.mean-nav ul').forEach(function (ul) {
				ul.style.display = 'none';
			});

			// Expand/collapse buttons for sub menus.
			document.querySelectorAll('.mean-nav ul ul').forEach(function (sub) {
				if (!sub.children.length) {
					return;
				}
				sub.parentNode.insertAdjacentHTML('beforeend', '<a class="mean-expand" href="#" style="font-size: ' + closeSize + '">' + expandText + '</a>');
			});
			document.querySelectorAll('.mean-expand').forEach(function (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					var sub = btn.previousElementSibling && btn.previousElementSibling.matches('ul') ? btn.previousElementSibling : null;
					var clicked = btn.classList.contains('mean-clicked');
					btn.textContent = clicked ? expandText : contractText;
					slide(sub, !clicked, 300);
					btn.classList.toggle('mean-clicked');
				});
			});

			var items = document.querySelectorAll('.mean-nav ul li');
			if (items.length) {
				items[items.length - 1].classList.add('mean-last');
			}

			reveal.classList.remove('meanclose');
			reveal.addEventListener('click', function (e) {
				e.preventDefault();
				var firstUl = document.querySelector('.mean-nav ul');
				if (!menuOn) {
					reveal.style.textAlign = 'center';
					reveal.style.textIndent = '0';
					reveal.style.fontSize = closeSize;
					slide(firstUl, true, 400);
					menuOn = true;
				} else {
					slide(firstUl, false, 400);
					menuOn = false;
				}
				reveal.classList.toggle('meanclose');
				revealInner();
			});
		}

		window.addEventListener('resize', function () {
			if (!isMobile) {
				original();
				if (currentWidth() <= screenWidth) {
					build();
				}
			} else if (currentWidth() <= screenWidth) {
				if (!menuExist) {
					build();
				}
			} else {
				original();
			}
		});

		build();
	}

	function init() {
		// Search bar
		document.body.addEventListener('click', function (e) {
			var button = e.target.closest('.search-button');
			var back = e.target.closest('.search-back');
			var bar = document.querySelector('.search-bar');

			if (button) {
				e.preventDefault();
				button.parentElement.classList.toggle('open');
				if (bar) {
					bar.style.maxWidth = '100%';
					bar.style.opacity = '1';
					var input = bar.querySelector('input');
					if (input) {
						input.focus();
					}
				}
			} else if (back) {
				e.preventDefault();
				if (bar) {
					bar.style.maxWidth = '0px';
					bar.style.opacity = '0';
				}
			}
		});

		// Search box
		document.addEventListener('click', function (e) {
			var box = document.querySelector('.search-box');
			if (e.target.closest('#search-icon')) {
				e.preventDefault();
				if (box) {
					box.classList.add('active');
				}
			} else if (e.target.closest('#serach-close-btn')) {
				e.preventDefault();
				if (box) {
					box.classList.remove('active');
				}
			}
		});

		mobileMenu({
			target: '#mobile-menu-active',
			screenWidth: 991,
			container: '.mobile-menu-area .mobile-menu'
		});

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
