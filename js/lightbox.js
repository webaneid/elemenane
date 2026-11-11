/**
 * Lightbox (vanilla JS, native <dialog>). Replaces Magnific Popup.
 *
 * - Links with class `gallery-popup` (images in content, branch gallery): clicking opens the image, with the other
 *   `gallery-popup` links inside the same container (.ane-content / .ane-sb__gallery / article) as previous / next.
 * - window.aneLightbox.open( items, index ) : items = [ { src, title } ].
 * - window.aneLightbox.openHtml( html, { className, onClose } ) : arbitrary markup (branch detail on product pages).
 * Esc closes (native), arrow keys and swipe navigate, focus returns to the trigger.
 *
 * @package elemenane
 */
(function () {
	'use strict';

	var l10n = window.elemenaneLightbox || {};
	var dlg = null;
	var els = {};
	var items = [];
	var index = 0;
	var returnFocus = null;
	var onClose = null;
	var touchX = null;

	function icon(path) {
		return '<svg viewBox="0 0 24 24" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false"><path d="' + path + '"/></svg>';
	}

	function build() {
		if (dlg) {
			return;
		}
		dlg = document.createElement('dialog');
		dlg.className = 'ane-lightbox';
		dlg.setAttribute('aria-label', l10n.label || 'Image viewer');
		dlg.innerHTML =
			'<button type="button" class="ane-lightbox__btn ane-lightbox__close" aria-label="' + (l10n.close || 'Close') + '">' + icon('M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z') + '</button>' +
			'<button type="button" class="ane-lightbox__btn ane-lightbox__nav ane-lightbox__prev" aria-label="' + (l10n.prev || 'Previous') + '">' + icon('M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z') + '</button>' +
			'<figure class="ane-lightbox__figure"><img class="ane-lightbox__img" alt=""><figcaption class="ane-lightbox__caption"></figcaption></figure>' +
			'<div class="ane-lightbox__html"></div>' +
			'<button type="button" class="ane-lightbox__btn ane-lightbox__nav ane-lightbox__next" aria-label="' + (l10n.next || 'Next') + '">' + icon('M8.59 16.59 13.17 12 8.59 7.41 10 6l6 6-6 6z') + '</button>' +
			'<span class="ane-lightbox__count" aria-live="polite"></span>';
		document.body.appendChild(dlg);

		els.img = dlg.querySelector('.ane-lightbox__img');
		els.cap = dlg.querySelector('.ane-lightbox__caption');
		els.html = dlg.querySelector('.ane-lightbox__html');
		els.count = dlg.querySelector('.ane-lightbox__count');
		els.prev = dlg.querySelector('.ane-lightbox__prev');
		els.next = dlg.querySelector('.ane-lightbox__next');

		dlg.querySelector('.ane-lightbox__close').addEventListener('click', function () { dlg.close(); });
		els.prev.addEventListener('click', function () { show(index - 1); });
		els.next.addEventListener('click', function () { show(index + 1); });
		// Click on the dark backdrop (the dialog itself, not its children) closes.
		dlg.addEventListener('click', function (e) {
			if (e.target === dlg || e.target.classList.contains('ane-lightbox__figure')) {
				dlg.close();
			}
		});
		dlg.addEventListener('keydown', function (e) {
			if (items.length > 1 && e.key === 'ArrowLeft') { show(index - 1); }
			if (items.length > 1 && e.key === 'ArrowRight') { show(index + 1); }
		});
		dlg.addEventListener('touchstart', function (e) { touchX = e.changedTouches[0].clientX; }, { passive: true });
		dlg.addEventListener('touchend', function (e) {
			if (touchX === null || items.length < 2) { return; }
			var dx = e.changedTouches[0].clientX - touchX;
			touchX = null;
			if (Math.abs(dx) > 50) { show(index + (dx < 0 ? 1 : -1)); }
		}, { passive: true });
		dlg.addEventListener('close', function () {
			document.documentElement.classList.remove('ane-lightbox-open');
			els.img.removeAttribute('src');
			els.html.innerHTML = '';
			if (typeof onClose === 'function') {
				var cb = onClose;
				onClose = null;
				cb();
			}
			if (returnFocus && returnFocus.focus) {
				returnFocus.focus();
			}
		});
	}

	function show(i) {
		if (!items.length) {
			return;
		}
		index = (i + items.length) % items.length;
		var item = items[index];
		els.img.src = item.src;
		els.img.alt = item.title || '';
		els.cap.textContent = item.title || '';
		els.cap.hidden = !item.title;
		els.count.textContent = items.length > 1 ? (index + 1) + ' / ' + items.length : '';
		// Warm the neighbour so arrow navigation feels instant.
		if (items.length > 1) {
			new Image().src = items[(index + 1) % items.length].src;
		}
	}

	function openDialog() {
		returnFocus = document.activeElement;
		document.documentElement.classList.add('ane-lightbox-open');
		dlg.showModal();
	}

	function open(list, start) {
		if (!list || !list.length) {
			return;
		}
		if (typeof HTMLDialogElement === 'undefined') {
			window.open(list[start || 0].src, '_blank', 'noopener');
			return;
		}
		build();
		items = list;
		dlg.className = 'ane-lightbox';
		dlg.classList.toggle('is-single', list.length === 1);
		els.html.hidden = true;
		els.img.parentNode.hidden = false;
		show(start || 0);
		openDialog();
	}

	function openHtml(html, options) {
		if (typeof HTMLDialogElement === 'undefined') {
			return;
		}
		build();
		options = options || {};
		items = [];
		dlg.className = 'ane-lightbox is-html' + (options.className ? ' ' + options.className : '');
		els.img.parentNode.hidden = true;
		els.html.hidden = false;
		els.html.innerHTML = html;
		els.count.textContent = '';
		onClose = options.onClose || null;
		openDialog();
	}

	window.aneLightbox = { open: open, openHtml: openHtml };

	// Image links in content / galleries.
	document.addEventListener('click', function (e) {
		var link = e.target.closest ? e.target.closest('a.gallery-popup') : null;
		if (!link || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) {
			return;
		}
		e.preventDefault();
		var scope = link.closest('.ane-content, .ane-sb__gallery, article') || document;
		var links = Array.prototype.slice.call(scope.querySelectorAll('a.gallery-popup'));
		var list = links.map(function (a) {
			var img = a.querySelector('img');
			return { src: a.href, title: a.getAttribute('title') || (img && img.alt) || '' };
		});
		open(list, Math.max(0, links.indexOf(link)));
	});
})();
