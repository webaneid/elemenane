/**
 * Default header behaviour (tp/header-default.php). Vanilla JS.
 *
 * - Off-canvas drawer below 992px (focus trap, ESC, backdrop, scroll lock)
 * - Accessible sub-menu toggles (buttons with aria-expanded)
 * - Search dialog (<dialog>)
 * - Sticky header: compact after scrolling, hides on scroll down on mobile
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var header = document.querySelector('[data-ane-header]');
	if (!header) {
		return;
	}

	var root = document.documentElement;
	var panel = header.querySelector('#ane-header-panel');
	var toggle = header.querySelector('[data-ane-menu-toggle]');
	var backdrop = header.querySelector('[data-ane-backdrop]');
	var desktop = window.matchMedia('(min-width: 992px)');
	var l10n = window.elemenaneHeader || {};
	var lastFocus = null;

	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])';

	/* ---- Drawer --------------------------------------------------------- */

	function isOpen() {
		return header.classList.contains('is-open');
	}

	// Keep the closed drawer out of the tab order / accessibility tree on mobile.
	function syncInert() {
		if (!panel) {
			return;
		}
		var hidden = !desktop.matches && !isOpen();
		if ('inert' in panel) {
			panel.inert = hidden;
		}
		panel.setAttribute('aria-hidden', hidden ? 'true' : 'false');
	}

	function setOpen(open) {
		if (!panel || !toggle || open === isOpen()) {
			return;
		}
		header.classList.toggle('is-open', open);
		root.classList.toggle('ane-lock', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		syncInert();

		if (open) {
			lastFocus = document.activeElement;
			var first = panel.querySelector('[data-ane-menu-close]') || panel.querySelector(FOCUSABLE);
			if (first) {
				first.focus();
			}
		} else if (lastFocus && typeof lastFocus.focus === 'function') {
			lastFocus.focus();
		}
	}

	if (toggle) {
		toggle.addEventListener('click', function () {
			setOpen(!isOpen());
		});
	}
	header.querySelectorAll('[data-ane-menu-close]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			setOpen(false);
		});
	});
	if (backdrop) {
		backdrop.addEventListener('click', function () {
			setOpen(false);
		});
	}

	// Leaving the mobile layout closes the drawer.
	function onBreakpoint() {
		if (desktop.matches) {
			setOpen(false);
			closeSubmenus(null, false); // drop drawer accordion state so no dropdown stays open
		}
		syncInert();
	}
	if (desktop.addEventListener) {
		desktop.addEventListener('change', onBreakpoint);
	} else if (desktop.addListener) {
		desktop.addListener(onBreakpoint);
	}
	syncInert();

	// Keep Tab inside the open drawer.
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			if (isOpen()) {
				setOpen(false);
			}
			closeSubmenus(null, true);
			return;
		}
		if (e.key !== 'Tab' || !isOpen() || !panel) {
			return;
		}
		var items = Array.prototype.filter.call(panel.querySelectorAll(FOCUSABLE), function (el) {
			return el.offsetParent !== null;
		});
		if (toggle) {
			items.push(toggle);
		}
		if (!items.length) {
			return;
		}
		var first = items[0];
		var last = items[items.length - 1];
		if (e.shiftKey && document.activeElement === first) {
			e.preventDefault();
			last.focus();
		} else if (!e.shiftKey && document.activeElement === last) {
			e.preventDefault();
			first.focus();
		}
	});

	// Following a link in the drawer closes it (same-page anchors).
	if (panel) {
		panel.addEventListener('click', function (e) {
			var link = e.target.closest('a[href]');
			if (link && isOpen() && !link.parentElement.classList.contains('menu-item-has-children')) {
				setOpen(false);
			}
		});
	}

	/* ---- Sub-menus ------------------------------------------------------- */

	function closeSubmenus(except, returnFocus) {
		header.querySelectorAll('.menu-item-has-children.is-open').forEach(function (li) {
			if (except && li.contains(except)) {
				return;
			}
			li.classList.remove('is-open');
			var btn = li.querySelector(':scope > .ane-header__subtoggle');
			if (btn) {
				btn.setAttribute('aria-expanded', 'false');
				if (returnFocus && li.contains(document.activeElement)) {
					btn.focus();
				}
			}
		});
	}

	header.querySelectorAll('.ane-header__menu .menu-item-has-children, .ane-header__menu .page_item_has_children').forEach(function (li) {
		li.classList.add('menu-item-has-children');
		var link = li.querySelector(':scope > a');
		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'ane-header__subtoggle';
		btn.setAttribute('aria-expanded', 'false');
		btn.setAttribute('aria-label', (l10n.submenu || 'Toggle submenu') + (link ? ': ' + link.textContent.trim() : ''));
		btn.innerHTML = '<span aria-hidden="true"></span>';
		if (link) {
			link.insertAdjacentElement('afterend', btn);
		}
		btn.addEventListener('click', function () {
			var open = !li.classList.contains('is-open');
			// Siblings close, like an accordion / single open dropdown.
			li.parentElement.querySelectorAll(':scope > .is-open').forEach(function (sib) {
				if (sib !== li) {
					sib.classList.remove('is-open');
					var sb = sib.querySelector(':scope > .ane-header__subtoggle');
					if (sb) {
						sb.setAttribute('aria-expanded', 'false');
					}
				}
			});
			li.classList.toggle('is-open', open);
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		// Open the branch of the current page inside the drawer (mobile layout only).
		if (!desktop.matches && (li.classList.contains('current-menu-ancestor') || li.classList.contains('current-menu-parent'))) {
			li.classList.add('is-open');
			btn.setAttribute('aria-expanded', 'true');
		}
	});

	// Click outside closes desktop dropdowns.
	document.addEventListener('click', function (e) {
		if (desktop.matches && !e.target.closest('.ane-header__menu')) {
			closeSubmenus(null, false);
		}
	});

	/* ---- Search dialog ---------------------------------------------------- */

	var dialog = document.getElementById('ane-search');
	if (dialog) {
		var openDialog = function () {
			setOpen(false);
			if (typeof dialog.showModal === 'function') {
				if (!dialog.open) {
					dialog.showModal();
				}
			} else {
				dialog.setAttribute('open', '');
			}
			var input = dialog.querySelector('input[type="search"]');
			if (input) {
				input.focus();
			}
		};
		var closeDialog = function () {
			if (typeof dialog.close === 'function') {
				dialog.close();
			} else {
				dialog.removeAttribute('open');
			}
		};

		document.querySelectorAll('[data-ane-search-open]').forEach(function (btn) {
			btn.addEventListener('click', openDialog);
		});
		dialog.querySelectorAll('[data-ane-search-close]').forEach(function (btn) {
			btn.addEventListener('click', closeDialog);
		});
		// Click on the dimmed area (the <dialog> itself) closes it.
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) {
				closeDialog();
			}
		});
		dialog.addEventListener('close', function () {
			root.classList.remove('ane-lock');
		});
	}

	/* ---- Sticky behaviour --------------------------------------------------- */

	var lastY = window.pageYOffset;
	var ticking = false;

	function onScroll() {
		var y = window.pageYOffset;
		header.classList.toggle('is-stuck', y > 8);

		// Hide while scrolling down on mobile, show again on any upward scroll.
		var delta = y - lastY;
		if (Math.abs(delta) > 6) {
			var hide = !desktop.matches && !isOpen() && delta > 0 && y > header.offsetHeight * 2;
			header.classList.toggle('is-hidden', hide);
			lastY = y;
		}
		if (y <= 0) {
			header.classList.remove('is-hidden');
		}
		ticking = false;
	}

	window.addEventListener(
		'scroll',
		function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(onScroll);
			}
		},
		{ passive: true }
	);
	header.addEventListener('focusin', function () {
		header.classList.remove('is-hidden');
	});
	onScroll();

	// Expose the header height so anchors and the layout can offset it.
	function syncHeight() {
		root.style.setProperty('--ane-header-h', header.offsetHeight + 'px');
	}
	syncHeight();
	if (window.ResizeObserver) {
		new ResizeObserver(syncHeight).observe(header);
	} else {
		window.addEventListener('resize', syncHeight);
	}
})();
